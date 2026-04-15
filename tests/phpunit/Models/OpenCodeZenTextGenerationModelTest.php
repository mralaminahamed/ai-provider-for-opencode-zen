<?php
/**
 * Tests for OpenCodeZenTextGenerationModel.
 *
 * @package AlAminAhamed\OpenCodeZenAiProvider\Tests\Models
 */

declare(strict_types=1);

namespace AlAminAhamed\OpenCodeZenAiProvider\Tests\Models;

use AlAminAhamed\OpenCodeZenAiProvider\Metadata\OpenCodeZenModelMetadataDirectory;
use AlAminAhamed\OpenCodeZenAiProvider\Models\OpenCodeZenTextGenerationModel;
use AlAminAhamed\OpenCodeZenAiProvider\Provider\OpenCodeZenProvider;
use PHPUnit\Framework\TestCase;

/**
 * Class OpenCodeZenTextGenerationModelTest
 *
 * @since 1.0.0
 */
class OpenCodeZenTextGenerationModelTest extends TestCase {

	/**
	 * Test model is created correctly.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function test_model_is_created_correctly(): void {
		$directory      = new OpenCodeZenModelMetadataDirectory();
		$provider_meta  = OpenCodeZenProvider::getProviderMetadata();
		$model_metadata = $directory->getModelMetadata( 'gpt-4o' );

		$model = OpenCodeZenProvider::createModel( $model_metadata, $provider_meta );

		$this->assertInstanceOf( OpenCodeZenTextGenerationModel::class, $model );
	}

	/**
	 * Test model has correct metadata.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function test_model_has_correct_metadata(): void {
		$directory      = new OpenCodeZenModelMetadataDirectory();
		$provider_meta  = OpenCodeZenProvider::getProviderMetadata();
		$model_metadata = $directory->getModelMetadata( 'gpt-4o' );

		$model = OpenCodeZenProvider::createModel( $model_metadata, $provider_meta );

		$this->assertEquals( 'gpt-4o', $model->getModelMetadata()->getId() );
	}

	/**
	 * Test model has OpenCode provider header.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function test_model_has_opencode_provider_header(): void {
		$directory      = new OpenCodeZenModelMetadataDirectory();
		$provider_meta  = OpenCodeZenProvider::getProviderMetadata();
		$model_metadata = $directory->getModelMetadata( 'gpt-4o' );

		$model = OpenCodeZenProvider::createModel( $model_metadata, $provider_meta );

		$headers = $model->getDefaultHeaders();

		$this->assertArrayHasKey( 'OpenCode-Provider', $headers );
		$this->assertEquals( 'wordpress-plugin', $headers['OpenCode-Provider'] );
	}
}
