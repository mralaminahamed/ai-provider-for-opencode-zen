<?php
/**
 * OpenCode Zen Model Metadata Directory.
 *
 * @package OpenCodeZen\OpenCodeZenAiProvider\Metadata
 */

declare(strict_types=1);

namespace OpenCodeZen\OpenCodeZenAiProvider\Metadata;

use WordPress\AiClient\Common\Exception\InvalidArgumentException;
use WordPress\AiClient\Messages\Enums\ModalityEnum;
use WordPress\AiClient\Providers\Contracts\ModelMetadataDirectoryInterface;
use WordPress\AiClient\Providers\Models\DTO\ModelMetadata;
use WordPress\AiClient\Providers\Models\DTO\SupportedOption;
use WordPress\AiClient\Providers\Models\Enums\CapabilityEnum;
use WordPress\AiClient\Providers\Models\Enums\OptionEnum;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Model metadata directory for OpenCode Zen.
 *
 * @since 1.0.0
 */
class ModelMetadataDirectory implements ModelMetadataDirectoryInterface {

	/**
	 * The models Zen documents as accepting image input.
	 *
	 * Zen's model table marks exactly one entry as vision-capable, and its
	 * pricing notes say images on that model are converted to tokens and billed
	 * as input. Nothing else in the catalogue is documented as reading images,
	 * so nothing else claims to.
	 *
	 * @since 1.6.0
	 *
	 * @var list<string>
	 */
	public const VISION_MODELS = array(
		'deepseek-v4-flash-vision-exp',
	);

	/**
	 * {@inheritDoc}
	 *
	 * @since 1.0.0
	 */
	public function listModelMetadata(): array {
		$models = $this->fetch_models_from_api();

		if ( ! empty( $models ) ) {
			return array_values( $models );
		}

		return array_values( $this->get_fallback_models() );
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
		/*
		 * This class is documented as usable outside WordPress, as a plain
		 * Composer package. It was only accidentally safe there: the API-key
		 * lookup guards `get_option()` and returned early, so the transient and
		 * HTTP calls below were never reached. Removing the key requirement
		 * removed that accident too, so the guard is now explicit.
		 *
		 * Without WordPress there is no HTTP layer to use and no cache to read;
		 * the caller falls back to the bundled catalogue.
		 */
		if ( ! function_exists( 'wp_remote_get' ) ) {
			return array();
		}

		$transient_key = 'opencode_zen_models_cache';
		$can_cache     = function_exists( 'get_transient' ) && function_exists( 'set_transient' );
		$cached        = $can_cache ? get_transient( $transient_key ) : false;

		if ( is_array( $cached ) ) {
			$valid = array();
			foreach ( $cached as $item ) {
				if ( $item instanceof ModelMetadata ) {
					$valid[] = $item;
				}
			}
			return $valid;
		}

		/*
		 * The catalogue is public.
		 *
		 * This used to return early without an API key, so a site that had not
		 * been configured yet — which is every site, at the moment somebody
		 * opens the settings page to configure it — saw the hardcoded fallback
		 * list instead of the real one. The model dropdown was therefore at its
		 * least accurate exactly when it was first being read.
		 *
		 * GET /zen/v1/models answers 200 with the full catalogue and no
		 * credentials. The key is still sent when there is one, because an
		 * authenticated caller may be shown models their account can reach that
		 * an anonymous one is not.
		 */
		$headers = array( 'Content-Type' => 'application/json' );
		$api_key = $this->get_api_key();

		if ( '' !== $api_key ) {
			$headers['Authorization'] = 'Bearer ' . $api_key;
		}

		$response = wp_remote_get(
			'https://opencode.ai/zen/v1/models',
			array(
				'headers' => $headers,
				'timeout' => 30,
			)
		);

		if ( is_wp_error( $response ) ) {
			$this->cache( $transient_key, array(), 5 * MINUTE_IN_SECONDS, $can_cache );
			return array();
		}

		$status_code = wp_remote_retrieve_response_code( $response );
		if ( $status_code < 200 || $status_code >= 300 ) {
			$this->cache( $transient_key, array(), 5 * MINUTE_IN_SECONDS, $can_cache );
			return array();
		}

		$body = wp_remote_retrieve_body( $response );
		$data = json_decode( $body, true );

		if ( ! is_array( $data ) || ! isset( $data['data'] ) || ! is_array( $data['data'] ) ) {
			$this->cache( $transient_key, array(), 5 * MINUTE_IN_SECONDS, $can_cache );
			return array();
		}

		$capabilities = $this->capabilities();

		$models = array();
		foreach ( $data['data'] as $model_data ) {
			if ( ! is_array( $model_data ) ) {
				continue;
			}

			$id = isset( $model_data['id'] ) && is_string( $model_data['id'] ) ? $model_data['id'] : '';
			if ( '' === $id ) {
				continue;
			}

			$name_raw = $model_data['name'] ?? $id;
			$name     = is_string( $name_raw ) ? $name_raw : $id;

			$models[] = new ModelMetadata( $id, $name, $capabilities, $this->supported_options( $this->is_vision_model( $id ) ) );
		}

		$this->cache( $transient_key, $models, HOUR_IN_SECONDS, $can_cache );

		return $models;
	}

