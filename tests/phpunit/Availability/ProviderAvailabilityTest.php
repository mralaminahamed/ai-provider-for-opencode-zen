<?php
/**
 * Tests for ProviderAvailability.
 *
 * @package OpenCodeZen\OpenCodeZenAiProvider\Tests\Availability
 */

declare(strict_types=1);

namespace OpenCodeZen\OpenCodeZenAiProvider\Tests\Availability;

use OpenCodeZen\OpenCodeZenAiProvider\Availability\ProviderAvailability;
use OpenCodeZen\OpenCodeZenAiProvider\Tests\AbstractProviderAvailabilityTest;

/**
 * Class ProviderAvailabilityTest
 *
 * @since 1.3.2
 */
class ProviderAvailabilityTest extends AbstractProviderAvailabilityTest {

	protected function getAvailabilityClass(): string {
		return ProviderAvailability::class;
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
