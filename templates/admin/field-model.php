<?php if ( ! defined( 'ABSPATH' ) ) { exit; }
/**
 * Template: Default model select field.
 *
 * @package AlAminAhamed\OpenCodeZenAiProvider\Settings
 *
 * @var string                                                                $option_key     Settings option key used for the field name attribute.
 * @var \AlAminAhamed\OpenCodeZenAiProvider\Metadata\ModelMetadataInterface[] $models         List of model metadata objects.
 * @var string                                                                $selected_model Currently saved model ID.
 */
?>
<select name="<?php echo esc_attr( $option_key ); ?>[default_model]" id="opencode_zen_default_model">
	<option value=""><?php echo esc_html__( 'Select a model', 'alamin-ai-provider-for-opencode-zen' ); ?></option>
	<?php foreach ( $models as $model ) : ?>
		<option value="<?php echo esc_attr( $model->getId() ); ?>" <?php selected( $selected_model, $model->getId() ); ?>>
			<?php echo esc_html( $model->getName() ); ?>
		</option>
	<?php endforeach; ?>
</select>
<p class="description"><?php echo esc_html__( 'The default model to use for text generation.', 'alamin-ai-provider-for-opencode-zen' ); ?></p>
