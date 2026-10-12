<?php
/**
 * Server-side render for the Hero Section block.
 *
 * @param array    $attributes Block attributes.
 * @param string   $content    Block content.
 * @param WP_Block $block      Block instance.
 */

$badge_text           = isset( $attributes['badgeText'] ) ? $attributes['badgeText'] : 'Available for freelance work';
$name                 = isset( $attributes['name'] ) ? $attributes['name'] : 'Jeremy Anderson';
$role                 = isset( $attributes['role'] ) ? $attributes['role'] : '{ Software Architect }';
$description          = isset( $attributes['description'] ) ? $attributes['description'] : 'I design and build secure, accessible, and compliant web platforms, AI-assisted systems, and custom software solutions. My work focuses on clean architecture, practical automation, and leveraging modern technologies to solve real-world problems.';
$primary_button_text  = isset( $attributes['primaryButtonText'] ) ? $attributes['primaryButtonText'] : 'View My Work';
$primary_button_url   = isset( $attributes['primaryButtonUrl'] ) ? $attributes['primaryButtonUrl'] : '/projects';
$secondary_button_text = isset( $attributes['secondaryButtonText'] ) ? $attributes['secondaryButtonText'] : 'About Me';
$secondary_button_url  = isset( $attributes['secondaryButtonUrl'] ) ? $attributes['secondaryButtonUrl'] : '/about';

$roles_raw = isset( $attributes['roles'] ) ? $attributes['roles'] : 'Full-Stack Developer, WordPress Engineer, AI Systems Builder, Cybersecurity Specialist, Cloud Architect';
$roles_arr = array_map( 'trim', explode( ',', $roles_raw ) );
$roles_arr = array_filter( $roles_arr );
$roles_json = wp_json_encode( array_values( $roles_arr ) );
?>
<div <?php echo get_block_wrapper_attributes( array( 'class' => 'hero-section alignfull' ) ); ?>>
	<div class="hero-background">
		<div class="hero-inner">

			<div class="hero-badge-wrapper">
				<span class="hero-badge-text has-violet-400-color has-text-color"><?php echo wp_kses_post( $badge_text ); ?></span>
			</div>

			<h1 class="hero-title wp-block-heading"><span class="developer-portfolio-gradient-text-light"><?php echo wp_kses_post( $name ); ?></span></h1>

			<span class="hero-role-text has-jetbrains-mono-font-family" data-roles="<?php echo esc_attr( $roles_json ); ?>"><span class="developer-portfolio-gradient-text"><?php echo wp_kses_post( $role ); ?></span></span>

			<p class="hero-description has-muted-color has-text-color"><?php echo wp_kses_post( $description ); ?></p>

			<div class="hero-buttons">
				<?php if ( ! empty( $primary_button_text ) ) : ?>
				<div class="wp-block-button hero-cta-primary">
					<a class="wp-block-button__link has-violet-600-to-cyan-600-gradient-background has-background wp-element-button" href="<?php echo esc_url( $primary_button_url ); ?>"><?php echo wp_kses_post( $primary_button_text ); ?></a>
				</div>
				<?php endif; ?>

				<?php if ( ! empty( $secondary_button_text ) ) : ?>
				<div class="wp-block-button is-style-outline hero-cta-secondary">
					<a class="wp-block-button__link has-foreground-color has-text-color has-background wp-element-button" href="<?php echo esc_url( $secondary_button_url ); ?>"><?php echo wp_kses_post( $secondary_button_text ); ?></a>
				</div>
				<?php endif; ?>
			</div>

		</div>
	</div>
</div>
