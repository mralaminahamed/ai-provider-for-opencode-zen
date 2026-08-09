<?php
/**
 * Tests for Provider.
 *
 * @package OpenCodeZen\OpenCodeZenAiProvider\Tests\Provider
 */

declare(strict_types=1);

namespace OpenCodeZen\OpenCodeZenAiProvider\Tests\Provider;

use OpenCodeZen\OpenCodeZenAiProvider\Provider\Provider;
use OpenCodeZen\OpenCodeZenAiProvider\Tests\AbstractProviderTest;

/**
 * Class ProviderTest
 *
 * @since 1.0.0
 */
class ProviderTest extends AbstractProviderTest {

	protected function getProviderClass(): string {
		return Provider::class;
	}

	protected function getExpectedBaseUrl(): string {
		return 'https://opencode.ai/zen/v1';
	}

	protected function getExpectedProviderId(): string {
		return 'opencode-zen';
	}

	protected function getExpectedProviderName(): string {
		return 'OpenCode Zen';
	}
}
