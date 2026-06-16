<?php
/**
 * Tests for OpenCodeZenModelMetadataDirectory.
 *
 * @package AlAminAhamed\OpenCodeZenAiProvider\Tests\Metadata
 */

declare(strict_types=1);

namespace AlAminAhamed\OpenCodeZenAiProvider\Tests\Metadata;

use AlAminAhamed\OpenCodeZenAiProvider\Metadata\OpenCodeZenModelMetadataDirectory;
use Brain\Monkey;
use Brain\Monkey\Functions;
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
		Monkey\setUp();
		Functions\when( 'get_option' )->justReturn( array() );
		$this->directory = new OpenCodeZenModelMetadataDirectory();
	}

	/**
	 * Tear down Brain Monkey after each test.
	 *
	 * @since 1.3.2
	 *
	 * @return void
	 */
	protected function tearDown(): void {
		Monkey\tearDown();
		parent::tearDown();
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

		$this->assertContains( 'gpt-5.5', $model_ids );
		$this->assertContains( 'claude-opus-4-7', $model_ids );
		$this->assertContains( 'gemini-3.5-flash', $model_ids );
		$this->assertContains( 'qwen3.6-plus', $model_ids );
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
		$this->assertTrue( $this->directory->hasModelMetadata( 'gpt-5.5' ) );
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
		$model = $this->directory->getModelMetadata( 'gpt-5.5' );

		$this->assertInstanceOf( ModelMetadata::class, $model );
		$this->assertEquals( 'gpt-5.5', $model->getId() );
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

	/**
	 * Test fallback model list contains exactly 42 models.
	 *
	 * @since 1.1.0
	 *
	 * @return void
	 */
	public function test_fallback_model_count(): void {
		$this->assertCount( 42, $this->directory->listModelMetadata() );
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
			'claude-opus-4-1',
			'claude-sonnet-4-6',
			'claude-sonnet-4-5',
			'claude-sonnet-4',
			'claude-haiku-4-5',
			'claude-3-5-haiku',
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

		$this->assertContains( 'gemini-3.5-flash', $ids );
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
			'qwen3.6-plus',
			'qwen3.5-plus',
			'minimax-m2.7',
			'minimax-m2.5',
			'glm-5.1',
			'kimi-k2.6',
			'kimi-k2.5',
			'grok-build-0.1',
			'big-pickle',
			'deepseek-v4-flash-free',
			'nemotron-3-super-free',
		);

		foreach ( $expected as $model_id ) {
			$this->assertContains( $model_id, $ids, "Missing model: {$model_id}" );
		}
	}

	/**
	 * Test all fallback models have non-empty display names.
	 *
	 * @since 1.1.0
	 *
	 * @return void
	 */
	public function test_all_models_have_non_empty_names(): void {
		foreach ( $this->directory->listModelMetadata() as $model ) {
			$this->assertNotEmpty( $model->getName(), "Empty name for model: {$model->getId()}" );
		}
	}

	/**
	 * Test all fallback models support the temperature option.
	 *
	 * @since 1.3.2
	 *
	 * @return void
	 */
	public function test_all_models_support_temperature_option(): void {
		foreach ( $this->directory->listModelMetadata() as $model ) {
			$names = array_map( static fn( $opt ) => (string) $opt->getName(), $model->getSupportedOptions() );
			$this->assertContains( 'temperature', $names, "Model {$model->getId()} missing temperature option" );
		}
	}

	/**
	 * Test all fallback models support the topP option.
	 *
	 * @since 1.3.2
	 *
	 * @return void
	 */
	public function test_all_models_support_top_p_option(): void {
		foreach ( $this->directory->listModelMetadata() as $model ) {
			$names = array_map( static fn( $opt ) => (string) $opt->getName(), $model->getSupportedOptions() );
			$this->assertContains( 'topP', $names, "Model {$model->getId()} missing topP option" );
		}
	}

	/**
	 * Test all fallback models support the presencePenalty option.
	 *
	 * @since 1.3.2
	 *
	 * @return void
	 */
	public function test_all_models_support_presence_penalty_option(): void {
		foreach ( $this->directory->listModelMetadata() as $model ) {
			$names = array_map( static fn( $opt ) => (string) $opt->getName(), $model->getSupportedOptions() );
			$this->assertContains( 'presencePenalty', $names, "Model {$model->getId()} missing presencePenalty option" );
		}
	}

	/**
	 * Test all fallback models support the frequencyPenalty option.
	 *
	 * @since 1.3.2
	 *
	 * @return void
	 */
	public function test_all_models_support_frequency_penalty_option(): void {
		foreach ( $this->directory->listModelMetadata() as $model ) {
			$names = array_map( static fn( $opt ) => (string) $opt->getName(), $model->getSupportedOptions() );
			$this->assertContains( 'frequencyPenalty', $names, "Model {$model->getId()} missing frequencyPenalty option" );
		}
	}

	/**
	 * Test all fallback models support the stopSequences option.
	 *
	 * @since 1.3.2
	 *
	 * @return void
	 */
	public function test_all_models_support_stop_sequences_option(): void {
		foreach ( $this->directory->listModelMetadata() as $model ) {
			$names = array_map( static fn( $opt ) => (string) $opt->getName(), $model->getSupportedOptions() );
			$this->assertContains( 'stopSequences', $names, "Model {$model->getId()} missing stopSequences option" );
		}
	}

	/**
	 * Test all fallback models support the systemInstruction option.
	 *
	 * @since 1.3.2
	 *
	 * @return void
	 */
	public function test_all_models_support_system_instruction_option(): void {
		foreach ( $this->directory->listModelMetadata() as $model ) {
			$names = array_map( static fn( $opt ) => (string) $opt->getName(), $model->getSupportedOptions() );
			$this->assertContains( 'systemInstruction', $names, "Model {$model->getId()} missing systemInstruction option" );
		}
	}

	/**
	 * Test all fallback models support the functionDeclarations option.
	 *
	 * @since 1.3.2
	 *
	 * @return void
	 */
	public function test_all_models_support_function_declarations_option(): void {
		foreach ( $this->directory->listModelMetadata() as $model ) {
			$names = array_map( static fn( $opt ) => (string) $opt->getName(), $model->getSupportedOptions() );
			$this->assertContains( 'functionDeclarations', $names, "Model {$model->getId()} missing functionDeclarations option" );
		}
	}

	/**
	 * Test all fallback model IDs are unique.
	 *
	 * @since 1.3.2
	 *
	 * @return void
	 */
	public function test_all_model_ids_are_unique(): void {
		$ids = array_map( static fn( $m ) => $m->getId(), $this->directory->listModelMetadata() );

		$this->assertCount( count( $ids ), array_unique( $ids ) );
	}

	/**
	 * Test all fallback model IDs are non-empty strings.
	 *
	 * @since 1.3.2
	 *
	 * @return void
	 */
	public function test_all_model_ids_are_non_empty_strings(): void {
		foreach ( $this->directory->listModelMetadata() as $model ) {
			$this->assertIsString( $model->getId() );
			$this->assertNotEmpty( $model->getId() );
		}
	}

	/**
	 * Test all fallback models are ModelMetadata instances.
	 *
	 * @since 1.3.2
	 *
	 * @return void
	 */
	public function test_all_models_are_model_metadata_instances(): void {
		foreach ( $this->directory->listModelMetadata() as $model ) {
			$this->assertInstanceOf( ModelMetadata::class, $model );
		}
	}
}
