<?php
/**
 * Plugin Name: AI Provider for OpenCode Zen
 * Plugin URI: https://github.com/mralaminahamed/ai-provider-for-opencode-zen
 * Description: OpenCode Zen AI provider for the WordPress AI Client.
 * Requires at least: 7.0
 * Requires PHP: 7.4
 * Version: 1.0.0
 * Author: Al Amin Ahamed
 * Author URI: https://github.com/mralaminahamed
 * License: GPL-2.0-or-later
 * License URI: https://spdx.org/licenses/GPL-2.0-or-later.html
 * Text Domain: ai-provider-for-opencode-zen
 *
 * @package WordPress\OpenCodeZenAiProvider
 */

declare(strict_types=1);

namespace WordPress\OpenCodeZenAiProvider;

use WordPress\AiClient\AiClient;
use WordPress\OpenCodeZenAiProvider\Provider\OpenCodeZenProvider;

if ( ! defined( 'ABSPATH' ) ) {
	return;
}

require_once __DIR__ . '/src/autoload.php';

/**
 * Registers the AI Provider for OpenCode Zen with the AI Client.
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
