# Architecture

How the plugin is put together and how a request flows through it.

## Provider identity

| | |
|---|---|
| Provider ID | `opencode-zen` |
| Base URL | `https://opencode.ai/zen/v1` |
| API style | OpenAI-compatible (`/chat/completions`, `/models`) |
| Settings page | `options-general.php?page=opencode-zen-settings` |
| Option key | `opencode_zen_settings` |

## File layout

```
includes/
  Provider/
    OpenCodeZenProvider.php               # Registers provider ID "opencode-zen", base URL
    OpenCodeZenTextGenerationModel.php     # OpenAI-compatible text generation
  Metadata/
    OpenCodeZenModelMetadataDirectory.php  # Live model discovery + transient cache + fallback list
  Settings/
    OpenCodeZenSettings.php               # WP admin settings page (logic only)
templates/
  admin/
    settings-page.php                     # <form> wrapper
    section-general.php                   # Section description
    field-model.php                       # Model <select>
    field-temperature.php                 # Temperature <input>
    field-max-tokens.php                  # Max tokens <input>
    field-top-p.php                       # Top P <input>
    field-presence-penalty.php            # Presence penalty <input>
    field-frequency-penalty.php           # Frequency penalty <input>
```

Settings logic lives in PHP; all markup lives in `templates/admin/` so the two never mix.

## Request flow

```
AiClient::prompt(…)->usingProvider('opencode-zen')->generateTextResult()
  └─ OpenCodeZenProvider                    resolves provider + base URL + credentials
       └─ OpenCodeZenTextGenerationModel    POST {base}/chat/completions (OpenAI-compatible)
            └─ returns GenerateTextResult
```

Model discovery is a separate path:

```
OpenCodeZenModelMetadataDirectory::listModelMetadata()
  └─ GET {base}/models   (when an API key is present)
       ├─ success → cache in transient (1 h) and return live list
       └─ failure → cache empty (5 min) and return the built-in fallback list
```

See [MODELS.md](MODELS.md) for the discovery/caching details and the full fallback catalogue.

## Credential resolution

The API key is resolved in priority order (first hit wins):

1. `OPENCODE_ZEN_API_KEY` environment variable
2. `connectors_ai_opencode_zen_api_key` option (WordPress 7.0+ Connectors screen)
3. `wp_ai_client_credentials['opencode-zen']['api_key']` (legacy AI Client credentials option)

`OpenCodeZenSettings::has_api_key()` centralises this check; it is reused by the credential filters described in [USAGE.md](USAGE.md).

## Runtime dependency: the AI Client SDK

The plugin builds on the [WordPress PHP AI Client](https://github.com/WordPress/php-ai-client) (`WordPress\AiClient\*`). WordPress 7.0+ bundles this SDK in core, so the shipped plugin **excludes** its own copy via a Composer `replace` — the classes are provided at runtime by core. See [DEVELOPMENT.md](DEVELOPMENT.md) for how the SDK is still made available to PHPStan and PHPUnit without shipping it.
