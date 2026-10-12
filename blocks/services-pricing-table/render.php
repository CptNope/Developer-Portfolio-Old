<?php
/**
 * Server-side render for the Services Pricing Table block.
 *
 * @param array    $attributes Block attributes.
 * @param string   $content    Block content.
 * @param WP_Block $block      Block instance.
 */

$section_tag           = isset( $attributes['sectionTag'] ) ? $attributes['sectionTag'] : 'Hourly Rates';
$heading               = isset( $attributes['heading'] ) ? $attributes['heading'] : '';
$intro                 = isset( $attributes['intro'] ) ? $attributes['intro'] : '';
$category              = isset( $attributes['category'] ) ? $attributes['category'] : 'hourly-rates';
$volume_discount_title = isset( $attributes['volumeDiscountTitle'] ) ? $attributes['volumeDiscountTitle'] : '';
$volume_discount_text  = isset( $attributes['volumeDiscountText'] ) ? $attributes['volumeDiscountText'] : '';

$services = new WP_Query( array(
	'post_type'      => 'service',
	'post_status'    => 'publish',
	'posts_per_page' => 20,
	'meta_key'       => 'service_sort_order',
	'orderby'        => 'meta_value_num',
	'order'          => 'ASC',
	'tax_query'      => array(
		array(
			'taxonomy' => 'service_category',
			'field'    => 'slug',
			'terms'    => $category,
		),
	),
) );

$badge_classes = array(
	'wp'    => 'svc-badge-wp',
	'ai'    => 'svc-badge-ai',
	'sec'   => 'svc-badge-sec',
	'maint' => 'svc-badge-maint',
);
?>
<div <?php echo get_block_wrapper_attributes( array( 'class' => 'svc-section alignfull' ) ); ?>>
	<div class="svc-section-inner">
		<span class="svc-section-tag svc-tag-rate"><?php echo esc_html( $section_tag ); ?></span>
		<h2 class="svc-section-heading"><?php echo esc_html( $heading ); ?></h2>
		<?php if ( $intro ) : ?>
		<p class="svc-section-intro"><?php echo esc_html( $intro ); ?></p>
		<?php endif; ?>

		<?php if ( $services->have_posts() ) : ?>
		<table class="svc-pricing-table">
			<thead>
				<tr>
					<th>Service Area</th>
					<th>Rate</th>
					<th>Typical Use Cases</th>
					<th>Min. Hours</th>
				</tr>
			</thead>
			<tbody>
				<?php while ( $services->have_posts() ) : $services->the_post();
					$price        = get_post_meta( get_the_ID(), 'service_price', true );
					$price_note   = get_post_meta( get_the_ID(), 'service_price_note', true );
					$badge_type   = get_post_meta( get_the_ID(), 'service_badge_type', true );
					$typical_uses = get_post_meta( get_the_ID(), 'service_typical_uses', true );
					$min_hours    = get_post_meta( get_the_ID(), 'service_min_hours', true );
					$subtitle     = get_post_meta( get_the_ID(), 'service_subtitle', true );
					$badge_class  = isset( $badge_classes[ $badge_type ] ) ? $badge_classes[ $badge_type ] : 'svc-badge-wp';
				?>
				<tr>
					<td>
						<div class="svc-pt-service"><?php the_title(); ?></div>
						<div class="svc-pt-sub"><span class="svc-pt-badge <?php echo esc_attr( $badge_class ); ?>"><?php echo esc_html( $subtitle ); ?></span></div>
					</td>
					<td>
						<div class="svc-pt-rate"><?php echo esc_html( $price ); ?></div>
						<div class="svc-pt-note"><?php echo esc_html( $price_note ); ?></div>
					</td>
					<td class="svc-pt-note"><?php echo esc_html( $typical_uses ); ?></td>
					<td class="svc-pt-note"><?php echo esc_html( $min_hours ); ?></td>
				</tr>
				<?php endwhile; wp_reset_postdata(); ?>
			</tbody>
		</table>
		<?php endif; ?>

		<?php if ( $volume_discount_text ) : ?>
		<div class="svc-callout">
			<span class="svc-callout-title"><?php echo esc_html( $volume_discount_title ); ?></span>
			<p class="svc-callout-text"><?php echo wp_kses( $volume_discount_text, array( 'strong' => array(), 'em' => array(), 'br' => array() ) ); ?></p>
		</div>
		<?php endif; ?>
	</div>
</div>
