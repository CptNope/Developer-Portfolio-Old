<?php
/**
 * Server-side render for the Services Add-Ons block.
 *
 * @param array    $attributes Block attributes.
 * @param string   $content    Block content.
 * @param WP_Block $block      Block instance.
 */

$section_tag = isset( $attributes['sectionTag'] ) ? $attributes['sectionTag'] : 'Add-Ons';
$heading     = isset( $attributes['heading'] ) ? $attributes['heading'] : '';
$intro       = isset( $attributes['intro'] ) ? $attributes['intro'] : '';
$category    = isset( $attributes['category'] ) ? $attributes['category'] : 'add-ons';

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
?>
<div <?php echo get_block_wrapper_attributes( array( 'class' => 'svc-section alignfull' ) ); ?>>
	<div class="svc-section-inner">
		<span class="svc-section-tag svc-tag-rate"><?php echo esc_html( $section_tag ); ?></span>
		<h2 class="svc-section-heading"><?php echo esc_html( $heading ); ?></h2>
		<?php if ( $intro ) : ?>
		<p class="svc-section-intro"><?php echo esc_html( $intro ); ?></p>
		<?php endif; ?>

		<?php if ( $services->have_posts() ) : ?>
		<div class="svc-addon-grid">
			<?php while ( $services->have_posts() ) : $services->the_post();
				$price = get_post_meta( get_the_ID(), 'service_price', true );
				$icon  = get_post_meta( get_the_ID(), 'service_icon', true );
			?>
			<div class="svc-addon-card">
				<?php if ( $icon ) : ?>
				<div class="svc-addon-icon"><?php echo esc_html( $icon ); ?></div>
				<?php endif; ?>
				<div>
					<div class="svc-addon-name"><?php the_title(); ?></div>
					<?php if ( $price ) : ?>
					<div class="svc-addon-price"><?php echo esc_html( $price ); ?></div>
					<?php endif; ?>
					<?php if ( has_excerpt() ) : ?>
					<div class="svc-addon-desc"><?php echo esc_html( get_the_excerpt() ); ?></div>
					<?php endif; ?>
				</div>
			</div>
			<?php endwhile; wp_reset_postdata(); ?>
		</div>
		<?php endif; ?>
	</div>
</div>
