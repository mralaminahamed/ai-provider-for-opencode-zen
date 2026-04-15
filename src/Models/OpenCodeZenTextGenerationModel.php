<?php
/**
 * OpenCode Zen Text Generation Model.
 *
 * @package WordPress\OpenCodeZenAiProvider\Models
 */

declare(strict_types=1);

namespace WordPress\OpenCodeZenAiProvider\Models;

use WordPress\AiClient\Providers\DTO\ProviderMetadata;
use WordPress\AiClient\Providers\Models\DTO\ModelMetadata;
use WordPress\AiClient\Providers\Models\TextGenerationModel;

/**
 * Text generation model for OpenCode Zen.
 *
 * @since 1.0.0
 */
class OpenCodeZenTextGenerationModel extends TextGenerationModel {

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param ModelMetadata    $model_metadata    Model metadata.
	 * @param ProviderMetadata $provider_metadata Provider metadata.
	 */
	public function __construct(
		ModelMetadata $model_metadata,
		ProviderMetadata $provider_metadata
	) {
		parent::__construct( $model_metadata, $provider_metadata );
	}

	/**
	 * Get the default headers for the model.
	 *
	 * @since 1.0.0
	 *
	 * @return array<string, string>
	 */
	protected function getDefaultHeaders(): array {
		$headers = parent::getDefaultHeaders();

		$headers['OpenCode-Provider'] = 'wordpress-plugin';

		return $headers;
	}
}
