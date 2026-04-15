<?php
/**
 * OpenCode Zen Settings.
 *
 * @package AlAminAhamed\OpenCodeZenAiProvider\Settings
 */

declare(strict_types=1);

namespace AlAminAhamed\OpenCodeZenAiProvider\Settings;

use AlAminAhamed\OpenCodeZenAiProvider\Metadata\OpenCodeZenModelMetadataDirectory;
use AlAminAhamed\OpenCodeZenAiProvider\Provider\OpenCodeZenProvider;

/**
 * Class OpenCodeZenSettings
 *
 * @since 1.0.0
 */
class OpenCodeZenSettings {

	/**
	 * Option key for settings.
	 *
	 * @since 1.0.0
	 */
	public const OPTION_KEY = 'opencode_zen_settings';

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
			__( 'OpenCode Zen Settings', 'ai-provider-for-opencode-zen' ),
			__( 'OpenCode Zen', 'ai-provider-for-opencode-zen' ),
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
			__( 'General Settings', 'ai-provider-for-opencode-zen' ),
			array( self::class, 'render_general_section' ),
			'opencode-zen-settings'
		);

		add_settings_field(
			'default_model',
			__( 'Default Model', 'ai-provider-for-opencode-zen' ),
			array( self::class, 'render_model_field' ),
			'opencode-zen-settings',
			'opencode_zen_general'
		);

		add_settings_field(
			'temperature',
			__( 'Temperature', 'ai-provider-for-opencode-zen' ),
			array( self::class, 'render_temperature_field' ),
			'opencode-zen-settings',
			'opencode_zen_general'
		);

		add_settings_field(
			'max_tokens',
			__( 'Max Tokens', 'ai-provider-for-opencode-zen' ),
			array( self::class, 'render_max_tokens_field' ),
			'opencode-zen-settings',
			'opencode_zen_general'
		);
	}

	/**
	 * Sanitize settings.
	 *
	 * @since 1.0.0
	 *
	 * @param array $input Settings input.
	 * @return array
	 */
	public static function sanitize_settings( array $input ): array {
		$sanitized = array();

		$sanitized['default_model'] = sanitize_text_field( $input['default_model'] ?? '' );
		$sanitized['temperature']   = floatval( $input['temperature'] ?? 0.7 );
		$sanitized['max_tokens']    = absint( $input['max_tokens'] ?? 4096 );

		$sanitized['temperature'] = max( 0, min( 2, $sanitized['temperature'] ) );
		$sanitized['max_tokens']  = max( 1, min( 200000, $sanitized['max_tokens'] ) );

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
		echo '<p>' . esc_html__( 'Configure default settings for the OpenCode Zen AI provider.', 'ai-provider-for-opencode-zen' ) . '</p>';
	}

	/**
	 * Render model field.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public static function render_model_field(): void {
		$settings  = self::get_settings();
		$directory = new OpenCodeZenModelMetadataDirectory();
		$models    = $directory->listModelMetadata();

		echo '<select name="' . esc_attr( self::OPTION_KEY ) . '[default_model]" id="opencode_zen_default_model">';
		echo '<option value="">' . esc_html__( 'Select a model', 'ai-provider-for-opencode-zen' ) . '</option>';

		foreach ( $models as $model ) {
			$selected = selected( $settings['default_model'] ?? '', $model->getId(), false );
			echo '<option value="' . esc_attr( $model->getId() ) . '" ' . $selected . '>';
			echo esc_html( $model->getName() );
			echo '</option>';
		}

		echo '</select>';
		echo '<p class="description">' . esc_html__( 'The default model to use for text generation.', 'ai-provider-for-opencode-zen' ) . '</p>';
	}

	/**
	 * Render temperature field.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public static function render_temperature_field(): void {
		$settings = self::get_settings();
		$value    = $settings['temperature'] ?? 0.7;

		echo '<input type="number" step="0.1" min="0" max="2"';
		echo ' name="' . esc_attr( self::OPTION_KEY ) . '[temperature]"';
		echo ' id="opencode_zen_temperature"';
		echo ' value="' . esc_attr( $value ) . '"';
		echo ' class="small-text" />';
		echo '<p class="description">' . esc_html__( 'Controls randomness. Lower values make output more focused. Range: 0-2.', 'ai-provider-for-opencode-zen' ) . '</p>';
	}

	/**
	 * Render max tokens field.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public static function render_max_tokens_field(): void {
		$settings = self::get_settings();
		$value    = $settings['max_tokens'] ?? 4096;

		echo '<input type="number" step="1" min="1" max="200000"';
		echo ' name="' . esc_attr( self::OPTION_KEY ) . '[max_tokens]"';
		echo ' id="opencode_zen_max_tokens"';
		echo ' value="' . esc_attr( $value ) . '"';
		echo ' class="small-text" />';
		echo '<p class="description">' . esc_html__( 'Maximum number of tokens to generate.', 'ai-provider-for-opencode-zen' ) . '</p>';
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

		?>
		<div class="wrap">
			<h1><?php echo esc_html__( 'OpenCode Zen Settings', 'ai-provider-for-opencode-zen' ); ?></h1>
			<form method="post" action="options.php">
				<?php
				settings_fields( self::OPTION_KEY );
				do_settings_sections( 'opencode-zen-settings' );
				submit_button();
				?>
			</form>
		</div>
		<?php
	}

	/**
	 * Get settings.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public static function get_settings(): array {
		$defaults = array(
			'temperature' => 0.7,
			'max_tokens'  => 4096,
		);

		$saved = get_option( self::OPTION_KEY, array() );

		return wp_parse_args( $saved, $defaults );
	}
}
