# AGENTS.md

## Overview

This is a **WordPress AI Provider plugin** for OpenCode Zen. It registers OpenCode Zen as an AI provider using the [WordPress PHP AI Client SDK](https://github.com/WordPress/wp-ai-client).

## Architecture Pattern

**Follow the official WordPress AI Provider structure** from these repositories:

- **Reference Implementation**: https://github.com/WordPress/ai-provider-for-anthropic
- **Base SDK**: https://github.com/WordPress/wp-ai-client
- **Other Providers**: `ai-provider-for-openai`, `ai-provider-for-google`

### Correct Structure

```
ai-provider-for-opencode-zen/
├── plugin.php                              # Entry point (registers provider on 'init')
├── src/
│   ├── autoload.php                        # Composer autoloader
│   ├── Provider/
│   │   └── OpenCodeZenProvider.php         # Extends AbstractApiProvider
│   ├── Models/
│   │   └── OpenCodeZenTextGenerationModel.php
│   └── Metadata/
│       └── OpenCodeZenModelMetadataDirectory.php
├── composer.json
├── composer.lock
├── readme.txt
└── .wordpress-org/                         # Plugin assets (icon, banner)
```

### Key Implementation Rules

1. **Provider class** must extend `WordPress\AiClient\Providers\ApiBasedImplementation\AbstractApiProvider`
2. **Plugin file** registers provider via `AiClient::defaultRegistry()->registerProvider()` on `init` hook
3. **No hardcoded model lists** - use dynamic discovery via `/v1/models` API
4. **API endpoint**: `https://opencode.ai/zen/v1` (OpenAI-compatible)
5. **Namespace**: `WordPress\OpenCodeZenAiProvider`
6. **Package name**: `wordpress/ai-provider-for-opencode-zen`

### Required AbstractApiProvider Methods

```php
protected static function baseUrl(): string
protected static function createModel(ModelMetadata $modelMetadata, ProviderMetadata $providerMetadata): ModelInterface
protected static function createProviderMetadata(): ProviderMetadata
protected static function createProviderAvailability(): ProviderAvailabilityInterface
protected static function createModelMetadataDirectory(): ModelMetadataDirectoryInterface
```

## Common Mistakes to Avoid

- **Do NOT** use custom static method patterns (like `OpenCode_Zen_Provider::init()`)
- **Do NOT** manually unregister/register connectors in `wp_connectors_init`
- **Do NOT** hardcode model arrays - models are discovered dynamically
- **Do NOT** skip the metadata directory for model capabilities

## Dependencies

- PHP 7.4+
- WordPress 7.0+ (or `wordpress/php-ai-client` package for older versions)
- `WordPress\AiClient\AiClient` class

## Important Files to Reference

| File | Purpose |
|------|---------|
| `src/Provider/AnthropicProvider.php` | Provider class pattern |
| `src/Models/AnthropicTextGenerationModel.php` | Model implementation pattern |
| `plugin.php` in ai-provider-for-anthropic | Plugin registration pattern |
