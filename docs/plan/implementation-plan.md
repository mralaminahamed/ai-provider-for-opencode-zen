# Implementation Plan: AI Provider for OpenCode Zen WordPress Plugin

**Author**: [Your Name]  
**Date**: April 2026  
**Version**: 1.1.0  
**Purpose**: Provide a detailed, step-by-step plan to develop and deploy a custom AI provider plugin for **OpenCode Zen** that integrates seamlessly with the **WordPress PHP AI Client** and **Connectors API** (available in WordPress 7.0+).

## 1. Plugin Overview

- **Plugin Name**: AI Provider for OpenCode Zen
- **Plugin Slug**: `alamin-ai-provider-for-opencode-zen`
- **Description**: Registers OpenCode Zen as a native AI provider using its OpenAI-compatible API endpoint. Enables chat completions and model selection through **Settings > Connectors**.
- **Key Benefits**:
  - Automatic discovery in the WordPress AI ecosystem.
  - Secure API key management via the Connectors interface.
  - Support for high-performance models optimized for coding and general tasks.
- **Target Audience**: Developers building AI-powered WordPress plugins, themes, or sites that require reliable LLM integration.

**Official OpenCode Zen Endpoint**:  
Base URL: `https://opencode.ai/zen/v1` (OpenAI-compatible)

## 2. Prerequisites

- WordPress 7.0 or higher (with built-in PHP AI Client and Connectors API).
- PHP 7.4 or higher.
- Composer (recommended for dependency management).
- Active OpenCode Zen account with an API key (obtain from https://opencode.ai/zen).
- Basic knowledge of WordPress plugin development.

## 3. Folder Structure

```
alamin-ai-provider-for-opencode-zen/
├── alamin-ai-provider-for-opencode-zen.php          # Main plugin file
├── composer.json                             # Dependencies
├── readme.txt                                # Documentation (optional for WordPress.org)
└── assets/                                   # Optional: logo or other assets
```

## 4. Detailed Code Implementation

### 4.1 composer.json

```json
{
    "name": "mralaminahamed/alamin-ai-provider-for-opencode-zen",
    "description": "AI Provider for OpenCode Zen — independent OpenCode Zen integration for the WordPress PHP AI Client",
    "type": "wordpress-plugin",
    "require": {
        "wordpress/php-ai-client": "^1.0"
    },
    "autoload": {
        "classmap": ["."]
    }
}
```

Run `composer install` after placing this file.

### 4.2 Main Plugin File (`alamin-ai-provider-for-opencode-zen.php`)

```php
<?php
/**
 * Plugin Name:       AI Provider for OpenCode Zen
 * Description:       Registers OpenCode Zen (https://opencode.ai/zen) as an AI provider using its OpenAI-compatible endpoint.
 * Version:           1.1.0
 * Requires at least: 7.0
 * Requires PHP:      7.4
 * Author:            Your Name
 * Text Domain:       alamin-ai-provider-for-opencode-zen
 * License:           GPL-2.0-or-later
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Load Composer autoloader
if ( file_exists( __DIR__ . '/vendor/autoload.php' ) ) {
    require_once __DIR__ . '/vendor/autoload.php';
}

class OpenCode_Zen_Provider {

    const PROVIDER_ID = 'opencode-zen';
    const ENV_API_KEY = 'OPEN_CODE_ZEN_API_KEY';
    const BASE_URL    = 'https://opencode.ai/zen/v1';

    public static function init() {
        if ( ! class_exists( '\WordPress\AiClient\AiClient' ) ) {
            return;
        }

        $registry = \WordPress\AiClient\AiClient::defaultRegistry();
        $registry->registerProvider( self::class );

        add_action( 'wp_connectors_init', [ self::class, 'register_connector_metadata' ] );
    }

    public static function register_connector_metadata( $registry ) {
        if ( ! $registry->is_registered( self::PROVIDER_ID ) ) {
            return;
        }

        $connector = $registry->unregister( self::PROVIDER_ID );

        $connector['name']            = __( 'OpenCode Zen', 'alamin-ai-provider-for-opencode-zen' );
        $connector['description']     = __( 'Curated high-performance models optimized for coding and general tasks. OpenAI-compatible API by OpenCode.', 'alamin-ai-provider-for-opencode-zen' );
        $connector['logo_url']        = 'https://opencode.ai/favicon.ico';
        $connector['credentials_url'] = 'https://opencode.ai/zen';
        $connector['auth_type']       = 'api_key';

        $registry->register( self::PROVIDER_ID, $connector );
    }

    public static function get_config() {
        return [
            'base_url'    => self::BASE_URL,
            'api_key_env' => self::ENV_API_KEY,
            'models'      => self::get_models(),
            'supports'    => [
                'chat'       => true,
                'stream'     => true,
                'embeddings' => false,
                'images'     => false,
                'vision'     => false,
            ],
        ];
    }

    public static function get_models() {
        return [
            'gpt-5.4'       => [ 'name' => 'GPT 5.4',       'context' => 128000 ],
            'gpt-5.4-pro'   => [ 'name' => 'GPT 5.4 Pro',   'context' => 128000 ],
            'gpt-5.4-mini'  => [ 'name' => 'GPT 5.4 Mini',  'context' => 128000 ],
            'gpt-5.4-nano'  => [ 'name' => 'GPT 5.4 Nano',  'context' => 128000 ],
            'gpt-5.3-codex' => [ 'name' => 'GPT 5.3 Codex', 'context' => 128000 ],
            // Add more models from https://opencode.ai/docs/zen/ as needed
        ];
    }
}

// Initialize
add_action( 'init', [ 'OpenCode_Zen_Provider', 'init' ] );

// Helper for API key (optional)
if ( ! defined( 'OPEN_CODE_ZEN_API_KEY' ) ) {
    define( 'OPEN_CODE_ZEN_API_KEY', getenv( OpenCode_Zen_Provider::ENV_API_KEY ) );
}
```

## 5. Installation and Activation Steps

1. Create the plugin folder and files as specified.
2. Run `composer install` inside the plugin directory.
3. Upload the plugin to `wp-content/plugins/` and activate it.
4. Navigate to **Settings → Connectors**.
5. Locate **OpenCode Zen**, enter your API key, and save.
6. The provider is now ready for use across the site.

## 6. Usage Examples

```php
use WordPress\AiClient\AiClient;

$result = AiClient::prompt( 'Explain best practices for WordPress plugin development.' )
    ->usingProvider( 'opencode-zen' )
    ->usingModel( 'gpt-5.4' )
    ->generateText();
```

## 7. Advanced Enhancements (Optional)

- Implement dynamic model fetching using the `/v1/models` endpoint with WordPress transients for caching.
- Add a custom settings field to override the base URL.
- Extend support for streaming, function calling, or additional capabilities.
- Improve error handling and logging.

## 8. Testing Recommendations

- Verify the connector appears correctly in **Settings > Connectors**.
- Test text generation with multiple models.
- Check performance, error handling (invalid key, rate limits), and context length.

## 9. Maintenance and Deployment

- Update version and changelog for new features or model additions.
- Monitor OpenCode Zen documentation for endpoint or model changes.
- Consider submitting the plugin to the WordPress.org repository.

**References**:
- OpenCode Zen Documentation: https://opencode.ai/docs/zen/
- WordPress PHP AI Client: Official GitHub repository

---

**End of Document**
