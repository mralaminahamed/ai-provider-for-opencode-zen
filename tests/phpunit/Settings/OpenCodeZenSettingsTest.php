<?php
/**
 * Tests for OpenCodeZenSettings.
 *
 * @package AlAminAhamed\OpenCodeZenAiProvider\Tests\Settings
 */

declare(strict_types=1);

namespace AlAminAhamed\OpenCodeZenAiProvider\Tests\Settings;

use AlAminAhamed\OpenCodeZenAiProvider\Settings\OpenCodeZenSettings;
use Brain\Monkey;
use Brain\Monkey\Functions;
use PHPUnit\Framework\TestCase;

/**
 * Class OpenCodeZenSettingsTest
 *
 * @since 1.0.0
 */
class OpenCodeZenSettingsTest extends TestCase {

	/**
	 * Set up Brain Monkey before each test.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	protected function setUp(): void {
		parent::setUp();
		Monkey\setUp();
	}

	/**
	 * Tear down Brain Monkey after each test.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	protected function tearDown(): void {
		Monkey\tearDown();
		parent::tearDown();
	}

	/**
	 * Test option key constant value.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function test_option_key_constant(): void {
		$this->assertEquals( 'opencode_zen_settings', OpenCodeZenSettings::OPTION_KEY );
	}

	/**
	 * Test sanitize settings returns array for valid input.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function test_sanitize_settings_returns_array(): void {
		Functions\when( 'sanitize_text_field' )->returnArg();

		$result = OpenCodeZenSettings::sanitize_settings(
			array(
				'default_model' => 'gpt-4o',
				'temperature'   => 1.0,
				'max_tokens'    => 2048,
			)
		);

		$this->assertIsArray( $result );
	}

	/**
	 * Test sanitize settings stores model ID.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function test_sanitize_settings_stores_model(): void {
		Functions\when( 'sanitize_text_field' )->returnArg();

		$result = OpenCodeZenSettings::sanitize_settings(
			array(
				'default_model' => 'claude-sonnet-4',
				'temperature'   => 1.0,
				'max_tokens'    => 2048,
			)
		);

		$this->assertEquals( 'claude-sonnet-4', $result['default_model'] );
	}

	/**
	 * Test sanitize settings clamps temperature below 0 to 0.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function test_sanitize_settings_clamps_temperature_min(): void {
		Functions\when( 'sanitize_text_field' )->returnArg();

		$result = OpenCodeZenSettings::sanitize_settings(
			array(
				'default_model' => '',
				'temperature'   => -5.0,
				'max_tokens'    => 100,
			)
		);

		$this->assertEquals( 0.0, $result['temperature'] );
	}

	/**
	 * Test sanitize settings clamps temperature above 2 to 2.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function test_sanitize_settings_clamps_temperature_max(): void {
		Functions\when( 'sanitize_text_field' )->returnArg();

		$result = OpenCodeZenSettings::sanitize_settings(
			array(
				'default_model' => '',
				'temperature'   => 99.9,
				'max_tokens'    => 100,
			)
		);

		$this->assertEquals( 2.0, $result['temperature'] );
	}

	/**
	 * Test sanitize settings accepts temperature within valid range.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function test_sanitize_settings_accepts_valid_temperature(): void {
		Functions\when( 'sanitize_text_field' )->returnArg();

		$result = OpenCodeZenSettings::sanitize_settings(
			array(
				'default_model' => '',
				'temperature'   => 0.7,
				'max_tokens'    => 100,
			)
		);

		$this->assertEquals( 0.7, $result['temperature'] );
	}

	/**
	 * Test sanitize settings clamps max_tokens of 0 to 1.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function test_sanitize_settings_clamps_max_tokens_min(): void {
		Functions\when( 'sanitize_text_field' )->returnArg();

		$result = OpenCodeZenSettings::sanitize_settings(
			array(
				'default_model' => '',
				'temperature'   => 1.0,
				'max_tokens'    => 0,
			)
		);

		$this->assertEquals( 1, $result['max_tokens'] );
	}

	/**
	 * Test sanitize settings clamps max_tokens above 200000 to 200000.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function test_sanitize_settings_clamps_max_tokens_max(): void {
		Functions\when( 'sanitize_text_field' )->returnArg();

		$result = OpenCodeZenSettings::sanitize_settings(
			array(
				'default_model' => '',
				'temperature'   => 1.0,
				'max_tokens'    => 999999,
			)
		);

		$this->assertEquals( 200000, $result['max_tokens'] );
	}

	/**
	 * Test sanitize settings handles empty input array.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function test_sanitize_settings_handles_empty_array(): void {
		Functions\when( 'sanitize_text_field' )->returnArg();

		$result = OpenCodeZenSettings::sanitize_settings( array() );

		$this->assertArrayHasKey( 'default_model', $result );
		$this->assertArrayHasKey( 'temperature', $result );
		$this->assertArrayHasKey( 'max_tokens', $result );
	}

	/**
	 * Test sanitize settings returns empty array for null input.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function test_sanitize_settings_returns_empty_for_null(): void {
		$result = OpenCodeZenSettings::sanitize_settings( null );

		$this->assertIsArray( $result );
		$this->assertEmpty( $result );
	}

	/**
	 * Test sanitize settings returns empty array for non-array input.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function test_sanitize_settings_returns_empty_for_string(): void {
		$result = OpenCodeZenSettings::sanitize_settings( 'not-an-array' );

		$this->assertIsArray( $result );
		$this->assertEmpty( $result );
	}

	/**
	 * Test get settings returns array with temperature default.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function test_get_settings_default_temperature(): void {
		Functions\when( 'get_option' )->justReturn( array() );
		Functions\when( 'wp_parse_args' )->alias(
			static function ( array $args, array $defaults ): array {
				return array_merge( $defaults, $args );
			}
		);

		$settings = OpenCodeZenSettings::get_settings();

		$this->assertEquals( 0.7, $settings['temperature'] );
	}

	/**
	 * Test get settings returns array with max_tokens default.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function test_get_settings_default_max_tokens(): void {
		Functions\when( 'get_option' )->justReturn( array() );
		Functions\when( 'wp_parse_args' )->alias(
			static function ( array $args, array $defaults ): array {
				return array_merge( $defaults, $args );
			}
		);

		$settings = OpenCodeZenSettings::get_settings();

		$this->assertEquals( 4096, $settings['max_tokens'] );
	}

	/**
	 * Test get settings merges saved values over defaults.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function test_get_settings_merges_saved_values(): void {
		Functions\when( 'get_option' )->justReturn(
			array(
				'temperature' => 1.5,
				'max_tokens'  => 8192,
			)
		);
		Functions\when( 'wp_parse_args' )->alias(
			static function ( array $args, array $defaults ): array {
				return array_merge( $defaults, $args );
			}
		);

		$settings = OpenCodeZenSettings::get_settings();

		$this->assertEquals( 1.5, $settings['temperature'] );
		$this->assertEquals( 8192, $settings['max_tokens'] );
	}
}
