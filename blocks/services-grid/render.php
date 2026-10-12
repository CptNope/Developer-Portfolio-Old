<?php
/**
 * Server-side render for the Services Grid block.
 *
 * @param array    $attributes Block attributes.
 * @param string   $content    Block content.
 * @param WP_Block $block      Block instance.
 */

$section_label     = isset( $attributes['sectionLabel'] ) ? $attributes['sectionLabel'] : '// services';
$heading           = isset( $attributes['heading'] ) ? $attributes['heading'] : 'What I Offer';
$heading_highlight = isset( $attributes['headingHighlight'] ) ? $attributes['headingHighlight'] : 'Offer';
$description       = isset( $attributes['description'] ) ? $attributes['description'] : '';
$link_text         = isset( $attributes['linkText'] ) ? $attributes['linkText'] : 'View all services →';
$link_url          = isset( $attributes['linkUrl'] ) ? $attributes['linkUrl'] : '/services';
$post_ids_raw      = isset( $attributes['postIds'] ) ? $attributes['postIds'] : '[]';
$count             = isset( $attributes['count'] ) ? intval( $attributes['count'] ) : 3;

$post_ids = json_decode( $post_ids_raw, true );
if ( ! is_array( $post_ids ) ) {
	$post_ids = array();
}

$query_args = array(
	'post_type'      => 'service',
	'post_status'    => 'publish',
	'posts_per_page' => $count,
	'orderby'        => 'date',
	'order'          => 'DESC',
);

if ( ! empty( $post_ids ) ) {
	$query_args['post__in']       = array_map( 'intval', $post_ids );
	$query_args['orderby']        = 'post__in';
	$query_args['posts_per_page'] = count( $post_ids );
}

$services = new WP_Query( $query_args );

// Build heading with gradient highlight.
$heading_html = esc_html( $heading );
if ( ! empty( $heading_highlight ) ) {
	$heading_html = str_replace(
		esc_html( $heading_highlight ),
		'<span class="developer-portfolio-gradient-text">' . esc_html( $heading_highlight ) . '</span>',
		$heading_html
	);
}
?>
<div <?php echo get_block_wrapper_attributes( array( 'class' => 'services-grid-section alignfull' ) ); ?>>
	<div class="sg-inner">

		<!-- Header -->
		<div class="sg-section-header">
			<p class="has-primary-color has-text-color has-jetbrains-mono-font-family" style="font-size:0.875rem"><?php echo esc_html( $section_label ); ?></p>
			<div class="sg-header-row">
				<h2 class="wp-block-heading" style="font-size:clamp(1.75rem, 4vw, 2.5rem);font-weight:700"><?php echo $heading_html; ?></h2>
				<?php if ( $link_text && $link_url ) : ?>
				<a href="<?php echo esc_url( $link_url ); ?>" class="has-violet-400-color has-text-color has-link-color"><?php echo esc_html( $link_text ); ?></a>
				<?php endif; ?>
			</div>
			<?php if ( $description ) : ?>
			<p class="has-muted-color has-text-color" style="font-size:1.1rem;line-height:1.7;margin-top:0.5rem"><?php echo esc_html( $description ); ?></p>
			<?php endif; ?>
		</div>

		<!-- Service Cards Grid -->
		<?php if ( $services->have_posts() ) : ?>
		<div class="sg-grid">
			<?php while ( $services->have_posts() ) : $services->the_post(); ?>
			<div class="service-card has-border-color has-card-background-color has-background" style="border-color:rgba(139, 92, 246, 0.15);border-width:1px;border-style:solid;border-radius:1rem;overflow:hidden">
				<?php if ( has_post_thumbnail() ) : ?>
				<a href="<?php the_permalink(); ?>">
					<?php the_post_thumbnail( 'medium_large', array( 'style' => 'width:100%;height:12rem;object-fit:cover;display:block' ) ); ?>
				</a>
				<?php endif; ?>
				<div style="padding:2rem 1.5rem">
				<?php
				$cats = get_the_terms( get_the_ID(), 'service_category' );
				if ( $cats && ! is_wp_error( $cats ) ) :
				?>
				<div class="post-category-badge has-jetbrains-mono-font-family" style="font-size:0.7rem;margin-bottom:0.75rem">
					<a href="<?php echo esc_url( get_term_link( $cats[0] ) ); ?>" class="has-violet-400-color has-text-color"><?php echo esc_html( $cats[0]->name ); ?></a>
				</div>
				<?php endif; ?>
				<h3 style="font-size:1.25rem;font-weight:600;margin:0 0 0.75rem">
					<a href="<?php the_permalink(); ?>" style="color:inherit;text-decoration:none"><?php the_title(); ?></a>
				</h3>
				<?php if ( has_excerpt() || get_the_content() ) : ?>
				<p class="has-muted-color has-text-color" style="font-size:0.875rem;line-height:1.7;margin:0 0 0.75rem"><?php echo wp_trim_words( get_the_excerpt(), 25 ); ?></p>
				<?php endif; ?>
				<?php
				$price = get_post_meta( get_the_ID(), 'service_price_range', true );
				if ( $price ) :
				?>
				<p class="has-cyan-400-color has-text-color" style="font-size:0.875rem;font-weight:600;margin:0"><?php echo esc_html( $price ); ?></p>
				<?php endif; ?>
				</div>
			</div>
			<?php endwhile; wp_reset_postdata(); ?>
		</div>
		<?php else : ?>
		<p class="has-text-align-center has-muted-color has-text-color">No services yet. Check back soon!</p>
		<?php endif; ?>

	</div>
</div>
