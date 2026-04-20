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
use WordPress\AiClient\Providers\Contracts\ModelMetadataDirectoryInterface;
use WordPress\AiClient\Providers\Models\DTO\ModelMetadata;
use WordPress\AiClient\Common\Exception\InvalidArgumentException;

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
	 * Test directory implements the interface.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function test_implements_interface(): void {
		$this->assertInstanceOf( ModelMetadataDirectoryInterface::class, $this->directory );
	}

	/**
	 * Test list model metadata returns an array.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function test_list_model_metadata_returns_array(): void {
		$this->assertIsArray( $this->directory->listModelMetadata() );
	}

	/**
	 * Test fallback models list is non-empty.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function test_fallback_models_are_returned(): void {
		$this->assertNotEmpty( $this->directory->listModelMetadata() );
	}

	/**
	 * Test all expected fallback model IDs are present.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function test_all_fallback_model_ids_present(): void {
		$model_ids = array_map(
			static fn( $m ) => $m->getId(),
			$this->directory->listModelMetadata()
		);

		$this->assertContains( 'gpt-4o', $model_ids );
		$this->assertContains( 'gpt-4o-mini', $model_ids );
		$this->assertContains( 'claude-sonnet-4', $model_ids );
		$this->assertContains( 'claude-3-5-sonnet', $model_ids );
	}

	/**
	 * Test all fallback models have human-readable names.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function test_fallback_models_have_names(): void {
		foreach ( $this->directory->listModelMetadata() as $model ) {
			$this->assertNotEmpty( $model->getName(), "Model {$model->getId()} has empty name" );
		}
	}

	/**
	 * Test has model metadata returns true for known model.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function test_has_model_metadata_returns_true(): void {
		$this->assertTrue( $this->directory->hasModelMetadata( 'gpt-4o' ) );
	}

	/**
	 * Test has model metadata returns false for unknown model.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function test_has_model_metadata_returns_false_for_unknown(): void {
		$this->assertFalse( $this->directory->hasModelMetadata( 'unknown-model' ) );
	}

	/**
	 * Test get model metadata returns correct ModelMetadata instance.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function test_get_model_metadata_returns_model(): void {
		$model = $this->directory->getModelMetadata( 'gpt-4o' );

		$this->assertInstanceOf( ModelMetadata::class, $model );
		$this->assertEquals( 'gpt-4o', $model->getId() );
	}

	/**
	 * Test get model metadata returns correct name.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function test_get_model_metadata_returns_correct_name(): void {
		$this->assertEquals( 'GPT-4o', $this->directory->getModelMetadata( 'gpt-4o' )->getName() );
		$this->assertEquals( 'GPT-4o Mini', $this->directory->getModelMetadata( 'gpt-4o-mini' )->getName() );
		$this->assertEquals( 'Claude Sonnet 4', $this->directory->getModelMetadata( 'claude-sonnet-4' )->getName() );
		$this->assertEquals( 'Claude 3.5 Sonnet', $this->directory->getModelMetadata( 'claude-3-5-sonnet' )->getName() );
	}

	/**
	 * Test get model metadata throws for unknown model ID.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function test_get_model_metadata_throws_for_unknown(): void {
		$this->expectException( InvalidArgumentException::class );

		$this->directory->getModelMetadata( 'unknown-model' );
	}

	/**
	 * Test all fallback models have text generation capability.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function test_all_models_have_text_generation_capability(): void {
		foreach ( $this->directory->listModelMetadata() as $model ) {
			$caps = $model->getSupportedCapabilities();
			$this->assertNotEmpty( $caps, "Model {$model->getId()} has no capabilities" );
			$this->assertTrue(
				$caps[0]->isTextGeneration(),
				"Model {$model->getId()} first capability is not text generation"
			);
		}
	}

	/**
	 * Test all fallback models support max_tokens option.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function test_all_models_support_max_tokens_option(): void {
		foreach ( $this->directory->listModelMetadata() as $model ) {
			$option_names = array_map(
				static fn( $opt ) => (string) $opt->getName(),
				$model->getSupportedOptions()
			);
			$this->assertContains(
				'maxTokens',
				$option_names,
				"Model {$model->getId()} does not support maxTokens option"
			);
		}
	}
}