	/**
	 * Write to the transient cache when there is one to write to.
	 *
	 * @since 1.5.0
	 *
	 * @param string          $key       Transient key.
	 * @param ModelMetadata[] $value     Value to store.
	 * @param int             $ttl       Lifetime in seconds.
	 * @param bool            $can_cache Whether the transient API is available.
	 * @return void
	 */
	private function cache( string $key, array $value, int $ttl, bool $can_cache ): void {
		if ( ! $can_cache ) {
			return;
		}

		set_transient( $key, $value, $ttl );
	}

	/**
	 * The generation options Zen honours.
	 *
	 * `SupportedOption` is a promise, not a wish list. The AI Client uses it to
	 * decide which model can satisfy a request, so declaring an option here
	 * routes callers who need that option to Zen — and if Zen ignores it, they
	 * get a silently wrong answer rather than an error.
	 *
	 * This used to be written out twice, once for the live catalogue and once
	 * for the fallback, which is two places for the same promise to drift.
	 *
	 * @since 1.6.0
	 *
	 * @param bool $vision Whether this model reads images.
	 * @return list<SupportedOption>
	 */
	private function supported_options( bool $vision = false ): array {
		$options = array(
			new SupportedOption( OptionEnum::temperature() ),
			new SupportedOption( OptionEnum::maxTokens() ),
			new SupportedOption( OptionEnum::topP() ),
			new SupportedOption( OptionEnum::presencePenalty() ),
			new SupportedOption( OptionEnum::frequencyPenalty() ),
			new SupportedOption( OptionEnum::stopSequences() ),
			new SupportedOption( OptionEnum::systemInstruction() ),
			new SupportedOption( OptionEnum::functionDeclarations() ),

			/*
			 * A passthrough for anything the SDK does not model. The base class
			 * already merges these into the request body — the option was
			 * simply never declared, so no caller could reach it. All three
			 * official WordPress providers declare it.
			 *
			 * It matters more here than for a single-vendor provider: Zen
			 * fronts eight vendors, and a parameter that only one of them
			 * understands has nowhere else to go.
			 */
			new SupportedOption( OptionEnum::customOptions() ),
		);

		/*
		 * Declared per model, and only where Zen documents it. All three
		 * official WordPress providers declare input modalities, and without
		 * the declaration the AI Client will not route an image prompt here at
		 * all.
		 *
		 * Zen fronts several vendors whose models are multimodal upstream, but
		 * Zen is a coding gateway and documents image input for exactly one
		 * entry in its catalogue — the one that says so in its name, and the
		 * only one with a note about images being billed as input tokens.
		 * Declaring vision for the rest on the strength of what the underlying
		 * vendor supports would be a guess, and a guess here routes real
		 * traffic.
		 *
		 * @link https://opencode.ai/docs/zen/
		 */
		if ( $vision ) {
			$options[] = new SupportedOption(
				OptionEnum::inputModalities(),
				array(
					array( ModalityEnum::text() ),
					array( ModalityEnum::text(), ModalityEnum::image() ),
				)
			);
		}

		return $options;
	}

	/**
	 * Whether Zen documents this model as accepting images.
	 *
	 * @since 1.6.0
	 *
	 * @param string $model_id Model id.
	 * @return bool
	 */
	private function is_vision_model( string $model_id ): bool {
		return in_array( $model_id, self::VISION_MODELS, true );
	}

