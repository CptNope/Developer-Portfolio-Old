<?php
/**
 * Server-side render for the Contact CTA block.
 *
 * @param array    $attributes Block attributes.
 * @param string   $content    Block content.
 * @param WP_Block $block      Block instance.
 */

$heading           = isset( $attributes['heading'] ) ? $attributes['heading'] : 'Let\'s Build Something';
$heading_highlight = isset( $attributes['headingHighlight'] ) ? $attributes['headingHighlight'] : 'Amazing';
$description       = isset( $attributes['description'] ) ? $attributes['description'] : '';
$button_text       = isset( $attributes['buttonText'] ) ? $attributes['buttonText'] : 'Get In Touch ↗';
$button_url        = isset( $attributes['buttonUrl'] ) ? $attributes['buttonUrl'] : 'mailto:info@jeremyanderson.tech';
?>
<div <?php echo get_block_wrapper_attributes( array( 'class' => 'cta-section alignfull' ) ); ?>>
	<div class="cta-card has-border-color has-card-gradient-gradient-background has-background">

		<div class="cta-icon">
			<span>✉</span>
		</div>

		<h2 class="cta-heading wp-block-heading has-text-align-center"><?php echo wp_kses_post( $heading ); ?> <span class="developer-portfolio-gradient-text"><?php echo wp_kses_post( $heading_highlight ); ?></span></h2>

		<p class="cta-description has-muted-color has-text-color has-text-align-center"><?php echo wp_kses_post( $description ); ?></p>

		<?php if ( ! empty( $button_text ) ) : ?>
		<div class="cta-button-wrapper">
			<div class="wp-block-button hero-cta-primary">
				<a class="wp-block-button__link has-violet-600-to-cyan-600-gradient-background has-background wp-element-button" href="<?php echo esc_url( $button_url ); ?>"><?php echo wp_kses_post( $button_text ); ?></a>
			</div>
		</div>
		<?php endif; ?>

	</div>
</div>
