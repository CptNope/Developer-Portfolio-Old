<?php
/**
 * Server-side render for the About Experience block.
 *
 * @param array    $attributes Block attributes.
 * @param string   $content    Block content.
 * @param WP_Block $block      Block instance.
 */

$section_label     = isset( $attributes['sectionLabel'] ) ? $attributes['sectionLabel'] : '// experience';
$heading           = isset( $attributes['heading'] ) ? $attributes['heading'] : 'Where I\'ve Worked';
$heading_highlight = isset( $attributes['headingHighlight'] ) ? $attributes['headingHighlight'] : 'Worked';
$jobs_raw          = isset( $attributes['jobs'] ) ? $attributes['jobs'] : '[]';
$edu_emoji         = isset( $attributes['eduEmoji'] ) ? $attributes['eduEmoji'] : '🎓';
$edu_title         = isset( $attributes['eduTitle'] ) ? $attributes['eduTitle'] : 'Education';
$edu_items_raw     = isset( $attributes['eduItems'] ) ? $attributes['eduItems'] : '[]';
$achieve_emoji     = isset( $attributes['achieveEmoji'] ) ? $attributes['achieveEmoji'] : '🏆';
$achieve_title     = isset( $attributes['achieveTitle'] ) ? $attributes['achieveTitle'] : 'Achievements';
$achieve_items_raw = isset( $attributes['achieveItems'] ) ? $attributes['achieveItems'] : '[]';

$jobs          = json_decode( $jobs_raw, true );
$edu_items     = json_decode( $edu_items_raw, true );
$achieve_items = json_decode( $achieve_items_raw, true );

if ( ! is_array( $jobs ) ) { $jobs = array(); }
if ( ! is_array( $edu_items ) ) { $edu_items = array(); }
if ( ! is_array( $achieve_items ) ) { $achieve_items = array(); }

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
<div <?php echo get_block_wrapper_attributes( array( 'class' => 'about-experience-section alignwide' ) ); ?>>

	<!-- Header -->
	<div class="about-exp-header" style="margin-bottom:2.5rem">
		<p class="has-primary-color has-text-color has-jetbrains-mono-font-family" style="font-size:0.875rem"><?php echo esc_html( $section_label ); ?></p>
		<h2 class="wp-block-heading" style="font-size:1.75rem;font-weight:700"><?php echo $heading_html; ?></h2>
	</div>

	<!-- Timeline -->
	<?php if ( ! empty( $jobs ) ) : ?>
	<div class="experience-timeline">
		<?php foreach ( $jobs as $job ) : ?>
		<div class="timeline-item" style="border-left:2px solid rgba(139, 92, 246, 0.3);padding-left:2rem">
			<div class="has-border-color has-card-background-color has-background" style="border-color:rgba(139, 92, 246, 0.15);border-width:1px;border-style:solid;border-radius:1rem;padding:1.5rem">
				<div style="display:flex;justify-content:space-between;flex-wrap:wrap;gap:0.5rem;margin-bottom:0.5rem">
					<p style="font-size:1.1rem;font-weight:600;margin:0"><?php echo esc_html( $job['role'] ); ?></p>
					<p class="has-muted-color has-text-color" style="font-size:0.85rem;margin:0"><?php echo esc_html( $job['period'] ); ?></p>
				</div>
				<p class="has-violet-400-color has-text-color" style="font-size:0.9rem;margin:0 0 0.5rem">💼 <?php echo esc_html( $job['company'] ); ?></p>
				<p class="has-muted-color has-text-color" style="font-size:0.9rem;line-height:1.7;margin:0"><?php echo esc_html( $job['description'] ); ?></p>
			</div>
		</div>
		<?php endforeach; ?>
	</div>
	<?php endif; ?>

	<!-- Education & Achievements -->
	<div class="edu-achieve-grid">
		<?php if ( ! empty( $edu_items ) ) : ?>
		<div class="has-border-color has-card-background-color has-background" style="border-color:rgba(139, 92, 246, 0.15);border-width:1px;border-style:solid;border-radius:1rem;padding:1.5rem">
			<div style="display:flex;align-items:center;gap:0.75rem;margin-bottom:1rem">
				<span style="font-size:1.25rem"><?php echo $edu_emoji; ?></span>
				<span style="font-weight:600"><?php echo esc_html( $edu_title ); ?></span>
			</div>
			<?php foreach ( $edu_items as $item ) : ?>
			<div style="margin-bottom:1rem">
				<p style="font-weight:500;margin:0"><?php echo esc_html( $item['title'] ); ?></p>
				<p class="has-muted-color has-text-color" style="font-size:0.9rem;margin:0"><?php echo esc_html( $item['subtitle'] ); ?></p>
			</div>
			<?php endforeach; ?>
		</div>
		<?php endif; ?>

		<?php if ( ! empty( $achieve_items ) ) : ?>
		<div class="has-border-color has-card-background-color has-background" style="border-color:rgba(139, 92, 246, 0.15);border-width:1px;border-style:solid;border-radius:1rem;padding:1.5rem">
			<div style="display:flex;align-items:center;gap:0.75rem;margin-bottom:1rem">
				<span style="font-size:1.25rem"><?php echo $achieve_emoji; ?></span>
				<span style="font-weight:600"><?php echo esc_html( $achieve_title ); ?></span>
			</div>
			<?php foreach ( $achieve_items as $item ) : ?>
			<div style="margin-bottom:1rem">
				<p style="font-weight:500;margin:0"><?php echo esc_html( $item['title'] ); ?></p>
				<p class="has-muted-color has-text-color" style="font-size:0.9rem;margin:0"><?php echo esc_html( $item['subtitle'] ); ?></p>
			</div>
			<?php endforeach; ?>
		</div>
		<?php endif; ?>
	</div>

</div>
