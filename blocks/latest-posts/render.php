<?php
/**
 * Server-side render for the Latest Posts block.
 *
 * @param array    $attributes Block attributes.
 * @param string   $content    Block content.
 * @param WP_Block $block      Block instance.
 */

$section_label     = isset( $attributes['sectionLabel'] ) ? $attributes['sectionLabel'] : '// latest posts';
$heading           = isset( $attributes['heading'] ) ? $attributes['heading'] : 'From the Blog';
$heading_highlight = isset( $attributes['headingHighlight'] ) ? $attributes['headingHighlight'] : 'Blog';
$link_text         = isset( $attributes['linkText'] ) ? $attributes['linkText'] : 'Read all posts →';
$link_url          = isset( $attributes['linkUrl'] ) ? $attributes['linkUrl'] : '/blog';
$post_ids_raw      = isset( $attributes['postIds'] ) ? $attributes['postIds'] : '[]';
$count             = isset( $attributes['count'] ) ? intval( $attributes['count'] ) : 3;

$post_ids = json_decode( $post_ids_raw, true );
if ( ! is_array( $post_ids ) ) {
	$post_ids = array();
}

$query_args = array(
	'post_type'      => 'post',
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
<div <?php echo get_block_wrapper_attributes( array( 'class' => 'latest-posts-section alignfull' ) ); ?>>
	<div class="lp-inner">

		<!-- Header -->
		<div class="lp-section-header">
			<p class="has-primary-color has-text-color has-jetbrains-mono-font-family" style="font-size:0.875rem"><?php echo esc_html( $section_label ); ?></p>
			<div class="lp-header-row">
				<h2 class="wp-block-heading" style="font-size:clamp(1.75rem, 4vw, 2.5rem);font-weight:700"><?php echo $heading_html; ?></h2>
				<?php if ( $link_text && $link_url ) : ?>
				<a href="<?php echo esc_url( $link_url ); ?>" class="has-violet-400-color has-text-color has-link-color"><?php echo esc_html( $link_text ); ?></a>
				<?php endif; ?>
			</div>
		</div>

		<!-- Post Cards List -->
		<?php if ( $posts_query->have_posts() ) : ?>
		<div class="lp-list">
			<?php while ( $posts_query->have_posts() ) : $posts_query->the_post(); ?>
			<div class="blog-post-card has-border-color has-card-background-color has-background" style="border-color:rgba(139, 92, 246, 0.15);border-width:1px;border-radius:1rem;padding:1.5rem;display:flex;align-items:center;justify-content:space-between;gap:1rem">
				<?php if ( has_post_thumbnail() ) : ?>
				<div class="bl-card-thumb" style="flex-shrink:0;width:120px;height:80px;border-radius:0.5rem;overflow:hidden">
					<?php the_post_thumbnail( 'medium', array( 'style' => 'width:100%;height:100%;object-fit:cover;display:block' ) ); ?>
				</div>
				<?php endif; ?>
				<div style="flex:1;min-width:0">
					<div style="display:flex;align-items:center;gap:0.75rem;margin-bottom:0.75rem">
						<?php
						$categories = get_the_category();
						if ( ! empty( $categories ) ) :
						?>
						<span class="post-category-badge has-jetbrains-mono-font-family" style="font-size:0.75rem">
							<a href="<?php echo esc_url( get_category_link( $categories[0]->term_id ) ); ?>" class="has-violet-400-color has-text-color"><?php echo esc_html( $categories[0]->name ); ?></a>
						</span>
						<?php endif; ?>
						<span class="has-muted-color has-text-color" style="font-size:0.8rem"><?php echo get_the_date(); ?></span>
					</div>
					<h3 style="font-size:1.125rem;font-weight:600;margin:0 0 0.5rem">
						<a href="<?php the_permalink(); ?>" style="color:inherit;text-decoration:none"><?php the_title(); ?></a>
					</h3>
					<?php if ( has_excerpt() || get_the_content() ) : ?>
					<p class="has-muted-color has-text-color" style="font-size:0.9rem;line-height:1.7;margin:0"><?php echo wp_trim_words( get_the_excerpt(), 25 ); ?></p>
					<?php endif; ?>
				</div>
				<span class="has-muted-color has-text-color" style="font-size:1.25rem;flex-shrink:0">→</span>
			</div>
			<?php endwhile; wp_reset_postdata(); ?>
		</div>
		<?php else : ?>
		<p class="has-text-align-center has-muted-color has-text-color">No posts yet. Start writing from the WordPress admin!</p>
		<?php endif; ?>

	</div>
</div>
