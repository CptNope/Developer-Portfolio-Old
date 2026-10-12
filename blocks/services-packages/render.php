<?php
/**
 * Server-side render for the Services Packages block.
 *
 * @param array    $attributes Block attributes.
 * @param string   $content    Block content.
 * @param WP_Block $block      Block instance.
 */

$section_tag      = isset( $attributes['sectionTag'] ) ? $attributes['sectionTag'] : '';
$section_tag_type = isset( $attributes['sectionTagType'] ) ? $attributes['sectionTagType'] : 'wp';
$heading          = isset( $attributes['heading'] ) ? $attributes['heading'] : '';
$intro            = isset( $attributes['intro'] ) ? $attributes['intro'] : '';
$category         = isset( $attributes['category'] ) ? $attributes['category'] : 'wordpress-packages';

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

$tag_class = 'svc-tag-' . esc_attr( $section_tag_type );
?>
<div <?php echo get_block_wrapper_attributes( array( 'class' => 'svc-section alignfull' ) ); ?>>
	<div class="svc-section-inner">
		<span class="svc-section-tag <?php echo $tag_class; ?>"><?php echo esc_html( $section_tag ); ?></span>
		<h2 class="svc-section-heading"><?php echo esc_html( $heading ); ?></h2>
		<?php if ( $intro ) : ?>
		<p class="svc-section-intro"><?php echo esc_html( $intro ); ?></p>
		<?php endif; ?>

		<?php if ( $services->have_posts() ) : ?>
		<div class="svc-pkg-grid">
			<?php while ( $services->have_posts() ) : $services->the_post();
				$price      = get_post_meta( get_the_ID(), 'service_price', true );
				$price_note = get_post_meta( get_the_ID(), 'service_price_note', true );
				$tier       = get_post_meta( get_the_ID(), 'service_tier', true );
				$featured   = get_post_meta( get_the_ID(), 'service_featured', true );
				$cta_text   = get_post_meta( get_the_ID(), 'service_cta_text', true );
				$cta_url    = get_post_meta( get_the_ID(), 'service_cta_url', true );
				$features   = json_decode( get_post_meta( get_the_ID(), 'service_features', true ), true );
				if ( ! is_array( $features ) ) {
					$features = array();
				}
				$card_class = 'svc-pkg-card';
				if ( $featured ) {
					$card_class .= ' svc-pkg-featured';
				}
			?>
			<div class="<?php echo esc_attr( $card_class ); ?>">
				<?php if ( $featured ) : ?>
				<span class="svc-pkg-popular">MOST POPULAR</span>
				<?php endif; ?>
				<div class="svc-pkg-eyebrow"><?php echo esc_html( $tier ); ?></div>
				<div class="svc-pkg-name"><?php the_title(); ?></div>
				<div class="svc-pkg-price"><?php echo esc_html( $price ); ?></div>
				<div class="svc-pkg-price-sub"><?php echo esc_html( $price_note ); ?></div>
				<?php if ( has_excerpt() ) : ?>
				<div class="svc-pkg-desc"><?php echo esc_html( get_the_excerpt() ); ?></div>
				<?php endif; ?>
				<div class="svc-pkg-divider"></div>
				<?php if ( ! empty( $features ) ) : ?>
				<ul class="svc-pkg-features">
					<?php foreach ( $features as $feature ) : ?>
					<li><?php echo esc_html( $feature ); ?></li>
					<?php endforeach; ?>
				</ul>
				<?php endif; ?>
				<?php if ( $cta_text && $cta_url ) : ?>
				<a href="<?php echo esc_url( $cta_url ); ?>" class="svc-pkg-cta<?php echo $featured ? ' svc-pkg-cta-primary' : ''; ?>"><?php echo esc_html( $cta_text ); ?></a>
				<?php endif; ?>
			</div>
			<?php endwhile; wp_reset_postdata(); ?>
		</div>
		<?php endif; ?>
	</div>
</div>
