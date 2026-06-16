<?php
/**
 * Tests for OpenCodeZenProviderAvailability.
 *
 * @package AlAminAhamed\OpenCodeZenAiProvider\Tests\Availability
 */

declare(strict_types=1);

namespace AlAminAhamed\OpenCodeZenAiProvider\Tests\Availability;

use AlAminAhamed\OpenCodeZenAiProvider\Availability\OpenCodeZenProviderAvailability;
use AlAminAhamed\OpenCodeZenAiProvider\Tests\AbstractProviderAvailabilityTest;

/**
 * Class OpenCodeZenProviderAvailabilityTest
 *
 * @since 1.3.2
 */
class OpenCodeZenProviderAvailabilityTest extends AbstractProviderAvailabilityTest {

	protected function getAvailabilityClass(): string {
		return OpenCodeZenProviderAvailability::class;
	}

	protected function getEnvVarName(): string {
		return 'OPENCODE_ZEN_API_KEY';
	}

	protected function getConnectorsOptionKey(): string {
		return 'connectors_ai_opencode_zen_api_key';
	}

	protected function getLegacyCredentialsKey(): string {
		return 'opencode-zen';
	}
}
