<?php
/**
 * Verifies that the configured API key actually works.
 *
 * @package OpenCodeZen\OpenCodeZenAiProvider\Connection
 */

declare(strict_types=1);

namespace OpenCodeZen\OpenCodeZenAiProvider\Connection;

use OpenCodeZen\OpenCodeZenAiProvider\Settings\Settings;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Asks OpenCode Zen whether the key is good.
 *
 * The settings page and the Connectors screen both reported "connected" as soon
 * as a key was present anywhere, without ever asking Zen about it. A key that
 * had been revoked, mistyped, or copied from the wrong account read as
 * configured, and the first sign of trouble was a generation failing somewhere
 * else entirely.
 *
 * Unlike most providers, Zen has no free endpoint that can answer this.
 * `GET /zen/v1/models` is public: it returns the full catalogue with no
 * credentials, and returns it just the same when handed a key that is nonsense.
 * So the question has to be asked of a generation endpoint, which is why this
 * uses one of Zen's own free-tier models and caps the answer at a single token —
 * enough to be authenticated, not enough to be a bill.
 *
 * @link https://opencode.ai/docs/zen/
 *
 * @since 1.6.0
 */
class ConnectionTest {

	/**
	 * The key is good.
	 *
	 * @since 1.6.0
	 */
	public const VALID = 'valid';

	/**
	 * Zen rejected the key.
	 *
	 * @since 1.6.0
	 */
	public const INVALID = 'invalid';

	/**
	 * Zen could not be asked.
	 *
	 * Distinct from `INVALID` on purpose. A site behind a firewall, or one that
	 * timed out, has learned nothing about its key, and telling the owner their
	 * key is wrong on that evidence would send them to reissue a working one.
	 *
	 * @since 1.6.0
	 */
	public const UNKNOWN = 'unknown';

	/**
	 * The model the check is addressed to.
	 *
	 * A free-tier model, so a site owner testing their connection is not
	 * spending anything to do it. If Zen ever retires it the request comes back
	 * as a model error rather than an auth error, which is reported as
	 * "could not be checked" rather than as a bad key.
	 *
	 * @since 1.6.0
	 */
	public const PROBE_MODEL = 'mimo-v2.5-free';

	/**
	 * How long a result is worth reusing.
	 *
	 * @since 1.6.0
	 */
	public const CACHE_TTL = 300;

	/**
	 * Where the last result is kept.
	 *
	 * @since 1.6.0
	 */
	public const TRANSIENT = 'opencode_zen_connection_test';

	/**
	 * Asks Zen whether the configured key works.
	 *
	 * @since 1.6.0
	 *
	 * @return array{status: string, message: string}
	 */
	public static function run(): array {
		$api_key = Settings::get_api_key();

		if ( '' === $api_key ) {
			return self::result(
				self::INVALID,
				__( 'No API key is configured.', 'alamin-ai-provider-for-opencode-zen' )
			);
		}

		$response = wp_remote_post(
			'https://opencode.ai/zen/v1/chat/completions',
			array(
				'timeout' => 20,
				'headers' => array(
					'Authorization'     => 'Bearer ' . $api_key,
					'Content-Type'      => 'application/json',
					'OpenCode-Provider' => 'wordpress-plugin',
				),
				'body'    => (string) wp_json_encode(
					array(
						'model'      => self::PROBE_MODEL,
						'messages'   => array(
							array(
								'role'    => 'user',
								'content' => 'ping',
							),
						),
						'max_tokens' => 1,
					)
				),
			)
		);

		if ( is_wp_error( $response ) ) {
			return self::result(
				self::UNKNOWN,
				sprintf(
					/* translators: %s: error message returned by WordPress. */
					__( 'OpenCode Zen could not be reached: %s', 'alamin-ai-provider-for-opencode-zen' ),
					$response->get_error_message()
				)
			);
		}

		$status = (int) wp_remote_retrieve_response_code( $response );

		if ( $status >= 200 && $status < 300 ) {
			return self::result(
				self::VALID,
				__( 'Connected. OpenCode Zen accepted the API key.', 'alamin-ai-provider-for-opencode-zen' )
			);
		}

		if ( 401 === $status || 403 === $status ) {
			return self::result(
				self::INVALID,
				sprintf(
					/* translators: %s: error message returned by OpenCode Zen. */
					__( 'OpenCode Zen rejected the API key: %s', 'alamin-ai-provider-for-opencode-zen' ),
					self::describe( $response )
				)
			);
		}

		/*
		 * Anything else — a retired probe model, a rate limit, a 5xx — says
		 * nothing about the key.
		 */
		return self::result(
			self::UNKNOWN,
			sprintf(
				/* translators: 1: HTTP status code. 2: error message returned by OpenCode Zen. */
				__( 'OpenCode Zen answered %1$d and the key could not be checked: %2$s', 'alamin-ai-provider-for-opencode-zen' ),
				$status,
				self::describe( $response )
			)
		);
	}

	/**
	 * The last result, if one was taken recently.
	 *
	 * @since 1.6.0
	 *
	 * @return array{status: string, message: string}|null
	 */
	public static function last_result(): ?array {
		$cached = get_transient( self::TRANSIENT );

		if ( ! is_array( $cached ) ) {
			return null;
		}

		$status  = $cached['status'] ?? null;
		$message = $cached['message'] ?? null;

		if ( ! is_string( $status ) || ! is_string( $message ) ) {
			return null;
		}

		return array(
			'status'  => $status,
			'message' => $message,
		);
	}

	/**
	 * Forgets the last result.
	 *
	 * Called when a key changes, because a cached "connected" outlives the key
	 * it was about.
	 *
	 * @since 1.6.0
	 *
	 * @return void
	 */
	public static function forget(): void {
		delete_transient( self::TRANSIENT );
	}

	/**
	 * Stores a result and returns it.
	 *
	 * @since 1.6.0
	 *
	 * @param string $status  One of the class constants.
	 * @param string $message Human-readable explanation.
	 * @return array{status: string, message: string}
	 */
	private static function result( string $status, string $message ): array {
		$result = array(
			'status'  => $status,
			'message' => $message,
		);

		set_transient( self::TRANSIENT, $result, self::CACHE_TTL );

		return $result;
	}

	/**
	 * Pulls Zen's own explanation out of an error response.
	 *
	 * @since 1.6.0
	 *
	 * @param array<string, mixed>|\WP_Error $response The HTTP response.
	 * @return string
	 */
	private static function describe( $response ): string {
		$body = json_decode( wp_remote_retrieve_body( $response ), true );

		if ( is_array( $body ) ) {
			$error = $body['error'] ?? null;

			if ( is_array( $error ) ) {
				$message = $error['message'] ?? null;

				if ( is_string( $message ) && '' !== $message ) {
					return $message;
				}
			}
		}

		$message = wp_remote_retrieve_response_message( $response );

		return '' !== $message
			? $message
			: __( 'no explanation given', 'alamin-ai-provider-for-opencode-zen' );
	}
}
