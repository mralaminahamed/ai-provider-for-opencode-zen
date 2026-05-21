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
use WordPress\AiClient\Providers\Contracts\ModelMetadataDirectoryInterface;
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
		$this->assertEquals( 'https://opencode.ai/zen/v1', OpenCodeZenProvider::url() );
	}

	/**
	 * Test provider metadata has correct ID.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function test_provider_metadata_has_correct_id(): void {
		$this->assertEquals( 'opencode-zen', OpenCodeZenProvider::metadata()->getId() );
	}

	/**
	 * Test provider metadata has correct name.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function test_provider_metadata_has_correct_name(): void {
		$this->assertEquals( 'OpenCode Zen', OpenCodeZenProvider::metadata()->getName() );
	}

	/**
	 * Test provider metadata has correct type.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function test_provider_metadata_has_correct_type(): void {
		$this->assertEquals( ProviderTypeEnum::cloud(), OpenCodeZenProvider::metadata()->getType() );
	}

	/**
	 * Test provider metadata has API key authentication.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function test_provider_metadata_has_api_key_auth(): void {
		$this->assertEquals(
			RequestAuthenticationMethod::apiKey(),
			OpenCodeZenProvider::metadata()->getAuthenticationMethod()
		);
	}

	/**
	 * Test provider returns a model metadata directory.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function test_provider_has_model_metadata_directory(): void {
		$this->assertInstanceOf(
			ModelMetadataDirectoryInterface::class,
			OpenCodeZenProvider::modelMetadataDirectory()
		);
	}

	/**
	 * Test provider metadata has a credentials URL.
	 *
	 * @since 1.1.0
	 *
	 * @return void
	 */
	public function test_provider_has_credentials_url(): void {
		$url = OpenCodeZenProvider::metadata()->getCredentialsUrl();

		$this->assertNotEmpty( $url );
		$this->assertStringStartsWith( 'https://', $url );
	}

	/**
	 * Test provider logo SVG file exists on disk.
	 *
	 * @since 1.1.0
	 *
	 * @return void
	 */
	public function test_provider_logo_file_exists(): void {
		$path = OpenCodeZenProvider::metadata()->getLogoPath();

		$this->assertNotEmpty( $path );
		$this->assertFileExists( $path );
		$this->assertStringEndsWith( '.svg', $path );
	}
}
