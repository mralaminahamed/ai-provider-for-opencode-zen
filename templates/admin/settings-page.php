<?php if ( ! defined( 'ABSPATH' ) ) { exit; }
/**
 * Template: Settings page wrapper.
 *
 * @package AlAminAhamed\OpenCodeZenAiProvider\Settings
 *
 * @var string $option_key Settings option key used by settings_fields().
 */
?>
<div class="wrap">
	<h1><?php echo esc_html__( 'OpenCode Zen Settings', 'alamin-ai-provider-for-opencode-zen' ); ?></h1>
	<form method="post" action="options.php">
		<?php
		settings_fields( $option_key );
		do_settings_sections( 'opencode-zen-settings' );
		submit_button();
		?>
	</form>
</div>
