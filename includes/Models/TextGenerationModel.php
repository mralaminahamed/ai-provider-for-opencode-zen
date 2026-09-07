<?php
/**
 * OpenCode Zen Text Generation Model.
 *
 * @package OpenCodeZen\OpenCodeZenAiProvider\Models
 */

declare(strict_types=1);

namespace OpenCodeZen\OpenCodeZenAiProvider\Models;

use OpenCodeZen\OpenCodeZenAiProvider\Provider\Provider;
use OpenCodeZen\OpenCodeZenAiProvider\Settings\Settings;
use WordPress\AiClient\Messages\DTO\Message;
use WordPress\AiClient\Providers\Http\DTO\Request;
use WordPress\AiClient\Providers\Http\Enums\HttpMethodEnum;
use WordPress\AiClient\Providers\OpenAiCompatibleImplementation\AbstractOpenAiCompatibleTextGenerationModel;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Text generation model for OpenCode Zen.
 *
 * @since 1.0.0
 */
class TextGenerationModel extends AbstractOpenAiCompatibleTextGenerationModel {

	/**
	 * Applies the site's saved defaults to a generation request.
	 *
	 * The settings screen has stored these since 1.0.0 and nothing has ever
	 * read them — no code outside `Settings` touched
	 * `opencode_zen_settings`. Saving the form wrote a row to `wp_options` and
	 * changed no request that followed.
	 *
	 * A caller's own value always wins; this fills only what was left unset.
	 *
	 * @since 1.5.0
	 *
	 * @param Message[] $prompt The prompt to generate text for.
	 *
	 * @phpstan-param list<Message> $prompt
	 * @return array<string, mixed> The parameters for the API request.
	 */
	protected function prepareGenerateTextParams( array $prompt ): array {
		$params = parent::prepareGenerateTextParams( $prompt );

		if ( ! function_exists( 'get_option' ) ) {
			return $params;
		}

		$settings = Settings::get_settings();

		$keys = array(
			'temperature'       => 'temperature',
			'max_tokens'        => 'max_tokens',
			'top_p'             => 'top_p',
			'presence_penalty'  => 'presence_penalty',
			'frequency_penalty' => 'frequency_penalty',
		);

		foreach ( $keys as $param => $setting ) {
			if ( ! isset( $params[ $param ] ) && isset( $settings[ $setting ] ) ) {
				$params[ $param ] = $settings[ $setting ];
			}
		}

		/**
		 * Filters the parameters sent to the OpenCode Zen endpoint.
		 *
		 * Zen fronts several vendors on one API, and they do not accept an
		 * identical parameter set — the penalties mean nothing to the Claude and
		 * Gemini families, for instance. This is where a site can drop or add
		 * parameters for the model it actually routes to.
		 *
		 * @since 1.5.0
		 *
		 * @param array<string, mixed> $params   The request parameters.
		 * @param array<string, mixed> $settings The saved plugin settings.
		 * @param string               $model_id The model being called.
		 */
		$filtered = apply_filters(
			'opencode_zen_generate_text_params',
			$params,
			$settings,
			$this->metadata()->getId()
		);

		/*
		 * Checked rather than trusted: a filter returns whatever a third party
		 * gave it, and a callback returning null or a string would otherwise
		 * become the request body.
		 *
		 * PHPStan reads the filter's own `@param` as its return contract and so
		 * calls this redundant. It is redundant only for callbacks that honour
		 * the contract, which is not something this code can assume.
		 *
		 * @phpstan-ignore function.alreadyNarrowedType
		 */
		if ( ! is_array( $filtered ) ) {
			return $params;
		}

		return $filtered;
	}

	/**
	 * Creates a request object for the provider's API.
	 *
	 * The SDK hands this method a path relative to the provider's base URI —
	 * `chat/completions` — and expects an absolute URL back. It was passed
	 * straight into the `Request`, so every generation request this plugin has
	 * ever made was addressed to a host-less URI and could not be sent. The
	 * three official WordPress providers all resolve the path the same way,
	 * through their provider's `url()`.
	 *
	 * @since 1.0.0
	 * @since 1.6.0 Resolves the path against the provider's base URL.
	 *
	 * @param HttpMethodEnum                     $method The HTTP method.
	 * @param string                             $path   The API endpoint path, relative to the base URI.
	 * @param array<string, string|list<string>> $headers The request headers.
	 * @param string|array<string, mixed>|null   $data   The request data.
	 * @return Request The request object.
	 */
	protected function createRequest( HttpMethodEnum $method, string $path, array $headers = array(), $data = null ): Request {
		$headers['OpenCode-Provider'] = 'wordpress-plugin';

		return new Request(
			$method,
			Provider::url( $path ),
			$headers,
			$data,
			$this->getRequestOptions()
		);
	}
}
