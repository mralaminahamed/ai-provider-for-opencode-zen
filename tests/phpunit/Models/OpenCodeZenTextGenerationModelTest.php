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
		$model = OpenCodeZenProvider::model( 'gpt-4o' );

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
		$model = OpenCodeZenProvider::model( 'gpt-4o' );

		$this->assertEquals( 'gpt-4o', $model->metadata()->getId() );
	}

	/**
	 * Test model has OpenCode provider header.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function test_model_has_provider_metadata(): void {
		$model = OpenCodeZenProvider::model( 'gpt-4o' );

		$this->assertEquals( 'OpenCode Zen', $model->providerMetadata()->getName() );
	}
}
