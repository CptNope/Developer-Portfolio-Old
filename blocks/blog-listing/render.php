<?php
/**
 * Server-side render for the Blog Listing block.
 *
 * @param array    $attributes Block attributes.
 * @param string   $content    Block content.
 * @param WP_Block $block      Block instance.
 */

$section_label     = isset( $attributes['sectionLabel'] ) ? $attributes['sectionLabel'] : '// blog';
$heading           = isset( $attributes['heading'] ) ? $attributes['heading'] : 'Thoughts & Insights';
$heading_highlight = isset( $attributes['headingHighlight'] ) ? $attributes['headingHighlight'] : 'Insights';
$description       = isset( $attributes['description'] ) ? $attributes['description'] : '';
$per_page          = isset( $attributes['perPage'] ) ? intval( $attributes['perPage'] ) : 10;

$paged = max( 1, get_query_var( 'paged', 1 ) );

$query_args = array(
	'post_type'      => 'post',
	'post_status'    => 'publish',
	'posts_per_page' => $per_page,
	'paged'          => $paged,
	'orderby'        => 'date',
	'order'          => 'DESC',
);

$posts_query = new WP_Query( $query_args );

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
<div <?php echo get_block_wrapper_attributes( array( 'class' => 'blog-listing-section alignfull' ) ); ?>>

	<!-- Hero Header -->
	<div class="bl-hero-section" style="padding:10rem 1.5rem 4rem">
		<div class="bl-hero-inner" style="max-width:1152px;margin:0 auto">
			<p class="has-primary-color has-text-color has-jetbrains-mono-font-family" style="font-size:0.875rem;margin-bottom:0.5rem"><?php echo esc_html( $section_label ); ?></p>
			<h1 class="wp-block-heading" style="font-size:clamp(2rem, 5vw, 3rem);font-weight:700;margin-bottom:0.5rem"><?php echo $heading_html; ?></h1>
			<?php if ( $description ) : ?>
			<p class="has-muted-color has-text-color" style="font-size:1.05rem;line-height:1.7"><?php echo esc_html( $description ); ?></p>
			<?php endif; ?>
		</div>
	</div>

	<!-- Posts List -->
	<div class="bl-posts-section" style="padding:0 1.5rem 6rem">
		<div class="bl-posts-inner" style="max-width:1152px;margin:0 auto">
			<?php if ( $posts_query->have_posts() ) : ?>
			<div class="lp-list">
				<?php while ( $posts_query->have_posts() ) : $posts_query->the_post(); ?>
				<a href="<?php the_permalink(); ?>" class="blog-post-card has-border-color has-card-background-color has-background" style="border-color:rgba(139, 92, 246, 0.15);border-width:1px;border-style:solid;border-radius:0.75rem;padding:1.25rem 1.5rem;display:flex;align-items:center;justify-content:space-between;gap:1rem;text-decoration:none;color:inherit">
					<?php if ( has_post_thumbnail() ) : ?>
					<div class="bl-card-thumb" style="flex-shrink:0;width:120px;height:80px;border-radius:0.5rem;overflow:hidden">
						<?php the_post_thumbnail( 'medium', array( 'style' => 'width:100%;height:100%;object-fit:cover;display:block' ) ); ?>
					</div>
					<?php endif; ?>
					<div style="flex:1;min-width:0">
						<div style="display:flex;align-items:center;gap:0.75rem;margin-bottom:0.5rem">
							<?php
							$categories = get_the_category();
							if ( ! empty( $categories ) ) :
							?>
							<span class="post-category-badge has-jetbrains-mono-font-family" style="font-size:0.7rem">
								<span class="has-violet-400-color has-text-color"><?php echo esc_html( $categories[0]->name ); ?></span>
							</span>
							<?php endif; ?>
							<span class="has-muted-color has-text-color" style="font-size:0.8rem"><?php echo get_the_date(); ?></span>
						</div>
						<h3 style="font-size:1rem;font-weight:600;margin:0 0 0.5rem;color:var(--wp--preset--color--foreground)"><?php the_title(); ?></h3>
						<?php if ( has_excerpt() || get_the_content() ) : ?>
						<p class="has-muted-color has-text-color hide-on-mobile" style="font-size:0.875rem;line-height:1.6;margin:0"><?php echo wp_trim_words( get_the_excerpt(), 25 ); ?></p>
						<?php endif; ?>
					</div>
					<span class="has-muted-color has-text-color" style="font-size:1.25rem;flex-shrink:0">→</span>
				</a>
				<?php endwhile; wp_reset_postdata(); ?>
			</div>

			<?php
			// Pagination.
			$total_pages = $posts_query->max_num_pages;
			if ( $total_pages > 1 ) :
			?>
			<nav class="bl-pagination" style="display:flex;justify-content:center;gap:0.5rem;margin-top:3rem" aria-label="Posts pagination">
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
			<p class="has-text-align-center has-muted-color has-text-color" style="font-size:1.125rem">No posts found.</p>
			<?php endif; ?>
		</div>
	</div>
</div>
