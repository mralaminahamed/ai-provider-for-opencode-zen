<?php
/**
 * Autoloader for the OpenCode Zen AI Provider.
 *
 * @package WordPress\OpenCodeZenAiProvider
 */

declare(strict_types=1);

namespace WordPress\OpenCodeZenAiProvider;

if ( class_exists( __NAMESPACE__ . '\\OpenCodeZenProvider' ) ) {
	return;
}

spl_autoload_register(
	function ( string $class ): void {
		$prefix   = __NAMESPACE__ . '\\';
		$base_dir = __DIR__ . '/';

		$len = strlen( $prefix );
		if ( strncmp( $prefix, $class, $len ) !== 0 ) {
			return;
		}

		$relative_class = substr( $class, $len );
		$file          = $base_dir . str_replace( '\\', '/', $relative_class ) . '.php';

		if ( file_exists( $file ) ) {
			require $file;
		}
	}
);
