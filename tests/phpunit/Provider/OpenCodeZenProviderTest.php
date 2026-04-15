<?php
/**
 * Tests for OpenCodeZenProvider.
 *
 * @package AlAminAhamed\OpenCodeZenAiProvider\Tests\Provider
 */

declare(strict_types=1);

namespace AlAminAhamed\OpenCodeZenAiProvider\Tests\Provider;

use AlAminAhamed\OpenCodeZenAiProvider\Provider\OpenCodeZenProvider;
use PHPUnit\Framework\TestCase;
use WordPress\AiClient\Providers\Enums\ProviderTypeEnum;
use WordPress\AiClient\Providers\Http\Enums\RequestAuthenticationMethod;

/**
 * Class OpenCodeZenProviderTest
 *
 * @since 1.0.0
 */
class OpenCodeZenProviderTest extends TestCase {

	/**
	 * Test provider has correct base URL.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function test_provider_has_correct_base_url(): void {
		$base_url = OpenCodeZenProvider::url();

		$this->assertEquals( 'https://opencode.ai/zen/v1', $base_url );
	}

	/**
	 * Test provider metadata has correct ID.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function test_provider_metadata_has_correct_id(): void {
		$metadata = OpenCodeZenProvider::getProviderMetadata();

		$this->assertEquals( 'opencode-zen', $metadata->getId() );
	}

	/**
	 * Test provider metadata has correct name.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function test_provider_metadata_has_correct_name(): void {
		$metadata = OpenCodeZenProvider::getProviderMetadata();

		$this->assertEquals( 'OpenCode Zen', $metadata->getName() );
	}

	/**
	 * Test provider metadata has correct type.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function test_provider_metadata_has_correct_type(): void {
		$metadata = OpenCodeZenProvider::getProviderMetadata();

		$this->assertEquals( ProviderTypeEnum::cloud(), $metadata->getType() );
	}

	/**
	 * Test provider metadata has correct authentication method.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function test_provider_metadata_has_api_key_auth(): void {
		$metadata = OpenCodeZenProvider::getProviderMetadata();

		$this->assertEquals( RequestAuthenticationMethod::apiKey(), $metadata->getAuthenticationMethod() );
	}

	/**
	 * Test provider metadata directory is set.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function test_provider_has_model_metadata_directory(): void {
		$directory = OpenCodeZenProvider::modelMetadataDirectory();

		$this->assertInstanceOf(
			\WordPress\AiClient\Providers\Contracts\ModelMetadataDirectoryInterface::class,
			$directory
		);
	}
}
