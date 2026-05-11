# Changelog

## [1.0.0] - 2026-05-11

### Added

- Initial release
- OpenCode Zen AI provider integration for the WordPress AI Client
- Dynamic model discovery from OpenCode Zen API with transient caching (1 hour TTL; 5 min on error)
- Fallback hardcoded model list when API is unavailable
- WordPress admin settings page for default model, temperature, and max tokens
- API key resolution via `OPENCODE_ZEN_API_KEY` environment variable or WordPress AI Client credentials
