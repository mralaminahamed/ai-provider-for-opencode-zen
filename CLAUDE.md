# CLAUDE.md

Guidance for Claude Code (claude.ai/code) working in this repository.

Every command and path below was checked against the tree on 2026-08-09. If one
turns out to be wrong, fix the code or fix this file — do not work around it
silently.

## What this plugin is

A provider for the WordPress AI Client: it teaches WordPress how to talk to
OpenCode Zen, and nothing else. It does not add AI features to a site — the
plugins that consume the AI Client do that.

OpenCode Zen is an **aggregator**, which makes this provider unusual. One key
and one base URL front 61 models from OpenAI, Anthropic, Google, xAI, DeepSeek,
Moonshot, Alibaba and others. Two consequences shape the code:

**The catalogue is the product, and it moves.** Models are added and retired on
OpenCode Zen's schedule, not on this plugin's release schedule. `GET
/zen/v1/models` is public — no key required — so the live list is always
fetched and the bundled list is only a floor for when the endpoint is
unreachable. Re-sync it with `curl https://opencode.ai/zen/v1/models`, never by
hand.

**One parameter set does not fit every model.** The penalties mean something to
the GPT family and nothing to Claude or Gemini. That is what the
`opencode_zen_generate_text_params` filter exists for: it receives the model id
so a site can shape the request for whatever it actually routes to.

**Accuracy is the product.** `ModelMetadata` is not documentation; the AI
Client reads it to decide which provider can satisfy a request. Declaring a
capability the gateway cannot serve routes real work here and fails it. Every
version up to 1.4.0 omitted `chatHistory`, which the gateway has always
supported, so conversation requests were routed elsewhere.

## Commands

```bash
# Run all tests
composer test

# Run a single test file
./vendor/bin/phpunit tests/phpunit/Provider/ProviderTest.php

# Run a single test method
./vendor/bin/phpunit --filter test_provider_has_correct_base_url

# Lint (check only)
composer phpcs

# Lint (auto-fix)
composer phpcbf

# Static analysis
composer phpstan

# Build production release (strips dev deps, optimises autoloader)
composer install --no-dev --no-interaction --prefer-dist -o
```

## Reference Repositories

