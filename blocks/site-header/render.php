<?php
/**
 * Server-side render for the Site Header block.
 *
 * @param array    $attributes Block attributes.
 * @param string   $content    Block content.
 * @param WP_Block $block      Block instance.
 */

$nav_items_raw = isset( $attributes['navItems'] ) ? $attributes['navItems'] : '[]';
$cta_text      = isset( $attributes['ctaText'] ) ? $attributes['ctaText'] : 'Let\'s Talk';
$cta_url       = isset( $attributes['ctaUrl'] ) ? $attributes['ctaUrl'] : 'mailto:info@jeremyanderson.tech';
$block_title      = isset( $attributes['siteTitle'] ) ? $attributes['siteTitle'] : '';
$block_logo_id    = isset( $attributes['logoId'] ) ? (int) $attributes['logoId'] : 0;
$block_favicon_id = isset( $attributes['faviconId'] ) ? (int) $attributes['faviconId'] : 0;

$nav_items = json_decode( $nav_items_raw, true );
if ( ! is_array( $nav_items ) ) {
	$nav_items = array();
}

// Use block attributes if set, otherwise fall back to WP settings.
$logo_id    = $block_logo_id ? $block_logo_id : (int) get_theme_mod( 'custom_logo' );
$site_title = $block_title ? $block_title : get_bloginfo( 'name' );
$home_url   = home_url( '/' );

// Determine the current URL path for active state.
$current_path = trailingslashit( wp_parse_url( $_SERVER['REQUEST_URI'], PHP_URL_PATH ) );
?>
<div class="site-header is-position-sticky has-transparent-background-color has-background" style="border-bottom-color:rgba(139, 92, 246, 0.15);border-bottom-width:1px;padding-top:1rem;padding-right:1.5rem;padding-bottom:1rem;padding-left:1.5rem;top:0px;position:sticky;z-index:100">
<div class="sh-inner" style="max-width:1152px;margin:0 auto;display:flex;justify-content:space-between;align-items:center">

	<!-- Logo + Title -->
	<a href="<?php echo esc_url( $home_url ); ?>" class="sh-brand" style="display:flex;align-items:center;gap:0.5rem;text-decoration:none;color:inherit">
		<?php if ( $logo_id ) : ?>
		<img src="<?php echo esc_url( wp_get_attachment_image_url( $logo_id, 'thumbnail' ) ); ?>" alt="<?php echo esc_attr( $site_title ); ?>" width="32" height="32" style="border-radius:6px">
		<?php endif; ?>
		<span class="has-jetbrains-mono-font-family" style="font-size:1.1rem;font-weight:700"><?php echo esc_html( $site_title ); ?></span>
	</a>

	<!-- Navigation (desktop) -->
	<nav class="sh-nav main-navigation" style="display:flex;gap:0.25rem;font-size:0.9rem;font-weight:500" aria-label="Main Navigation">
		<?php foreach ( $nav_items as $item ) :
			$item_path = trailingslashit( wp_parse_url( $item['url'], PHP_URL_PATH ) );
			$is_active = ( $current_path === $item_path );
			$classes   = 'sh-nav-link has-muted-color has-text-color';
			if ( $is_active ) {
				$classes .= ' current-menu-item';
			}
		?>
		<a href="<?php echo esc_url( $item['url'] ); ?>" class="<?php echo esc_attr( $classes ); ?>" style="padding:0.5rem 0.75rem;text-decoration:none;border-radius:0.375rem<?php echo $is_active ? ';color:var(--wp--preset--color--foreground);background:rgba(139,92,246,0.1)' : ''; ?>"><?php echo esc_html( $item['label'] ); ?></a>
		<?php endforeach; ?>
	</nav>

	<!-- CTA Button (desktop) -->
	<?php if ( $cta_text && $cta_url ) : ?>
	<div class="wp-block-buttons sh-cta-wrap">
		<div class="wp-block-button header-cta-button" style="font-size:0.875rem">
			<a class="wp-block-button__link has-violet-600-to-cyan-600-gradient-background has-background wp-element-button" href="<?php echo esc_url( $cta_url ); ?>" style="border-radius:0.5rem;padding:0.5rem 1.25rem"><?php echo esc_html( $cta_text ); ?></a>
		</div>
	</div>
	<?php endif; ?>

	<!-- Burger button (mobile) -->
	<button class="sh-burger" type="button" aria-label="Open menu" aria-expanded="false" aria-controls="sh-mobile-overlay">
		<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
	</button>

</div>
</div>

<!-- Mobile overlay (outside .site-header to avoid backdrop-filter containing block) -->
<div id="sh-mobile-overlay" class="sh-mobile-overlay" aria-hidden="true">
	<div class="sh-mobile-overlay__header">
		<a href="<?php echo esc_url( $home_url ); ?>" class="sh-brand" style="display:flex;align-items:center;gap:0.5rem;text-decoration:none;color:inherit">
			<?php if ( $logo_id ) : ?>
			<img src="<?php echo esc_url( wp_get_attachment_image_url( $logo_id, 'thumbnail' ) ); ?>" alt="<?php echo esc_attr( $site_title ); ?>" width="32" height="32" style="border-radius:6px">
			<?php endif; ?>
			<span class="has-jetbrains-mono-font-family" style="font-size:1.1rem;font-weight:700"><?php echo esc_html( $site_title ); ?></span>
		</a>
		<button class="sh-mobile-close" type="button" aria-label="Close menu">
			<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
		</button>
	</div>
	<nav class="sh-mobile-nav" aria-label="Mobile Navigation">
		<?php foreach ( $nav_items as $item ) :
			$item_path = trailingslashit( wp_parse_url( $item['url'], PHP_URL_PATH ) );
			$is_active = ( $current_path === $item_path );
			$classes   = 'sh-mobile-link';
			if ( $is_active ) {
				$classes .= ' current-menu-item';
			}
		?>
		<a href="<?php echo esc_url( $item['url'] ); ?>" class="<?php echo esc_attr( $classes ); ?>"><?php echo esc_html( $item['label'] ); ?></a>
		<?php endforeach; ?>
	</nav>
	<?php if ( $cta_text && $cta_url ) : ?>
	<div class="sh-mobile-cta">
		<a class="wp-block-button__link has-violet-600-to-cyan-600-gradient-background has-background wp-element-button" href="<?php echo esc_url( $cta_url ); ?>" style="border-radius:0.5rem;padding:0.75rem 1.5rem;font-size:1rem;display:inline-block;text-decoration:none"><?php echo esc_html( $cta_text ); ?></a>
	</div>
	<?php endif; ?>
</div>
<?php
// Favicon override: output inline JS to update the document favicon.
$favicon_id = $block_favicon_id ? $block_favicon_id : (int) get_option( 'site_icon' );
if ( $favicon_id ) :
	$favicon_url = wp_get_attachment_image_url( $favicon_id, array( 512, 512 ) );
	if ( $favicon_url ) :
?>
<script>
(function(){var u=<?php echo wp_json_encode( $favicon_url ); ?>;var el=document.querySelector('link[rel="icon"]')||document.querySelector('link[rel="shortcut icon"]');if(el){el.href=u;}else{var l=document.createElement('link');l.rel='icon';l.href=u;document.head.appendChild(l);}})();
</script>
<?php endif; endif; ?>
