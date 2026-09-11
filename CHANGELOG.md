# Changelog

## [1.5.1] - 2026-09-12

### Changed

- Directory listing copy: the readme title, tags and short description now lead with "connector" and the model count, matching what people search the plugin directory for. No code changes.

## [1.5.0] - 2026-08-09

### Added

- Saved settings are now applied to generation requests. Temperature, max tokens, top_p and the penalties had been stored since 1.0.0 and never read — nothing outside the settings class touched `opencode_zen_settings`. A caller's own value still wins; the saved values fill in what was left unset.
- `opencode_zen_generate_text_params` filter, which also receives the model id — useful because Zen fronts several vendors and they do not all accept the same parameters.
- Custom options. Arbitrary passthrough parameters, which the SDK's base class already merged into the request body but which no caller could reach, because the option was not declared. It matters more here than for a single-vendor provider: Zen fronts eight vendors, and a parameter only one of them understands has nowhere else to go.
- Chat history. The models are now declared as supporting multi-turn conversations, not just single prompts. They always could (the endpoint takes a `messages` array and the AI Client already sends one), but the capability was never declared and the AI Client routes on the declaration, so conversation requests went to other providers. Every official WordPress AI provider declares this.

### Changed

- PHP namespace is now `OpenCodeZen\OpenCodeZenAiProvider\` (was `AlAminAhamed\OpenCodeZenAiProvider\`).
- Class names dropped their `OpenCodeZen` prefix, which the namespace already carries: `Provider`, `Settings`, `ModelMetadataDirectory`, `TextGenerationModel`, `ProviderAvailability`.
- The model list no longer requires an API key. The catalogue is public, and the plugin was returning early without a key — so a site saw the built-in fallback at exactly the moment it was first being configured. A key is still sent when there is one.
- Refreshed the built-in fallback list against the live `/zen/v1/models` catalogue — now **61 models**. Adds Claude Opus 5, Claude Sonnet 4, Kimi K3, Ling 3.0 Flash Free, Ling 3.0 Tiny Free and LongCat 2.0 Free, on top of the Gemini 3.6 Flash, Gemini 3.5 Flash Lite and Laguna S 2.1 Free that were staged here unreleased.

- **Restructured the bootstrap.** The plugin file is now an entry point — constants, autoloader, boot — and all wiring moved into an `AI_Provider_For_OpenCode_Zen` singleton in `class-ai-provider-for-opencode-zen.php`, so every hook the plugin registers is visible in one file. Matches the layout used across this author's other plugins.
- Added `OPENCODE_ZEN_VERSION`, `OPENCODE_ZEN_URL` and `OPENCODE_ZEN_PATH` constants; only `OPENCODE_ZEN_PLUGIN_FILE` existed before.

### Fixed

- The plugin no longer fatals when `vendor/autoload.php` is missing, which is the case for a git checkout with no `composer install`. It now returns quietly and leaves the rest of the site up.
- Removed `qwen3.7-max` and `qwen3.7-plus`, which the fallback list offered and OpenCode Zen does not serve. Selecting either produced a failed generation rather than an invalid-setting warning. They were added in the unreleased entry this release absorbs, so no shipped version ever offered them.
- The model directory no longer calls WordPress functions when running outside WordPress, which the class is documented to support. It was previously safe only by accident: the API-key lookup returned early before reaching them.

## [1.4.0] - 2026-07-21

### Added

- Connection status on the settings page — indicates whether an OpenCode Zen API key is configured, with a link to the Connectors screen to add one.
- A "Connectors" quick link on the Plugins screen, next to the existing "Settings" link.

### Changed

- Synced the built-in fallback model list to the current OpenCode Zen catalogue — now 54 models (added GPT 5.6 Sol/Terra/Luna, Claude Sonnet 5, Grok 4.5, DeepSeek V4 Pro/Flash, MiniMax M3, GLM 5.2/5, Kimi K2.7 Code, and free-tier models; removed the retired Claude 3.5 Haiku and Nemotron 3 Super Free).
- A default model (`gpt-5.5`) is now selected out of the box instead of an empty choice, so generation works before the live model list loads.

### Fixed

- Autoloader collision with WordPress core's bundled AI Client. When the plugin's copy of `wordpress/php-ai-client` registered the `WordPress\AiClient\` namespace before core did, any request that triggered the AI Client hit a `TypeError`. The package is now excluded from the shipped plugin via a Composer `replace`; WordPress core provides those classes at runtime.
- Restored PHPStan, PHPUnit, and CI, which the change above had broken by removing `WordPress\AiClient\*` from `vendor/`. Those classes are now loaded for static analysis and the test suite only, from a dev-only side install (`tools/ai-client/`), and are never registered on the plugin's runtime autoloader.

## [1.2.1] - 2026-05-21

### Fixed

- Connector no longer shows as "Connected" on the WordPress Connectors page before any API key is entered — replaced `ListModelsApiBasedProviderAvailability` with a custom availability class that checks for an actual API key (env var, WP Connectors option, or legacy credentials option)

## [1.2.0] - 2026-05-21

### Added

- Provider logo SVG displayed on the WordPress Connectors page alongside Anthropic, Google, and OpenAI

### Fixed

- Declared credential availability via `wpai_has_ai_credentials` and `wpai_pre_has_valid_credentials_check` filters so the WordPress AI admin page no longer shows a false "no valid connector" warning when an API key is configured via the Connectors page

### Changed

- Expanded test suite from 37 to 48 tests covering full model list, provider logo path, settings edge cases, and all model IDs by family

## [1.1.0] - 2026-05-21

### Changed

- Updated fallback model list from 4 stale models to 40 current models sourced from the OpenCode Zen pricing page, including GPT 5.x, Claude Opus/Sonnet/Haiku 4.x, Gemini 3.x, Qwen, MiniMax, GLM 5.1, Kimi, Grok, and free-tier models

## [1.0.0] - 2026-05-11

### Added

- Initial release
- OpenCode Zen AI provider integration for the WordPress AI Client
- Dynamic model discovery from OpenCode Zen API with transient caching (1 hour TTL; 5 min on error)
- Fallback hardcoded model list when API is unavailable
- WordPress admin settings page for default model, temperature, and max tokens
- API key resolution via `OPENCODE_ZEN_API_KEY` environment variable or WordPress AI Client credentials
