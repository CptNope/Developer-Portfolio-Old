<?php
/**
 * Server-side render for the Services Hero block.
 *
 * @param array    $attributes Block attributes.
 * @param string   $content    Block content.
 * @param WP_Block $block      Block instance.
 */

$eyebrow           = isset( $attributes['eyebrow'] ) ? $attributes['eyebrow'] : '';
$heading            = isset( $attributes['heading'] ) ? $attributes['heading'] : 'Services &';
$heading_highlight  = isset( $attributes['headingHighlight'] ) ? $attributes['headingHighlight'] : 'Pricing Guide';
$subtitle           = isset( $attributes['subtitle'] ) ? $attributes['subtitle'] : '';
$chips_raw          = isset( $attributes['chips'] ) ? $attributes['chips'] : '[]';
$chips              = json_decode( $chips_raw, true );
if ( ! is_array( $chips ) ) {
	$chips = array();
}
?>
<div <?php echo get_block_wrapper_attributes( array( 'class' => 'svc-hero-section alignfull' ) ); ?>>
	<div class="svc-hero-inner">
		<div class="svc-hero-orb svc-hero-orb--1"></div>
		<div class="svc-hero-orb svc-hero-orb--2"></div>

		<?php if ( $eyebrow ) : ?>
		<p class="svc-hero-eyebrow"><?php echo esc_html( $eyebrow ); ?></p>
		<?php endif; ?>

		<h1 class="svc-hero-heading">
			<?php echo esc_html( $heading ); ?><br>
			<span class="developer-portfolio-gradient-text"><?php echo esc_html( $heading_highlight ); ?></span>
		</h1>

		<?php if ( $subtitle ) : ?>
		<p class="svc-hero-subtitle"><?php echo esc_html( $subtitle ); ?></p>
		<?php endif; ?>

		<?php if ( ! empty( $chips ) ) : ?>
		<div class="svc-hero-chips">
			<?php foreach ( $chips as $chip ) : ?>
			<span class="svc-chip"><?php echo esc_html( $chip ); ?></span>
			<?php endforeach; ?>
		</div>
		<?php endif; ?>
	</div>
</div>
