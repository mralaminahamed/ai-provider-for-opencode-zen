<?php if ( ! defined( 'ABSPATH' ) ) { exit; }
/**
 * Template: Frequency penalty number field.
 *
 * @package OpenCodeZen\OpenCodeZenAiProvider\Settings
 *
 * @var string $option_key Settings option key used for the field name attribute.
 * @var float  $value      Current frequency_penalty value.
 */
?>
<input type="number" step="0.1" min="-2" max="2"
	name="<?php echo esc_attr( $option_key ); ?>[frequency_penalty]"
	id="opencode_zen_frequency_penalty"
	value="<?php echo esc_attr( (string) $value ); ?>"
	class="small-text" />
<p class="description"><?php echo esc_html__( 'Penalizes tokens based on their frequency in the output so far. Range: -2 to 2.', 'alamin-ai-provider-for-opencode-zen' ); ?></p>
