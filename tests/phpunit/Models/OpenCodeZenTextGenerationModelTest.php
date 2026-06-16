<?php
/**
 * Tests for OpenCodeZenTextGenerationModel.
 *
 * @package AlAminAhamed\OpenCodeZenAiProvider\Tests\Models
 */

declare(strict_types=1);

namespace AlAminAhamed\OpenCodeZenAiProvider\Tests\Models;

use AlAminAhamed\OpenCodeZenAiProvider\Models\OpenCodeZenTextGenerationModel;
use AlAminAhamed\OpenCodeZenAiProvider\Provider\OpenCodeZenProvider;
use AlAminAhamed\OpenCodeZenAiProvider\Tests\AbstractTextGenerationModelTest;

/**
 * Class OpenCodeZenTextGenerationModelTest
 *
 * @since 1.0.0
 */
class OpenCodeZenTextGenerationModelTest extends AbstractTextGenerationModelTest {

	protected function getModelClass(): string {
		return OpenCodeZenTextGenerationModel::class;
	}

	protected function getProviderModelId(): string {
		return 'gpt-5.5';
	}

	protected function getProviderName(): string {
		return 'OpenCode Zen';
	}

	protected function getCustomHeaderName(): string {
		return 'OpenCode-Provider';
	}

	protected function createModel( string $modelId ): object {
		return OpenCodeZenProvider::model( $modelId );
	}

	/**
	 * Test model can be created for each fallback model ID.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function test_all_fallback_models_are_creatable(): void {
		$model_ids = array( 'gpt-5.5', 'claude-opus-4-7', 'gemini-3.5-flash', 'qwen3.6-plus' );

		foreach ( $model_ids as $model_id ) {
			$model = OpenCodeZenProvider::model( $model_id );
			$this->assertInstanceOf( OpenCodeZenTextGenerationModel::class, $model );
			$this->assertEquals( $model_id, $model->metadata()->getId() );
		}
	}
}
