<?php
/**
 * OpenCode Zen Model Metadata Directory.
 *
 * @package WordPress\OpenCodeZenAiProvider\Metadata
 */

declare(strict_types=1);

namespace WordPress\OpenCodeZenAiProvider\Metadata;

use WordPress\AiClient\Providers\Contracts\ModelMetadataDirectoryInterface;
use WordPress\AiClient\Providers\Models\Capabilities\TextGenerationCapability;
use WordPress\AiClient\Providers\Models\DTO\ModelMetadata;
use WordPress\AiClient\Providers\Models\Enums\CapabilityEnum;

/**
 * Model metadata directory for OpenCode Zen.
 *
 * @since 1.0.0
 */
class OpenCodeZenModelMetadataDirectory implements ModelMetadataDirectoryInterface {

	/**
	 * Get all model metadata.
	 *
	 * @since 1.0.0
	 *
	 * @return ModelMetadata[]
	 */
	public function get_all(): array {
		$models = $this->fetch_models_from_api();

		if ( ! empty( $models ) ) {
			return $models;
		}

		return $this->get_fallback_models();
	}

	/**
	 * Get model metadata by ID.
	 *
	 * @since 1.0.0
	 *
	 * @param string $model_id Model ID.
	 * @return ModelMetadata|null
	 */
	public function get( string $model_id ): ?ModelMetadata {
		$all_models = $this->get_all();

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
			return array();
		}

		$body    = wp_remote_retrieve_body( $response );
		$data    = json_decode( $body, true );

		if ( ! isset( $data['data'] ) || ! is_array( $data['data'] ) ) {
			return array();
		}

		$models = array();
		foreach ( $data['data'] as $model_data ) {
			if ( ! isset( $model_data['id'] ) ) {
				continue;
			}

			$models[] = new ModelMetadata(
				$model_data['id'],
				$model_data.get( 'name', $model_data['id'] ),
				array(
					new TextGenerationCapability(
						CapabilityEnum::text_generation(),
						array(
							'max_tokens'       => $model_data['details']['max_tokens'] ?? 128000,
							'context_window'  => $model_data['details']['context_window'] ?? 128000,
							'supports_vision' => false,
						)
					),
				)
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
		return array(
			new ModelMetadata(
				'gpt-4o',
				'GPT-4o',
				array(
					new TextGenerationCapability(
						CapabilityEnum::text_generation(),
						array(
							'max_tokens'       => 128000,
							'context_window'  => 128000,
							'supports_vision' => true,
						)
					),
				)
			),
			new ModelMetadata(
				'gpt-4o-mini',
				'GPT-4o Mini',
				array(
					new TextGenerationCapability(
						CapabilityEnum::text_generation(),
						array(
							'max_tokens'       => 128000,
							'context_window'  => 128000,
							'supports_vision' => false,
						)
					),
				)
			),
			new ModelMetadata(
				'claude-sonnet-4',
				'Claude Sonnet 4',
				array(
					new TextGenerationCapability(
						CapabilityEnum::text_generation(),
						array(
							'max_tokens'       => 200000,
							'context_window'  => 200000,
							'supports_vision' => true,
						)
					),
				)
			),
			new ModelMetadata(
				'claude-3-5-sonnet',
				'Claude 3.5 Sonnet',
				array(
					new TextGenerationCapability(
						CapabilityEnum::text_generation(),
						array(
							'max_tokens'       => 200000,
							'context_window'  => 200000,
							'supports_vision' => true,
						)
					),
				)
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
