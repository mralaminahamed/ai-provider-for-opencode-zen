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
	 * Test model has correct metadata ID.
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
	 * Test model has correct provider name.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function test_model_has_provider_metadata(): void {
		$model = OpenCodeZenProvider::model( 'gpt-4o' );

		$this->assertEquals( 'OpenCode Zen', $model->providerMetadata()->getName() );
	}

	/**
	 * Test model can be created for each fallback model ID.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function test_all_fallback_models_are_creatable(): void {
		$model_ids = array( 'gpt-4o', 'gpt-4o-mini', 'claude-sonnet-4', 'claude-3-5-sonnet' );

		foreach ( $model_ids as $model_id ) {
			$model = OpenCodeZenProvider::model( $model_id );
			$this->assertInstanceOf( OpenCodeZenTextGenerationModel::class, $model );
			$this->assertEquals( $model_id, $model->metadata()->getId() );
		}
	}

	/**
	 * Test model injects OpenCode-Provider header via createRequest.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function test_model_injects_opencode_provider_header(): void {
		$model = OpenCodeZenProvider::model( 'gpt-4o' );

		$reflection = new \ReflectionMethod( OpenCodeZenTextGenerationModel::class, 'createRequest' );
		$reflection->setAccessible( true );

		$request = $reflection->invoke(
			$model,
			\WordPress\AiClient\Providers\Http\Enums\HttpMethodEnum::POST(),
			'chat/completions',
			array( 'Content-Type' => 'application/json' ),
			null
		);

		$this->assertTrue( $request->hasHeader( 'OpenCode-Provider' ) );
		$this->assertEquals( 'wordpress-plugin', $request->getHeaderAsString( 'OpenCode-Provider' ) );
	}
}
