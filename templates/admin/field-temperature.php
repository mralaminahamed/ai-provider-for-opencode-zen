<?php
/**
 * Template: Temperature number field.
 *
 * @package OpenCodeZen\OpenCodeZenAiProvider\Settings
 *
 * @var string $option_key Settings option key used for the field name attribute.
 * @var float  $value      Current temperature value.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<input type="number" step="0.1" min="0" max="2"
	name="<?php echo esc_attr( $option_key ); ?>[temperature]"
	id="opencode_zen_temperature"
	value="<?php echo esc_attr( (string) $value ); ?>"
	class="small-text" />
<p class="description"><?php echo esc_html__( 'Controls randomness. Lower values make output more focused. Range: 0-2.', 'alamin-ai-provider-for-opencode-zen' ); ?></p>
