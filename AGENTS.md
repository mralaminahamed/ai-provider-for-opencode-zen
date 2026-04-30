# AGENTS.md

## Overview

This is a **WordPress AI Provider plugin** for OpenCode Zen. It registers OpenCode Zen as an AI provider using the [WordPress PHP AI Client SDK](https://github.com/WordPress/wp-ai-client).

## Reference Repositories

| Repository | URL |
|-----------|-----|
| Base SDK | https://github.com/WordPress/wp-ai-client |
| Reference Provider | https://github.com/WordPress/ai-provider-for-anthropic |
| OpenAI Provider | https://github.com/WordPress/ai-provider-for-openai |
| Google Provider | https://github.com/WordPress/ai-provider-for-google |

## Architecture Pattern

**Follow the official WordPress AI Provider structure** from these repositories.

### Correct Structure

```
alamin-ai-provider-for-opencode-zen/
├── alamin-ai-provider-for-opencode-zen.php # Entry point (registers provider on 'init')
├── src/
│   ├── Provider/
│   │   └── OpenCodeZenProvider.php         # Extends AbstractApiProvider
│   ├── Models/
│   │   └── OpenCodeZenTextGenerationModel.php
│   └── Metadata/
│       └── OpenCodeZenModelMetadataDirectory.php
├── composer.json
├── readme.txt
└── .wordpress-org/                         # Plugin assets (icon, banner) — excluded from prod zip
```

### Key Implementation Rules

1. **Provider class** must extend `WordPress\AiClient\Providers\ApiBasedImplementation\AbstractApiProvider`
2. **Plugin file** registers provider via `AiClient::defaultRegistry()->registerProvider()` on `init` hook
3. **No hardcoded model lists** - use dynamic discovery via `/v1/models` API
4. **API endpoint**: `https://opencode.ai/zen/v1` (OpenAI-compatible)
5. **Namespace**: `AlAminAhamed\OpenCodeZenAiProvider`
6. **Package name**: `mralaminahamed/ai-provider-for-opencode-zen`

### Required AbstractApiProvider Methods

```php
protected static function baseUrl(): string
protected static function createModel(ModelMetadata $modelMetadata, ProviderMetadata $providerMetadata): ModelInterface
protected static function createProviderMetadata(): ProviderMetadata
protected static function createProviderAvailability(): ProviderAvailabilityInterface
protected static function createModelMetadataDirectory(): ModelMetadataDirectoryInterface
```

### Required ModelMetadataDirectoryInterface Methods

```php
public function listModelMetadata(): array
public function hasModelMetadata(string $model_id): bool
public function getModelMetadata(string $model_id): ModelMetadata
```

### ModelMetadata Constructor

```php
new ModelMetadata(
    $model_id,                           // string
    $model_name,                         // string
    [CapabilityEnum::textGeneration()],  // array<CapabilityEnum>
    [new SupportedOption(OptionEnum::maxTokens())] // array<SupportedOption>
)
```

## Common Mistakes to Avoid

- **Do NOT** use `TextGenerationCapability` class - it doesn't exist
- **Do NOT** hardcode model arrays - models are discovered dynamically
- **Do NOT** skip the metadata directory for model capabilities
- **Do NOT** use custom static method patterns

## Dependencies

- PHP 7.4+ or 8.0+
- WordPress 7.0+ (or `wordpress/php-ai-client` package for older versions)
- `WordPress\AiClient\AiClient` class
- `wordpress/wp-ai-client` package v0.4+

## Configuration

- **API Key env**: `OPENCODE_ZEN_API_KEY`
- **Provider ID**: `opencode-zen`
- **Base URL**: `https://opencode.ai/zen/v1`

## Important Files to Reference

| File | Purpose |
|------|---------|
| `vendor/wordpress/wp-ai-client/src/Providers/Models/DTO/ModelMetadata.php` | ModelMetadata DTO |
| `vendor/wordpress/wp-ai-client/src/Providers/Models/Enums/CapabilityEnum.php` | Capability enum |
| `vendor/wordpress/wp-ai-client/src/Providers/Models/Enums/OptionEnum.php` | Option enum |
| `vendor/wordpress/wp-ai-client/src/Providers/Contracts/ModelMetadataDirectoryInterface.php` | Interface |
