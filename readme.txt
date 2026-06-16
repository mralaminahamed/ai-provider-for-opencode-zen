=== AI Provider for OpenCode Zen ===
Contributors:      mralaminahamed
Tags:              ai, opencode, llm, connector, artificial-intelligence
Requires at least: 7.0
Tested up to:      7.0
Stable tag:        1.3.1
Requires PHP:      7.4
License:           GPL-2.0-or-later
License URI:       https://www.gnu.org/licenses/gpl-2.0.html

OpenCode Zen provider for the WordPress AI Client. One API key — 40+ models including GPT 5, Claude 4, and Gemini 3.

== Description ==

This plugin provides [OpenCode Zen](https://opencode.ai) integration for the WordPress AI Client. It enables WordPress sites to use OpenCode Zen's AI gateway — accessing GPT, Claude, Gemini, Qwen, MiniMax, Kimi, and more through a single OpenAI-compatible API.

This plugin is an independent, third-party integration and is not affiliated with, endorsed by, or sponsored by OpenCode Zen.

= Why OpenCode Zen? =

OpenCode Zen is an **AI model aggregator** — one API key gives you access to 40+ frontier models from OpenAI, Anthropic, Google, and others. Instead of managing separate API keys and billing accounts for each AI provider, you connect once to OpenCode Zen and switch between models freely.

This makes it ideal for WordPress sites that want to experiment with different AI models, compare output quality, or use specialised models (e.g. a coding model for code-related tasks and a creative model for content).

= Features =

* **40+ models from one API key** — GPT 5.x, Claude 4.x, Gemini 3.x, Qwen, MiniMax, Kimi, Grok, DeepSeek, and more
* **Automatic model discovery** — live model list fetched from the OpenCode Zen API and cached hourly; falls back to a hardcoded list when offline
* **Full parameter control** — temperature, max tokens, top P, presence penalty, frequency penalty, stop sequences, system instruction, and function declarations
* **Settings page** — configure default model and generation parameters without touching code
* **API key via Connectors** — enter your key once in **Settings > Connectors**; all AI-enabled plugins share it automatically
* **Environment variable support** — `OPENCODE_ZEN_API_KEY` for server-level configuration, bypassing the database entirely
* **OpenAI-compatible API** — built on the same protocol as OpenAI, so any model that works with the OpenAI provider works here
* **No vendor lock-in** — switch between GPT, Claude, and Gemini without changing plugin or configuration

= What Can You Do With AI in WordPress? =

Once this provider is configured, any WordPress plugin or theme that integrates with the WordPress AI Client can use it:

* **Block editor (Gutenberg)** — AI writing assistance, rephrasing, summarising, and expanding content
* **WooCommerce** — AI-generated product descriptions, SEO meta titles, and customer review summaries
* **SEO plugins** — generate meta descriptions, focus keyphrases, and Open Graph content
* **Customer support** — power AI chatbots and FAQ auto-responses
* **Translation and localisation** — translate and adapt content for different markets
* **Code generation** — use a coding-specialised model (GPT 5.3 Codex, Grok Build) directly in the editor
* **Image alt text** — generate accessible alt attributes for media library images

= Supported Models =

When an API key is configured, the live model list is fetched from the OpenCode Zen API. The built-in fallback list includes:

**GPT models**
GPT 5.5, GPT 5.5 Pro, GPT 5.4, GPT 5.4 Pro, GPT 5.4 Mini, GPT 5.4 Nano, GPT 5.3 Codex, GPT 5.3 Codex Spark, GPT 5.2, GPT 5.2 Codex, GPT 5.1, GPT 5.1 Codex, GPT 5.1 Codex Max, GPT 5.1 Codex Mini, GPT 5, GPT 5 Codex, GPT 5 Nano

**Claude models**
Claude Fable 5, Claude Opus 4.8, Claude Opus 4.7, Claude Opus 4.6, Claude Opus 4.5, Claude Opus 4.1, Claude Sonnet 4.6, Claude Sonnet 4.5, Claude Sonnet 4, Claude Haiku 4.5, Claude 3.5 Haiku

**Gemini models**
Gemini 3.5 Flash, Gemini 3.1 Pro, Gemini 3 Flash

**Other models**
Qwen 3.6 Plus, Qwen 3.5 Plus, MiniMax M2.7, MiniMax M2.5, GLM 5.1, Kimi K2.6, Kimi K2.5, Grok Build 0.1, Big Pickle, DeepSeek V4 Flash Free, Nemotron 3 Super Free

= Requirements =

* PHP 7.4 or higher
* WordPress 7.0 or higher
* An [OpenCode Zen](https://opencode.ai) account and API key

= How the WordPress AI Provider System Works =

WordPress 7.0 introduced a built-in AI Client SDK. Plugins and themes call a standard API (e.g. "generate text from this prompt") without knowing which AI provider is active. Provider plugins like this one register themselves with WordPress and handle the actual API calls.

This means:

1. Install this plugin → OpenCode Zen is registered as an AI provider
2. Enter your API key in **Settings > Connectors**
3. Every AI-enabled plugin on your site can now use OpenCode Zen automatically

You can also install multiple provider plugins and switch between them from the Connectors screen — no re-configuration of individual plugins needed.

= Settings =

Go to **Settings > OpenCode Zen** to configure:

* **Default Model** — the model used when no explicit model is requested by a plugin
* **Temperature** — controls output randomness (0.0–2.0, default 0.7)
* **Max Tokens** — maximum tokens in the response (1–200,000, default 4096)
* **Top P** — nucleus sampling threshold (0.0–1.0, default 1.0)
* **Presence Penalty** — penalises repeated topics (-2.0–2.0, default 0.0)
* **Frequency Penalty** — penalises repeated tokens (-2.0–2.0, default 0.0)

= For Developers =

This plugin follows the official WordPress AI Provider pattern and is fully compatible with any plugin built on the WordPress AI Client SDK.

**Supported SupportedOptions:** `temperature`, `maxTokens`, `topP`, `presencePenalty`, `frequencyPenalty`, `stopSequences`, `systemInstruction`, `functionDeclarations`

**Provider ID:** `opencode-zen`

**Base URL:** `https://opencode.ai/zen/v1`

**API key resolution order:**
1. `OPENCODE_ZEN_API_KEY` environment variable
2. WordPress option `connectors_ai_opencode_zen_api_key` (Settings > Connectors)
3. WordPress option `wp_ai_client_credentials['opencode-zen']['api_key']` (legacy)

== Installation ==

1. Upload the plugin files to `/wp-content/plugins/alamin-ai-provider-for-opencode-zen/`.
2. Activate the plugin through the **Plugins** menu in WordPress.
3. Go to **Settings > Connectors** and enter your OpenCode Zen API key.
4. Go to **Settings > OpenCode Zen** to choose a default model and tune generation parameters.
5. Install any AI-enabled plugin (e.g. the [AI Experiments](https://wordpress.org/plugins/ai/) plugin) to start using AI features.

= Getting Your API Key =

1. Sign up at [opencode.ai](https://opencode.ai)
2. Go to [opencode.ai/zen/settings/api-keys](https://opencode.ai/zen/settings/api-keys)
3. Create a new API key and copy it
4. Paste it into **Settings > Connectors** in your WordPress admin

= WP-CLI Installation =

`wp plugin install alamin-ai-provider-for-opencode-zen --activate`

== Frequently Asked Questions ==

= What is OpenCode Zen? =

OpenCode Zen is an AI platform that acts as a gateway to multiple AI providers. Instead of needing separate API keys for OpenAI (GPT), Anthropic (Claude), and Google (Gemini), you get access to all of them through a single OpenCode Zen API key. Learn more at [opencode.ai](https://opencode.ai).

= Where do I enter my API key? =

Go to **Settings > Connectors** in your WordPress admin and enter your OpenCode Zen API key there. All AI-enabled plugins on your site will automatically use it. Alternatively, set the `OPENCODE_ZEN_API_KEY` environment variable on your server.

= Do I need to install a separate AI Client plugin? =

No. WordPress 7.0 and higher include the AI Client SDK natively — no additional plugin is required.

= Which model should I choose as the default? =

It depends on your use case:

* **Content writing** — Claude Sonnet 4.6 or GPT 5.4 for natural, fluent prose
* **Coding tasks** — GPT 5.3 Codex or Grok Build 0.1 for code generation and review
* **Speed/cost balance** — GPT 5.4 Mini or Gemini 3 Flash for quick, low-cost tasks
* **Research/analysis** — Claude Opus 4.x or GPT 5.5 Pro for depth and accuracy

= What happens if the OpenCode Zen API is unreachable? =

The plugin falls back to a hardcoded list of 42+ models so the AI Client continues to function and AI-enabled plugins stay operational.

= Can I use multiple AI provider plugins at the same time? =

Yes. WordPress lets you install multiple provider plugins. You can switch the active provider from **Settings > Connectors** at any time without reconfiguring any plugin.

= Is my API key stored securely? =

Your API key is stored in the WordPress options table, which is protected by your database credentials. For higher security, set the `OPENCODE_ZEN_API_KEY` environment variable on your server — this keeps the key entirely out of the database.

= How does billing work? =

Billing is handled entirely by OpenCode Zen. Your usage is billed according to [OpenCode Zen's pricing](https://opencode.ai) based on tokens consumed per model. WordPress and this plugin do not charge anything.

= What generation parameters does this provider support? =

Temperature, max tokens, top P, presence penalty, frequency penalty, stop sequences, system instruction, and function declarations — the full set of options supported by the WordPress AI Client SDK.

= Can I use this for WooCommerce product descriptions? =

Yes, if you have a WooCommerce plugin that integrates with the WordPress AI Client. Once this provider is active and your API key is set, any AI-enabled WooCommerce plugin can generate product descriptions, SEO meta, and more using any of the 42+ models.

= Does this work with the Gutenberg block editor? =

Yes. Any Gutenberg plugin or block that uses the WordPress AI Client will automatically use this provider once configured.

= Do you have a developer API? =

This plugin implements the standard WordPress AI Client provider interface. Developers building plugins or themes on top of the WordPress AI Client do not need to do anything special — if this provider is active, it will be available automatically.

== External Services ==

This plugin connects to the **OpenCode Zen API** (`https://opencode.ai/zen/v1`) to:

1. Retrieve the list of available AI models (cached for 1 hour via WordPress transients)
2. Send text generation requests using your configured model

**Service:** OpenCode Zen
**API endpoint:** `https://opencode.ai/zen/v1`
**When data is sent:** When generating AI text or refreshing the model list
**Data sent:** Your API key (via Authorization header) and the text prompt/conversation
**Provider site:** [opencode.ai](https://opencode.ai/) — refer to the OpenCode Zen website for their current Terms of Service and Privacy Policy.

No data is sent to the OpenCode Zen API until you enter an API key and a WordPress feature triggers a text generation request.

== Screenshots ==

1. Settings > OpenCode Zen screen showing default model selection and generation parameter configuration.
2. Settings > Connectors screen where you enter your OpenCode Zen API key.

== Changelog ==

= 1.3.1 - 2026-06-16 =

**Fixed**
- API key stored via Settings > Connectors (`connectors_ai_opencode_zen_api_key`) now correctly used for live model list fetching — previously fell back to the hardcoded model list even when the Connectors key was set.
- Settings `get_settings()` now returns all 6 default fields when the saved option is corrupt or missing; previously returned only `temperature` and `max_tokens`.

**Changed**
- Added Claude Fable 5 and Claude Opus 4.8 to the fallback model list (42+ models total).

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
- Improved plugin file header field ordering per WordPress.org standard.
- Added file-level PHPDoc block to plugin bootstrap file.

= 1.0.0 - 2026-01-01 =

* Initial release.
* OpenCode Zen provider registration with WordPress AI Client.
* Dynamic model discovery with transient caching and fallback list.
* Settings page for default model configuration.
* Support for `OPENCODE_ZEN_API_KEY` environment variable.

== Upgrade Notice ==

= 1.3.1 =
Fixes live model list not loading when API key is set via Settings > Connectors. Adds Claude Fable 5 and Claude Opus 4.8 to the fallback list. No database changes required.

= 1.3.0 =
Adds Top P, Presence Penalty, and Frequency Penalty settings. No database changes required. Requires WordPress 7.0 or higher.

= 1.2.1 =
Fixes connector showing "Connected" before an API key is entered. No database changes required.

= 1.2.0 =
Fixes a false "no valid connector" warning on the AI admin page. No database changes required.
