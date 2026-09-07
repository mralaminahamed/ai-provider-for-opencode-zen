<?php
/**
 * Tests for Settings.
 *
 * @package OpenCodeZen\OpenCodeZenAiProvider\Tests\Settings
 */

declare(strict_types=1);

namespace OpenCodeZen\OpenCodeZenAiProvider\Tests\Settings;

use Brain\Monkey\Functions;
use OpenCodeZen\OpenCodeZenAiProvider\Settings\Settings;
use OpenCodeZen\OpenCodeZenAiProvider\Tests\AbstractSettingsTest;

/**
 * Class SettingsTest
 *
 * @since 1.0.0
 */
class SettingsTest extends AbstractSettingsTest {

	protected function getSettingsClass(): string {
		return Settings::class;
	}

	protected function getOptionKey(): string {
		return 'opencode_zen_settings';
	}

	/**
	 * The settings page offers a way to actually check the key.
	 *
	 * The old banner said "OpenCode Zen is connected" on the strength of a key
	 * being present in the database, which is a different claim from one Zen
	 * has agreed to. The page now says what it knows and offers to find out.
	 *
	 * @since 1.6.0
	 *
	 * @return void
	 */
	public function test_settings_page_offers_a_connection_test(): void {
		if ( ! defined( 'OPENCODE_ZEN_PLUGIN_FILE' ) ) {
			define( 'OPENCODE_ZEN_PLUGIN_FILE', dirname( __DIR__, 3 ) . '/alamin-ai-provider-for-opencode-zen.php' );
		}

		Functions\when( 'current_user_can' )->justReturn( true );
		Functions\when( 'get_option' )->alias(
			static function ( $name ) {
				return 'connectors_ai_opencode_zen_api_key' === $name ? 'a-key' : array();
			}
		);
		Functions\when( 'get_transient' )->justReturn( false );
		Functions\when( 'admin_url' )->alias( static fn( $path = '' ) => 'https://example.test/wp-admin/' . $path );
		Functions\when( 'esc_attr' )->returnArg();
		Functions\when( 'esc_html' )->returnArg();
		Functions\when( 'esc_html__' )->returnArg();
		Functions\when( 'esc_url' )->returnArg();
		Functions\when( 'selected' )->justReturn( '' );
		Functions\when( 'wp_nonce_field' )->justReturn( '' );
		Functions\when( 'settings_fields' )->justReturn( '' );
		Functions\when( 'do_settings_sections' )->justReturn( '' );
		Functions\when( 'submit_button' )->alias(
			static function ( $text = 'Save Changes' ) {
				echo '<button>' . $text . '</button>';
			}
		);

		ob_start();
		Settings::render_settings_page();
		$html = (string) ob_get_clean();

		$this->assertStringContainsString( Settings::TEST_ACTION, $html );
		$this->assertStringContainsString( 'Test connection', $html );

		// The old wording claimed more than the plugin knew.
		$this->assertStringNotContainsString( 'OpenCode Zen is connected', $html );
	}
}
