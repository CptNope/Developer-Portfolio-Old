<?php
/**
 * Server-side render for the Services Listing block.
 *
 * @param array    $attributes Block attributes.
 * @param string   $content    Block content.
 * @param WP_Block $block      Block instance.
 */

$section_label     = isset( $attributes['sectionLabel'] ) ? $attributes['sectionLabel'] : '// services';
$heading           = isset( $attributes['heading'] ) ? $attributes['heading'] : 'What I Offer';
$heading_highlight = isset( $attributes['headingHighlight'] ) ? $attributes['headingHighlight'] : 'Offer';
$description       = isset( $attributes['description'] ) ? $attributes['description'] : '';
$per_page          = isset( $attributes['perPage'] ) ? intval( $attributes['perPage'] ) : 9;
$columns           = isset( $attributes['columns'] ) ? intval( $attributes['columns'] ) : 3;

$paged = max( 1, get_query_var( 'paged', 1 ) );

$query_args = array(
	'post_type'      => 'service',
	'post_status'    => 'publish',
	'posts_per_page' => $per_page,
	'paged'          => $paged,
	'orderby'        => 'date',
	'order'          => 'DESC',
);

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
<div <?php echo get_block_wrapper_attributes( array( 'class' => 'services-listing-section alignfull' ) ); ?>>

	<!-- Hero Header -->
	<div class="bl-hero-section" style="padding:10rem 1.5rem 4rem">
		<div class="bl-hero-inner" style="max-width:1152px;margin:0 auto">
			<p class="has-primary-color has-text-color has-jetbrains-mono-font-family" style="font-size:0.875rem;margin-bottom:0.5rem"><?php echo esc_html( $section_label ); ?></p>
			<h1 class="wp-block-heading" style="font-size:clamp(2rem, 5vw, 3rem);font-weight:700;margin-bottom:0.5rem"><?php echo $heading_html; ?></h1>
			<?php if ( $description ) : ?>
			<p class="has-muted-color has-text-color" style="font-size:1.1rem;line-height:1.7"><?php echo esc_html( $description ); ?></p>
			<?php endif; ?>
		</div>
	</div>

	<!-- Services Grid -->
	<div class="sl-grid-section" style="padding:0 1.5rem 6rem">
		<div class="sl-grid-inner" style="max-width:1152px;margin:0 auto">
			<?php if ( $services->have_posts() ) : ?>
			<div class="sg-grid" style="grid-template-columns:repeat(<?php echo $columns; ?>, 1fr)">
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
						<a href="<?php echo esc_url( get_term_link( $cats[0] ) ); ?>" class="has-violet-400-color has-text-color" style="text-decoration:none"><?php echo esc_html( $cats[0]->name ); ?></a>
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

			<?php
			$total_pages = $services->max_num_pages;
			if ( $total_pages > 1 ) :
			?>
			<nav class="bl-pagination" style="display:flex;justify-content:center;gap:0.5rem;margin-top:3rem" aria-label="Services pagination">
				<?php
				echo paginate_links( array(
					'total'     => $total_pages,
					'current'   => $paged,
					'prev_text' => '←',
					'next_text' => '→',
					'type'      => 'list',
				) );
				?>
			</nav>
			<?php endif; ?>

			<?php else : ?>
			<p class="has-text-align-center has-muted-color has-text-color" style="font-size:1rem">No services yet. Check back soon!</p>
			<?php endif; ?>
		</div>
	</div>
</div>
