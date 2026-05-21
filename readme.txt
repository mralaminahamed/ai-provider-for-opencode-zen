=== AI Provider for OpenCode Zen ===
Donate link: https://alaminahamed.com/donate
Contributors: mralaminahamed
Tags: ai, opencode, llm, claude, gpt
Requires at least: 6.7
Tested up to: 7.0
Requires PHP: 7.4
Stable tag: 1.2.0
License: GPL-2.0-or-later
License URI: https://spdx.org/licenses/GPL-2.0-or-later.html

Integrates OpenCode Zen as an AI provider for the WordPress AI Client, enabling access to 40+ models including GPT 5.x, Claude 4.x, Gemini 3.x, and more.

== Description ==

This plugin integrates [OpenCode Zen](https://opencode.ai) as an AI provider for the WordPress AI Client. It enables access to high-performance AI models optimized for coding and general tasks through the OpenCode Zen API.

This plugin is an independent, third-party integration and is not affiliated with, endorsed by, or sponsored by OpenCode Zen. "OpenCode Zen" is the name of the third-party service this plugin connects to.

**Features:**

* Seamless integration with the WordPress AI Client plugin
* Dynamic model discovery from the OpenCode Zen API with hourly caching
* Support for 40+ models including GPT 5.x, Claude 4.x, Gemini 3.x, Qwen, MiniMax, Kimi, Grok, and more
* Secure API key management via WordPress settings or environment variable
* Fallback to a hardcoded model list when the API is unavailable

**Supported Models (fallback list):**

GPT models: GPT 5.5, GPT 5.5 Pro, GPT 5.4, GPT 5.4 Pro, GPT 5.4 Mini, GPT 5.4 Nano, GPT 5.3 Codex, GPT 5.3 Codex Spark, GPT 5.2, GPT 5.2 Codex, GPT 5.1, GPT 5.1 Codex, GPT 5.1 Codex Max, GPT 5.1 Codex Mini, GPT 5, GPT 5 Codex, GPT 5 Nano

Claude models: Claude Opus 4.7, Claude Opus 4.6, Claude Opus 4.5, Claude Opus 4.1, Claude Sonnet 4.6, Claude Sonnet 4.5, Claude Sonnet 4, Claude Haiku 4.5, Claude 3.5 Haiku

Gemini models: Gemini 3.5 Flash, Gemini 3.1 Pro, Gemini 3 Flash

Other models: Qwen 3.6 Plus, Qwen 3.5 Plus, MiniMax M2.7, MiniMax M2.5, GLM 5.1, Kimi K2.6, Kimi K2.5, Grok Build 0.1, Big Pickle, DeepSeek V4 Flash Free, Nemotron 3 Super Free

When an API key is configured, the live model list is fetched directly from the OpenCode Zen API.

**Requirements:**

* WordPress AI Client plugin (or WordPress 7.0+ with built-in AI Client)
* An [OpenCode Zen](https://opencode.ai) account and API key

**API Key Configuration:**

Set your API key in one of two ways:

1. `Settings > OpenCode Zen` admin page
2. `OPENCODE_ZEN_API_KEY` environment variable (takes priority)

== Installation ==

= As a WordPress Plugin =

1. Download the plugin zip file
2. Go to **Plugins > Add New > Upload Plugin** in your WordPress admin
3. Upload the zip and click **Install Now**
4. Ensure the **WordPress AI Client** plugin is installed and activated
5. Activate **AI Provider for OpenCode Zen**
6. Go to **Settings > OpenCode Zen** and enter your API key

= Manual Installation =

1. Upload the `alamin-ai-provider-for-opencode-zen` folder to `/wp-content/plugins/`
2. Follow steps 4–6 above

= As a Composer Package =

`composer require mralaminahamed/ai-provider-for-opencode-zen`

== Frequently Asked Questions ==

= What is OpenCode Zen? =

OpenCode Zen is an AI platform that provides access to various AI models including Claude and GPT models through an OpenAI-compatible API. Learn more at [opencode.ai](https://opencode.ai).

= Do I need the WordPress AI Client? =

Yes. This plugin is a provider add-on for the WordPress AI Client. Install and activate that plugin first.

= Where do I get an API key? =

Sign up at [opencode.ai](https://opencode.ai) and generate an API key from your account dashboard.

= Is my API key stored securely? =

Your API key is stored in the WordPress options table using WordPress's standard options API. For higher security, set the `OPENCODE_ZEN_API_KEY` environment variable on your server instead.

= What happens if the OpenCode Zen API is unreachable? =

The plugin falls back to a hardcoded list of 41 supported models (GPT 5.x, Claude 4.x, Gemini 3.x, and more) so the AI Client continues to function.

== External Services ==

This plugin connects to the **OpenCode Zen API** to:

1. Retrieve the list of available AI models (cached for 1 hour via WordPress transients)
2. Send text generation requests using your configured AI model

**Service:** OpenCode Zen
**API endpoint:** `https://api.opencode.ai` (or as configured)
**When data is sent:** When generating AI text responses or refreshing the model list
**Data sent:** Your API key (via Authorization header) and the text prompt/conversation
**Provider site:** [opencode.ai](https://opencode.ai/) — refer to the OpenCode Zen website for their current Terms of Service and Privacy Policy.

No data is sent to the OpenCode Zen API until you enter an API key and a WordPress feature triggers a text generation request.

== Screenshots ==

1. The OpenCode Zen settings page where you configure your API key and default model.

== Changelog ==

= 1.2.0 =
* Added provider logo displayed on the WordPress Connectors page alongside Anthropic, Google, and OpenAI
* Fixed false "no valid connector" warning on the AI admin page when API key is set via the Connectors page
* Expanded test suite from 37 to 48 tests covering all model families, provider logo path, and settings edge cases

= 1.1.0 =
* Updated fallback model list from 4 stale models to 41 current models sourced from the OpenCode Zen pricing page (GPT 5.x, Claude Opus/Sonnet/Haiku 4.x, Gemini 3.x, Qwen, MiniMax, GLM 5.1, Kimi, Grok, and free-tier models)
* Added Domain Path header field to plugin file
* Improved plugin file header field ordering and alignment per WordPress.org standard
* Added file-level PHPDoc block to plugin bootstrap file
* Updated readme.txt documentation to reflect expanded model support

= 1.0.0 =
* Initial release
* OpenCode Zen provider registration with WordPress AI Client
* Dynamic model discovery with transient caching and fallback list
* Settings page for API key and default model configuration
* Support for `OPENCODE_ZEN_API_KEY` environment variable

== Upgrade Notice ==

= 1.2.0 =
Fixes a false "no valid connector" warning on the AI admin page. No database changes required.

= 1.1.0 =
Expanded fallback model list to 40 models. No database changes or manual steps required.

= 1.0.0 =
Initial release. No upgrade steps required.
