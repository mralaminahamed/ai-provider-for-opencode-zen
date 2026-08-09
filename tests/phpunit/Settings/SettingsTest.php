<?php
/**
 * Tests for Settings.
 *
 * @package OpenCodeZen\OpenCodeZenAiProvider\Tests\Settings
 */

declare(strict_types=1);

namespace OpenCodeZen\OpenCodeZenAiProvider\Tests\Settings;

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
}
