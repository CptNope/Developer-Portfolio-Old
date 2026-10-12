<?php
/**
 * Server-side render for the Services Retainers block.
 *
 * @param array    $attributes Block attributes.
 * @param string   $content    Block content.
 * @param WP_Block $block      Block instance.
 */

$section_tag    = isset( $attributes['sectionTag'] ) ? $attributes['sectionTag'] : 'Monthly Retainers';
$heading        = isset( $attributes['heading'] ) ? $attributes['heading'] : '';
$intro          = isset( $attributes['intro'] ) ? $attributes['intro'] : '';
$category       = isset( $attributes['category'] ) ? $attributes['category'] : 'monthly-retainers';
$terms_title    = isset( $attributes['retainerTermsTitle'] ) ? $attributes['retainerTermsTitle'] : '';
$terms_text     = isset( $attributes['retainerTermsText'] ) ? $attributes['retainerTermsText'] : '';

$services = new WP_Query( array(
	'post_type'      => 'service',
	'post_status'    => 'publish',
	'posts_per_page' => 10,
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
		<span class="svc-section-tag svc-tag-maint"><?php echo esc_html( $section_tag ); ?></span>
		<h2 class="svc-section-heading"><?php echo esc_html( $heading ); ?></h2>
		<?php if ( $intro ) : ?>
		<p class="svc-section-intro"><?php echo esc_html( $intro ); ?></p>
		<?php endif; ?>

		<?php if ( $services->have_posts() ) : ?>
		<div class="svc-retainer-grid">
			<?php while ( $services->have_posts() ) : $services->the_post();
				$price      = get_post_meta( get_the_ID(), 'service_price', true );
				$price_note = get_post_meta( get_the_ID(), 'service_price_note', true );
				$tier       = get_post_meta( get_the_ID(), 'service_tier', true );
				$hours      = get_post_meta( get_the_ID(), 'service_hours_included', true );
				$color_bar  = get_post_meta( get_the_ID(), 'service_color_bar', true );
				$features   = json_decode( get_post_meta( get_the_ID(), 'service_features', true ), true );
				if ( ! is_array( $features ) ) {
					$features = array();
				}
			?>
			<div class="svc-retainer-card">
				<div class="svc-ret-color-bar" style="background:<?php echo esc_attr( $color_bar ?: '#334155' ); ?>"></div>
				<div class="svc-ret-tier"><?php echo esc_html( $tier ); ?></div>
				<div class="svc-ret-name"><?php the_title(); ?></div>
				<div class="svc-ret-price"><?php echo esc_html( $price ); ?></div>
				<div class="svc-ret-period"><?php echo esc_html( $price_note ); ?></div>
				<?php if ( $hours ) : ?>
				<div class="svc-ret-hours"><?php echo esc_html( $hours ); ?></div>
				<?php endif; ?>
				<div class="svc-ret-divider"></div>
				<?php if ( ! empty( $features ) ) : ?>
				<ul class="svc-ret-list">
					<?php foreach ( $features as $feature ) : ?>
					<li><?php echo esc_html( $feature ); ?></li>
					<?php endforeach; ?>
				</ul>
				<?php endif; ?>
			</div>
			<?php endwhile; wp_reset_postdata(); ?>
		</div>
		<?php endif; ?>

		<?php if ( $terms_text ) : ?>
		<div class="svc-callout svc-callout-icon">
			<span class="svc-callout-emoji">📋</span>
			<div>
				<div class="svc-callout-title"><?php echo esc_html( $terms_title ); ?></div>
				<p class="svc-callout-text"><?php echo esc_html( $terms_text ); ?></p>
			</div>
		</div>
		<?php endif; ?>
	</div>
</div>
