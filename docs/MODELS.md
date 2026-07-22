# Models

OpenCode Zen is an AI model aggregator: one API key exposes frontier models from OpenAI, Anthropic, Google, and others through a single OpenAI-compatible gateway.

## How the model list is resolved

1. **Live discovery** — when an API key is configured, the plugin calls `GET https://opencode.ai/zen/v1/models` and uses whatever the gateway returns. This is always the source of truth.
2. **Caching** — the live list is stored in the `opencode_zen_models_cache` transient for **1 hour**. On an API error the plugin caches an empty result for **5 minutes** and serves the fallback, so a transient outage does not hammer the endpoint.
3. **Fallback** — with no API key, or while an error is cached, the plugin serves a built-in list so the AI Client and any AI-enabled plugins keep working.

The default model (used when a request specifies none) is **`gpt-5.5`**.

## Fallback catalogue

Mirrors the [`/zen/v1/models` catalogue](https://opencode.ai/docs/zen/) — **57 models**. This list is only a safety net; the live API overrides it whenever a key is present.

### GPT (20)

`gpt-5.6-sol`, `gpt-5.6-terra`, `gpt-5.6-luna`, `gpt-5.5`, `gpt-5.5-pro`, `gpt-5.4`, `gpt-5.4-pro`, `gpt-5.4-mini`, `gpt-5.4-nano`, `gpt-5.3-codex`, `gpt-5.3-codex-spark`, `gpt-5.2`, `gpt-5.2-codex`, `gpt-5.1`, `gpt-5.1-codex`, `gpt-5.1-codex-max`, `gpt-5.1-codex-mini`, `gpt-5`, `gpt-5-codex`, `gpt-5-nano`

### Claude (9)

`claude-fable-5`, `claude-opus-4-8`, `claude-opus-4-7`, `claude-opus-4-6`, `claude-opus-4-5`, `claude-sonnet-5`, `claude-sonnet-4-6`, `claude-sonnet-4-5`, `claude-haiku-4-5`

### Gemini (5)

`gemini-3.6-flash`, `gemini-3.5-flash`, `gemini-3.5-flash-lite`, `gemini-3.1-pro`, `gemini-3-flash`

### Other (23)

`grok-4.5`, `grok-build-0.1`, `qwen3.7-max`, `qwen3.7-plus`, `qwen3.6-plus`, `qwen3.5-plus`, `deepseek-v4-pro`, `deepseek-v4-flash`, `deepseek-v4-flash-free`, `minimax-m3`, `minimax-m2.7`, `minimax-m2.5`, `glm-5.2`, `glm-5.1`, `glm-5`, `kimi-k2.7-code`, `kimi-k2.6`, `kimi-k2.5`, `big-pickle`, `mimo-v2.5-free`, `laguna-s-2.1-free`, `nemotron-3-ultra-free`, `north-mini-code-free`

## Keeping the fallback in sync

The fallback lives in `includes/Metadata/OpenCodeZenModelMetadataDirectory.php` (`get_fallback_models()`). When the official catalogue changes, update that array **and** its PHPUnit expectations in `tests/phpunit/Metadata/OpenCodeZenModelMetadataDirectoryTest.php` (`getExpectedModelCount()` plus the per-family ID lists), then refresh the counts in `README.md` and `readme.txt`. Source of truth: <https://opencode.ai/docs/zen/>.