	/**
	 * What these models can do.
	 *
	 * `chatHistory` was missing, and every official WordPress provider declares
	 * it. It means the model accepts a conversation rather than a single
	 * prompt — which OpenCode Zen has always done, because the endpoint takes a
	 * `messages` array and the SDK already sends one. Without the declaration
	 * the AI Client will not route a chat request here, so the plugin was
	 * declining work it could do.
	 *
	 * @since 1.5.0
	 *
	 * @return list<CapabilityEnum>
	 */
	private function capabilities(): array {
		return array(
			CapabilityEnum::textGeneration(),
			CapabilityEnum::chatHistory(),
		);
	}

	/**
	 * Get fallback models when API is not available.
	 *
	 * @since 1.0.0
	 *
	 * @return ModelMetadata[]
	 */
	private function get_fallback_models(): array {
		$capabilities = $this->capabilities();

		/*
		 * Mirrors the catalogue returned by GET /zen/v1/models.
		 *
		 * Synced against the live endpoint on 2026-09-07. That sync added fourteen
		 * models and dropped five the endpoint no longer serves — a stale entry
		 * here is worse than a missing one, because selecting it produces a model
		 * the API rejects.
		 *
		 * This list is only reached when the endpoint cannot be read at all. It
		 * is a floor, not a source of truth, and it will drift: re-sync it with
		 * `curl https://opencode.ai/zen/v1/models` rather than by hand.
		 */
		$model_list = array(
			// Claude models.
			array( 'claude-fable-5', 'Claude Fable 5' ),
			array( 'claude-fable-5-1', 'Claude Fable 5.1' ),
			array( 'claude-opus-5', 'Claude Opus 5' ),
			array( 'claude-opus-4-8', 'Claude Opus 4.8' ),
			array( 'claude-opus-4-7', 'Claude Opus 4.7' ),
			array( 'claude-opus-4-6', 'Claude Opus 4.6' ),
			array( 'claude-opus-4-5', 'Claude Opus 4.5' ),
			array( 'claude-sonnet-5', 'Claude Sonnet 5' ),
			array( 'claude-sonnet-4-6', 'Claude Sonnet 4.6' ),
			array( 'claude-sonnet-4-5', 'Claude Sonnet 4.5' ),
			array( 'claude-sonnet-4', 'Claude Sonnet 4' ),
			array( 'claude-haiku-4-5', 'Claude Haiku 4.5' ),
			// GPT models.
			array( 'gpt-6-astra', 'GPT 6 Astra' ),
			array( 'gpt-5.6-sol', 'GPT 5.6 Sol' ),
			array( 'gpt-5.6-terra', 'GPT 5.6 Terra' ),
			array( 'gpt-5.6-luna', 'GPT 5.6 Luna' ),
			array( 'gpt-5.5', 'GPT 5.5' ),
			array( 'gpt-5.5-pro', 'GPT 5.5 Pro' ),
			array( 'gpt-5.4', 'GPT 5.4' ),
			array( 'gpt-5.4-pro', 'GPT 5.4 Pro' ),
			array( 'gpt-5.4-mini', 'GPT 5.4 Mini' ),
			array( 'gpt-5.4-nano', 'GPT 5.4 Nano' ),
			array( 'gpt-5.3-codex-spark', 'GPT 5.3 Codex Spark' ),
			array( 'gpt-5.3-codex', 'GPT 5.3 Codex' ),
			array( 'gpt-5.2', 'GPT 5.2' ),
			array( 'gpt-5.2-codex', 'GPT 5.2 Codex' ),
			array( 'gpt-5.1', 'GPT 5.1' ),
			array( 'gpt-5.1-codex-max', 'GPT 5.1 Codex Max' ),
			array( 'gpt-5.1-codex', 'GPT 5.1 Codex' ),
			array( 'gpt-5.1-codex-mini', 'GPT 5.1 Codex Mini' ),
			array( 'gpt-5', 'GPT 5' ),
			array( 'gpt-5-codex', 'GPT 5 Codex' ),
			array( 'gpt-5-nano', 'GPT 5 Nano' ),
			// Gemini models.
			array( 'gemini-3.6-flash', 'Gemini 3.6 Flash' ),
			array( 'gemini-3.8-flash', 'Gemini 3.8 Flash' ),
			array( 'gemini-3.7-flash', 'Gemini 3.7 Flash' ),
			array( 'gemini-3.5-flash-lite', 'Gemini 3.5 Flash Lite' ),
			array( 'gemini-3.5-flash', 'Gemini 3.5 Flash' ),
			array( 'gemini-3.1-pro', 'Gemini 3.1 Pro' ),
			array( 'gemini-3-flash', 'Gemini 3 Flash' ),
			// Grok models.
			array( 'grok-build-0.1', 'Grok Build 0.1' ),
			array( 'grok-4.6', 'Grok 4.6' ),
			array( 'grok-4.5', 'Grok 4.5' ),
			// DeepSeek models.
			array( 'deepseek-v4-pro', 'DeepSeek V4 Pro' ),
			array( 'deepseek-v4-flash', 'DeepSeek V4 Flash' ),
			array( 'deepseek-v4-flash-vision-exp', 'DeepSeek v4 Flash Vision Exp' ),
			array( 'deepseek-v4-flash-free', 'DeepSeek V4 Flash Free' ),
			// GLM models.
			array( 'glm-5.3-flash', 'GLM 5.3 Flash' ),
			array( 'glm-5.3', 'GLM 5.3' ),
			array( 'glm-5.2', 'GLM 5.2' ),
			array( 'glm-5.1', 'GLM 5.1' ),
			array( 'glm-5', 'GLM 5' ),
			// Kimi models.
			array( 'kimi-k3', 'Kimi K3' ),
			array( 'kimi-k2.7-code', 'Kimi K2.7 Code' ),
			array( 'kimi-k2.6', 'Kimi K2.6' ),
			array( 'kimi-k2.5', 'Kimi K2.5' ),
			// Qwen models.
			array( 'qwen3.6-plus', 'Qwen 3.6 Plus' ),
			array( 'qwen3.5-plus', 'Qwen 3.5 Plus' ),
			// MiniMax models.
			array( 'minimax-m3', 'MiniMax M3' ),
			array( 'minimax-m2.7', 'MiniMax M2.7' ),
			array( 'minimax-m2.5', 'MiniMax M2.5' ),
			// Other vendors and free tiers.
			array( 'muse-spark-1.3', 'Muse Spark 1.3' ),
			array( 'muse-spark-1.2', 'Muse Spark 1.2' ),
			array( 'big-pickle', 'Big Pickle' ),
			array( 'muse-spark-1.3-contributor-free', 'Muse Spark 1.3 Contributor Free' ),
			array( 'muse-spark-1.2-contributor-free', 'Muse Spark 1.2 Contributor Free' ),
			array( 'mimo-v2.5-free', 'MiMo v2.5 Free' ),
			array( 'ling-3.0-flash-fin-free', 'Ling 3.0 Flash Fin Free' ),
			array( 'nemotron-3-ultra-free', 'Nemotron 3 Ultra Free' ),
			array( 'nemotron-3.5-lightning-free', 'Nemotron 3.5 Lightning Free' ),
		);

		$models = array();
		foreach ( $model_list as $item ) {
			$models[] = new ModelMetadata( $item[0], $item[1], $capabilities, $this->supported_options( $this->is_vision_model( $item[0] ) ) );
		}

		return $models;
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

		if ( ! function_exists( 'get_option' ) ) {
			return '';
		}

		// Key stored by WordPress Connectors page (WP 7.0+).
		$connectors_key = get_option( 'connectors_ai_opencode_zen_api_key', '' );
		if ( is_string( $connectors_key ) && '' !== $connectors_key ) {
			return $connectors_key;
		}

		// Key stored via legacy wp_ai_client_credentials option.
		$option = get_option( 'wp_ai_client_credentials', array() );
		if ( ! is_array( $option ) ) {
			return '';
		}
		$credentials = $option['opencode-zen'] ?? array();
		if ( ! is_array( $credentials ) ) {
			return '';
		}
		$api_key_value = $credentials['api_key'] ?? '';
		return is_string( $api_key_value ) ? $api_key_value : '';
	}
}
