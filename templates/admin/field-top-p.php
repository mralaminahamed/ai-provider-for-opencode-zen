<?php
/**
 * Template: Top P number field.
 *
 * @package OpenCodeZen\OpenCodeZenAiProvider\Settings
 *
 * @var string $option_key Settings option key used for the field name attribute.
 * @var float  $value      Current top_p value.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<input type="number" step="0.01" min="0" max="1"
	name="<?php echo esc_attr( $option_key ); ?>[top_p]"
	id="opencode_zen_top_p"
	value="<?php echo esc_attr( (string) $value ); ?>"
	class="small-text" />
<p class="description"><?php echo esc_html__( 'Nucleus sampling threshold. 1.0 disables top-p sampling. Range: 0-1.', 'alamin-ai-provider-for-opencode-zen' ); ?></p>
