<?php
/**
 * Server-side render for the About Intro block.
 *
 * @param array    $attributes Block attributes.
 * @param string   $content    Block content.
 * @param WP_Block $block      Block instance.
 */

$section_label     = isset( $attributes['sectionLabel'] ) ? $attributes['sectionLabel'] : '// about me';
$heading           = isset( $attributes['heading'] ) ? $attributes['heading'] : 'The Developer Behind the Code';
$heading_highlight = isset( $attributes['headingHighlight'] ) ? $attributes['headingHighlight'] : 'Developer';
$image_url         = isset( $attributes['imageUrl'] ) ? $attributes['imageUrl'] : '';
$image_alt         = isset( $attributes['imageAlt'] ) ? $attributes['imageAlt'] : '';
$location          = isset( $attributes['location'] ) ? $attributes['location'] : '';
$greeting          = isset( $attributes['greeting'] ) ? $attributes['greeting'] : '';
$bio1              = isset( $attributes['bio1'] ) ? $attributes['bio1'] : '';
$bio2              = isset( $attributes['bio2'] ) ? $attributes['bio2'] : '';
$bio3              = isset( $attributes['bio3'] ) ? $attributes['bio3'] : '';
$bio4              = isset( $attributes['bio4'] ) ? $attributes['bio4'] : '';
$bio5              = isset( $attributes['bio5'] ) ? $attributes['bio5'] : '';
$bio6              = isset( $attributes['bio6'] ) ? $attributes['bio6'] : '';
$bio7              = isset( $attributes['bio7'] ) ? $attributes['bio7'] : '';
$bio8              = isset( $attributes['bio8'] ) ? $attributes['bio8'] : '';

// Build heading with gradient highlight.
$heading_html = esc_html( $heading );
if ( ! empty( $heading_highlight ) ) {
	$heading_html = str_replace(
		esc_html( $heading_highlight ),
		'<span class="developer-portfolio-gradient-text">' . esc_html( $heading_highlight ) . '</span>',
		$heading_html
	);
}

// Stats.
$stats = array();
for ( $i = 1; $i <= 4; $i++ ) {
	$emoji  = isset( $attributes[ 'stat' . $i . 'Emoji' ] ) ? $attributes[ 'stat' . $i . 'Emoji' ] : '';
	$number = isset( $attributes[ 'stat' . $i . 'Number' ] ) ? $attributes[ 'stat' . $i . 'Number' ] : '';
	$label  = isset( $attributes[ 'stat' . $i . 'Label' ] ) ? $attributes[ 'stat' . $i . 'Label' ] : '';
	if ( ! empty( $number ) || ! empty( $label ) ) {
		$stats[] = array(
			'emoji'  => $emoji,
			'number' => $number,
			'label'  => $label,
		);
	}
}
?>
<div <?php echo get_block_wrapper_attributes( array( 'class' => 'about-intro-section alignwide' ) ); ?>>

	<!-- Header -->
	<div class="about-intro-header" style="margin-bottom:4rem">
		<p class="has-primary-color has-text-color has-jetbrains-mono-font-family" style="font-size:0.875rem"><?php echo esc_html( $section_label ); ?></p>
		<h1 class="wp-block-heading" style="font-size:clamp(2rem, 5vw, 3rem);font-weight:700"><?php echo $heading_html; ?></h1>
	</div>

	<!-- Bio columns -->
	<div class="about-intro-columns" style="margin-bottom:5rem">
		<div class="about-intro-image-col">
			<div class="about-image-wrapper">
				<?php if ( ! empty( $image_url ) ) : ?>
				<figure class="wp-block-image size-large has-custom-border">
					<img src="<?php echo esc_url( $image_url ); ?>" alt="<?php echo esc_attr( $image_alt ); ?>" style="border-color:rgba(139, 92, 246, 0.15);border-width:1px;border-radius:1rem;border-style:solid;width:100%;height:auto" />
				</figure>
				<?php endif; ?>

				<?php if ( ! empty( $location ) ) : ?>
				<div class="location-badge has-violet-600-to-cyan-600-gradient-background has-background" style="border-radius:0.75rem;padding:0.5rem 1rem">
					<p class="has-white-color has-text-color" style="font-size:0.875rem;margin:0">📍 <?php echo esc_html( $location ); ?></p>
				</div>
				<?php endif; ?>
			</div>
		</div>

		<div class="about-intro-text-col">
			<?php if ( ! empty( $greeting ) ) : ?>
			<h2 class="wp-block-heading" style="margin-bottom:1rem;font-size:1.5rem;font-weight:600"><?php echo esc_html( $greeting ); ?></h2>
			<?php endif; ?>

			<?php if ( ! empty( $bio1 ) ) : ?>
			<p class="has-muted-color has-text-color" style="font-size:1.05rem;line-height:1.8"><?php echo esc_html( $bio1 ); ?></p>
			<?php endif; ?>

			<?php if ( ! empty( $bio2 ) ) : ?>
			<p class="has-muted-color has-text-color" style="font-size:1.05rem;line-height:1.8"><?php echo esc_html( $bio2 ); ?></p>
			<?php endif; ?>

			<?php if ( ! empty( $bio3 ) ) : ?>
			<p class="has-muted-color has-text-color" style="font-size:1.05rem;line-height:1.8"><?php echo esc_html( $bio3 ); ?></p>
			<?php endif; ?>

			<?php if ( ! empty( $bio4 ) ) : ?>
			<p class="has-muted-color has-text-color" style="font-size:1.05rem;line-height:1.8"><?php echo esc_html( $bio4 ); ?></p>
			<?php endif; ?>

			<?php if ( ! empty( $bio5 ) ) : ?>
			<p class="has-muted-color has-text-color" style="font-size:1.05rem;line-height:1.8"><?php echo esc_html( $bio5 ); ?></p>
			<?php endif; ?>

			<?php if ( ! empty( $bio6 ) ) : ?>
			<p class="has-muted-color has-text-color" style="font-size:1.05rem;line-height:1.8"><?php echo esc_html( $bio6 ); ?></p>
			<?php endif; ?>

			<?php if ( ! empty( $bio7 ) ) : ?>
			<p class="has-muted-color has-text-color" style="font-size:1.05rem;line-height:1.8"><?php echo esc_html( $bio7 ); ?></p>
			<?php endif; ?>

			<?php if ( ! empty( $bio8 ) ) : ?>
			<p class="has-muted-color has-text-color" style="font-size:1.05rem;line-height:1.8"><?php echo esc_html( $bio8 ); ?></p>
			<?php endif; ?>
		</div>
	</div>

	<!-- Stats grid -->
	<?php if ( ! empty( $stats ) ) : ?>
	<div class="about-stats-grid">
		<?php foreach ( $stats as $stat ) : ?>
		<div class="stat-card has-border-color has-card-background-color has-background" style="border-color:rgba(139, 92, 246, 0.15);border-width:1px;border-radius:1rem;border-style:solid;padding:1.5rem;text-align:center">
			<?php if ( ! empty( $stat['emoji'] ) ) : ?>
			<p style="font-size:1.5rem;margin:0 0 0.25rem"><?php echo $stat['emoji']; ?></p>
			<?php endif; ?>
			<p style="font-size:1.75rem;font-weight:700;margin:0 0 0.25rem"><?php echo esc_html( $stat['number'] ); ?></p>
			<p class="has-muted-color has-text-color" style="font-size:0.85rem;margin:0"><?php echo nl2br( esc_html( $stat['label'] ) ); ?></p>
		</div>
		<?php endforeach; ?>
	</div>
	<?php endif; ?>

</div>
