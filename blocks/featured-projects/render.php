<?php
/**
 * Server-side render for the Featured Projects block.
 *
 * @param array    $attributes Block attributes.
 * @param string   $content    Block content.
 * @param WP_Block $block      Block instance.
 */

$section_label     = isset( $attributes['sectionLabel'] ) ? $attributes['sectionLabel'] : '// featured work';
$heading           = isset( $attributes['heading'] ) ? $attributes['heading'] : 'Recent Projects';
$heading_highlight = isset( $attributes['headingHighlight'] ) ? $attributes['headingHighlight'] : 'Projects';
$link_text         = isset( $attributes['linkText'] ) ? $attributes['linkText'] : 'View all projects →';
$link_url          = isset( $attributes['linkUrl'] ) ? $attributes['linkUrl'] : '/projects';
$post_ids_raw      = isset( $attributes['postIds'] ) ? $attributes['postIds'] : '[]';
$count             = isset( $attributes['count'] ) ? intval( $attributes['count'] ) : 3;

$post_ids = json_decode( $post_ids_raw, true );
if ( ! is_array( $post_ids ) ) {
	$post_ids = array();
}

// Build query args.
$query_args = array(
	'post_type'      => 'project',
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
<div <?php echo get_block_wrapper_attributes( array( 'class' => 'featured-projects-section alignfull' ) ); ?>>
	<div class="fp-inner">

		<!-- Header -->
		<div class="fp-section-header">
			<p class="has-primary-color has-text-color has-jetbrains-mono-font-family" style="font-size:0.875rem"><?php echo esc_html( $section_label ); ?></p>
			<div class="fp-header-row">
				<h2 class="wp-block-heading" style="font-size:clamp(1.75rem, 4vw, 2.5rem);font-weight:700"><?php echo $heading_html; ?></h2>
				<?php if ( $link_text && $link_url ) : ?>
				<a href="<?php echo esc_url( $link_url ); ?>" class="has-violet-400-color has-text-color has-link-color"><?php echo esc_html( $link_text ); ?></a>
				<?php endif; ?>
			</div>
		</div>

		<!-- Project Cards Grid -->
		<?php if ( $projects->have_posts() ) : ?>
		<div class="fp-grid">
			<?php while ( $projects->have_posts() ) : $projects->the_post(); ?>
			<div class="project-card has-border-color has-card-background-color has-background" style="border-color:rgba(139, 92, 246, 0.15);border-width:1px;border-radius:1rem;overflow:hidden">
				<?php if ( has_post_thumbnail() ) : ?>
				<a href="<?php the_permalink(); ?>" class="fp-card-img-link">
					<?php the_post_thumbnail( 'medium_large', array( 'style' => 'width:100%;height:12rem;object-fit:cover;display:block' ) ); ?>
				</a>
				<?php endif; ?>
				<div style="padding:1.25rem">
					<h3 style="font-size:1.125rem;font-weight:600;margin:0 0 0.5rem">
						<a href="<?php the_permalink(); ?>" style="color:inherit;text-decoration:none"><?php the_title(); ?></a>
					</h3>
					<?php if ( has_excerpt() ) : ?>
					<p class="has-muted-color has-text-color" style="font-size:0.875rem;line-height:1.7;margin:0 0 0.75rem"><?php echo wp_trim_words( get_the_excerpt(), 20 ); ?></p>
					<?php endif; ?>
					<?php
					$tags = get_the_terms( get_the_ID(), 'project_tag' );
					if ( $tags && ! is_wp_error( $tags ) ) :
					?>
					<div class="project-tags has-jetbrains-mono-font-family" style="font-size:0.75rem">
						<?php foreach ( $tags as $i => $tag ) : ?>
							<?php if ( $i > 0 ) echo ', '; ?>
							<a href="<?php echo esc_url( get_term_link( $tag ) ); ?>" class="has-violet-400-color has-text-color"><?php echo esc_html( $tag->name ); ?></a>
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
		<?php else : ?>
		<p class="has-text-align-center has-muted-color has-text-color">No projects yet. Add some from the WordPress admin!</p>
		<?php endif; ?>

	</div>
</div>
