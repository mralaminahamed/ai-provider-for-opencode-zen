<?php
/**
 * Tests for TextGenerationModel.
 *
 * @package OpenCodeZen\OpenCodeZenAiProvider\Tests\Models
 */

declare(strict_types=1);

namespace OpenCodeZen\OpenCodeZenAiProvider\Tests\Models;

use OpenCodeZen\OpenCodeZenAiProvider\Models\TextGenerationModel;
use OpenCodeZen\OpenCodeZenAiProvider\Provider\Provider;
use OpenCodeZen\OpenCodeZenAiProvider\Tests\AbstractTextGenerationModelTest;

/**
 * Class TextGenerationModelTest
 *
 * @since 1.0.0
 */
class TextGenerationModelTest extends AbstractTextGenerationModelTest {

	protected function getModelClass(): string {
		return TextGenerationModel::class;
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
		return Provider::model( $modelId );
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
			$model = Provider::model( $model_id );
			$this->assertInstanceOf( TextGenerationModel::class, $model );
			$this->assertEquals( $model_id, $model->metadata()->getId() );
		}
	}
	/**
	 * Saved settings reach the request.
	 *
	 * Before 1.5.0 nothing read `opencode_zen_settings`, so every value on the
	 * settings screen was decorative.
	 *
	 * @since 1.5.0
	 *
	 * @return void
	 */
	public function test_saved_settings_are_applied_to_params(): void {
		$settings = array(
			'temperature'       => 0.25,
			'max_tokens'        => 1234,
			'top_p'             => 0.5,
			'presence_penalty'  => 0.75,
			'frequency_penalty' => -0.5,
		);

		\Brain\Monkey\Functions\when( 'get_option' )->alias(
			static function ( $key, $default = false ) use ( $settings ) {
				return 'opencode_zen_settings' === $key ? $settings : $default;
			}
		);
		\Brain\Monkey\Functions\when( 'apply_filters' )->returnArg( 2 );

		$model  = $this->createModel( $this->getProviderModelId() );
		$method = new \ReflectionMethod( TextGenerationModel::class, 'prepareGenerateTextParams' );
		$method->setAccessible( true );

		$params = $method->invoke( $model, array( new \WordPress\AiClient\Messages\DTO\Message(
			\WordPress\AiClient\Messages\Enums\MessageRoleEnum::user(),
			array( new \WordPress\AiClient\Messages\DTO\MessagePart( 'Hello' ) )
		) ) );

		$this->assertSame( 0.25, $params['temperature'] );
		$this->assertSame( 1234, $params['max_tokens'] );
		$this->assertSame( 0.5, $params['top_p'] );
		$this->assertSame( 0.75, $params['presence_penalty'] );
		$this->assertSame( -0.5, $params['frequency_penalty'] );
	}

}
