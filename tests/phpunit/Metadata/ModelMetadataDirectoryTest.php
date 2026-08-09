<?php
/**
 * Tests for ModelMetadataDirectory.
 *
 * @package OpenCodeZen\OpenCodeZenAiProvider\Tests\Metadata
 */

declare(strict_types=1);

namespace OpenCodeZen\OpenCodeZenAiProvider\Tests\Metadata;

use OpenCodeZen\OpenCodeZenAiProvider\Metadata\ModelMetadataDirectory;
use OpenCodeZen\OpenCodeZenAiProvider\Tests\AbstractModelMetadataDirectoryTest;
use WordPress\AiClient\Providers\Contracts\ModelMetadataDirectoryInterface;

/**
 * Class ModelMetadataDirectoryTest
 *
 * @since 1.0.0
 */
class ModelMetadataDirectoryTest extends AbstractModelMetadataDirectoryTest {

	protected function createDirectory(): ModelMetadataDirectoryInterface {
		return new ModelMetadataDirectory();
	}

	protected function getKnownModelId(): string {
		return 'gpt-5.5';
	}

	protected function getExpectedModelCount(): int {
		return 61;
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

		$this->assertContains( 'gpt-5.5', $model_ids );
		$this->assertContains( 'claude-opus-4-7', $model_ids );
		$this->assertContains( 'gemini-3.5-flash', $model_ids );
		$this->assertContains( 'qwen3.6-plus', $model_ids );
	}

	/**
	 * Test get model metadata returns correct name.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function test_get_model_metadata_returns_correct_name(): void {
		$this->assertEquals( 'GPT 5.5', $this->directory->getModelMetadata( 'gpt-5.5' )->getName() );
		$this->assertEquals( 'GPT 5 Nano', $this->directory->getModelMetadata( 'gpt-5-nano' )->getName() );
		$this->assertEquals( 'Claude Opus 4.7', $this->directory->getModelMetadata( 'claude-opus-4-7' )->getName() );
		$this->assertEquals( 'Gemini 3.5 Flash', $this->directory->getModelMetadata( 'gemini-3.5-flash' )->getName() );
	}

	/**
	 * Test all GPT 5.x model IDs are present.
	 *
	 * @since 1.1.0
	 *
	 * @return void
	 */
	public function test_all_gpt_models_present(): void {
		$ids = array_map( static fn( $m ) => $m->getId(), $this->directory->listModelMetadata() );

		$expected = array(
			'gpt-5.6-sol',
			'gpt-5.6-terra',
			'gpt-5.6-luna',
			'gpt-5.5',
			'gpt-5.5-pro',
			'gpt-5.4',
			'gpt-5.4-pro',
			'gpt-5.4-mini',
			'gpt-5.4-nano',
			'gpt-5.3-codex',
			'gpt-5.3-codex-spark',
			'gpt-5.2',
			'gpt-5.2-codex',
			'gpt-5.1',
			'gpt-5.1-codex',
			'gpt-5.1-codex-max',
			'gpt-5.1-codex-mini',
			'gpt-5',
			'gpt-5-codex',
			'gpt-5-nano',
		);

		foreach ( $expected as $model_id ) {
			$this->assertContains( $model_id, $ids, "Missing GPT model: {$model_id}" );
		}
	}

	/**
	 * Test all Claude 4.x model IDs are present.
	 *
	 * @since 1.1.0
	 *
	 * @return void
	 */
	public function test_all_claude_models_present(): void {
		$ids = array_map( static fn( $m ) => $m->getId(), $this->directory->listModelMetadata() );

		$expected = array(
			'claude-fable-5',
			'claude-opus-4-8',
			'claude-opus-4-7',
			'claude-opus-4-6',
			'claude-opus-4-5',
			'claude-sonnet-5',
			'claude-sonnet-4-6',
			'claude-sonnet-4-5',
			'claude-haiku-4-5',
		);

		foreach ( $expected as $model_id ) {
			$this->assertContains( $model_id, $ids, "Missing Claude model: {$model_id}" );
		}
	}

	/**
	 * Test all Gemini 3.x model IDs are present.
	 *
	 * @since 1.1.0
	 *
	 * @return void
	 */
	public function test_all_gemini_models_present(): void {
		$ids = array_map( static fn( $m ) => $m->getId(), $this->directory->listModelMetadata() );

		$this->assertContains( 'gemini-3.6-flash', $ids );
		$this->assertContains( 'gemini-3.5-flash', $ids );
		$this->assertContains( 'gemini-3.5-flash-lite', $ids );
		$this->assertContains( 'gemini-3.1-pro', $ids );
		$this->assertContains( 'gemini-3-flash', $ids );
	}

	/**
	 * Test other provider models are present.
	 *
	 * @since 1.1.0
	 *
	 * @return void
	 */
	public function test_all_other_models_present(): void {
		$ids = array_map( static fn( $m ) => $m->getId(), $this->directory->listModelMetadata() );

		$expected = array(
			'grok-4.5',
			'grok-build-0.1',
			'qwen3.6-plus',
			'qwen3.5-plus',
			'deepseek-v4-pro',
			'deepseek-v4-flash',
			'deepseek-v4-flash-free',
			'minimax-m3',
			'minimax-m2.7',
			'minimax-m2.5',
			'glm-5.2',
			'glm-5.1',
			'glm-5',
			'kimi-k3',
			'kimi-k2.7-code',
			'kimi-k2.6',
			'kimi-k2.5',
			'big-pickle',
			'mimo-v2.5-free',
			'ling-3.0-flash-free',
			'ling-3.0-tiny-free',
			'laguna-s-2.1-free',
			'longcat-2.0-free',
			'nemotron-3-ultra-free',
			'north-mini-code-free',
		);

		foreach ( $expected as $model_id ) {
			$this->assertContains( $model_id, $ids, "Missing model: {$model_id}" );
		}
	}

	/**
	 * Models OpenCode Zen does not serve are not offered.
	 *
	 * Both of these were in the bundled catalogue and are not in the API's.
	 * Choosing one produced a model id the API rejects, which surfaces to the
	 * user as a failed generation rather than as an invalid setting.
	 *
	 * @since 1.5.0
	 *
	 * @return void
	 */
	public function test_models_not_served_are_absent(): void {
		$ids = array_map( static fn( $m ) => $m->getId(), $this->directory->listModelMetadata() );

		foreach ( array( 'qwen3.7-max', 'qwen3.7-plus' ) as $model_id ) {
			$this->assertNotContains( $model_id, $ids, "Retired model still offered: {$model_id}" );
		}
	}
}
