<?php
/**
 * Server-side render for the Tech Stack block.
 *
 * @param array    $attributes Block attributes.
 * @param string   $content    Block content.
 * @param WP_Block $block      Block instance.
 */

$post_id = isset( $block->context['postId'] ) ? $block->context['postId'] : get_the_ID();
$data    = get_post_meta( $post_id, 'tech_stack_data', true );

if ( empty( $data ) ) {
	return;
}

$categories = json_decode( $data, true );
if ( ! is_array( $categories ) || empty( $categories ) ) {
	return;
}

$color_map = array(
	'violet'  => array( 'dot' => '#8b5cf6', 'text' => '#a78bfa' ),
	'cyan'    => array( 'dot' => '#06b6d4', 'text' => '#22d3ee' ),
	'emerald' => array( 'dot' => '#10b981', 'text' => '#34d399' ),
	'rose'    => array( 'dot' => '#f43f5e', 'text' => '#fb7185' ),
	'amber'   => array( 'dot' => '#f59e0b', 'text' => '#fbbf24' ),
);

$align_class = isset( $attributes['align'] ) ? 'align' . $attributes['align'] : 'alignfull';

// Split into rows: first 3 in wide row, rest in centered narrow row.
$top_row    = array_slice( $categories, 0, 3 );
$bottom_row = array_slice( $categories, 3 );
?>
<div <?php echo get_block_wrapper_attributes( array( 'class' => 'tech-stack-section ' . $align_class ) ); ?>>
	<div class="tech-stack-header">
		<p class="tech-stack-label has-primary-color has-text-color has-jetbrains-mono-font-family">// tech stack</p>
		<h2 class="tech-stack-title wp-block-heading">Technologies I <span class="developer-portfolio-gradient-text">Work With</span></h2>
	</div>

	<?php if ( ! empty( $top_row ) ) : ?>
	<div class="tech-stack-grid-top">
		<?php foreach ( $top_row as $cat ) :
			$name   = esc_html( $cat['name'] );
			$color  = esc_attr( $cat['color'] );
			$badges = isset( $cat['badges'] ) ? $cat['badges'] : array();
			$c      = isset( $color_map[ $color ] ) ? $color_map[ $color ] : $color_map['violet'];
		?>
		<div role="group" aria-label="<?php echo $name; ?> technologies">
			<div class="tech-card has-card-background-color has-background">
				<div class="tech-card-header">
					<div class="tech-card-dot" style="background-color:<?php echo esc_attr( $c['dot'] ); ?>" aria-hidden="true"></div>
					<p class="tech-card-name" style="color:<?php echo esc_attr( $c['text'] ); ?>"><?php echo $name; ?></p>
				</div>
				<p class="tech-badges tech-badges-<?php echo $color; ?>">
					<?php foreach ( $badges as $badge ) : ?>
						<span class="tech-badge <?php echo $color; ?>"><?php echo esc_html( $badge ); ?></span>
					<?php endforeach; ?>
				</p>
			</div>
		</div>
		<?php endforeach; ?>
	</div>
	<?php endif; ?>

	<?php if ( ! empty( $bottom_row ) ) : ?>
	<div class="tech-stack-grid-bottom">
		<?php foreach ( $bottom_row as $cat ) :
			$name   = esc_html( $cat['name'] );
			$color  = esc_attr( $cat['color'] );
			$badges = isset( $cat['badges'] ) ? $cat['badges'] : array();
			$c      = isset( $color_map[ $color ] ) ? $color_map[ $color ] : $color_map['violet'];
		?>
		<div role="group" aria-label="<?php echo $name; ?> technologies">
			<div class="tech-card has-card-background-color has-background">
				<div class="tech-card-header">
					<div class="tech-card-dot" style="background-color:<?php echo esc_attr( $c['dot'] ); ?>" aria-hidden="true"></div>
					<p class="tech-card-name" style="color:<?php echo esc_attr( $c['text'] ); ?>"><?php echo $name; ?></p>
				</div>
				<p class="tech-badges tech-badges-<?php echo $color; ?>">
					<?php foreach ( $badges as $badge ) : ?>
						<span class="tech-badge <?php echo $color; ?>"><?php echo esc_html( $badge ); ?></span>
					<?php endforeach; ?>
				</p>
			</div>
		</div>
		<?php endforeach; ?>
	</div>
	<?php endif; ?>
</div>
