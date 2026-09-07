<?php
/**
 * OpenCode Zen Settings.
 *
 * @package OpenCodeZen\OpenCodeZenAiProvider\Settings
 */

declare(strict_types=1);

namespace OpenCodeZen\OpenCodeZenAiProvider\Settings;

use OpenCodeZen\OpenCodeZenAiProvider\Connection\ConnectionTest;
use OpenCodeZen\OpenCodeZenAiProvider\Metadata\ModelMetadataDirectory;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Settings
 *
 * @since 1.0.0
 */
class Settings {

	/**
	 * Option key for settings.
	 *
	 * @since 1.0.0
	 */
	public const OPTION_KEY = 'opencode_zen_settings';

	/**
	 * The `admin-post.php` action behind the connection test.
	 *
	 * @since 1.6.0
	 */
	public const TEST_ACTION = 'opencode_zen_test_connection';

	/**
	 * Default model used when none has been chosen.
	 *
	 * Matches the flagship entry in ModelMetadataDirectory's built-in
	 * list, so it is always a valid selection even before the API is reachable.
	 *
	 * @since 1.3.2
	 */
	public const DEFAULT_MODEL = 'gpt-5.5';

	/**
	 * Initialize settings.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public static function init(): void {
		add_action( 'admin_menu', array( self::class, 'add_settings_page' ) );
		add_action( 'admin_init', array( self::class, 'register_settings' ) );
		add_filter( 'plugin_action_links_' . plugin_basename( OPENCODE_ZEN_PLUGIN_FILE ), array( self::class, 'add_action_links' ) );

		add_action( 'admin_post_' . self::TEST_ACTION, array( self::class, 'handle_test_connection' ) );

		/*
		 * A cached verdict outlives the key it was about. Both places a key can
		 * be stored are watched, so changing one drops the old answer rather
		 * than leaving a stale "connected" on the screen.
		 */
		add_action( 'update_option_wp_ai_client_credentials', array( ConnectionTest::class, 'forget' ) );
		add_action( 'update_option_connectors_ai_opencode_zen_api_key', array( ConnectionTest::class, 'forget' ) );
	}

	/**
	 * Runs the connection test and returns to the settings page.
	 *
	 * @since 1.6.0
	 *
	 * @return void
	 */
	public static function handle_test_connection(): void {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You are not allowed to test this connection.', 'alamin-ai-provider-for-opencode-zen' ), '', array( 'response' => 403 ) );
		}

		check_admin_referer( self::TEST_ACTION );

		ConnectionTest::run();

		wp_safe_redirect(
			add_query_arg(
				array(
					'page'                => 'opencode-zen-settings',
					'opencode-zen-tested' => '1',
				),
				admin_url( 'options-general.php' )
			)
		);
		exit;
	}

	/**
	 * Add action links to plugins page.
	 *
	 * @since 1.0.0
	 *
	 * @param array<int|string, string> $links Existing action links.
	 * @return array<int|string, string>
	 */
	public static function add_action_links( array $links ): array {
		$settings_link   = '<a href="' . esc_url( admin_url( 'options-general.php?page=opencode-zen-settings' ) ) . '">' . esc_html__( 'Settings', 'alamin-ai-provider-for-opencode-zen' ) . '</a>';
		$connectors_link = '<a href="' . esc_url( admin_url( 'options-connectors.php' ) ) . '">' . esc_html__( 'Connectors', 'alamin-ai-provider-for-opencode-zen' ) . '</a>';
		array_unshift( $links, $settings_link, $connectors_link );
		return $links;
	}

	/**
	 * Add settings page.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public static function add_settings_page(): void {
		add_options_page(
			__( 'OpenCode Zen Settings', 'alamin-ai-provider-for-opencode-zen' ),
			__( 'OpenCode Zen', 'alamin-ai-provider-for-opencode-zen' ),
			'manage_options',
			'opencode-zen-settings',
			array( self::class, 'render_settings_page' )
		);
	}

	/**
	 * Register settings.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public static function register_settings(): void {
		register_setting(
			self::OPTION_KEY,
			self::OPTION_KEY,
			array(
				'sanitize_callback' => array( self::class, 'sanitize_settings' ),
			)
		);

		add_settings_section(
			'opencode_zen_general',
			__( 'General Settings', 'alamin-ai-provider-for-opencode-zen' ),
			array( self::class, 'render_general_section' ),
			'opencode-zen-settings'
		);

		add_settings_field(
			'default_model',
			__( 'Default Model', 'alamin-ai-provider-for-opencode-zen' ),
			array( self::class, 'render_model_field' ),
			'opencode-zen-settings',
			'opencode_zen_general'
		);

		add_settings_field(
			'temperature',
			__( 'Temperature', 'alamin-ai-provider-for-opencode-zen' ),
			array( self::class, 'render_temperature_field' ),
			'opencode-zen-settings',
			'opencode_zen_general'
		);

		add_settings_field(
			'max_tokens',
			__( 'Max Tokens', 'alamin-ai-provider-for-opencode-zen' ),
			array( self::class, 'render_max_tokens_field' ),
			'opencode-zen-settings',
			'opencode_zen_general'
		);

		add_settings_field(
			'top_p',
			__( 'Top P', 'alamin-ai-provider-for-opencode-zen' ),
			array( self::class, 'render_top_p_field' ),
			'opencode-zen-settings',
			'opencode_zen_general'
		);

		add_settings_field(
			'presence_penalty',
			__( 'Presence Penalty', 'alamin-ai-provider-for-opencode-zen' ),
			array( self::class, 'render_presence_penalty_field' ),
			'opencode-zen-settings',
			'opencode_zen_general'
		);

		add_settings_field(
			'frequency_penalty',
			__( 'Frequency Penalty', 'alamin-ai-provider-for-opencode-zen' ),
			array( self::class, 'render_frequency_penalty_field' ),
			'opencode-zen-settings',
			'opencode_zen_general'
		);
	}

	/**
	 * Sanitize settings.
	 *
	 * @since 1.0.0
	 *
	 * @param mixed $input Settings input.
	 * @return array<string, mixed>
	 */
	public static function sanitize_settings( $input ): array {
		if ( ! is_array( $input ) ) {
			return array();
		}

		$sanitized = array();

		$default_model_raw          = $input['default_model'] ?? '';
		$default_model              = sanitize_text_field( is_string( $default_model_raw ) ? $default_model_raw : '' );
		$sanitized['default_model'] = '' !== $default_model ? $default_model : self::DEFAULT_MODEL;

		$temperature_raw          = $input['temperature'] ?? 0.7;
		$sanitized['temperature'] = is_numeric( $temperature_raw ) ? (float) $temperature_raw : 0.7;

		$max_tokens_raw          = $input['max_tokens'] ?? 4096;
		$sanitized['max_tokens'] = is_numeric( $max_tokens_raw ) ? (int) $max_tokens_raw : 4096;

		$top_p_raw          = $input['top_p'] ?? 1.0;
		$sanitized['top_p'] = is_numeric( $top_p_raw ) ? (float) $top_p_raw : 1.0;

		$presence_penalty_raw          = $input['presence_penalty'] ?? 0.0;
		$sanitized['presence_penalty'] = is_numeric( $presence_penalty_raw ) ? (float) $presence_penalty_raw : 0.0;

		$frequency_penalty_raw          = $input['frequency_penalty'] ?? 0.0;
		$sanitized['frequency_penalty'] = is_numeric( $frequency_penalty_raw ) ? (float) $frequency_penalty_raw : 0.0;

		$sanitized['temperature']       = max( 0, min( 2, $sanitized['temperature'] ) );
		$sanitized['max_tokens']        = max( 1, min( 200000, $sanitized['max_tokens'] ) );
		$sanitized['top_p']             = max( 0, min( 1, $sanitized['top_p'] ) );
		$sanitized['presence_penalty']  = max( -2, min( 2, $sanitized['presence_penalty'] ) );
		$sanitized['frequency_penalty'] = max( -2, min( 2, $sanitized['frequency_penalty'] ) );

		return $sanitized;
	}

	/**
	 * Render general section description.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public static function render_general_section(): void {
		require dirname( OPENCODE_ZEN_PLUGIN_FILE ) . '/templates/admin/section-general.php';
	}

	/**
	 * Render model field.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public static function render_model_field(): void {
		$settings       = self::get_settings();
		$directory      = new ModelMetadataDirectory();
		$models         = $directory->listModelMetadata();
		$selected_model = $settings['default_model'] ?? '';
		$option_key     = self::OPTION_KEY;

		require dirname( OPENCODE_ZEN_PLUGIN_FILE ) . '/templates/admin/field-model.php';
	}

	/**
	 * Render temperature field.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public static function render_temperature_field(): void {
		$settings   = self::get_settings();
		$temp_raw   = $settings['temperature'] ?? 0.7;
		$value      = is_numeric( $temp_raw ) ? (float) $temp_raw : 0.7;
		$option_key = self::OPTION_KEY;

		require dirname( OPENCODE_ZEN_PLUGIN_FILE ) . '/templates/admin/field-temperature.php';
	}

	/**
	 * Render max tokens field.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public static function render_max_tokens_field(): void {
		$settings   = self::get_settings();
		$tokens_raw = $settings['max_tokens'] ?? 4096;
		$value      = is_int( $tokens_raw ) ? $tokens_raw : 4096;
		$option_key = self::OPTION_KEY;

		require dirname( OPENCODE_ZEN_PLUGIN_FILE ) . '/templates/admin/field-max-tokens.php';
	}

	/**
	 * Render top p field.
	 *
	 * @since 1.3.0
	 *
	 * @return void
	 */
	public static function render_top_p_field(): void {
		$settings   = self::get_settings();
		$raw        = $settings['top_p'] ?? 1.0;
		$value      = is_numeric( $raw ) ? (float) $raw : 1.0;
		$option_key = self::OPTION_KEY;

		require dirname( OPENCODE_ZEN_PLUGIN_FILE ) . '/templates/admin/field-top-p.php';
	}

	/**
	 * Render presence penalty field.
	 *
	 * @since 1.3.0
	 *
	 * @return void
	 */
	public static function render_presence_penalty_field(): void {
		$settings   = self::get_settings();
		$raw        = $settings['presence_penalty'] ?? 0.0;
		$value      = is_numeric( $raw ) ? (float) $raw : 0.0;
		$option_key = self::OPTION_KEY;

		require dirname( OPENCODE_ZEN_PLUGIN_FILE ) . '/templates/admin/field-presence-penalty.php';
	}

	/**
	 * Render frequency penalty field.
	 *
	 * @since 1.3.0
	 *
	 * @return void
	 */
	public static function render_frequency_penalty_field(): void {
		$settings   = self::get_settings();
		$raw        = $settings['frequency_penalty'] ?? 0.0;
		$value      = is_numeric( $raw ) ? (float) $raw : 0.0;
		$option_key = self::OPTION_KEY;

		require dirname( OPENCODE_ZEN_PLUGIN_FILE ) . '/templates/admin/field-frequency-penalty.php';
	}

	/**
	 * Render settings page.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public static function render_settings_page(): void {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$option_key     = self::OPTION_KEY;
		$is_connected   = self::has_api_key();
		$connectors_url = admin_url( 'options-connectors.php' );
		$test_action    = self::TEST_ACTION;
		$test_result    = ConnectionTest::last_result();

		require dirname( OPENCODE_ZEN_PLUGIN_FILE ) . '/templates/admin/settings-page.php';
	}

	/**
	 * Whether an OpenCode Zen API key is configured.
	 *
	 * Checks the same sources as the AI Client credential filter: the
	 * OPENCODE_ZEN_API_KEY environment variable, the WordPress Connectors page
	 * option (WP 7.0+), and the legacy nested wp_ai_client_credentials option.
	 *
	 * @since 1.3.2
	 *
	 * @return bool True when an API key is present in any supported location.
	 */
	public static function has_api_key(): bool {
		return '' !== self::get_api_key();
	}

	/**
	 * The configured OpenCode Zen API key, or an empty string.
	 *
	 * Checks the same sources as the AI Client credential filter, in the order
	 * a site owner would expect them to win: an environment variable set by the
	 * server, then the WordPress Connectors page option (WP 7.0+), then the
	 * legacy nested `wp_ai_client_credentials` option.
	 *
	 * @since 1.6.0
	 *
	 * @return string
	 */
	public static function get_api_key(): string {
		$env_key = getenv( 'OPENCODE_ZEN_API_KEY' );
		if ( is_string( $env_key ) && '' !== $env_key ) {
			return $env_key;
		}

		if ( ! function_exists( 'get_option' ) ) {
			return '';
		}

		$connectors_key = get_option( 'connectors_ai_opencode_zen_api_key', '' );
		if ( is_string( $connectors_key ) && '' !== $connectors_key ) {
			return $connectors_key;
		}

		$option      = get_option( 'wp_ai_client_credentials', array() );
		$credentials = is_array( $option ) ? ( $option['opencode-zen'] ?? array() ) : array();
		$key         = is_array( $credentials ) ? ( $credentials['api_key'] ?? '' ) : '';

		return is_string( $key ) ? $key : '';
	}

	/**
	 * Get settings.
	 *
	 * @since 1.0.0
	 *
	 * @return array<string, mixed>
	 */
	public static function get_settings(): array {
		$defaults = array(
			'default_model'     => self::DEFAULT_MODEL,
			'temperature'       => 0.7,
			'max_tokens'        => 4096,
			'top_p'             => 1.0,
			'presence_penalty'  => 0.0,
			'frequency_penalty' => 0.0,
		);

		$saved = get_option( self::OPTION_KEY, array() );
		if ( ! is_array( $saved ) ) {
			return $defaults;
		}

		return array(
			'default_model'     => isset( $saved['default_model'] ) && is_string( $saved['default_model'] ) && '' !== $saved['default_model']
				? $saved['default_model']
				: self::DEFAULT_MODEL,
			'temperature'       => isset( $saved['temperature'] ) && is_numeric( $saved['temperature'] )
				? (float) $saved['temperature']
				: $defaults['temperature'],
			'max_tokens'        => isset( $saved['max_tokens'] ) && is_int( $saved['max_tokens'] )
				? $saved['max_tokens']
				: $defaults['max_tokens'],
			'top_p'             => isset( $saved['top_p'] ) && is_numeric( $saved['top_p'] )
				? (float) $saved['top_p']
				: 1.0,
			'presence_penalty'  => isset( $saved['presence_penalty'] ) && is_numeric( $saved['presence_penalty'] )
				? (float) $saved['presence_penalty']
				: 0.0,
			'frequency_penalty' => isset( $saved['frequency_penalty'] ) && is_numeric( $saved['frequency_penalty'] )
				? (float) $saved['frequency_penalty']
				: 0.0,
		);
	}
}
