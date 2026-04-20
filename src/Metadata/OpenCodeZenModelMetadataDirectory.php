<?php
/**
 * OpenCode Zen Model Metadata Directory.
 *
 * @package AlAminAhamed\OpenCodeZenAiProvider\Metadata
 */

declare(strict_types=1);

namespace AlAminAhamed\OpenCodeZenAiProvider\Metadata;

use WordPress\AiClient\Common\Exception\InvalidArgumentException;
use WordPress\AiClient\Providers\Contracts\ModelMetadataDirectoryInterface;
use WordPress\AiClient\Providers\Models\DTO\ModelMetadata;
use WordPress\AiClient\Providers\Models\DTO\SupportedOption;
use WordPress\AiClient\Providers\Models\Enums\CapabilityEnum;
use WordPress\AiClient\Providers\Models\Enums\OptionEnum;

/**
 * Model metadata directory for OpenCode Zen.
 *
 * @since 1.0.0
 */
class OpenCodeZenModelMetadataDirectory implements ModelMetadataDirectoryInterface {

	/**
	 * {@inheritDoc}
	 *
	 * @since 1.0.0
	 */
	public function listModelMetadata(): array {
		$models = $this->fetch_models_from_api();

		if ( ! empty( $models ) ) {
			return $models;
		}

		return $this->get_fallback_models();
	}

	/**
	 * {@inheritDoc}
	 *
	 * @since 1.0.0
	 *
	 * @param string $model_id Model ID.
	 */
	public function hasModelMetadata( string $model_id ): bool {
		return $this->get( $model_id ) !== null;
	}

	/**
	 * {@inheritDoc}
	 *
	 * @since 1.0.0
	 *
	 * @param string $model_id Model ID.
	 * @throws InvalidArgumentException If model metadata not found.
	 */
	public function getModelMetadata( string $model_id ): ModelMetadata {
		$model = $this->get( $model_id );

		if ( null === $model ) {
			throw new InvalidArgumentException(
				sprintf( 'Model metadata not found for model: %s', $model_id ) // phpcs:ignore WordPress.Security.EscapeOutput.ExceptionNotEscaped
			);
		}

		return $model;
	}

	/**
	 * Get model metadata by ID.
	 *
	 * @since 1.0.0
	 *
	 * @param string $model_id Model ID.
	 * @return ModelMetadata|null
	 */
	private function get( string $model_id ): ?ModelMetadata {
		$all_models = $this->listModelMetadata();

		foreach ( $all_models as $model ) {
			if ( $model->getId() === $model_id ) {
				return $model;
			}
		}

		return null;
	}

	/**
	 * Fetch models from the OpenCode Zen API.
	 *
	 * @since 1.0.0
	 *
	 * @return ModelMetadata[]
	 */
	private function fetch_models_from_api(): array {
		$api_key = $this->get_api_key();

		if ( empty( $api_key ) ) {
			return array();
		}

		$transient_key = 'opencode_zen_models_cache';
		$cached        = get_transient( $transient_key );

		if ( false !== $cached ) {
			return $cached;
		}

		$response = wp_remote_get(
			'https://opencode.ai/zen/v1/models',
			array(
				'headers' => array(
					'Authorization' => 'Bearer ' . $api_key,
					'Content-Type'  => 'application/json',
				),
				'timeout' => 30,
			)
		);

		if ( is_wp_error( $response ) ) {
			set_transient( $transient_key, array(), 5 * MINUTE_IN_SECONDS );
			return array();
		}

		$status_code = wp_remote_retrieve_response_code( $response );
		if ( $status_code < 200 || $status_code >= 300 ) {
			set_transient( $transient_key, array(), 5 * MINUTE_IN_SECONDS );
			return array();
		}

		$body = wp_remote_retrieve_body( $response );
		$data = json_decode( $body, true );

		if ( ! is_array( $data ) || ! isset( $data['data'] ) || ! is_array( $data['data'] ) ) {
			set_transient( $transient_key, array(), 5 * MINUTE_IN_SECONDS );
			return array();
		}

		$capabilities = array(
			CapabilityEnum::textGeneration(),
		);
		$options      = array(
			new SupportedOption( OptionEnum::maxTokens() ),
		);

		$models = array();
		foreach ( $data['data'] as $model_data ) {
			if ( ! isset( $model_data['id'] ) ) {
				continue;
			}

			$models[] = new ModelMetadata(
				$model_data['id'],
				$model_data['name'] ?? $model_data['id'],
				$capabilities,
				$options
			);
		}

		set_transient( $transient_key, $models, HOUR_IN_SECONDS );

		return $models;
	}

	/**
	 * Get fallback models when API is not available.
	 *
	 * @since 1.0.0
	 *
	 * @return ModelMetadata[]
	 */
	private function get_fallback_models(): array {
		$capabilities = array(
			CapabilityEnum::textGeneration(),
		);
		$options      = array(
			new SupportedOption( OptionEnum::maxTokens() ),
		);

		return array(
			new ModelMetadata(
				'gpt-4o',
				'GPT-4o',
				$capabilities,
				$options
			),
			new ModelMetadata(
				'gpt-4o-mini',
				'GPT-4o Mini',
				$capabilities,
				$options
			),
			new ModelMetadata(
				'claude-sonnet-4',
				'Claude Sonnet 4',
				$capabilities,
				$options
			),
			new ModelMetadata(
				'claude-3-5-sonnet',
				'Claude 3.5 Sonnet',
				$capabilities,
				$options
			),
		);
	}

	/**
	 * Get the API key.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	private function get_api_key(): string {
		$api_key = getenv( 'OPENCODE_ZEN_API_KEY' );

		if ( ! empty( $api_key ) ) {
			return $api_key;
		}

		if ( function_exists( 'get_option' ) ) {
			$option = get_option( 'wp_ai_client_credentials', array() );
			return $option['opencode-zen']['api_key'] ?? '';
		}

		return '';
	}
}
