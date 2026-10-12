<?php
/**
 * Server-side render for the Services CTA block.
 *
 * @param array    $attributes Block attributes.
 * @param string   $content    Block content.
 * @param WP_Block $block      Block instance.
 */

$heading             = isset( $attributes['heading'] ) ? $attributes['heading'] : 'Ready to Talk?';
$description         = isset( $attributes['description'] ) ? $attributes['description'] : '';
$primary_btn_text    = isset( $attributes['primaryButtonText'] ) ? $attributes['primaryButtonText'] : '';
$primary_btn_url     = isset( $attributes['primaryButtonUrl'] ) ? $attributes['primaryButtonUrl'] : '';
$secondary_btn_text  = isset( $attributes['secondaryButtonText'] ) ? $attributes['secondaryButtonText'] : '';
$secondary_btn_url   = isset( $attributes['secondaryButtonUrl'] ) ? $attributes['secondaryButtonUrl'] : '';
?>
<div <?php echo get_block_wrapper_attributes( array( 'class' => 'svc-section alignfull' ) ); ?>>
	<div class="svc-section-inner">
		<div class="svc-cta-box">
			<h3 class="svc-cta-heading"><?php echo esc_html( $heading ); ?></h3>
			<?php if ( $description ) : ?>
			<p class="svc-cta-desc"><?php echo esc_html( $description ); ?></p>
			<?php endif; ?>
			<div class="svc-cta-buttons">
				<?php if ( $primary_btn_text && $primary_btn_url ) : ?>
				<a href="<?php echo esc_url( $primary_btn_url ); ?>" class="svc-cta-btn-primary"><?php echo esc_html( $primary_btn_text ); ?></a>
				<?php endif; ?>
				<?php if ( $secondary_btn_text && $secondary_btn_url ) : ?>
				<a href="<?php echo esc_url( $secondary_btn_url ); ?>" class="svc-cta-btn-secondary"><?php echo esc_html( $secondary_btn_text ); ?></a>
				<?php endif; ?>
			</div>
		</div>
	</div>
</div>
