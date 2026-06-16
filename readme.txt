=== AI Provider for OpenCode Zen ===
Contributors:      mralaminahamed
Tags:              ai, opencode, llm, connector, artificial-intelligence
Requires at least: 7.0
Tested up to:      7.0
Stable tag:        1.3.0
Requires PHP:      7.4
License:           GPL-2.0-or-later
License URI:       https://www.gnu.org/licenses/gpl-2.0.html

OpenCode Zen provider for the WordPress AI Client.

== Description ==

This plugin provides [OpenCode Zen](https://opencode.ai) integration for the WordPress AI Client. It enables WordPress sites to use OpenCode Zen's AI models for text generation and other AI capabilities through an OpenAI-compatible API.

This plugin is an independent, third-party integration and is not affiliated with, endorsed by, or sponsored by OpenCode Zen.

**Features:**

* Text generation with 40+ models including GPT, Claude, Gemini, Qwen, MiniMax, Kimi, and more
* Automatic model discovery from the OpenCode Zen API with hourly caching
* Fallback to a hardcoded model list when the API is unavailable
* Full generation parameter control: temperature, max tokens, top P, presence penalty, frequency penalty, stop sequences, system instruction, and function declarations
* Settings page for default model and generation parameters
* API key configured via **Settings > Connectors** or the `OPENCODE_ZEN_API_KEY` environment variable

**Supported Models (fallback list):**

GPT models: GPT 5.5, GPT 5.5 Pro, GPT 5.4, GPT 5.4 Pro, GPT 5.4 Mini, GPT 5.4 Nano, GPT 5.3 Codex, GPT 5.3 Codex Spark, GPT 5.2, GPT 5.2 Codex, GPT 5.1, GPT 5.1 Codex, GPT 5.1 Codex Max, GPT 5.1 Codex Mini, GPT 5, GPT 5 Codex, GPT 5 Nano

Claude models: Claude Opus 4.7, Claude Opus 4.6, Claude Opus 4.5, Claude Opus 4.1, Claude Sonnet 4.6, Claude Sonnet 4.5, Claude Sonnet 4, Claude Haiku 4.5, Claude 3.5 Haiku

Gemini models: Gemini 3.5 Flash, Gemini 3.1 Pro, Gemini 3 Flash

Other models: Qwen 3.6 Plus, Qwen 3.5 Plus, MiniMax M2.7, MiniMax M2.5, GLM 5.1, Kimi K2.6, Kimi K2.5, Grok Build 0.1, Big Pickle, DeepSeek V4 Flash Free, Nemotron 3 Super Free

When an API key is configured, the live model list is fetched directly from the OpenCode Zen API.

**Requirements:**

* PHP 7.4 or higher
* WordPress 7.0 or higher

== Installation ==

1. Upload the plugin files to `/wp-content/plugins/alamin-ai-provider-for-opencode-zen/`.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Enter your OpenCode Zen API key via **Settings > Connectors**.
4. Go to **Settings > OpenCode Zen** to configure the default model and generation parameters.

== Frequently Asked Questions ==

= How do I get an OpenCode Zen API key? =

Visit [opencode.ai/zen/settings/api-keys](https://opencode.ai/zen/settings/api-keys) to create an account and generate an API key.

= Where do I enter my API key? =

Go to **Settings > Connectors** in your WordPress admin and enter the key there. Alternatively, set the `OPENCODE_ZEN_API_KEY` environment variable on your server.

= Does this plugin work without the PHP AI Client? =

WordPress 7.0 and higher include the AI Client SDK natively — no additional plugin is required.

= What happens if the OpenCode Zen API is unreachable? =

The plugin falls back to a hardcoded list of 40+ models so text generation continues to work.

= What generation parameters are supported? =

Temperature, max tokens, top P, presence penalty, frequency penalty, stop sequences, system instruction, and function declarations.

== External Services ==

This plugin connects to the **OpenCode Zen API** (`https://opencode.ai/zen/v1`) to:

1. Retrieve the list of available AI models (cached for 1 hour via WordPress transients)
2. Send text generation requests using your configured model

**Service:** OpenCode Zen
**API endpoint:** `https://opencode.ai/zen/v1`
**When data is sent:** When generating AI text or refreshing the model list
**Data sent:** Your API key (via Authorization header) and the text prompt/conversation
**Provider site:** [opencode.ai](https://opencode.ai/) — refer to the OpenCode Zen website for their Terms of Service and Privacy Policy.

No data is sent to the OpenCode Zen API until you enter an API key and a WordPress feature triggers a text generation request.

== Screenshots ==

1. Settings > OpenCode Zen screen showing default model selection and generation parameter configuration.
2. Settings > Connectors screen where you enter your OpenCode Zen API key.

== Changelog ==

= 1.3.0 - 2026-06-16 =

**Added**
- Top P, Presence Penalty, and Frequency Penalty settings fields on the admin settings page.
- Full SupportedOptions coverage: temperature, top P, presence penalty, frequency penalty, stop sequences, system instruction, function declarations, and max tokens.

**Changed**
- Extracted all admin HTML markup to `templates/admin/` for cleaner separation of logic and presentation.
- Renamed plugin class directory from `src/` to `includes/` per WordPress plugin conventions.
- Removed AI Client SDK from Composer production dependencies — WordPress 7.0+ provides it natively at runtime.
- Updated `Requires at least` to 7.0.

= 1.2.1 - 2026-05-01 =

**Fixed**
- Connector showing as "Connected" before any API key is entered — provider availability now correctly checks for a configured API key.

= 1.2.0 - 2026-04-01 =

**Added**
- Provider logo displayed on the WordPress Connectors page alongside Anthropic, Google, and OpenAI.
- Expanded test suite from 37 to 48 tests covering all model families, provider logo path, and settings edge cases.

**Fixed**
- False "no valid connector" warning on the AI admin page when API key is set via the Connectors page.

= 1.1.0 - 2026-03-01 =

**Added**
- Updated fallback model list from 4 stale models to 40+ current models (GPT 5.x, Claude 4.x, Gemini 3.x, Qwen, MiniMax, GLM, Kimi, Grok, and free-tier models).
- Domain Path header field to plugin file.

**Changed**
- Improved plugin file header field ordering and alignment per WordPress.org standard.
- Added file-level PHPDoc block to plugin bootstrap file.

= 1.0.0 - 2026-01-01 =

* Initial release.
* OpenCode Zen provider registration with WordPress AI Client.
* Dynamic model discovery with transient caching and fallback list.
* Settings page for default model configuration.
* Support for `OPENCODE_ZEN_API_KEY` environment variable.

== Upgrade Notice ==

= 1.3.0 =
Adds Top P, Presence Penalty, and Frequency Penalty settings. No database changes required. Requires WordPress 7.0 or higher.

= 1.2.1 =
Fixes connector showing "Connected" before an API key is entered. No database changes required.

= 1.2.0 =
Fixes a false "no valid connector" warning on the AI admin page. No database changes required.
