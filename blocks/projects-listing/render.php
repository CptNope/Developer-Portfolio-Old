<?php
/**
 * Server-side render for the Projects Listing block.
 *
 * @param array    $attributes Block attributes.
 * @param string   $content    Block content.
 * @param WP_Block $block      Block instance.
 */

$section_label     = isset( $attributes['sectionLabel'] ) ? $attributes['sectionLabel'] : '// projects';
$heading           = isset( $attributes['heading'] ) ? $attributes['heading'] : 'All Projects';
$heading_highlight = isset( $attributes['headingHighlight'] ) ? $attributes['headingHighlight'] : 'Projects';
$description       = isset( $attributes['description'] ) ? $attributes['description'] : '';
$category_slug     = isset( $attributes['category'] ) ? $attributes['category'] : '';
$is_hero           = isset( $attributes['isHero'] ) ? (bool) $attributes['isHero'] : true;
$per_page          = isset( $attributes['perPage'] ) ? intval( $attributes['perPage'] ) : 9;
$columns           = isset( $attributes['columns'] ) ? intval( $attributes['columns'] ) : 2;

$paged = max( 1, get_query_var( 'paged', 1 ) );

$query_args = array(
	'post_type'      => 'project',
	'post_status'    => 'publish',
	'orderby'        => 'date',
	'order'          => 'DESC',
);

if ( $category_slug ) {
	$query_args['tax_query'] = array(
		array(
			'taxonomy' => 'project_category',
			'field'    => 'slug',
			'terms'    => $category_slug,
		),
	);
	$query_args['posts_per_page'] = -1;
} else {
	$query_args['posts_per_page'] = $per_page;
	$query_args['paged']          = $paged;
}

$projects = new WP_Query( $query_args );

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
<div <?php echo get_block_wrapper_attributes( array( 'class' => 'projects-listing-section alignfull' ) ); ?>>

	<?php if ( $is_hero ) : ?>
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
	<?php else : ?>
	<!-- Section Header -->
	<div class="pl-section-header" style="padding:2rem 1.5rem 2rem">
		<div style="max-width:1152px;margin:0 auto">
			<p class="has-primary-color has-text-color has-jetbrains-mono-font-family" style="font-size:0.875rem;margin-bottom:0.5rem"><?php echo esc_html( $section_label ); ?></p>
			<h2 class="wp-block-heading" style="font-size:clamp(1.5rem, 4vw, 2.25rem);font-weight:700;margin-bottom:0.5rem"><?php echo $heading_html; ?></h2>
			<?php if ( $description ) : ?>
			<p class="has-muted-color has-text-color" style="font-size:1rem;line-height:1.7"><?php echo esc_html( $description ); ?></p>
			<?php endif; ?>
		</div>
	</div>
	<?php endif; ?>

	<!-- Projects Grid -->
	<div class="pl-grid-section" style="padding:0 1.5rem 4rem">
		<div class="pl-grid-inner" style="max-width:1152px;margin:0 auto">
			<?php if ( $projects->have_posts() ) : ?>
			<div class="fp-grid" style="grid-template-columns:repeat(<?php echo $columns; ?>, 1fr)">
				<?php while ( $projects->have_posts() ) : $projects->the_post(); ?>
				<div class="project-card has-border-color has-card-background-color has-background" style="border-color:rgba(139, 92, 246, 0.15);border-width:1px;border-style:solid;border-radius:1rem;overflow:hidden">
					<?php if ( has_post_thumbnail() ) : ?>
					<a href="<?php the_permalink(); ?>">
						<?php the_post_thumbnail( 'medium_large', array( 'style' => 'width:100%;height:12rem;object-fit:cover;display:block' ) ); ?>
					</a>
					<?php endif; ?>
					<div style="padding:1.25rem">
						<h3 style="font-size:1.125rem;font-weight:600;margin:0 0 0.25rem">
							<a href="<?php the_permalink(); ?>" style="color:inherit;text-decoration:none"><?php the_title(); ?></a>
						</h3>
						<?php
						$subtitle = get_post_meta( get_the_ID(), 'project_subtitle', true );
						if ( $subtitle ) :
						?>
						<p class="has-primary-color has-text-color" style="font-size:0.85rem;font-style:italic;margin:0 0 0.5rem"><?php echo esc_html( $subtitle ); ?></p>
						<?php endif; ?>
						<?php if ( has_excerpt() || get_the_content() ) : ?>
						<p class="has-muted-color has-text-color" style="font-size:0.875rem;line-height:1.7;margin:0 0 0.75rem"><?php echo wp_trim_words( get_the_excerpt(), 25 ); ?></p>
						<?php endif; ?>
						<?php
						$tags = get_the_terms( get_the_ID(), 'project_tag' );
						if ( $tags && ! is_wp_error( $tags ) ) :
						?>
						<div class="project-tags has-jetbrains-mono-font-family" style="font-size:0.75rem;display:flex;flex-wrap:wrap;gap:0.35rem;align-items:center">
							<?php foreach ( $tags as $i => $tag ) : ?>
								<?php if ( $i > 0 ) : ?><span class="has-muted-color has-text-color">,</span><?php endif; ?>
								<a href="<?php echo esc_url( get_term_link( $tag ) ); ?>" class="project-tag-chip"><?php echo esc_html( $tag->name ); ?></a>
							<?php endforeach; ?>
						</div>
						<?php endif; ?>
						<?php
						$github_url = get_post_meta( get_the_ID(), 'project_github_url', true );
						$live_url   = get_post_meta( get_the_ID(), 'project_live_url', true );
						if ( $github_url || $live_url ) :
						?>
						<div class="project-card-links" style="display:flex;gap:1rem;margin-top:0.75rem;font-size:0.85rem">
							<?php if ( $github_url ) : ?>
							<a href="<?php echo esc_url( $github_url ); ?>" target="_blank" rel="noopener" class="has-primary-color has-text-color" style="text-decoration:none">GitHub →</a>
							<?php endif; ?>
							<?php if ( $live_url ) : ?>
							<a href="<?php echo esc_url( $live_url ); ?>" target="_blank" rel="noopener" class="has-primary-color has-text-color" style="text-decoration:none">Live Site →</a>
							<?php endif; ?>
						</div>
						<?php endif; ?>
					</div>
				</div>
				<?php endwhile; wp_reset_postdata(); ?>
			</div>

			<?php
			if ( empty( $category_slug ) ) :
				$total_pages = $projects->max_num_pages;
				if ( $total_pages > 1 ) :
			?>
			<nav class="bl-pagination" style="display:flex;justify-content:center;gap:0.5rem;margin-top:3rem" aria-label="Projects pagination">
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
			<?php endif; endif; ?>

			<?php else : ?>
			<p class="has-text-align-center has-muted-color has-text-color" style="font-size:1rem">No projects yet. Check back soon!</p>
			<?php endif; ?>
		</div>
	</div>
</div>
