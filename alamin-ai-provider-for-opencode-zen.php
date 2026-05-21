<?php
/**
 * AI Provider for OpenCode Zen — plugin bootstrap.
 *
 * Loads the autoloader, registers the OpenCode Zen provider with the
 * WordPress AI Client registry, and initialises the wp-admin settings page.
 *
 * @package AlAminAhamed\OpenCodeZenAiProvider
 * @author  Al Amin Ahamed
 * @link    https://github.com/mralaminahamed/ai-provider-for-opencode-zen
 * @since   1.0.0
 *
 * @wordpress-plugin
 * Plugin Name:       AI Provider for OpenCode Zen
 * Plugin URI:        https://github.com/mralaminahamed/ai-provider-for-opencode-zen
 * Description:       OpenCode Zen AI provider for the WordPress AI Client. Not affiliated with OpenCode Zen.
 * Version:           1.2.1
 * Requires at least: 6.7
 * Requires PHP:      7.4
 * Author:            Al Amin Ahamed
 * Author URI:        https://alaminahamed.com
 * License:           GPL-2.0-or-later
 * License URI:       https://spdx.org/licenses/GPL-2.0-or-later.html
 * Text Domain:       alamin-ai-provider-for-opencode-zen
 * Domain Path:       /languages
 */

declare(strict_types=1);

namespace AlAminAhamed\OpenCodeZenAiProvider;

use WordPress\AiClient\AiClient;
use AlAminAhamed\OpenCodeZenAiProvider\Provider\OpenCodeZenProvider;
use AlAminAhamed\OpenCodeZenAiProvider\Settings\OpenCodeZenSettings;

define( 'OPENCODE_ZEN_PLUGIN_FILE', __FILE__ );

if ( ! defined( 'ABSPATH' ) ) {
	return;
}

require_once __DIR__ . '/vendor/autoload.php';

/**
 * Registers the OpenCode Zen provider with the AI Client.
 *
 * @since 1.0.0
 *
 * @return void
 */
function register_provider(): void {
	if ( ! class_exists( AiClient::class ) ) {
		return;
	}

	$registry = AiClient::defaultRegistry();

	if ( $registry->hasProvider( OpenCodeZenProvider::class ) ) {
		return;
	}

	$registry->registerProvider( OpenCodeZenProvider::class );
}

add_action( 'init', __NAMESPACE__ . '\\register_provider', 5 );

/**
 * Initialize settings page.
 *
 * @since 1.0.0
 *
 * @return void
 */
function init_settings(): void {
	OpenCodeZenSettings::init();
}

add_action( 'init', __NAMESPACE__ . '\\init_settings', 5 );

/**
 * Declare credential availability to the AI plugin.
 *
 * The AI plugin's has_ai_credentials() only checks connectors that store an
 * API key as a flat WP option. OpenCode Zen stores its key under the nested
 * wp_ai_client_credentials option, so we must hook this filter explicitly.
 *
 * @since 1.1.0
 *
 * @param bool $has_credentials Current credential status.
 * @return bool
 */
function declare_credentials( bool $has_credentials ): bool {
	if ( $has_credentials ) {
		return true;
	}

	$api_key = getenv( 'OPENCODE_ZEN_API_KEY' );
	if ( ! empty( $api_key ) ) {
		return true;
	}

	// Key stored by WordPress Connectors page (WP 7.0+).
	$connectors_key = get_option( 'connectors_ai_opencode_zen_api_key', '' );
	if ( ! empty( $connectors_key ) ) {
		return true;
	}

	// Key stored via legacy wp_ai_client_credentials option.
	$option      = get_option( 'wp_ai_client_credentials', array() );
	$credentials = is_array( $option ) ? ( $option['opencode-zen'] ?? array() ) : array();
	$key         = is_array( $credentials ) ? ( $credentials['api_key'] ?? '' ) : '';

	return ! empty( $key );
}

add_filter( 'wpai_has_ai_credentials', __NAMESPACE__ . '\\declare_credentials' );

/**
 * Short-circuit the valid credentials check when OpenCode Zen key is configured.
 *
 * The default check calls wp_ai_client_prompt()->is_supported_for_text_generation()
 * which may not resolve our provider correctly since we store the API key
 * outside the standard wp_ai_client_credentials option. Return true early when
 * we can confirm the key exists so the AI plugin admin page shows no error.
 *
 * @since 1.1.0
 *
 * @param bool|null $valid Current validity status; null means "use default check".
 * @return bool|null
 */
function declare_valid_credentials( $valid ) {
	if ( true === $valid ) {
		return true;
	}

	return declare_credentials( false ) ? true : null;
}

add_filter( 'wpai_pre_has_valid_credentials_check', __NAMESPACE__ . '\\declare_valid_credentials' );
