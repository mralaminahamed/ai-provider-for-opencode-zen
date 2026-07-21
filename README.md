# AI Provider for OpenCode Zen

An independent, third-party OpenCode Zen provider for the [WordPress PHP AI Client](https://github.com/WordPress/php-ai-client) SDK. Works as both a Composer package and a WordPress plugin. Not affiliated with, endorsed by, or sponsored by OpenCode Zen.

## Requirements

- PHP 7.4 or higher
- WordPress 7.0 or higher (AI Client SDK is included in WordPress core)
  - On older WordPress releases, the [WordPress AI Client](https://wordpress.org/plugins/wp-ai-client/) plugin must be installed separately

## Installation

### As a WordPress Plugin

1. Download the plugin zip
2. Go to **Plugins > Add New > Upload Plugin** in your WordPress admin
3. Upload and activate

### As a Composer Package

```bash
composer require mralaminahamed/ai-provider-for-opencode-zen
```

## Configuration

### WordPress Admin

Go to **Settings > OpenCode Zen** to configure:

| Setting | Description | Default |
|---|---|---|
| API Key | Your OpenCode Zen API key | — |
| Default Model | Model used when none is specified | First available |
| Temperature | Output randomness (0.0–2.0) | 1.0 |
| Max Tokens | Maximum response length | 2048 |
| Top P | Nucleus sampling threshold (0.0–1.0) | 1.0 |
| Presence Penalty | Penalise repeated topics (-2.0–2.0) | 0.0 |
| Frequency Penalty | Penalise repeated tokens (-2.0–2.0) | 0.0 |

### Environment Variable

`OPENCODE_ZEN_API_KEY` takes priority over the database setting:

```bash
export OPENCODE_ZEN_API_KEY=your-api-key
```

Get your API key at [opencode.ai/zen/settings/api-keys](https://opencode.ai/zen/settings/api-keys).

## Usage

### With WordPress (automatic)

The provider registers itself on the `init` hook. No manual setup required beyond entering your API key.

```php
use WordPress\AiClient\AiClient;

$result = AiClient::prompt('Explain quantum computing')
    ->usingProvider('opencode-zen')
    ->generateTextResult();

echo $result->toText();
```

### As a Standalone Composer Package

```php
use WordPress\AiClient\AiClient;
use AlAminAhamed\OpenCodeZenAiProvider\Provider\OpenCodeZenProvider;

$registry = AiClient::defaultRegistry();
$registry->registerProvider(OpenCodeZenProvider::class);

putenv('OPENCODE_ZEN_API_KEY=your-api-key');

$result = AiClient::prompt('Explain quantum computing')
    ->usingProvider('opencode-zen')
    ->generateTextResult();

echo $result->toText();
```

## Supported Models

Models are discovered dynamically from the OpenCode Zen API (cached for 1 hour). The fallback list mirrors the [`/zen/v1/models` catalogue](https://opencode.ai/docs/zen/) — 54 models:

- **GPT 5.x** — GPT 5.6 Sol/Terra/Luna, GPT 5.5, GPT 5.4, GPT 5.3 Codex, GPT 5.2, GPT 5.1, GPT 5, and variants
- **Claude** — Claude Fable 5, Opus 4.8/4.7/4.6/4.5/4.1, Sonnet 5/4.6/4.5/4, Haiku 4.5
- **Gemini 3.x** — Gemini 3.5 Flash, 3.1 Pro, 3 Flash
- **Other** — Grok 4.5, Qwen, DeepSeek V4, MiniMax M3/M2, GLM 5.x, Kimi K2, and free-tier models

## Architecture

```
includes/
  Provider/
    OpenCodeZenProvider.php          # Registers provider ID "opencode-zen"
    OpenCodeZenTextGenerationModel.php  # OpenAI-compatible text generation
  Metadata/
    OpenCodeZenModelMetadataDirectory.php  # API model discovery + transient cache
  Settings/
    OpenCodeZenSettings.php          # WP admin settings page (logic only)
templates/
  admin/
    settings-page.php                # <form> wrapper
    section-general.php              # Section description
    field-model.php                  # Model <select>
    field-temperature.php            # Temperature <input>
    field-max-tokens.php             # Max tokens <input>
    field-top-p.php                  # Top P <input>
    field-presence-penalty.php       # Presence penalty <input>
    field-frequency-penalty.php      # Frequency penalty <input>
```

Settings page: `options-general.php?page=opencode-zen-settings`
Option key: `opencode_zen_settings`

## Development

```bash
# Install dependencies
composer install

# Run tests
composer test

# Lint
composer phpcs

# Auto-fix lint issues
composer phpcbf

# Static analysis
composer phpstan

# Build release zip
composer release
```

The release script runs `composer install --no-dev --optimize-autoloader` so only the plugin's own classmap is in the vendor directory — the AI Client SDK is excluded entirely (it is provided by WordPress 7.0+ at runtime).

## License

GPL-2.0-or-later