| Repository | Read it for |
|---|---|
| [wp-ai-client](https://github.com/WordPress/wp-ai-client) | The SDK. `CapabilityEnum` and `OptionEnum` define everything a provider may declare |
| [ai-provider-for-anthropic](https://github.com/WordPress/ai-provider-for-anthropic) | The minimal shape — text only, plus a custom authentication class |
| [ai-provider-for-openai](https://github.com/WordPress/ai-provider-for-openai) | A second modality: `OpenAiImageGenerationModel`, and `textToSpeechConversion` |
| [ai-provider-for-google](https://github.com/WordPress/ai-provider-for-google) | The fullest example — image, combined text-and-image, and a shared trait for aspect ratio |

[OpenCode Zen docs](https://opencode.ai/docs/zen/) — and the live catalogue at
`https://opencode.ai/zen/v1/models`, which is the authority when the two
disagree.

## Directory Structure

```
alamin-ai-provider-for-opencode-zen/
├── alamin-ai-provider-for-opencode-zen.php   # Entry point: constants, autoloader, boot
├── class-ai-provider-for-opencode-zen.php  # Main class: every hook the plugin registers
├── includes/
│   ├── Availability/
│   │   └── ProviderAvailability.php
│   ├── Metadata/
│   │   └── ModelMetadataDirectory.php
│   ├── Models/
│   │   └── TextGenerationModel.php
│   ├── Provider/
│   │   └── Provider.php
│   └── Settings/
│       └── Settings.php
├── templates/
│   └── admin/
│       ├── field-frequency-penalty.php
│       ├── field-max-tokens.php
│       ├── field-model.php
│       ├── field-presence-penalty.php
│       ├── field-temperature.php
│       ├── field-top-p.php
│       ├── section-general.php
│       └── settings-page.php
├── assets/
│   └── images/
│       └── opencode-zen.svg
├── .wordpress-org/          # WP.org assets (icon, banner, screenshots)
├── composer.json
├── composer.lock
└── readme.txt
```

## Architecture

Dual-purpose codebase: works as a standalone Composer package **and** a WordPress plugin. Entry point is `alamin-ai-provider-for-opencode-zen.php`; all PHP logic lives under `includes/`; admin UI templates live under `templates/admin/`.

### Class hierarchy (SDK pattern)

All classes extend from `wordpress/wp-ai-client` (provided by WordPress core on WP 7.0+):

```
AbstractApiProvider  (SDK)
  └── Provider          # provider ID "opencode-zen", base URL, auth method

AbstractOpenAiCompatibleTextGenerationModel  (SDK)
  └── TextGenerationModel   # adds OpenCode-Provider header via createRequest()

ModelMetadataDirectoryInterface  (SDK)
  └── ModelMetadataDirectory  # fetches /v1/models, caches via WP transients
                                          # (1hr on success, 5min on failure),
                                          # falls back to hardcoded list when API unavailable

ProviderAvailabilityInterface  (SDK)
  └── ProviderAvailability    # checks all 3 API key sources (see below)
```

`Settings` — standalone WP settings page (not in SDK hierarchy). Option key: `opencode_zen_settings`. Settings page: `options-general.php?page=opencode-zen-settings`.

### Bootstrap flow (WordPress)

1. `alamin-ai-provider-for-opencode-zen.php` defines `OPENCODE_ZEN_VERSION`, `OPENCODE_ZEN_PLUGIN_FILE`, `OPENCODE_ZEN_URL` and `OPENCODE_ZEN_PATH`, then loads `vendor/autoload.php` — returning early rather than fataling if it is absent, which is the case for a git checkout with no `composer install`
2. `ai_provider_for_opencode_zen()` returns the `AI_Provider_For_OpenCode_Zen` singleton and `init()` registers every hook
3. `init` hook (priority 5): `register_provider()` → registers `Provider` with `AiClient::defaultRegistry()`
4. `init` hook (priority 5): `init_settings()` → `Settings::init()` → wires up `admin_menu` and `admin_init`
5. `wpai_has_ai_credentials` / `wpai_pre_has_valid_credentials_check` → `declare_credentials()` / `declare_valid_credentials()`

Priority 5 is deliberate: anything generating text on `init` needs the provider
in the registry before it asks.

`class-ai-provider-for-opencode-zen.php` holds the wiring and nothing else — every hook the plugin
registers is visible in one file. The work lives in `includes/`.

### API key resolution (priority order)

All three sources are checked by `ProviderAvailability::isConfigured()`, the `wpai_has_ai_credentials` filter, and `get_api_key()` in `ModelMetadataDirectory`:

1. `OPENCODE_ZEN_API_KEY` environment variable
2. WordPress option `connectors_ai_opencode_zen_api_key` (WP 7.0+ Connectors page)
3. WordPress option `wp_ai_client_credentials['opencode-zen']['api_key']` (legacy)

**Important:** all three sources must be consistent across `ProviderAvailability`, `ModelMetadataDirectory::get_api_key()`, and the `wpai_has_ai_credentials` / `wpai_pre_has_valid_credentials_check` filter callbacks in the plugin bootstrap.

### Required AbstractApiProvider methods

```php
protected static function baseUrl(): string
protected static function createModel(ModelMetadata $model_metadata, ProviderMetadata $provider_metadata): ModelInterface
protected static function createProviderMetadata(): ProviderMetadata
protected static function createProviderAvailability(): ProviderAvailabilityInterface
protected static function createModelMetadataDirectory(): ModelMetadataDirectoryInterface
```

### Required ModelMetadataDirectoryInterface methods

```php
public function listModelMetadata(): array           // returns ModelMetadata[]
public function hasModelMetadata(string $model_id): bool
public function getModelMetadata(string $model_id): ModelMetadata  // throws InvalidArgumentException
```

### ModelMetadata constructor

```php
new ModelMetadata(
    $model_id,                                       // string
    $model_name,                                     // string
    $this->capabilities(),                           // list<CapabilityEnum>
    $options                                         // list<SupportedOption>
)
```

`capabilities()` is a single private method shared by the live-fetch path and
the fallback path. Keep it that way — when the list was inlined twice, the two
copies drifted.

### What this provider declares, and why

| Declared | Reason |
|---|---|
| `CapabilityEnum::textGeneration()` | The obvious one |
| `CapabilityEnum::chatHistory()` | The endpoint takes a `messages` array; multi-turn has always worked |
| `OptionEnum::temperature()` | — |
| `OptionEnum::maxTokens()` | — |
| `OptionEnum::topP()` | — |
| `OptionEnum::presencePenalty()` | Honoured by the GPT family; ignored by Claude and Gemini |
| `OptionEnum::frequencyPenalty()` | Same |
| `OptionEnum::stopSequences()` | — |
| `OptionEnum::systemInstruction()` | — |
| `OptionEnum::functionDeclarations()` | Sent as `tools` |
| `OptionEnum::customOptions()` | Passthrough the SDK base already merges into the body — worth more here than for a single-vendor provider, since a parameter only one of eight vendors understands has nowhere else to go |

The declaration is per-provider, not per-model, so it has to describe what the
gateway accepts across the catalogue. Where a specific model ignores something,
that is what the `opencode_zen_generate_text_params` filter is for — it
receives the model id precisely so a site can strip what will not apply.

**Not declared, and deliberately:** `inputModalities` and `outputSchema`. Many
of the models behind Zen support vision and structured output — GPT-5, Claude
and Gemini all do — but the declaration is per *provider*, not per model, and
Zen's `/zen/v1/models` returns only an id with no capability data. Declaring
either would promise it for all 61 models including the ones that cannot. A
caller who knows which model they are routing to can reach both through
`customOptions`.

Do not declare a capability the gateway cannot serve at all. Zen is a text and
code gateway: **every one of its 61 models is an LLM**. There is no image,
speech, video or embedding endpoint, so `imageGeneration()`,
`textToSpeechConversion()`, `videoGeneration()`, `musicGeneration()` and
`embeddingGeneration()` are all wrong here — unlike the MiniMax provider, where
the API does offer them.

### Model catalogue

Resolution order is live fetch → transient cache → bundled fallback:

1. `GET https://opencode.ai/zen/v1/models`, **public**, key sent when present
2. Cached in `opencode_zen_models_cache` — 1 hour on success, 5 minutes on
   failure so an outage does not hammer the endpoint on every page load
3. `get_fallback_models()` only when the endpoint cannot be reached at all

The fallback list drifts by design. Re-sync it against the live endpoint rather
than editing entries by hand, and update the count in `readme.txt` (short
description, description and FAQ), `README.md`, `docs/MODELS.md` and the test's
`getExpectedModelCount()` at the same time — the count appears in more places
than is comfortable.

The readme **title** deliberately no longer carries the count. It read "One AI
Connector for 61 LLM Models" and went stale every time the gateway shipped a
model, in the one field that costs a release to correct.

## Coding Standards

- WordPress Coding Standards (`phpcs.xml.dist`) — text domain `alamin-ai-provider-for-opencode-zen`
- `declare(strict_types=1)` on every PHP file
- Namespace root: `OpenCodeZen\OpenCodeZenAiProvider\`
- PHPStan at `level: max` (WP function stubs via `szepeviktor/phpstan-wordpress`)
- All output escaped: `esc_html()`, `esc_attr()`, `esc_url()`
- All strings wrapped: `__()` / `esc_html__()`; a `translators:` comment goes
  immediately above the `__()` it describes, not above an enclosing
  `wp_kses()` — make-pot associates by adjacency and silently drops it otherwise
- Templates set variables then `require` the template file — no logic inside template files

### Naming

Class names carry no vendor prefix — the namespace supplies it. A class is
named for what it *is*: `Provider`, `Settings`, `ModelMetadataDirectory`,
`TextGenerationModel`, `ProviderAvailability`. This diverges from the official
providers, which prefix everything (`AnthropicProvider`), and matches the rest
of this author's plugins.

### Comments

Comments explain **why**, and earn their place where the code looks wrong but is
not. A comment restating the line above it is noise; a comment recording that
the catalogue endpoint needs no API key saves the next person from re-adding
the guard. Where a change corrects a real defect, say what the defect was.

No AI attribution anywhere — not in comments, commits, or output.

### Commits

Conventional Commits: `type(scope): description`. Branch off `trunk`, never
commit to it directly. Merge with `gh pr merge --merge` — **never `--squash`**.
Stage explicit paths; never `git add -A`.

## Common Mistakes to Avoid

- **Do NOT declare a capability the gateway cannot serve.** `SupportedOption`
  and `CapabilityEnum` are routing promises, not feature lists — see *What this
  plugin is*.
- **Do NOT** re-add an API-key requirement to the model list. The catalogue
  endpoint is public, and requiring a key meant a site saw the stale bundled
  list at exactly the moment it was first being configured.
- **Do NOT** hand-edit the fallback model list. Re-sync it from
  `curl https://opencode.ai/zen/v1/models`. Two models that the gateway does not
  serve reached the list by hand and produced failed generations.
- **Do NOT** add `wordpress/php-ai-client` to Composer production deps — the SDK is provided by WordPress core (WP 7.0+)
- **Do NOT** use `TextGenerationCapability` class — use `CapabilityEnum::textGeneration()`
- **Do NOT** omit the `connectors_ai_opencode_zen_api_key` option check from any code that reads the API key
- **Do NOT** add a setting without wiring it into
  `TextGenerationModel::prepareGenerateTextParams()`. Every setting on the
  screen was stored and never read until 1.5.0; a control that changes nothing
  is worse than a missing one.
- **Do NOT** add a root-level PHP file without adding it to `phpcs.xml.dist`
  *and* `phpstan.neon.dist`. Both list files explicitly, so a new file is
  silently unchecked.
- **Do NOT** call WordPress functions in `includes/` without a
  `function_exists()` guard. The classes are documented as usable as a plain
  Composer package, and that is only true while the guards hold.
- **Do NOT** downgrade GitHub Actions versions — current baseline: `actions/checkout@v6`, `actions/cache@v5`, `actions/upload-artifact@v7`, `actions/download-artifact@v8`, `softprops/action-gh-release@v3`

## WordPress.org listing

Five tag slots, and a tag listing is ordered by **active installs** — so a tag
holding a thousand plugins shows a thirty-install plugin to nobody. Pick tags by
where this plugin actually lands, not by how well the word describes it.

Measured 2026-08-10 against
`https://api.wordpress.org/plugins/info/1.2/?action=query_plugins&request[tag]=…`:

| Tag | Plugins in it | We place | Kept |
|---|---|---|---|
| `text generation` | 5 | #2 | yes |
| `llm` | 88 | #19 | yes |
| `artificial intelligence` | 121 | #24 | yes |
| `connector` | 80 | #29 | yes |
| `opencode` | — | — | yes — brand |
| `ai` | 1832 | #275 | **dropped**, never reached |

Re-measure before changing tags; installs move and so does the answer. Do not
re-add `ai` on the reasoning that this is an AI plugin — it is, and the tag
still returns nothing.

No `image generation` tag, unlike the MiniMax provider: every model here is an
LLM. Tagging for a capability the gateway does not serve routes the wrong
searches here, which is the same mistake as declaring a `CapabilityEnum` it
cannot serve.

The readme title is the highest-weighted field in directory search, which is why
it no longer carries the model count — see *Model catalogue* above.

readme-only changes need no version bump: pushing `readme.txt` to `trunk` fires
`svn-readme-assets-update.yml`. Only the plugin header version and `Stable tag`
require a release.

## CI / Release Workflows

### Release pipeline (`.github/workflows/svn-deploy.yml`)

Five-job DAG triggered on any tag push:

```
meta ──┬── lint ─────┐
       └── phpstan ───┴── package ── deploy
```

| Job | Purpose | Blocks |
|---|---|---|
| `meta` | Validate tag == plugin header version; detect prerelease (`alpha`/`beta`/`rc`) | all |
| `lint` | Sensitive-file scan + PHP syntax lint + PHPCS | `package` |
| `phpstan` | PHPStan level max, 2G memory | `package` |
| `package` | `composer --no-dev --classmap-authoritative` + distignore rsync into `dist/<slug>/` + clean-dist guard + zip | `deploy` |
| `deploy` | 10up SVN (`BUILD_DIR: dist/<slug>`, `ASSETS_DIR: .wordpress-org`) + GH release with prerelease flag + job summary | — |

**Action versions (verified 2026-06):** `checkout@v6`, `cache@v5`, `upload-artifact@v7`, `download-artifact@v8`, `setup-php@v2`, `10up/action-wordpress-plugin-deploy@stable`, `softprops/action-gh-release@v3`

**Per-job Composer caches** — `composer-lint-*`, `composer-phpstan-*` (separate keys prevent cross-job cache poisoning).

**Distributable guard** — `package` job exits 1 if `.git`, `.github`, `tests`, `CLAUDE.md`, `phpcs*.xml*`, `.DS_Store` etc appear inside `dist/<slug>/` after the rsync step.

### Asset/readme sync (`.github/workflows/svn-readme-assets-update.yml`)

Triggered on push to `trunk` when `.wordpress-org/**` or `readme.txt` changes, or manually via `workflow_dispatch`. Uses `10up/action-wordpress-plugin-asset-update@stable`.

### CI (`.github/workflows/ci.yml`)

Three jobs run on push/PR to `trunk`/`main`: `lint` (PHPCS), `analyze` (PHPStan), `test` (PHPUnit across PHP 7.4–8.3 matrix with MySQL service). Cache keys are per-PHP-version (`composer-${{ runner.os }}-php${{ matrix.php }}-*`).

### Tagging a release

```bash
# 1. Bump version in plugin .php header and readme.txt Stable tag
# 2. Add changelog entry to readme.txt == Changelog ==
# 3. Commit and push to trunk
git add alamin-ai-provider-for-opencode-zen.php readme.txt
git commit -m "chore: bump version to X.Y.Z"
git push

# 4. Tag and push — this triggers svn-deploy.yml
git tag X.Y.Z
git push origin X.Y.Z
```

## Key SDK Classes

The SDK is provided by **WordPress core** (WP 7.0+) — not bundled via Composer. These classes are autoloaded by WordPress at runtime:

| Class | Namespace |
|---|---|
| `ModelMetadata` | `WordPress\AiClient\Providers\Models\DTO` |
| `SupportedOption` | `WordPress\AiClient\Providers\Models\DTO` |
| `CapabilityEnum` | `WordPress\AiClient\Providers\Models\Enums` |
| `OptionEnum` | `WordPress\AiClient\Providers\Models\Enums` |
| `ModelMetadataDirectoryInterface` | `WordPress\AiClient\Providers\Contracts` |
| `ProviderAvailabilityInterface` | `WordPress\AiClient\Providers\Contracts` |
| `ProviderMetadata` | `WordPress\AiClient\Providers\DTO` |
| `AbstractApiProvider` | `WordPress\AiClient\Providers\ApiBasedImplementation` |
| `AbstractOpenAiCompatibleTextGenerationModel` | `WordPress\AiClient\Providers\OpenAiCompatibleImplementation` |
| `ApiKeyRequestAuthentication` | `WordPress\AiClient\Providers\Http\DTO` |
| `AiClient` | `WordPress\AiClient` |
