# Alamin AI Provider for OpenCode Zen

An independent, third-party OpenCode Zen provider for the [PHP AI Client](https://github.com/WordPress/php-ai-client) SDK. Works as both a Composer package and a WordPress plugin. Not affiliated with, endorsed by, or sponsored by OpenCode Zen.

## Requirements

- PHP 7.4 or higher
- When using with WordPress, requires WordPress 7.0 or higher
    - If using an older WordPress release, the [wordpress/php-ai-client](https://github.com/WordPress/php-ai-client) package must be installed

## Installation

### As a Composer Package

```bash
composer require mralaminahamed/alamin-ai-provider-for-opencode-zen
```

### As a WordPress Plugin

1. Download the plugin files
2. Upload to `/wp-content/plugins/alamin-ai-provider-for-opencode-zen/`
3. Ensure the PHP AI Client plugin is installed and activated
4. Activate the plugin through the WordPress admin

## Usage

### With WordPress

The provider automatically registers itself with the PHP AI Client on the `init` hook. Simply ensure both plugins are active and configure your API key:

```php
// Set your OpenCode Zen API key (or use the OPENCODE_ZEN_API_KEY environment variable)
putenv('OPENCODE_ZEN_API_KEY=your-api-key');

// Use the provider
$result = AiClient::prompt('Hello, world!')
    ->usingProvider('opencode-zen')
    ->generateTextResult();
```

### As a Standalone Package

```php
use WordPress\AiClient\AiClient;
use AlAminAhamed\OpenCodeZenAiProvider\Provider\OpenCodeZenProvider;

// Register the provider
$registry = AiClient::defaultRegistry();
$registry->registerProvider(OpenCodeZenProvider::class);

// Set your API key
putenv('OPENCODE_ZEN_API_KEY=your-api-key');

// Generate text
$result = AiClient::prompt('Explain quantum computing')
    ->usingProvider('opencode-zen')
    ->generateTextResult();

echo $result->toText();
```

## Supported Models

Available models are dynamically discovered from the OpenCode Zen API. This includes GPT and Claude models for text generation. The provider falls back to a default model list when the API is unavailable.

## Configuration

The provider uses the `OPENCODE_ZEN_API_KEY` environment variable for authentication. You can set this in your environment or via PHP:

```php
putenv('OPENCODE_ZEN_API_KEY=your-api-key');
```

## License

GPL-2.0-or-later
