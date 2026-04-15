# AI Provider for OpenCode Zen

An OpenCode Zen AI provider for the [WordPress AI Client](https://github.com/WordPress/wp-ai-client). Created by [Al Amin Ahamed](https://github.com/mralaminahamed). Works as both a Composer package and a WordPress plugin.

## Requirements

- PHP 7.4 or higher
- When using with WordPress, requires WordPress 7.0 or higher
  - If using an older WordPress release, the [wordpress/php-ai-client](https://github.com/WordPress/php-ai-client) package must be installed

## Installation

### As a Composer Package

```bash
composer require wordpress/ai-provider-for-opencode-zen
```

### As a WordPress Plugin

1. Download the plugin files
2. Upload to `/wp-content/plugins/ai-provider-for-opencode-zen/`
3. Ensure the WordPress AI Client plugin is installed and activated
4. Activate the plugin through the WordPress admin

## Configuration

Set your OpenCode Zen API key via the `OPENCODE_ZEN_API_KEY` environment variable:

```php
putenv('OPENCODE_ZEN_API_KEY=your-api-key');
```

Or configure it in WordPress Settings > Connectors.

## Usage

### With WordPress

The provider automatically registers itself with the WordPress AI Client on the `init` hook. Simply ensure both plugins are active and configure your API key:

```php
$result = AiClient::prompt('Hello, world!')
    ->usingProvider('opencode-zen')
    ->generateTextResult();
```

### As a Standalone Package

```php
use WordPress\AiClient\AiClient;
use WordPress\OpenCodeZenAiProvider\Provider\OpenCodeZenProvider;

$registry = AiClient::defaultRegistry();
$registry->registerProvider(OpenCodeZenProvider::class);

putenv('OPENCODE_ZEN_API_KEY=your-api-key');

$result = AiClient::prompt('Explain quantum computing')
    ->usingProvider('opencode-zen')
    ->generateTextResult();

echo $result->toText();
```

## Supported Models

Available models are dynamically discovered from the OpenCode Zen API. This includes GPT and Claude models for text generation.

## License

GPL-2.0-or-later
