=== AI Provider for OpenCode Zen ===
Contributors: mralaminahamed
Tags: ai, opencode, artificial intelligence, llm, text generation
Requires at least: 6.7
Tested up to: 6.9
Requires PHP: 7.4
Stable tag: 1.0.0
License: GPL-2.0-or-later
License URI: https://spdx.org/licenses/GPL-2.0-or-later.html

Integrates OpenCode Zen as an AI provider for the WordPress AI Client, enabling Claude and GPT model access for coding and general tasks.

== Description ==

This plugin integrates [OpenCode Zen](https://opencode.ai) as an AI provider for the WordPress AI Client. It enables access to high-performance AI models optimized for coding and general tasks through the OpenCode Zen API.

This plugin is an independent, third-party integration and is not affiliated with, endorsed by, or sponsored by OpenCode Zen. "OpenCode Zen" is the name of the third-party service this plugin connects to.

**Features:**

* Seamless integration with the WordPress AI Client plugin
* Dynamic model discovery from the OpenCode Zen API with hourly caching
* Support for text generation using Claude and GPT models
* Secure API key management via WordPress settings or environment variable
* Fallback to a hardcoded model list when the API is unavailable

**Supported Models (fallback list):**

* GPT-4o
* GPT-4o Mini
* Claude Sonnet 4
* Claude 3.5 Sonnet

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

`composer require mralaminahamed/alamin-ai-provider-for-opencode-zen`

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

The plugin falls back to a hardcoded list of supported models (GPT-4o, GPT-4o Mini, Claude Sonnet 4, Claude 3.5 Sonnet) so the AI Client continues to function.

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

= 1.0.0 =
* Initial release
* OpenCode Zen provider registration with WordPress AI Client
* Dynamic model discovery with transient caching and fallback list
* Settings page for API key and default model configuration
* Support for `OPENCODE_ZEN_API_KEY` environment variable

== Upgrade Notice ==

= 1.0.0 =
Initial release. No upgrade steps required.
