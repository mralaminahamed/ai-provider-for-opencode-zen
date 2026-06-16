<?php
/**
 * Tests for OpenCodeZenSettings.
 *
 * @package AlAminAhamed\OpenCodeZenAiProvider\Tests\Settings
 */

declare(strict_types=1);

namespace AlAminAhamed\OpenCodeZenAiProvider\Tests\Settings;

use AlAminAhamed\OpenCodeZenAiProvider\Settings\OpenCodeZenSettings;
use AlAminAhamed\OpenCodeZenAiProvider\Tests\AbstractSettingsTest;

/**
 * Class OpenCodeZenSettingsTest
 *
 * @since 1.0.0
 */
class OpenCodeZenSettingsTest extends AbstractSettingsTest {

	protected function getSettingsClass(): string {
		return OpenCodeZenSettings::class;
	}

	protected function getOptionKey(): string {
		return 'opencode_zen_settings';
	}
}
