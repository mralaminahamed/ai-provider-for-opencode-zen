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
 * Version:           1.5.0
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

define( 'OPENCODE_ZEN_VERSION', '1.5.0' );
define( 'OPENCODE_ZEN_PLUGIN_FILE', __FILE__ );
define( 'OPENCODE_ZEN_URL', plugin_dir_url( __FILE__ ) );
define( 'OPENCODE_ZEN_PATH', plugin_dir_path( __FILE__ ) );

/*
 * The plugin's own autoloader, which ships with the source.
 *
 * This used to load Composer's, and bail silently when `vendor/` was absent —
 * so a copy installed from git activated, registered nothing and explained
 * nothing. There is no runtime dependency to justify Composer here:
 * `composer.json` requires `php` and `ext-json`, and everything in `vendor/`
 * is development tooling.
 */
require_once __DIR__ . '/includes/autoload.php';

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
