# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Commands

```bash
# Run all tests
composer test

# Run a single test file
./vendor/bin/phpunit tests/phpunit/Provider/OpenCodeZenProviderTest.php

# Run a single test method
./vendor/bin/phpunit --filter test_provider_has_correct_base_url

# Lint (check only)
composer phpcs

# Lint (auto-fix)
composer phpcbf

# Static analysis
composer phpstan
```

## Architecture

Dual-purpose codebase: works as a standalone Composer package **and** a WordPress plugin. Entry point is `alamin-ai-provider-for-opencode-zen.php` (file name matches plugin slug per WP.org convention); `src/` contains all logic.

### Class hierarchy (SDK pattern)

All classes extend from `wordpress/wp-ai-client` (SDK):

```
AbstractApiProvider  (SDK)
  └── OpenCodeZenProvider          # registers provider ID "opencode-zen", base URL, auth method

AbstractOpenAiCompatibleTextGenerationModel  (SDK)
  └── OpenCodeZenTextGenerationModel   # adds OpenCode-Provider header, createRequest override

ModelMetadataDirectoryInterface  (SDK)
  └── OpenCodeZenModelMetadataDirectory  # fetches models from API, caches via WP transients (1hr),
                                          # falls back to hardcoded list when API unavailable
```

`OpenCodeZenSettings` — standalone WP settings page, not part of the SDK hierarchy. Registers wp-admin options page at `options-general.php?page=opencode-zen-settings`, stores settings under option key `opencode_zen_settings`.

### Bootstrap flow (WordPress)

1. `alamin-ai-provider-for-opencode-zen.php` defines `OPENCODE_ZEN_PLUGIN_FILE` constant and loads `vendor/autoload.php`
2. `init` hook (priority 5): calls `register_provider()` → registers `OpenCodeZenProvider` with `AiClient::defaultRegistry()`
3. `admin_init` hook (priority 5): calls `OpenCodeZenSettings::init()` → wires up wp-admin settings

### API key resolution (priority order)

1. `OPENCODE_ZEN_API_KEY` environment variable
2. WordPress option `wp_ai_client_credentials['opencode-zen']['api_key']`

### Coding standards

- WordPress Coding Standards (`phpcs.xml.dist`) — text domain `alamin-ai-provider-for-opencode-zen`
- `declare(strict_types=1)` on every file
- Namespace root: `AlAminAhamed\OpenCodeZenAiProvider\`
- PHPStan at `level: max` (WP function stubs via `szepeviktor/phpstan-wordpress`)
- All output escaped with `esc_html()`, `esc_attr()`, `esc_url()`; all text wrapped with `__()` / `esc_html__()`
