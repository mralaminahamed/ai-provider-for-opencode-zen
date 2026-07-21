<?php if ( ! defined( 'ABSPATH' ) ) { exit; }
/**
 * Template: Settings page wrapper.
 *
 * @package AlAminAhamed\OpenCodeZenAiProvider\Settings
 *
 * @var string $option_key     Settings option key used by settings_fields().
 * @var bool   $is_connected   Whether an OpenCode Zen API key is configured.
 * @var string $connectors_url URL of the WordPress Connectors settings screen.
 */
?>
<div class="wrap">
	<h1><?php echo esc_html__( 'OpenCode Zen Settings', 'alamin-ai-provider-for-opencode-zen' ); ?></h1>

	<?php if ( $is_connected ) : ?>
		<div class="notice notice-success inline">
			<p><?php echo esc_html__( 'OpenCode Zen is connected — an API key is configured. The defaults below apply to new text generation requests.', 'alamin-ai-provider-for-opencode-zen' ); ?></p>
		</div>
	<?php else : ?>
		<div class="notice notice-warning inline">
			<p>
				<?php
				printf(
					/* translators: %s: URL of the WordPress Connectors settings screen. */
					wp_kses(
						__( 'No OpenCode Zen API key found. Add your key on the <a href="%s">Connectors screen</a> to activate this provider.', 'alamin-ai-provider-for-opencode-zen' ),
						array( 'a' => array( 'href' => array() ) )
					),
					esc_url( $connectors_url )
				);
				?>
			</p>
		</div>
	<?php endif; ?>

	<form method="post" action="options.php">
		<?php
		settings_fields( $option_key );
		do_settings_sections( 'opencode-zen-settings' );
		submit_button();
		?>
	</form>
</div>
