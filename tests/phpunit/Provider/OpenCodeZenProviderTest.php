<?php
/**
 * Tests for OpenCodeZenProvider.
 *
 * @package AlAminAhamed\OpenCodeZenAiProvider\Tests\Provider
 */

declare(strict_types=1);

namespace AlAminAhamed\OpenCodeZenAiProvider\Tests\Provider;

use AlAminAhamed\OpenCodeZenAiProvider\Provider\OpenCodeZenProvider;
use AlAminAhamed\OpenCodeZenAiProvider\Tests\AbstractProviderTest;

/**
 * Class OpenCodeZenProviderTest
 *
 * @since 1.0.0
 */
class OpenCodeZenProviderTest extends AbstractProviderTest {

	protected function getProviderClass(): string {
		return OpenCodeZenProvider::class;
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
