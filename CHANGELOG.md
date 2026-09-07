# Changelog

## [Unreleased]

### Added

- The plugin now autoloads its own classes from `includes/autoload.php` and ships no `vendor/` directory. There was never a runtime dependency to justify Composer here — `composer.json` requires `php` and `ext-json` and nothing else — and the three official WordPress AI provider plugins do the same.
- Image input on `deepseek-v4-flash-vision-exp`. All three official WordPress AI providers declare which input modalities a model accepts, and without the declaration the AI Client will not route an image prompt to Zen at all — so the plugin was declining work it can do. Declared per model and only where Zen documents it: Zen fronts several vendors whose models are multimodal upstream, but it is a coding gateway and documents image input for one entry in its catalogue.

- A **Test connection** button on the settings page. The page reported "OpenCode Zen is connected" as soon as a key was present anywhere, without ever asking Zen about it — a key that had been revoked, mistyped or copied from the wrong account read as connected, and the first sign of trouble was a generation failing somewhere else. Zen has no free endpoint that can answer this: `GET /zen/v1/models` is public and returns the full catalogue even when handed a key that is nonsense, so the check is addressed to a generation endpoint using one of Zen's own free-tier models, capped at a single token. A network failure is reported as "could not be checked" rather than as a bad key, since a firewalled site has learned nothing about its credentials. The verdict is cached for five minutes and dropped whenever a key is changed.
- An uninstall handler. Deleting the plugin now removes the `opencode_zen_settings` option and the `opencode_zen_models_cache` transient, which it used to leave behind for good. Credentials are deliberately left alone: `connectors_ai_opencode_zen_api_key` is written by the WordPress Connectors screen and `wp_ai_client_credentials` is shared by every AI provider on the site, so removing either would delete a row this plugin did not create — and in the second case, other providers' keys along with its own.
### Changed

- The settings page no longer claims the provider is connected on the strength of a key existing. It says a key is configured, notes that this is not the same as one that works, and offers to check.
- The live catalogue and the built-in fallback now share one supported-option builder. Each wrote the list out in full, which is two places for the same promise to drift.
- Re-synced the built-in fallback model catalogue with the live `/zen/v1/models` endpoint — 70 models, up from 61.
- The release build no longer installs and packages a production `vendor/` directory, because nothing under it ships any more.

- Declared as tested against WordPress 7.1.
### Fixed

- The chosen default model now decides which model the AI Client reaches for. The setting has been stored since 1.0.0 and never read: picking one wrote a row to `wp_options` and changed no request that followed. The catalogue is now returned with that model first, which is what the AI Client reads — it keeps matching models in catalogue order and, when the caller names neither a model nor a preference, uses the first. With seventy models from eight vendors, that fallback was a coin toss the site owner had no say in. Nothing is filtered, so a caller who names another model still gets it.
- A copy installed from git no longer activates and silently does nothing. It used to look for Composer's autoloader, find no `vendor/`, and return without registering a provider or saying why.
- Generation requests are now addressed to OpenCode Zen. The AI Client hands the plugin a path relative to the provider's base URL and expects an absolute URL back; the plugin returned the path unchanged, so every request named no host and could not be sent. The three official WordPress AI providers all resolve the path through their provider's `url()`, and this now does the same.

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
