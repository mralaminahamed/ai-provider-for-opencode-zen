<?php
/**
 * PHPUnit bootstrap file.
 *
 * @package AlAminAhamed\OpenCodeZenAiProvider\Tests
 */

declare(strict_types=1);

require_once dirname( __DIR__ ) . '/vendor/autoload.php';

// Patchwork must be loaded before any class that will be patched.
require_once dirname( __DIR__ ) . '/vendor/antecedent/patchwork/Patchwork.php';

// WordPress constants used by the plugin.
if ( ! defined( 'MINUTE_IN_SECONDS' ) ) {
	define( 'MINUTE_IN_SECONDS', 60 );
}
if ( ! defined( 'HOUR_IN_SECONDS' ) ) {
	define( 'HOUR_IN_SECONDS', 3600 );
}
