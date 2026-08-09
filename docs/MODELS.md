# Models

OpenCode Zen is an AI model aggregator: one API key exposes frontier models from OpenAI, Anthropic, Google, and others through a single OpenAI-compatible gateway.

## How the model list is resolved

1. **Live discovery** — the plugin calls `GET https://opencode.ai/zen/v1/models` and uses whatever the gateway returns. This is always the source of truth. The catalogue is public, so no API key is required to read it; a configured key is still sent, in case an account can reach models an anonymous caller cannot.
2. **Caching** — the live list is stored in the `opencode_zen_models_cache` transient for **1 hour**. On an API error the plugin caches an empty result for **5 minutes** and serves the fallback, so a transient outage does not hammer the endpoint.
3. **Fallback** — when the endpoint cannot be reached at all, or while an error is cached, the plugin serves a built-in list so the AI Client and any AI-enabled plugins keep working.

The default model (used when a request specifies none) is **`gpt-5.5`**.

## Fallback catalogue

Mirrors the [`/zen/v1/models` catalogue](https://opencode.ai/docs/zen/) — **61 models**, synced 2026-08-09. This list is only a safety net; the live API overrides it whenever the endpoint is reachable. Re-sync it with `curl https://opencode.ai/zen/v1/models` rather than by hand.

### GPT (20)

`gpt-5.6-sol`, `gpt-5.6-terra`, `gpt-5.6-luna`, `gpt-5.5`, `gpt-5.5-pro`, `gpt-5.4`, `gpt-5.4-pro`, `gpt-5.4-mini`, `gpt-5.4-nano`, `gpt-5.3-codex`, `gpt-5.3-codex-spark`, `gpt-5.2`, `gpt-5.2-codex`, `gpt-5.1`, `gpt-5.1-codex`, `gpt-5.1-codex-max`, `gpt-5.1-codex-mini`, `gpt-5`, `gpt-5-codex`, `gpt-5-nano`

### Claude (11)

`claude-fable-5`, `claude-opus-5`, `claude-opus-4-8`, `claude-opus-4-7`, `claude-opus-4-6`, `claude-opus-4-5`, `claude-sonnet-5`, `claude-sonnet-4-6`, `claude-sonnet-4-5`, `claude-sonnet-4`, `claude-haiku-4-5`

### Gemini (5)

`gemini-3.6-flash`, `gemini-3.5-flash`, `gemini-3.5-flash-lite`, `gemini-3.1-pro`, `gemini-3-flash`

### Other (16)

`grok-build-0.1`, `grok-4.5`, `deepseek-v4-pro`, `deepseek-v4-flash`, `glm-5.2`, `glm-5.1`, `glm-5`, `minimax-m3`, `minimax-m2.7`, `minimax-m2.5`, `kimi-k3`, `kimi-k2.7-code`, `kimi-k2.6`, `kimi-k2.5`, `qwen3.6-plus`, `qwen3.5-plus`

### Free (9)

Offered by OpenCode Zen for a limited time while feedback is collected, so expect this group to change.

`big-pickle`, `deepseek-v4-flash-free`, `mimo-v2.5-free`, `ling-3.0-flash-free`, `ling-3.0-tiny-free`, `nemotron-3-ultra-free`, `north-mini-code-free`, `laguna-s-2.1-free`, `longcat-2.0-free`

## Keeping the fallback in sync

The fallback lives in `includes/Metadata/ModelMetadataDirectory.php` (`get_fallback_models()`). When the official catalogue changes, update that array **and** its PHPUnit expectations in `tests/phpunit/Metadata/ModelMetadataDirectoryTest.php` (`getExpectedModelCount()` plus the per-family ID lists), then refresh the counts in `README.md` and `readme.txt`. Source of truth: <https://opencode.ai/docs/zen/>.
