<?php
/**
 * AI Provider for OpenCode Zen
 *
 * @package           OpenCodeZen
 * @author            Al Amin Ahamed
 * @copyright         2026 Al Amin Ahamed
 * @license           GPL-2.0-or-later
 * @link              https://github.com/mralaminahamed/ai-provider-for-opencode-zen
 *
 * @wordpress-plugin
 * Plugin Name:       AI Provider for OpenCode Zen
 * Plugin URI:        https://github.com/mralaminahamed/ai-provider-for-opencode-zen
 * Description:       OpenCode Zen AI provider for the WordPress AI Client. Not affiliated with OpenCode Zen.
 * Version:           1.5.1
 * Requires at least: 7.0
 * Requires PHP:      7.4
 * Author:            Al Amin Ahamed
 * Author URI:        https://alaminahamed.com
 * License:           GPL-2.0-or-later
 * License URI:       https://spdx.org/licenses/GPL-2.0-or-later.html
 * Text Domain:       alamin-ai-provider-for-opencode-zen
 * Domain Path:       /languages
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'OPENCODE_ZEN_VERSION', '1.5.1' );
define( 'OPENCODE_ZEN_PLUGIN_FILE', __FILE__ );
define( 'OPENCODE_ZEN_URL', plugin_dir_url( __FILE__ ) );
define( 'OPENCODE_ZEN_PATH', plugin_dir_path( __FILE__ ) );

/*
 * Bail rather than fatal when the autoloader is absent.
 *
 * A plugin installed from git rather than from a built zip has no vendor
 * directory, and requiring a file that is not there takes the whole site down
 * instead of just this plugin.
 */
if ( ! file_exists( __DIR__ . '/vendor/autoload.php' ) ) {
	return;
}

require_once __DIR__ . '/vendor/autoload.php';

/**
 * Get the main plugin instance.
 *
 * @since 1.5.0
 *
 * @return AI_Provider_For_OpenCode_Zen Plugin instance.
 */
function ai_provider_for_opencode_zen(): AI_Provider_For_OpenCode_Zen {
	return AI_Provider_For_OpenCode_Zen::get_instance();
}

ai_provider_for_opencode_zen()->init();
