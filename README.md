# AI Provider for OpenCode Zen

An independent, third-party OpenCode Zen provider for the [WordPress PHP AI Client](https://github.com/WordPress/php-ai-client) SDK. Works as both a Composer package and a WordPress plugin. One API key gives the WordPress AI Client access to 57 frontier models — GPT 5.x, Claude, Gemini 3.x, Qwen, MiniMax, Kimi, Grok, DeepSeek, and more.

> Not affiliated with, endorsed by, or sponsored by OpenCode Zen.

## Requirements

- PHP 7.4 or higher
- WordPress 7.0 or higher (the AI Client SDK ships in WordPress core)
  - On older WordPress releases, install the [WordPress AI Client](https://wordpress.org/plugins/wp-ai-client/) plugin separately

## Installation

**As a WordPress plugin** — upload the zip via **Plugins > Add New > Upload Plugin**, then activate.

**As a Composer package:**

```bash
composer require mralaminahamed/ai-provider-for-opencode-zen
```

## Quick start

1. Activate the plugin.
2. Go to **Settings > OpenCode Zen** (or the WordPress 7.0+ **Settings > Connectors** screen) and paste your API key. Get one at [opencode.ai/zen/settings/api-keys](https://opencode.ai/zen/settings/api-keys).
3. Any AI-enabled plugin can now generate text through the `opencode-zen` provider.

```php
use WordPress\AiClient\AiClient;

echo AiClient::prompt('Explain quantum computing')
    ->usingProvider('opencode-zen')
    ->generateTextResult()
    ->toText();
```

## Documentation

| Doc | What's in it |
|---|---|
| [Usage](docs/USAGE.md) | WordPress + standalone Composer examples, API key resolution, generation options, credential filters |
| [Models](docs/MODELS.md) | How model discovery and caching work, and the full 57-model fallback catalogue |
| [Architecture](docs/ARCHITECTURE.md) | Provider identity, file layout, request flow, runtime SDK dependency |
| [Development](docs/DEVELOPMENT.md) | Build/test/lint/analysis scripts, the AI Client `replace` guard and dev-only stub install, CI and release |

Version history: [CHANGELOG.md](CHANGELOG.md).

## License

GPL-2.0-or-later
