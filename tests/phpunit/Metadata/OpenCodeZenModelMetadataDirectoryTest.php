<?php
/**
 * Tests for OpenCodeZenModelMetadataDirectory.
 *
 * @package AlAminAhamed\OpenCodeZenAiProvider\Tests\Metadata
 */

declare(strict_types=1);

namespace AlAminAhamed\OpenCodeZenAiProvider\Tests\Metadata;

use AlAminAhamed\OpenCodeZenAiProvider\Metadata\OpenCodeZenModelMetadataDirectory;
use PHPUnit\Framework\TestCase;

/**
 * Class OpenCodeZenModelMetadataDirectoryTest
 *
 * @since 1.0.0
 */
class OpenCodeZenModelMetadataDirectoryTest extends TestCase {

	/**
	 * @var OpenCodeZenModelMetadataDirectory
	 */
	private $directory;

	/**
	 * Set up test fixtures.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	protected function setUp(): void {
		parent::setUp();
		$this->directory = new OpenCodeZenModelMetadataDirectory();
	}

	/**
	 * Test directory implements interface.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function test_implements_interface(): void {
		$this->assertInstanceOf(
			\WordPress\AiClient\Providers\Contracts\ModelMetadataDirectoryInterface::class,
			$this->directory
		);
	}

	/**
	 * Test list model metadata returns array.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function test_list_model_metadata_returns_array(): void {
		$models = $this->directory->listModelMetadata();

		$this->assertIsArray( $models );
	}

	/**
	 * Test fallback models are returned.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function test_fallback_models_are_returned(): void {
		$models = $this->directory->listModelMetadata();

		$this->assertNotEmpty( $models );

		$model_ids = array_map(
			static function ( $model ) {
				return $model->getId();
			},
			$models
		);

		$this->assertContains( 'gpt-4o', $model_ids );
	}

	/**
	 * Test has model metadata returns boolean.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function test_has_model_metadata_returns_bool(): void {
		$result = $this->directory->hasModelMetadata( 'gpt-4o' );

		$this->assertIsBool( $result );
		$this->assertTrue( $result );
	}

	/**
	 * Test has model metadata returns false for unknown model.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function test_has_model_metadata_returns_false_for_unknown(): void {
		$result = $this->directory->hasModelMetadata( 'unknown-model' );

		$this->assertFalse( $result );
	}

	/**
	 * Test get model metadata returns model.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function test_get_model_metadata_returns_model(): void {
		$model = $this->directory->getModelMetadata( 'gpt-4o' );

		$this->assertInstanceOf(
			\WordPress\AiClient\Providers\Models\DTO\ModelMetadata::class,
			$model
		);
		$this->assertEquals( 'gpt-4o', $model->getId() );
	}

	/**
	 * Test get model metadata throws for unknown model.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function test_get_model_metadata_throws_for_unknown(): void {
		$this->expectException(
			\WordPress\AiClient\Common\Exception\InvalidArgumentException::class
		);

		$this->directory->getModelMetadata( 'unknown-model' );
	}

	/**
	 * Test model has text generation capability.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function test_model_has_text_generation_capability(): void {
		$model = $this->directory->getModelMetadata( 'gpt-4o' );
		$caps  = $model->getSupportedCapabilities();

		$this->assertNotEmpty( $caps );
		$this->assertTrue( $caps[0]->isTextGeneration() );
	}
}
