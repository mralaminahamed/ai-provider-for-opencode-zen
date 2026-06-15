<?php if ( ! defined( 'ABSPATH' ) ) { exit; }
/**
 * Template: Max tokens number field.
 *
 * @package AlAminAhamed\OpenCodeZenAiProvider\Settings
 *
 * @var string $option_key Settings option key used for the field name attribute.
 * @var int    $value      Current max tokens value.
 */
?>
<input type="number" step="1" min="1" max="200000"
	name="<?php echo esc_attr( $option_key ); ?>[max_tokens]"
	id="opencode_zen_max_tokens"
	value="<?php echo esc_attr( (string) $value ); ?>"
	class="small-text" />
<p class="description"><?php echo esc_html__( 'Maximum number of tokens to generate.', 'alamin-ai-provider-for-opencode-zen' ); ?></p>
