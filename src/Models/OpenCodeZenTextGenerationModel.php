<?php
/**
 * OpenCode Zen Text Generation Model.
 *
 * @package AlAminAhamed\OpenCodeZenAiProvider\Models
 */

declare(strict_types=1);

namespace AlAminAhamed\OpenCodeZenAiProvider\Models;

use WordPress\AiClient\Providers\DTO\ProviderMetadata;
use WordPress\AiClient\Providers\Http\DTO\Request;
use WordPress\AiClient\Providers\Http\Enums\HttpMethodEnum;
use WordPress\AiClient\Providers\Models\DTO\ModelMetadata;
use WordPress\AiClient\Providers\OpenAiCompatibleImplementation\AbstractOpenAiCompatibleTextGenerationModel;

/**
 * Text generation model for OpenCode Zen.
 *
 * @since 1.0.0
 */
class OpenCodeZenTextGenerationModel extends AbstractOpenAiCompatibleTextGenerationModel {

	/**
	 * Creates a request object for the provider's API.
	 *
	 * @since 1.0.0
	 *
	 * @param HttpMethodEnum $method The HTTP method.
	 * @param string         $path   The API endpoint path, relative to the base URI.
	 * @param array<string, string|list<string>> $headers The request headers.
	 * @param string|array<string, mixed>|null   $data   The request data.
	 * @return Request The request object.
	 */
	protected function createRequest(
		HttpMethodEnum $method,
		string $path,
		array $headers = [],
		$data = null
	): Request {
		return new Request(
			$method,
			$path,
			$headers,
			$data,
			$this->getRequestOptions()
		);
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
