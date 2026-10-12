<?php
/**
 * Developer Portfolio Theme Functions
 *
 * @package Developer_Portfolio
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'DEV_PORTFOLIO_VERSION', '1.4.1' );
define( 'DEV_PORTFOLIO_DIR', get_template_directory() );
define( 'DEV_PORTFOLIO_URI', get_template_directory_uri() );

/**
 * Theme setup.
 */
function developer_portfolio_setup() {
	add_theme_support( 'wp-block-styles' );
	add_theme_support( 'editor-styles' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'custom-logo', array(
		'height'      => 36,
		'width'       => 36,
		'flex-height' => true,
		'flex-width'  => true,
	) );
	add_theme_support( 'html5', array(
		'search-form',
		'comment-form',
		'comment-list',
		'gallery',
		'caption',
		'style',
		'script',
		'navigation-widgets',
	) );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'title-tag' );
	add_theme_support( 'align-wide' );

	add_editor_style( 'assets/css/custom.css' );

}
add_action( 'after_setup_theme', 'developer_portfolio_setup' );


/**
 * Get the site owner display name from the first administrator.
 */
function developer_portfolio_get_owner_name() {
	$name = apply_filters( 'developer_portfolio_owner_name', '' );
	if ( ! empty( $name ) ) {
		return $name;
	}
	$admins = get_users( array( 'role' => 'administrator', 'number' => 1, 'orderby' => 'ID', 'order' => 'ASC' ) );
	if ( ! empty( $admins ) ) {
		return $admins[0]->display_name;
	}
	return get_bloginfo( 'name' );
}

/**
 * Get the site owner email from the admin settings.
 */
function developer_portfolio_get_owner_email() {
	$email = apply_filters( 'developer_portfolio_owner_email', '' );
	if ( ! empty( $email ) ) {
		return $email;
	}
	return get_option( 'admin_email' );
}

/**
 * Output JSON-LD structured data for SEO.
 */
function developer_portfolio_structured_data() {
	$site_url    = home_url( '/' );
	$site_name   = get_bloginfo( 'name' );
	$owner_name  = developer_portfolio_get_owner_name();

	// Global: WebSite + Person schema on every page
	$website_schema = array(
		'@context'      => 'https://schema.org',
		'@type'         => 'WebSite',
		'name'          => $site_name,
		'url'           => $site_url,
		'description'   => get_bloginfo( 'description' ),
		'publisher'     => array(
			'@type' => 'Person',
			'name'  => $owner_name,
		),
		'potentialAction' => array(
			'@type'       => 'SearchAction',
			'target'      => $site_url . '?s={search_term_string}',
			'query-input' => 'required name=search_term_string',
		),
	);

	echo '<script type="application/ld+json">' . wp_json_encode( $website_schema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT ) . '</script>' . "\n";

	// Person schema on Home and About pages
	$front_id = (int) get_option( 'page_on_front' );
	$about_page = get_page_by_path( 'about' );
	$about_id = $about_page ? $about_page->ID : 0;

	if ( is_page( $front_id ) || is_page( $about_id ) ) {
		$person_schema = array(
			'@context'    => 'https://schema.org',
			'@type'       => 'Person',
			'name'        => $owner_name,
			'url'         => $site_url,
			'description' => get_bloginfo( 'description' ),
		);

		// Only include email if explicitly provided via filter (privacy: don't expose admin email by default).
		$public_email = apply_filters( 'developer_portfolio_public_email', '' );
		if ( ! empty( $public_email ) ) {
			$person_schema['email'] = sanitize_email( $public_email );
		}

		$job_title = apply_filters( 'developer_portfolio_job_title', '' );
		if ( ! empty( $job_title ) ) {
			$person_schema['jobTitle'] = $job_title;
		}

		$same_as = apply_filters( 'developer_portfolio_social_links', array() );
		if ( ! empty( $same_as ) ) {
			$person_schema['sameAs'] = $same_as;
		}

		$knows_about = apply_filters( 'developer_portfolio_knows_about', array() );
		if ( ! empty( $knows_about ) ) {
			$person_schema['knowsAbout'] = $knows_about;
		}

		echo '<script type="application/ld+json">' . wp_json_encode( $person_schema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT ) . '</script>' . "\n";
	}

	// SoftwareApplication schema on single portfolio project pages
	if ( is_singular( 'project' ) ) {
		$project_id = get_the_ID();
		$github     = get_post_meta( $project_id, 'project_github_url', true );
		$live       = get_post_meta( $project_id, 'project_live_url', true );
		$subtitle   = get_post_meta( $project_id, 'project_subtitle', true );
		$thumb      = get_the_post_thumbnail_url( $project_id, 'full' );

		$project_schema = array(
			'@context'    => 'https://schema.org',
			'@type'       => 'SoftwareApplication',
			'name'        => get_the_title( $project_id ),
			'description' => ! empty( $subtitle ) ? $subtitle : wp_trim_words( strip_tags( get_the_content() ), 30 ),
			'url'         => get_permalink( $project_id ),
			'author'      => array(
				'@type' => 'Person',
				'name'  => $owner_name,
			),
			'applicationCategory' => 'WebApplication',
		);

		if ( $thumb ) {
			$project_schema['image'] = $thumb;
		}
		if ( ! empty( $live ) ) {
			$project_schema['installUrl'] = $live;
		}
		if ( ! empty( $github ) ) {
			$project_schema['codeRepository'] = $github;
		}

		echo '<script type="application/ld+json">' . wp_json_encode( $project_schema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT ) . '</script>' . "\n";
	}

	// Service schema on single service pages
	if ( is_singular( 'service' ) ) {
		$service_id  = get_the_ID();
		$subtitle    = get_post_meta( $service_id, 'service_subtitle', true );
		$price_range = get_post_meta( $service_id, 'service_price_range', true );
		$thumb       = get_the_post_thumbnail_url( $service_id, 'full' );

		$service_schema = array(
			'@context'    => 'https://schema.org',
			'@type'       => 'Service',
			'name'        => get_the_title( $service_id ),
			'description' => ! empty( $subtitle ) ? $subtitle : wp_trim_words( strip_tags( get_the_content() ), 30 ),
			'url'         => get_permalink( $service_id ),
			'provider'    => array(
				'@type' => 'Person',
				'name'  => $owner_name,
				'url'   => $site_url,
			),
		);

		if ( $thumb ) {
			$service_schema['image'] = $thumb;
		}
		if ( ! empty( $price_range ) ) {
			$service_schema['offers'] = array(
				'@type'         => 'Offer',
				'description'   => $price_range,
				'availability'  => 'https://schema.org/InStock',
			);
		}

		$categories = get_the_terms( $service_id, 'service_category' );
		if ( ! empty( $categories ) && ! is_wp_error( $categories ) ) {
			$service_schema['serviceType'] = wp_list_pluck( $categories, 'name' );
		}

		echo '<script type="application/ld+json">' . wp_json_encode( $service_schema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT ) . '</script>' . "\n";
	}

	// Article schema on single blog posts
	if ( is_singular( 'post' ) ) {
		$post_id = get_the_ID();
		$thumb   = get_the_post_thumbnail_url( $post_id, 'full' );

		$article_schema = array(
			'@context'      => 'https://schema.org',
			'@type'         => 'Article',
			'headline'      => get_the_title( $post_id ),
			'description'   => wp_trim_words( strip_tags( get_the_content() ), 30 ),
			'url'           => get_permalink( $post_id ),
			'datePublished' => get_the_date( 'c', $post_id ),
			'dateModified'  => get_the_modified_date( 'c', $post_id ),
			'author'        => array(
				'@type' => 'Person',
				'name'  => $owner_name,
			),
			'publisher'     => array(
				'@type' => 'Person',
				'name'  => $owner_name,
			),
		);

		if ( $thumb ) {
			$article_schema['image'] = $thumb;
		}

		echo '<script type="application/ld+json">' . wp_json_encode( $article_schema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT ) . '</script>' . "\n";
	}

	// BreadcrumbList schema (except on home page)
	if ( ! is_front_page() ) {
		$breadcrumbs = array(
			array(
				'@type'    => 'ListItem',
				'position' => 1,
				'name'     => 'Home',
				'item'     => $site_url,
			),
		);

		if ( is_singular( 'project' ) ) {
			$projects_page = get_page_by_path( 'projects' );
			if ( $projects_page ) {
				$breadcrumbs[] = array(
					'@type'    => 'ListItem',
					'position' => 2,
					'name'     => 'Projects',
					'item'     => get_permalink( $projects_page->ID ),
				);
			}
			$breadcrumbs[] = array(
				'@type'    => 'ListItem',
				'position' => 3,
				'name'     => get_the_title(),
			);
		} elseif ( is_singular( 'service' ) ) {
			$services_page = get_page_by_path( 'services' );
			if ( $services_page ) {
				$breadcrumbs[] = array(
					'@type'    => 'ListItem',
					'position' => 2,
					'name'     => 'Services',
					'item'     => get_permalink( $services_page->ID ),
				);
			}
			$breadcrumbs[] = array(
				'@type'    => 'ListItem',
				'position' => 3,
				'name'     => get_the_title(),
			);
		} elseif ( is_singular( 'post' ) ) {
			$blog_page_id = (int) get_option( 'page_for_posts' );
			if ( $blog_page_id ) {
				$breadcrumbs[] = array(
					'@type'    => 'ListItem',
					'position' => 2,
					'name'     => 'Blog',
					'item'     => get_permalink( $blog_page_id ),
				);
			}
			$breadcrumbs[] = array(
				'@type'    => 'ListItem',
				'position' => 3,
				'name'     => get_the_title(),
			);
		} elseif ( is_page() ) {
			$breadcrumbs[] = array(
				'@type'    => 'ListItem',
				'position' => 2,
				'name'     => get_the_title(),
			);
		}

		$breadcrumb_schema = array(
			'@context'        => 'https://schema.org',
			'@type'           => 'BreadcrumbList',
			'itemListElement' => $breadcrumbs,
		);

		echo '<script type="application/ld+json">' . wp_json_encode( $breadcrumb_schema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT ) . '</script>' . "\n";
	}
}
add_action( 'wp_head', 'developer_portfolio_structured_data', 5 );

/**
 * Output ProfilePage schema on About page (GEO / E-E-A-T signal).
 */
function developer_portfolio_profile_schema() {
	$about_page = get_page_by_path( 'about' );
	$about_id   = $about_page ? $about_page->ID : 0;

	if ( ! is_page( $about_id ) ) {
		return;
	}

	$owner_name = developer_portfolio_get_owner_name();
	$site_url   = home_url( '/' );

	$profile_schema = array(
		'@context'     => 'https://schema.org',
		'@type'        => 'ProfilePage',
		'name'         => get_the_title( $about_id ),
		'url'          => get_permalink( $about_id ),
		'mainEntity'   => array(
			'@type'       => 'Person',
			'name'        => $owner_name,
			'url'         => $site_url,
			'description' => get_bloginfo( 'description' ),
		),
		'dateModified' => get_the_modified_date( 'c', $about_id ),
	);

	$job_title = apply_filters( 'developer_portfolio_job_title', '' );
	if ( ! empty( $job_title ) ) {
		$profile_schema['mainEntity']['jobTitle'] = $job_title;
	}

	$same_as = apply_filters( 'developer_portfolio_social_links', array() );
	if ( ! empty( $same_as ) ) {
		$profile_schema['mainEntity']['sameAs'] = $same_as;
	}

	$knows_about = apply_filters( 'developer_portfolio_knows_about', array() );
	if ( ! empty( $knows_about ) ) {
		$profile_schema['mainEntity']['knowsAbout'] = $knows_about;
	}

	$thumb = get_the_post_thumbnail_url( $about_id, 'full' );
	if ( $thumb ) {
		$profile_schema['mainEntity']['image'] = $thumb;
	}

	echo '<script type="application/ld+json">' . wp_json_encode( $profile_schema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT ) . '</script>' . "\n";
}
add_action( 'wp_head', 'developer_portfolio_profile_schema', 6 );

/**
 * Output Speakable schema on blog posts and services for voice assistants (AEO).
 */
function developer_portfolio_speakable_schema() {
	if ( ! is_singular( array( 'post', 'service' ) ) ) {
		return;
	}

	$speakable_schema = array(
		'@context'  => 'https://schema.org',
		'@type'     => 'WebPage',
		'name'      => get_the_title(),
		'url'       => get_permalink(),
		'speakable' => array(
			'@type'       => 'SpeakableSpecification',
			'cssSelector' => array( '.entry-content', '.wp-block-post-content', 'h1', '.has-muted-color' ),
		),
	);

	echo '<script type="application/ld+json">' . wp_json_encode( $speakable_schema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT ) . '</script>' . "\n";
}
add_action( 'wp_head', 'developer_portfolio_speakable_schema', 7 );

/**
 * Output ItemList schema on projects and services listing pages (GEO).
 */
function developer_portfolio_itemlist_schema() {
	$projects_page = get_page_by_path( 'projects' );
	$services_page = get_page_by_path( 'services' );

	$post_type = '';
	$list_name = '';

	if ( $projects_page && is_page( $projects_page->ID ) ) {
		$post_type = 'project';
		$list_name = 'Projects';
	} elseif ( $services_page && is_page( $services_page->ID ) ) {
		$post_type = 'service';
		$list_name = 'Services';
	} elseif ( is_post_type_archive( 'project' ) ) {
		$post_type = 'project';
		$list_name = 'Projects';
	} elseif ( is_post_type_archive( 'service' ) ) {
		$post_type = 'service';
		$list_name = 'Services';
	}

	if ( empty( $post_type ) ) {
		return;
	}

	$items = get_posts( array(
		'post_type'      => $post_type,
		'posts_per_page' => 50,
		'post_status'    => 'publish',
		'orderby'        => 'date',
		'order'          => 'DESC',
	) );

	if ( empty( $items ) ) {
		return;
	}

	$list_items = array();
	foreach ( $items as $index => $item ) {
		$list_items[] = array(
			'@type'    => 'ListItem',
			'position' => $index + 1,
			'name'     => get_the_title( $item->ID ),
			'url'      => get_permalink( $item->ID ),
		);
	}

	$itemlist_schema = array(
		'@context'        => 'https://schema.org',
		'@type'           => 'ItemList',
		'name'            => $list_name,
		'numberOfItems'   => count( $list_items ),
		'itemListElement' => $list_items,
	);

	echo '<script type="application/ld+json">' . wp_json_encode( $itemlist_schema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT ) . '</script>' . "\n";
}
add_action( 'wp_head', 'developer_portfolio_itemlist_schema', 8 );

/**
 * Register FAQ shortcode for AEO — outputs FAQ schema + accessible HTML.
 * Usage: [faq question="What do you offer?"]Answer here.[/faq]
 */
function developer_portfolio_faq_shortcode( $atts, $content = '' ) {
	static $faq_items = array();

	$atts = shortcode_atts( array( 'question' => '' ), $atts, 'faq' );

	if ( empty( $atts['question'] ) || empty( $content ) ) {
		return '';
	}

	$question = esc_html( $atts['question'] );
	$answer   = wp_kses_post( $content );

	$faq_items[] = array(
		'@type'          => 'Question',
		'name'           => $question,
		'acceptedAnswer' => array(
			'@type' => 'Answer',
			'text'  => wp_strip_all_tags( $answer ),
		),
	);

	// Build accessible HTML
	$id   = 'faq-' . sanitize_title( $question );
	$html  = '<details class="faq-item" id="' . esc_attr( $id ) . '">';
	$html .= '<summary class="faq-question"><strong>' . $question . '</strong></summary>';
	$html .= '<div class="faq-answer">' . $answer . '</div>';
	$html .= '</details>';

	return $html;
}
add_shortcode( 'faq', 'developer_portfolio_faq_shortcode' );

/**
 * Output collected FAQ schema at wp_footer.
 */
function developer_portfolio_faq_schema() {
	global $post;
	if ( ! is_singular() || ! $post ) {
		return;
	}

	if ( ! has_shortcode( $post->post_content, 'faq' ) ) {
		return;
	}

	// Re-parse FAQ items from content.
	$faq_items = array();
	preg_match_all( '/\[faq\s+question="([^"]+)"\](.*?)\[\/faq\]/s', $post->post_content, $matches, PREG_SET_ORDER );

	foreach ( $matches as $match ) {
		$faq_items[] = array(
			'@type'          => 'Question',
			'name'           => $match[1],
			'acceptedAnswer' => array(
				'@type' => 'Answer',
				'text'  => wp_strip_all_tags( $match[2] ),
			),
		);
	}

	if ( empty( $faq_items ) ) {
		return;
	}

	$faq_schema = array(
		'@context'   => 'https://schema.org',
		'@type'      => 'FAQPage',
		'mainEntity' => $faq_items,
	);

	echo '<script type="application/ld+json">' . wp_json_encode( $faq_schema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT ) . '</script>' . "\n";
}
add_action( 'wp_footer', 'developer_portfolio_faq_schema', 99 );


/**
 * Output Open Graph, Twitter Card, canonical URL, and meta description.
 */
function developer_portfolio_meta_tags() {
	$title       = '';
	$description = '';
	$image       = '';
	$url         = '';
	$type        = 'website';

	if ( is_singular() ) {
		$post_id     = get_the_ID();
		$title       = get_the_title( $post_id );
		$url         = get_permalink( $post_id );
		$image       = get_the_post_thumbnail_url( $post_id, 'full' );
		$type        = is_singular( 'post' ) ? 'article' : 'website';

		$excerpt = get_the_excerpt( $post_id );
		if ( ! empty( $excerpt ) ) {
			$description = wp_strip_all_tags( $excerpt );
		} else {
			$description = wp_trim_words( wp_strip_all_tags( get_the_content( null, false, $post_id ) ), 30, '...' );
		}
	} elseif ( is_post_type_archive() ) {
		$post_type_obj = get_queried_object();
		$title         = $post_type_obj->label ?? get_bloginfo( 'name' );
		$url           = get_post_type_archive_link( $post_type_obj->name ?? '' );
		$description   = $post_type_obj->description ?? get_bloginfo( 'description' );
	} elseif ( is_home() || is_front_page() ) {
		$title       = get_bloginfo( 'name' );
		$url         = home_url( '/' );
		$description = get_bloginfo( 'description' );
	} elseif ( is_page() ) {
		$post_id     = get_the_ID();
		$title       = get_the_title( $post_id );
		$url         = get_permalink( $post_id );
		$description = get_the_excerpt( $post_id );
		if ( empty( $description ) ) {
			$description = get_bloginfo( 'description' );
		}
	} else {
		$title       = wp_get_document_title();
		$url         = home_url( $_SERVER['REQUEST_URI'] );
		$description = get_bloginfo( 'description' );
	}

	$title       = esc_attr( wp_strip_all_tags( $title ) );
	$description = esc_attr( wp_strip_all_tags( $description ) );
	$url         = esc_url( $url );
	$site_name   = esc_attr( get_bloginfo( 'name' ) );

	// Meta description
	if ( ! empty( $description ) ) {
		echo '<meta name="description" content="' . $description . '">' . "\n";
	}

	// Canonical URL
	if ( ! empty( $url ) ) {
		echo '<link rel="canonical" href="' . $url . '">' . "\n";
	}

	// Open Graph
	echo '<meta property="og:type" content="' . esc_attr( $type ) . '">' . "\n";
	echo '<meta property="og:title" content="' . $title . '">' . "\n";
	if ( ! empty( $description ) ) {
		echo '<meta property="og:description" content="' . $description . '">' . "\n";
	}
	echo '<meta property="og:url" content="' . $url . '">' . "\n";
	echo '<meta property="og:site_name" content="' . $site_name . '">' . "\n";
	echo '<meta property="og:locale" content="' . esc_attr( get_locale() ) . '">' . "\n";
	if ( ! empty( $image ) ) {
		echo '<meta property="og:image" content="' . esc_url( $image ) . '">' . "\n";
		echo '<meta property="og:image:alt" content="' . $title . '">' . "\n";
	}

	// Twitter Card
	echo '<meta name="twitter:card" content="' . ( ! empty( $image ) ? 'summary_large_image' : 'summary' ) . '">' . "\n";
	echo '<meta name="twitter:title" content="' . $title . '">' . "\n";
	if ( ! empty( $description ) ) {
		echo '<meta name="twitter:description" content="' . $description . '">' . "\n";
	}
	if ( ! empty( $image ) ) {
		echo '<meta name="twitter:image" content="' . esc_url( $image ) . '">' . "\n";
	}

	// Article-specific OG tags
	if ( is_singular( 'post' ) ) {
		$post_id = get_the_ID();
		echo '<meta property="article:published_time" content="' . esc_attr( get_the_date( 'c', $post_id ) ) . '">' . "\n";
		echo '<meta property="article:modified_time" content="' . esc_attr( get_the_modified_date( 'c', $post_id ) ) . '">' . "\n";
		echo '<meta property="article:author" content="' . esc_attr( developer_portfolio_get_owner_name() ) . '">' . "\n";
	}
}
add_action( 'wp_head', 'developer_portfolio_meta_tags', 2 );

/**
 * Add security headers.
 */
function developer_portfolio_security_headers() {
	if ( is_admin() ) {
		return;
	}
	header( 'X-Content-Type-Options: nosniff' );
	header( 'X-Frame-Options: SAMEORIGIN' );
	header( 'Referrer-Policy: strict-origin-when-cross-origin' );
	header( 'Permissions-Policy: camera=(), microphone=(), geolocation=(), payment=()' );
}
add_action( 'send_headers', 'developer_portfolio_security_headers' );

/**
 * Customize robots.txt output for SEO.
 */
function developer_portfolio_robots_txt( $output, $public ) {
	if ( '0' === $public ) {
		return $output;
	}

	$site_url = home_url( '/' );
	$output   = "User-agent: *\n";
	$output  .= "Allow: /\n";
	$output  .= "Disallow: /wp-admin/\n";
	$output  .= "Disallow: /wp-includes/\n";
	$output  .= "Disallow: /wp-content/plugins/\n";
	$output  .= "Disallow: /wp-content/cache/\n";
	$output  .= "Disallow: /wp-json/\n";
	$output  .= "Disallow: /?s=\n";
	$output  .= "Disallow: /search/\n\n";
	$output  .= "# Sitemaps\n";
	$output  .= "Sitemap: {$site_url}sitemap.xml\n";
	$output  .= "Sitemap: {$site_url}wp-sitemap.xml\n";

	return $output;
}
add_filter( 'robots_txt', 'developer_portfolio_robots_txt', 10, 2 );

/**
 * Remove WordPress version meta tag for security/SEO.
 */
remove_action( 'wp_head', 'wp_generator' );

/**
 * Add image dimensions to prevent CLS (Cumulative Layout Shift).
 */
function developer_portfolio_add_image_dimensions( $content ) {
	if ( is_admin() ) {
		return $content;
	}
	// Add loading="lazy" and decoding="async" to images that don't have them
	$content = preg_replace(
		'/<img((?!loading=)[^>]*)>/i',
		'<img$1 loading="lazy">',
		$content
	);
	$content = preg_replace(
		'/<img((?!decoding=)[^>]*)>/i',
		'<img$1 decoding="async">',
		$content
	);
	return $content;
}
add_filter( 'the_content', 'developer_portfolio_add_image_dimensions', 20 );

/**
 * Add skip-to-content link for keyboard navigation (WCAG 2.4.1).
 */
function developer_portfolio_skip_link() {
	echo '<a class="skip-to-content" href="#wp--skip-link--target">Skip to content</a>' . "\n";
}
add_action( 'wp_body_open', 'developer_portfolio_skip_link' );

/**
 * Enqueue theme assets.
 */
function developer_portfolio_enqueue_assets() {
	wp_enqueue_style(
		'developer-portfolio-custom',
		DEV_PORTFOLIO_URI . '/assets/css/custom.css',
		array(),
		DEV_PORTFOLIO_VERSION
	);

	wp_enqueue_script(
		'developer-portfolio-scripts',
		DEV_PORTFOLIO_URI . '/assets/js/theme.js',
		array(),
		DEV_PORTFOLIO_VERSION,
		true
	);
}
add_action( 'wp_enqueue_scripts', 'developer_portfolio_enqueue_assets' );

/**
 * Enqueue editor assets.
 */
function developer_portfolio_editor_assets() {
	$screen = get_current_screen();
	if ( $screen && 'project' === $screen->post_type ) {
		wp_enqueue_script(
			'developer-portfolio-project-meta',
			DEV_PORTFOLIO_URI . '/assets/js/project-meta-sidebar.js',
			array( 'wp-plugins', 'wp-edit-post', 'wp-components', 'wp-data', 'wp-element', 'wp-core-data' ),
			DEV_PORTFOLIO_VERSION,
			true
		);
	}

	if ( $screen && 'post' === $screen->post_type ) {
		wp_enqueue_script(
			'developer-portfolio-post-meta',
			DEV_PORTFOLIO_URI . '/assets/js/post-meta-sidebar.js',
			array( 'wp-plugins', 'wp-edit-post', 'wp-components', 'wp-data', 'wp-element', 'wp-core-data' ),
			DEV_PORTFOLIO_VERSION,
			true
		);
	}

	if ( $screen && 'service' === $screen->post_type ) {
		wp_enqueue_script(
			'developer-portfolio-service-meta',
			DEV_PORTFOLIO_URI . '/assets/js/service-meta-sidebar.js',
			array( 'wp-plugins', 'wp-edit-post', 'wp-components', 'wp-data', 'wp-element', 'wp-core-data' ),
			DEV_PORTFOLIO_VERSION,
			true
		);
	}

	/* Tech stack editing UI is now inside the block's InspectorControls. */
}
add_action( 'enqueue_block_editor_assets', 'developer_portfolio_editor_assets' );

/**
 * Register block pattern categories.
 */
function developer_portfolio_register_pattern_categories() {
	register_block_pattern_category( 'developer-portfolio-hero', array(
		'label'       => __( 'Hero Sections', 'developer-portfolio' ),
		'description' => __( 'Full-width hero sections for page headers.', 'developer-portfolio' ),
	) );

	register_block_pattern_category( 'developer-portfolio-sections', array(
		'label'       => __( 'Portfolio Sections', 'developer-portfolio' ),
		'description' => __( 'Content sections for the portfolio site.', 'developer-portfolio' ),
	) );

	register_block_pattern_category( 'developer-portfolio-cta', array(
		'label'       => __( 'Call to Action', 'developer-portfolio' ),
		'description' => __( 'Call to action blocks.', 'developer-portfolio' ),
	) );
}
add_action( 'init', 'developer_portfolio_register_pattern_categories' );

/**
 * Register the Projects custom post type.
 */
function developer_portfolio_register_post_types() {
	register_post_type( 'project', array(
		'labels'       => array(
			'name'               => __( 'Projects', 'developer-portfolio' ),
			'singular_name'      => __( 'Project', 'developer-portfolio' ),
			'add_new'            => __( 'Add New Project', 'developer-portfolio' ),
			'add_new_item'       => __( 'Add New Project', 'developer-portfolio' ),
			'edit_item'          => __( 'Edit Project', 'developer-portfolio' ),
			'new_item'           => __( 'New Project', 'developer-portfolio' ),
			'view_item'          => __( 'View Project', 'developer-portfolio' ),
			'search_items'       => __( 'Search Projects', 'developer-portfolio' ),
			'not_found'          => __( 'No projects found.', 'developer-portfolio' ),
			'not_found_in_trash' => __( 'No projects found in Trash.', 'developer-portfolio' ),
		),
		'public'       => true,
		'has_archive'  => true,
		'show_in_rest' => true,
		'menu_icon'    => 'dashicons-portfolio',
		'supports'     => array( 'title', 'editor', 'thumbnail', 'excerpt', 'custom-fields' ),
		'rewrite'      => array( 'slug' => 'project' ),
		'template'     => array(
			array( 'core/paragraph', array(
				'placeholder' => 'Project subtitle (e.g. AI-Assisted Training Platform)',
				'style'       => array(
					'typography' => array( 'fontSize' => '1.1rem' ),
					'color'      => array( 'text' => 'var:preset|color|violet-400' ),
				),
				'metadata'    => array(
					'bindings' => array(
						'content' => array(
							'source' => 'core/post-meta',
							'args'   => array( 'key' => 'project_subtitle' ),
						),
					),
				),
			) ),
			array( 'core/separator', array() ),
			array( 'core/paragraph', array(
				'placeholder' => 'Write the project description...',
			) ),
		),
	) );

	register_taxonomy( 'project_tag', 'project', array(
		'labels'       => array(
			'name'          => __( 'Project Tags', 'developer-portfolio' ),
			'singular_name' => __( 'Project Tag', 'developer-portfolio' ),
		),
		'public'       => true,
		'hierarchical' => false,
		'show_in_rest' => true,
		'rewrite'      => array( 'slug' => 'project-tag' ),
	) );

	register_taxonomy( 'project_category', 'project', array(
		'labels'       => array(
			'name'          => __( 'Project Categories', 'developer-portfolio' ),
			'singular_name' => __( 'Project Category', 'developer-portfolio' ),
		),
		'public'       => true,
		'hierarchical' => true,
		'show_in_rest' => true,
		'rewrite'      => array( 'slug' => 'project-category' ),
	) );
	register_post_type( 'service', array(
		'labels'       => array(
			'name'               => __( 'Services', 'developer-portfolio' ),
			'singular_name'      => __( 'Service', 'developer-portfolio' ),
			'add_new'            => __( 'Add New Service', 'developer-portfolio' ),
			'add_new_item'       => __( 'Add New Service', 'developer-portfolio' ),
			'edit_item'          => __( 'Edit Service', 'developer-portfolio' ),
			'new_item'           => __( 'New Service', 'developer-portfolio' ),
			'view_item'          => __( 'View Service', 'developer-portfolio' ),
			'search_items'       => __( 'Search Services', 'developer-portfolio' ),
			'not_found'          => __( 'No services found.', 'developer-portfolio' ),
			'not_found_in_trash' => __( 'No services found in Trash.', 'developer-portfolio' ),
		),
		'public'       => true,
		'has_archive'  => true,
		'show_in_rest' => true,
		'menu_icon'    => 'dashicons-admin-tools',
		'supports'     => array( 'title', 'editor', 'thumbnail', 'excerpt', 'custom-fields' ),
		'rewrite'      => array( 'slug' => 'service' ),
		'template'     => array(
			array( 'core/group', array(
				'style'  => array( 'spacing' => array( 'blockGap' => '0.25rem' ) ),
				'layout' => array( 'type' => 'flex', 'orientation' => 'vertical' ),
			), array(
				array( 'core/paragraph', array(
					'placeholder' => 'Subtitle (e.g. Custom WordPress Development)',
					'style'       => array( 'typography' => array( 'fontSize' => '0.85rem' ) ),
					'metadata'    => array(
						'bindings' => array(
							'content' => array(
								'source' => 'core/post-meta',
								'args'   => array( 'key' => 'service_subtitle' ),
							),
						),
					),
				) ),
				array( 'core/paragraph', array(
					'placeholder' => 'Tier (e.g. STARTER, PROFESSIONAL)',
					'style'       => array(
						'typography' => array( 'fontSize' => '0.75rem', 'textTransform' => 'uppercase', 'letterSpacing' => '0.06em' ),
						'color'      => array( 'text' => '#64748b' ),
					),
					'metadata'    => array(
						'bindings' => array(
							'content' => array(
								'source' => 'core/post-meta',
								'args'   => array( 'key' => 'service_tier' ),
							),
						),
					),
				) ),
			) ),
			array( 'core/group', array(
				'style'  => array( 'spacing' => array( 'blockGap' => '0.15rem', 'margin' => array( 'top' => '1rem' ) ) ),
				'layout' => array( 'type' => 'flex', 'orientation' => 'vertical' ),
			), array(
				array( 'core/heading', array(
					'level'       => 3,
					'placeholder' => 'Price (e.g. $115/hr or $3,500)',
					'metadata'    => array(
						'bindings' => array(
							'content' => array(
								'source' => 'core/post-meta',
								'args'   => array( 'key' => 'service_price' ),
							),
						),
					),
				) ),
				array( 'core/paragraph', array(
					'placeholder' => 'Price note (e.g. per hour, flat fee · 3–4 weeks)',
					'style'       => array( 'color' => array( 'text' => '#64748b' ), 'typography' => array( 'fontSize' => '0.8rem' ) ),
					'metadata'    => array(
						'bindings' => array(
							'content' => array(
								'source' => 'core/post-meta',
								'args'   => array( 'key' => 'service_price_note' ),
							),
						),
					),
				) ),
			) ),
			array( 'core/separator', array() ),
			array( 'core/paragraph', array(
				'placeholder' => 'Service description — visible in the excerpt and package cards...',
			) ),
			array( 'core/buttons', array(
				'style' => array( 'spacing' => array( 'margin' => array( 'top' => '1rem' ) ) ),
			), array(
				array( 'core/button', array(
					'placeholder' => 'CTA Button (e.g. Get a Quote →)',
					'metadata'    => array(
						'bindings' => array(
							'text' => array(
								'source' => 'core/post-meta',
								'args'   => array( 'key' => 'service_cta_text' ),
							),
							'url'  => array(
								'source' => 'core/post-meta',
								'args'   => array( 'key' => 'service_cta_url' ),
							),
						),
					),
				) ),
			) ),
		),
	) );

	register_taxonomy( 'service_category', 'service', array(
		'labels'            => array(
			'name'          => __( 'Service Categories', 'developer-portfolio' ),
			'singular_name' => __( 'Service Category', 'developer-portfolio' ),
		),
		'public'            => true,
		'hierarchical'      => true,
		'show_in_rest'      => true,
		'show_admin_column' => true,
		'rewrite'           => array( 'slug' => 'service-category' ),
	) );
}
add_action( 'init', 'developer_portfolio_register_post_types' );

/**
 * Register custom meta fields for Services.
 */
function developer_portfolio_register_service_meta() {
	$meta_fields = array(
		'service_subtitle'       => 'string',
		'service_price_range'    => 'string',
		'service_cta_url'        => 'string',
		'service_price'          => 'string',
		'service_price_note'     => 'string',
		'service_tier'           => 'string',
		'service_features'       => 'string',
		'service_icon'           => 'string',
		'service_badge_type'     => 'string',
		'service_featured'       => 'string',
		'service_cta_text'       => 'string',
		'service_typical_uses'   => 'string',
		'service_min_hours'      => 'string',
		'service_hours_included' => 'string',
		'service_color_bar'      => 'string',
		'service_sort_order'     => 'string',
	);

	foreach ( $meta_fields as $key => $type ) {
		register_post_meta( 'service', $key, array(
			'show_in_rest'      => true,
			'single'            => true,
			'type'              => $type,
			'default'           => '',
			'sanitize_callback' => function ( $value ) use ( $key ) {
				$value = wp_strip_all_tags( $value );
				if ( strpos( $key, '_url' ) !== false && $value ) {
					$value = esc_url_raw( $value );
				}
				return $value;
			},
			'auth_callback'     => function () {
				return current_user_can( 'edit_posts' );
			},
		) );
	}
}
add_action( 'init', 'developer_portfolio_register_service_meta' );

/**
 * Register custom meta fields for Projects.
 */
function developer_portfolio_register_meta() {
	$meta_fields = array(
		'project_subtitle'  => 'string',
		'project_github_url' => 'string',
		'project_live_url'   => 'string',
	);

	foreach ( $meta_fields as $key => $type ) {
		register_post_meta( 'project', $key, array(
			'show_in_rest'      => true,
			'single'            => true,
			'type'              => $type,
			'default'           => '',
			'sanitize_callback' => function ( $value ) use ( $key ) {
				$value = wp_strip_all_tags( $value );
				if ( strpos( $key, '_url' ) !== false && $value ) {
					$value = esc_url_raw( $value );
				}
				return $value;
			},
			'auth_callback'     => function () {
				return current_user_can( 'edit_posts' );
			},
		) );
	}
}
add_action( 'init', 'developer_portfolio_register_meta' );

/**
 * Register custom meta fields for Blog Posts.
 */
function developer_portfolio_register_post_meta_fields() {
	$meta_fields = array(
		'post_subtitle' => 'string',
	);

	foreach ( $meta_fields as $key => $type ) {
		register_post_meta( 'post', $key, array(
			'show_in_rest'      => true,
			'single'            => true,
			'type'              => $type,
			'default'           => '',
			'sanitize_callback' => 'wp_strip_all_tags',
			'auth_callback'     => function () {
				return current_user_can( 'edit_posts' );
			},
		) );
	}
}
add_action( 'init', 'developer_portfolio_register_post_meta_fields' );

/**
 * Register tech stack meta field for pages.
 */
function developer_portfolio_register_page_meta() {
	register_post_meta( 'page', 'tech_stack_data', array(
		'show_in_rest'      => true,
		'single'            => true,
		'type'              => 'string',
		'default'           => '',
		'sanitize_callback' => function ( $value ) {
			$decoded = json_decode( $value, true );
			if ( ! is_array( $decoded ) ) {
				return '';
			}
			return wp_json_encode( $decoded );
		},
		'auth_callback'     => function () {
			return current_user_can( 'edit_pages' );
		},
	) );
}
add_action( 'init', 'developer_portfolio_register_page_meta' );

/**
 * Register the Tech Stack custom block.
 */
function developer_portfolio_register_blocks() {
	register_block_type( get_template_directory() . '/blocks/tech-stack' );
	register_block_type( get_template_directory() . '/blocks/hero-section' );
	register_block_type( get_template_directory() . '/blocks/contact-cta' );
	register_block_type( get_template_directory() . '/blocks/social-links' );
	register_block_type( get_template_directory() . '/blocks/about-intro' );
	register_block_type( get_template_directory() . '/blocks/about-experience' );
	register_block_type( get_template_directory() . '/blocks/featured-projects' );
	register_block_type( get_template_directory() . '/blocks/latest-posts' );
	register_block_type( get_template_directory() . '/blocks/services-grid' );
	register_block_type( get_template_directory() . '/blocks/site-header' );
	register_block_type( get_template_directory() . '/blocks/blog-listing' );
	register_block_type( get_template_directory() . '/blocks/projects-listing' );
	register_block_type( get_template_directory() . '/blocks/services-listing' );
	register_block_type( get_template_directory() . '/blocks/services-hero' );
	register_block_type( get_template_directory() . '/blocks/services-market-rates' );
	register_block_type( get_template_directory() . '/blocks/services-pricing-table' );
	register_block_type( get_template_directory() . '/blocks/services-packages' );
	register_block_type( get_template_directory() . '/blocks/services-retainers' );
	register_block_type( get_template_directory() . '/blocks/services-addons' );
	register_block_type( get_template_directory() . '/blocks/services-comparison' );
	register_block_type( get_template_directory() . '/blocks/services-faq' );
	register_block_type( get_template_directory() . '/blocks/services-cta' );
}
add_action( 'init', 'developer_portfolio_register_blocks' );

/**
 * Hide buttons with empty bound URLs on the frontend (for template-based buttons).
 */
function developer_portfolio_hide_empty_buttons( $block_content, $block ) {
	if ( 'core/button' !== $block['blockName'] ) {
		return $block_content;
	}

	$bindings = isset( $block['attrs']['metadata']['bindings']['url'] ) ? $block['attrs']['metadata']['bindings']['url'] : null;
	if ( ! $bindings || 'core/post-meta' !== $bindings['source'] ) {
		return $block_content;
	}

	$meta_key = isset( $bindings['args']['key'] ) ? $bindings['args']['key'] : '';
	if ( ! $meta_key ) {
		return $block_content;
	}

	$value = get_post_meta( get_the_ID(), $meta_key, true );
	if ( empty( $value ) ) {
		return '';
	}

	// Ensure links open in new window.
	if ( strpos( $block_content, 'target=' ) === false ) {
		$block_content = str_replace( '<a ', '<a target="_blank" rel="noopener noreferrer" ', $block_content );
	}

	return $block_content;
}
add_filter( 'render_block', 'developer_portfolio_hide_empty_buttons', 10, 2 );

/**
 * Show "Proprietary software" when all project link buttons are empty (for template-based buttons).
 */
function developer_portfolio_proprietary_label( $block_content, $block ) {
	if ( 'core/buttons' !== $block['blockName'] ) {
		return $block_content;
	}

	if ( 'project' !== get_post_type() ) {
		return $block_content;
	}

	$has_bound_buttons = false;
	if ( ! empty( $block['innerBlocks'] ) ) {
		foreach ( $block['innerBlocks'] as $inner ) {
			if ( isset( $inner['attrs']['metadata']['bindings']['url'] ) ) {
				$has_bound_buttons = true;
				break;
			}
		}
	}

	if ( ! $has_bound_buttons ) {
		return $block_content;
	}

	$github = get_post_meta( get_the_ID(), 'project_github_url', true );
	$live   = get_post_meta( get_the_ID(), 'project_live_url', true );

	if ( empty( $github ) && empty( $live ) ) {
		return '<p class="has-muted-color has-text-color" style="font-size:0.8rem;font-style:italic;font-weight:400">(Proprietary software)</p>';
	}

	return $block_content;
}
add_filter( 'render_block', 'developer_portfolio_proprietary_label', 11, 2 );

/**
 * Inject project link buttons into project-card groups on the frontend.
 * This handles cards in page content where bound button blocks can't be used.
 */
function developer_portfolio_inject_card_buttons( $block_content, $block ) {
	if ( 'core/group' !== $block['blockName'] ) {
		return $block_content;
	}

	$class_name = isset( $block['attrs']['className'] ) ? $block['attrs']['className'] : '';
	if ( strpos( $class_name, 'project-card' ) === false ) {
		return $block_content;
	}

	if ( 'project' !== get_post_type() ) {
		return $block_content;
	}

	// Check if buttons already exist in this card (from template-based rendering).
	if ( strpos( $block_content, 'wp-block-buttons' ) !== false ) {
		return $block_content;
	}

	$github = get_post_meta( get_the_ID(), 'project_github_url', true );
	$live   = get_post_meta( get_the_ID(), 'project_live_url', true );

	if ( empty( $github ) && empty( $live ) ) {
		$buttons_html = '<p class="has-muted-color has-text-color" style="font-size:0.8rem;font-style:italic;font-weight:400">(Proprietary software)</p>';
	} else {
		$links = '';
		if ( ! empty( $github ) ) {
			$links .= '<a target="_blank" rel="noopener noreferrer" href="' . esc_url( $github ) . '" class="project-text-link">GitHub<span class="sr-only"> (opens in new tab)</span> →</a>';
		}
		if ( ! empty( $live ) ) {
			$links .= '<a target="_blank" rel="noopener noreferrer" href="' . esc_url( $live ) . '" class="project-text-link">Live Site<span class="sr-only"> (opens in new tab)</span> →</a>';
		}
		$buttons_html = '<nav class="project-links" aria-label="Project links">' . $links . '</nav>';
	}

	// Inject before the closing </div> of the card.
	$block_content = preg_replace( '/<\/div>\s*$/', $buttons_html . '</div>', $block_content, 1 );

	return $block_content;
}
add_filter( 'render_block', 'developer_portfolio_inject_card_buttons', 12, 2 );

/**
 * Inject project text links into the single-project-links group on single project pages.
 */
function developer_portfolio_single_project_links( $block_content, $block ) {
	if ( 'core/group' !== $block['blockName'] ) {
		return $block_content;
	}

	$class_name = isset( $block['attrs']['className'] ) ? $block['attrs']['className'] : '';
	if ( strpos( $class_name, 'single-project-links' ) === false ) {
		return $block_content;
	}

	if ( 'project' !== get_post_type() ) {
		return $block_content;
	}

	$github = get_post_meta( get_the_ID(), 'project_github_url', true );
	$live   = get_post_meta( get_the_ID(), 'project_live_url', true );

	if ( empty( $github ) && empty( $live ) ) {
		return '<p class="has-muted-color has-text-color" style="font-size:0.875rem;font-style:italic">(Proprietary software)</p>';
	}

	$links = '';
	if ( ! empty( $live ) ) {
		$links .= '<a target="_blank" rel="noopener noreferrer" href="' . esc_url( $live ) . '" class="project-text-link">Live Site<span class="sr-only"> (opens in new tab)</span> →</a>';
	}
	if ( ! empty( $github ) ) {
		$links .= '<a target="_blank" rel="noopener noreferrer" href="' . esc_url( $github ) . '" class="project-text-link">GitHub<span class="sr-only"> (opens in new tab)</span> →</a>';
	}

	return '<nav class="project-links" aria-label="Project links">' . $links . '</nav>';
}
add_filter( 'render_block', 'developer_portfolio_single_project_links', 12, 2 );

/**
 * Render tech stack cards from page meta on the frontend.
 * Replaces a placeholder group with className 'tech-stack-cards' with dynamically generated cards.
 */
function developer_portfolio_render_tech_stack( $block_content, $block ) {
	if ( 'core/group' !== $block['blockName'] ) {
		return $block_content;
	}

	$class_name = isset( $block['attrs']['className'] ) ? $block['attrs']['className'] : '';
	if ( strpos( $class_name, 'tech-stack-cards' ) === false ) {
		return $block_content;
	}

	$data = get_post_meta( get_the_ID(), 'tech_stack_data', true );
	if ( empty( $data ) ) {
		return $block_content;
	}

	$categories = json_decode( $data, true );
	if ( ! is_array( $categories ) || empty( $categories ) ) {
		return $block_content;
	}

	// Split into rows: first 3 in wide row, rest in centered narrow row.
	$top_row    = array_slice( $categories, 0, 3 );
	$bottom_row = array_slice( $categories, 3 );

	$html = '';

	if ( ! empty( $top_row ) ) {
		$html .= '<div class="tech-stack-grid-top">';
		foreach ( $top_row as $cat ) {
			$html .= developer_portfolio_tech_card_html( $cat );
		}
		$html .= '</div>';
	}

	if ( ! empty( $bottom_row ) ) {
		$html .= '<div class="tech-stack-grid-bottom">';
		foreach ( $bottom_row as $cat ) {
			$html .= developer_portfolio_tech_card_html( $cat );
		}
		$html .= '</div>';
	}

	return $html;
}
add_filter( 'render_block', 'developer_portfolio_render_tech_stack', 10, 2 );

/**
 * Generate HTML for a single tech stack card.
 */
function developer_portfolio_tech_card_html( $cat ) {
	$name   = esc_html( $cat['name'] );
	$color  = esc_attr( $cat['color'] );
	$badges = isset( $cat['badges'] ) ? $cat['badges'] : array();

	$color_map = array(
		'violet'  => array( 'dot' => '#8b5cf6', 'text' => '#a78bfa' ),
		'cyan'    => array( 'dot' => '#06b6d4', 'text' => '#22d3ee' ),
		'emerald' => array( 'dot' => '#10b981', 'text' => '#34d399' ),
		'rose'    => array( 'dot' => '#f43f5e', 'text' => '#fb7185' ),
		'amber'   => array( 'dot' => '#f59e0b', 'text' => '#fbbf24' ),
	);

	$c = isset( $color_map[ $color ] ) ? $color_map[ $color ] : $color_map['violet'];

	$badge_html = '';
	foreach ( $badges as $badge ) {
		$badge_html .= '<span class="tech-badge ' . $color . '">' . esc_html( $badge ) . '</span> ';
	}

	$card  = '<div role="group" aria-label="' . $name . ' technologies"><div class="tech-card has-card-background-color has-background" style="border:1px solid rgba(139, 92, 246, 0.15);border-radius:1rem;padding:1.5rem">';
	$card .= '<div style="display:flex;align-items:center;gap:0.75rem;margin-bottom:0.75rem">';
	$card .= '<div style="width:0.75rem;height:0.75rem;border-radius:9999px;background-color:' . esc_attr( $c['dot'] ) . ';flex-shrink:0" aria-hidden="true"></div>';
	$card .= '<p style="color:' . esc_attr( $c['text'] ) . ';font-size:1rem;font-weight:600;margin:0;line-height:1.4">' . $name . '</p>';
	$card .= '</div>';
	$card .= '<p class="tech-badges tech-badges-' . $color . '" style="font-size:0.8rem;margin:0">' . trim( $badge_html ) . '</p>';
	$card .= '</div></div>';


	return $card;
}

/**
 * Initial page content: Home.
 */
function developer_portfolio_home_content() {
	return '<!-- wp:developer-portfolio/hero-section {"align":"full"} /-->
<!-- wp:developer-portfolio/featured-projects {"align":"full"} /-->
<!-- wp:developer-portfolio/tech-stack {"align":"full"} /-->
<!-- wp:developer-portfolio/services-grid {"align":"full"} /-->
<!-- wp:developer-portfolio/contact-cta {"align":"full"} /-->
<!-- wp:developer-portfolio/latest-posts {"align":"full"} /-->';
}

/**
 * Initial page content: Projects.
 */
function developer_portfolio_projects_content() {
	return '<!-- wp:developer-portfolio/projects-listing {"align":"full","sectionLabel":"// all projects","heading":"Selected Software Projects & Systems","headingHighlight":"Projects & Systems","description":"A collection of platforms, tools, and systems I\'ve designed and built — spanning web development, AI systems, security infrastructure, and operational software.","category":"main-projects","isHero":true} /-->
<!-- wp:developer-portfolio/projects-listing {"align":"full","sectionLabel":"// proprietary","heading":"Proprietary / Systems Architecture","headingHighlight":"Proprietary","description":"","category":"proprietary-systems-architecture","isHero":false} /-->
<!-- wp:developer-portfolio/contact-cta {"align":"full"} /-->';
}

/**
 * Initial page content: Services.
 */
function developer_portfolio_services_content() {
	return '<!-- wp:developer-portfolio/services-hero {"align":"full"} /-->

<!-- wp:developer-portfolio/services-market-rates {"align":"full"} /-->

<!-- wp:developer-portfolio/services-pricing-table {"align":"full"} /-->

<!-- wp:developer-portfolio/services-packages {"align":"full","sectionTag":"WordPress Development","sectionTagType":"wp","heading":"WordPress Project Packages","intro":"Fixed-fee packages for defined deliverables. Scope is documented and agreed before work begins. Includes one round of revisions unless noted otherwise.","category":"wordpress-packages"} /-->

<!-- wp:developer-portfolio/services-packages {"align":"full","sectionTag":"AI Consulting","sectionTagType":"ai","heading":"AI Consulting Packages","intro":"From a 90-minute discovery session to full AI system architecture — every engagement is scoped to your business needs and delivered with clear documentation.","category":"ai-packages"} /-->

<!-- wp:developer-portfolio/services-packages {"align":"full","sectionTag":"Cybersecurity","sectionTagType":"sec","heading":"Cybersecurity Packages","intro":"WordPress-focused security services ranging from one-time audits to full infrastructure monitoring — all tailored to small business and startup threat profiles.","category":"security-packages"} /-->

<!-- wp:developer-portfolio/services-retainers {"align":"full"} /-->

<!-- wp:developer-portfolio/services-addons {"align":"full"} /-->

<!-- wp:developer-portfolio/services-comparison {"align":"full"} /-->

<!-- wp:developer-portfolio/services-faq {"align":"full"} /-->

<!-- wp:developer-portfolio/services-cta {"align":"full"} /-->';
}

/**
 * Initial page content: Blog.
 */
function developer_portfolio_blog_content() {
	return '<!-- wp:developer-portfolio/blog-listing {"align":"full"} /-->
<!-- wp:developer-portfolio/contact-cta {"align":"full"} /-->';
}

/**
 * Initial page content: About.
 */
function developer_portfolio_about_content() {
	return '<!-- wp:developer-portfolio/about-intro {"align":"wide"} /-->
<!-- wp:developer-portfolio/about-experience {"align":"wide"} /-->
<!-- wp:developer-portfolio/tech-stack {"align":"full"} /-->
<!-- wp:developer-portfolio/contact-cta {"align":"full"} /-->';
}

/**
 * Create default pages and set up the site on theme activation.
 */
function developer_portfolio_activation_setup() {
	$pages = array(
		'home'     => array(
			'title'    => 'Home',
			'slug'     => 'home',
			'template' => '',
			'content'  => developer_portfolio_home_content(),
		),
		'projects' => array(
			'title'    => 'Projects',
			'slug'     => 'projects',
			'template' => 'page-projects',
			'content'  => developer_portfolio_projects_content(),
		),
		'services' => array(
			'title'    => 'Services',
			'slug'     => 'services',
			'template' => 'page-services',
			'content'  => developer_portfolio_services_content(),
		),
		'blog'     => array(
			'title'    => 'Blog',
			'slug'     => 'blog',
			'template' => 'page-blog',
			'content'  => developer_portfolio_blog_content(),
		),
		'about'    => array(
			'title'    => 'About',
			'slug'     => 'about',
			'template' => 'page-about',
			'content'  => developer_portfolio_about_content(),
		),
		'accessibility' => array(
			'title'    => 'Accessibility',
			'slug'     => 'accessibility',
			'template' => '',
			'content'  => '<!-- wp:paragraph {"textColor":"muted","style":{"typography":{"fontSize":"1.05rem","lineHeight":"1.8"}}} -->
<p class="has-muted-color has-text-color" style="font-size:1.05rem;line-height:1.8">We are committed to ensuring digital accessibility for people with disabilities. We continually improve the user experience for everyone and apply the relevant accessibility standards.</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":2,"style":{"typography":{"fontStyle":"normal","fontWeight":"600","fontSize":"1.5rem"}}} -->
<h2 class="wp-block-heading" style="font-size:1.5rem;font-style:normal;font-weight:600">Conformance Status</h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"textColor":"muted","style":{"typography":{"fontSize":"1.05rem","lineHeight":"1.8"}}} -->
<p class="has-muted-color has-text-color" style="font-size:1.05rem;line-height:1.8">This website strives to conform to the Web Content Accessibility Guidelines (WCAG) 2.1, Level AA. These guidelines explain how to make web content more accessible to people with a wide array of disabilities.</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":2,"style":{"typography":{"fontStyle":"normal","fontWeight":"600","fontSize":"1.5rem"}}} -->
<h2 class="wp-block-heading" style="font-size:1.5rem;font-style:normal;font-weight:600">Feedback</h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"textColor":"muted","style":{"typography":{"fontSize":"1.05rem","lineHeight":"1.8"}}} -->
<p class="has-muted-color has-text-color" style="font-size:1.05rem;line-height:1.8">We welcome your feedback on the accessibility of this site. Please let us know if you encounter accessibility barriers.</p>
<!-- /wp:paragraph -->',
		),
		'privacy-policy' => array(
			'title'    => 'Privacy Policy',
			'slug'     => 'privacy-policy',
			'template' => '',
			'content'  => '<!-- wp:paragraph {"textColor":"muted","style":{"typography":{"fontSize":"1.05rem","lineHeight":"1.8"}}} -->
<p class="has-muted-color has-text-color" style="font-size:1.05rem;line-height:1.8">Your privacy is important to us. This Privacy Policy explains how we collect, use, disclose, and safeguard your information when you visit this website.</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":2,"style":{"typography":{"fontStyle":"normal","fontWeight":"600","fontSize":"1.5rem"}}} -->
<h2 class="wp-block-heading" style="font-size:1.5rem;font-style:normal;font-weight:600">Information We Collect</h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"textColor":"muted","style":{"typography":{"fontSize":"1.05rem","lineHeight":"1.8"}}} -->
<p class="has-muted-color has-text-color" style="font-size:1.05rem;line-height:1.8">We may collect information about you in a variety of ways. The information we may collect via the website includes data you voluntarily provide when contacting us.</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":2,"style":{"typography":{"fontStyle":"normal","fontWeight":"600","fontSize":"1.5rem"}}} -->
<h2 class="wp-block-heading" style="font-size:1.5rem;font-style:normal;font-weight:600">Contact Us</h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"textColor":"muted","style":{"typography":{"fontSize":"1.05rem","lineHeight":"1.8"}}} -->
<p class="has-muted-color has-text-color" style="font-size:1.05rem;line-height:1.8">If you have questions or comments about this Privacy Policy, please contact us.</p>
<!-- /wp:paragraph -->',
		),
		'terms-of-service' => array(
			'title'    => 'Terms of Service',
			'slug'     => 'terms-of-service',
			'template' => '',
			'content'  => '<!-- wp:paragraph {"textColor":"muted","style":{"typography":{"fontSize":"1.05rem","lineHeight":"1.8"}}} -->
<p class="has-muted-color has-text-color" style="font-size:1.05rem;line-height:1.8">Please read these Terms of Service carefully before using this website. By accessing or using the site, you agree to be bound by these terms.</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":2,"style":{"typography":{"fontStyle":"normal","fontWeight":"600","fontSize":"1.5rem"}}} -->
<h2 class="wp-block-heading" style="font-size:1.5rem;font-style:normal;font-weight:600">Use of the Site</h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"textColor":"muted","style":{"typography":{"fontSize":"1.05rem","lineHeight":"1.8"}}} -->
<p class="has-muted-color has-text-color" style="font-size:1.05rem;line-height:1.8">You may use this website for lawful purposes only. You agree not to use the site in any way that violates any applicable laws or regulations.</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":2,"style":{"typography":{"fontStyle":"normal","fontWeight":"600","fontSize":"1.5rem"}}} -->
<h2 class="wp-block-heading" style="font-size:1.5rem;font-style:normal;font-weight:600">Intellectual Property</h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"textColor":"muted","style":{"typography":{"fontSize":"1.05rem","lineHeight":"1.8"}}} -->
<p class="has-muted-color has-text-color" style="font-size:1.05rem;line-height:1.8">The content, features, and functionality of this website are owned by the site owner and are protected by international copyright, trademark, and other intellectual property laws.</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":2,"style":{"typography":{"fontStyle":"normal","fontWeight":"600","fontSize":"1.5rem"}}} -->
<h2 class="wp-block-heading" style="font-size:1.5rem;font-style:normal;font-weight:600">Contact Us</h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"textColor":"muted","style":{"typography":{"fontSize":"1.05rem","lineHeight":"1.8"}}} -->
<p class="has-muted-color has-text-color" style="font-size:1.05rem;line-height:1.8">If you have any questions about these Terms of Service, please contact us.</p>
<!-- /wp:paragraph -->',
		),
	);

	$created_pages = array();

	foreach ( $pages as $key => $page_data ) {
		$existing = get_page_by_path( $page_data['slug'] );

		if ( $existing ) {
			$created_pages[ $key ] = $existing->ID;
			continue;
		}

		$page_id = wp_insert_post( array(
			'post_title'   => $page_data['title'],
			'post_name'    => $page_data['slug'],
			'post_status'  => 'publish',
			'post_type'    => 'page',
			'post_content' => $page_data['content'],
			'meta_input'   => array(
				'_wp_page_template' => $page_data['template'] ? $page_data['template'] : 'default',
			),
		) );

		if ( ! is_wp_error( $page_id ) ) {
			$created_pages[ $key ] = $page_id;
		}
	}

	if ( isset( $created_pages['home'] ) ) {
		update_option( 'show_on_front', 'page' );
		update_option( 'page_on_front', $created_pages['home'] );
	}

	if ( isset( $created_pages['blog'] ) ) {
		update_option( 'page_for_posts', $created_pages['blog'] );
	}

	developer_portfolio_delete_sample_page();
	developer_portfolio_create_block_navigation();

	flush_rewrite_rules();
}
add_action( 'after_switch_theme', 'developer_portfolio_activation_setup' );


/**
 * Delete the default WordPress "Sample Page".
 */
function developer_portfolio_delete_sample_page() {
	$sample = get_page_by_path( 'sample-page' );
	if ( $sample ) {
		wp_delete_post( $sample->ID, true );
	}
}

/**
 * Create a wp_navigation post for the FSE navigation block.
 */
function developer_portfolio_create_block_navigation() {
	$existing = get_posts( array(
		'post_type'   => 'wp_navigation',
		'post_status' => 'publish',
		'name'        => 'primary-navigation',
		'numberposts' => 1,
	) );

	if ( ! empty( $existing ) ) {
		return;
	}

	$pages_map = array(
		'Home'     => 'home',
		'Projects' => 'projects',
		'Services' => 'services',
		'Blog'     => 'blog',
		'About'    => 'about',
	);

	$nav_items = '';
	foreach ( $pages_map as $label => $slug ) {
		$page = get_page_by_path( $slug );
		if ( $page ) {
			$url = get_permalink( $page->ID );
			$nav_items .= '<!-- wp:navigation-link {"label":"' . $label . '","type":"page","id":' . $page->ID . ',"url":"' . $url . '","kind":"post-type","isTopLevelLink":true} /-->' . "\n";
		}
	}

	wp_insert_post( array(
		'post_title'   => 'Primary Navigation',
		'post_name'    => 'primary-navigation',
		'post_content' => $nav_items,
		'post_status'  => 'publish',
		'post_type'    => 'wp_navigation',
	) );
}




/**
 * Replace {year} placeholder with current year in rendered block content.
 */
function developer_portfolio_dynamic_year( $block_content ) {
	if ( strpos( $block_content, '{year}' ) !== false ) {
		$block_content = str_replace( '{year}', date( 'Y' ), $block_content );
	}
	return $block_content;
}
add_filter( 'render_block', 'developer_portfolio_dynamic_year' );

/**
 * Add custom excerpt length.
 */
function developer_portfolio_excerpt_length( $length ) {
	return 25;
}
add_filter( 'excerpt_length', 'developer_portfolio_excerpt_length' );

/**
 * Remove "Category:" prefix from archive titles.
 */
function developer_portfolio_archive_title( $title ) {
	if ( is_category() ) {
		$title = single_cat_title( '', false );
	} elseif ( is_tag() ) {
		$title = single_tag_title( '', false );
	} elseif ( is_author() ) {
		$title = get_the_author();
	} elseif ( is_post_type_archive() ) {
		$title = post_type_archive_title( '', false );
	}
	return $title;
}
add_filter( 'get_the_archive_title', 'developer_portfolio_archive_title' );

/**
 * One-time migration: delete Sample Page and create block navigation.
 * Runs on init and self-removes via an option flag.
 */
function developer_portfolio_run_migration() {
	if ( get_option( 'developer_portfolio_migration_104' ) ) {
		return;
	}

	developer_portfolio_delete_sample_page();
	developer_portfolio_create_block_navigation();

	update_option( 'developer_portfolio_migration_104', true );
}
add_action( 'init', 'developer_portfolio_run_migration' );

/**
 * One-time migration: backfill page content into existing empty pages.
 */
function developer_portfolio_backfill_page_content() {
	if ( get_option( 'developer_portfolio_migration_105' ) ) {
		return;
	}

	$content_map = array(
		'home'             => developer_portfolio_home_content(),
		'projects'         => developer_portfolio_projects_content(),
		'services'         => developer_portfolio_services_content(),
		'blog'             => developer_portfolio_blog_content(),
		'about'            => developer_portfolio_about_content(),
		'accessibility'    => '',
		'privacy-policy'   => '',
		'terms-of-service' => '',
	);

	foreach ( $content_map as $slug => $content ) {
		$page = get_page_by_path( $slug );
		if ( ! $page ) {
			continue;
		}
		$existing_content = trim( $page->post_content );
		if ( empty( $existing_content ) && ! empty( $content ) ) {
			wp_update_post( array(
				'ID'           => $page->ID,
				'post_content' => $content,
			) );
		}
	}

	update_option( 'developer_portfolio_migration_105', true );
}
add_action( 'init', 'developer_portfolio_backfill_page_content' );

/**
 * One-time migration: create any missing pages added after initial activation.
 */
function developer_portfolio_create_missing_pages() {
	if ( get_option( 'developer_portfolio_migration_107' ) ) {
		return;
	}

	$pages = array(
		'accessibility'    => array(
			'title'   => 'Accessibility',
			'content' => '<!-- wp:paragraph {"textColor":"muted","style":{"typography":{"fontSize":"1.05rem","lineHeight":"1.8"}}} -->
<p class="has-muted-color has-text-color" style="font-size:1.05rem;line-height:1.8">We are committed to ensuring digital accessibility for people with disabilities. We continually improve the user experience for everyone and apply the relevant accessibility standards.</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":2,"style":{"typography":{"fontStyle":"normal","fontWeight":"600","fontSize":"1.5rem"}}} -->
<h2 class="wp-block-heading" style="font-size:1.5rem;font-style:normal;font-weight:600">Conformance Status</h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"textColor":"muted","style":{"typography":{"fontSize":"1.05rem","lineHeight":"1.8"}}} -->
<p class="has-muted-color has-text-color" style="font-size:1.05rem;line-height:1.8">This website strives to conform to the Web Content Accessibility Guidelines (WCAG) 2.1, Level AA. These guidelines explain how to make web content more accessible to people with a wide array of disabilities.</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":2,"style":{"typography":{"fontStyle":"normal","fontWeight":"600","fontSize":"1.5rem"}}} -->
<h2 class="wp-block-heading" style="font-size:1.5rem;font-style:normal;font-weight:600">Feedback</h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"textColor":"muted","style":{"typography":{"fontSize":"1.05rem","lineHeight":"1.8"}}} -->
<p class="has-muted-color has-text-color" style="font-size:1.05rem;line-height:1.8">We welcome your feedback on the accessibility of this site. Please let us know if you encounter accessibility barriers.</p>
<!-- /wp:paragraph -->',
		),
		'privacy-policy'   => array(
			'title'   => 'Privacy Policy',
			'content' => '<!-- wp:paragraph {"textColor":"muted","style":{"typography":{"fontSize":"1.05rem","lineHeight":"1.8"}}} -->
<p class="has-muted-color has-text-color" style="font-size:1.05rem;line-height:1.8">Your privacy is important to us. This Privacy Policy explains how we collect, use, disclose, and safeguard your information when you visit this website.</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":2,"style":{"typography":{"fontStyle":"normal","fontWeight":"600","fontSize":"1.5rem"}}} -->
<h2 class="wp-block-heading" style="font-size:1.5rem;font-style:normal;font-weight:600">Information We Collect</h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"textColor":"muted","style":{"typography":{"fontSize":"1.05rem","lineHeight":"1.8"}}} -->
<p class="has-muted-color has-text-color" style="font-size:1.05rem;line-height:1.8">We may collect information about you in a variety of ways. The information we may collect via the website includes data you voluntarily provide when contacting us.</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":2,"style":{"typography":{"fontStyle":"normal","fontWeight":"600","fontSize":"1.5rem"}}} -->
<h2 class="wp-block-heading" style="font-size:1.5rem;font-style:normal;font-weight:600">Contact Us</h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"textColor":"muted","style":{"typography":{"fontSize":"1.05rem","lineHeight":"1.8"}}} -->
<p class="has-muted-color has-text-color" style="font-size:1.05rem;line-height:1.8">If you have questions or comments about this Privacy Policy, please contact us.</p>
<!-- /wp:paragraph -->',
		),
		'terms-of-service' => array(
			'title'   => 'Terms of Service',
			'content' => '<!-- wp:paragraph {"textColor":"muted","style":{"typography":{"fontSize":"1.05rem","lineHeight":"1.8"}}} -->
<p class="has-muted-color has-text-color" style="font-size:1.05rem;line-height:1.8">Please read these Terms of Service carefully before using this website. By accessing or using the site, you agree to be bound by these terms.</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":2,"style":{"typography":{"fontStyle":"normal","fontWeight":"600","fontSize":"1.5rem"}}} -->
<h2 class="wp-block-heading" style="font-size:1.5rem;font-style:normal;font-weight:600">Use of the Site</h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"textColor":"muted","style":{"typography":{"fontSize":"1.05rem","lineHeight":"1.8"}}} -->
<p class="has-muted-color has-text-color" style="font-size:1.05rem;line-height:1.8">You may use this website for lawful purposes only. You agree not to use the site in any way that violates any applicable laws or regulations.</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":2,"style":{"typography":{"fontStyle":"normal","fontWeight":"600","fontSize":"1.5rem"}}} -->
<h2 class="wp-block-heading" style="font-size:1.5rem;font-style:normal;font-weight:600">Intellectual Property</h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"textColor":"muted","style":{"typography":{"fontSize":"1.05rem","lineHeight":"1.8"}}} -->
<p class="has-muted-color has-text-color" style="font-size:1.05rem;line-height:1.8">The content, features, and functionality of this website are owned by the site owner and are protected by international copyright, trademark, and other intellectual property laws.</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":2,"style":{"typography":{"fontStyle":"normal","fontWeight":"600","fontSize":"1.5rem"}}} -->
<h2 class="wp-block-heading" style="font-size:1.5rem;font-style:normal;font-weight:600">Contact Us</h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"textColor":"muted","style":{"typography":{"fontSize":"1.05rem","lineHeight":"1.8"}}} -->
<p class="has-muted-color has-text-color" style="font-size:1.05rem;line-height:1.8">If you have any questions about these Terms of Service, please contact us.</p>
<!-- /wp:paragraph -->',
		),
	);

	foreach ( $pages as $slug => $page_data ) {
		$existing = get_page_by_path( $slug, OBJECT, 'page' );
		if ( ! $existing ) {
			// Also check for draft/private pages WP may have auto-created.
			$drafts = get_posts( array(
				'name'        => $slug,
				'post_type'   => 'page',
				'post_status' => array( 'draft', 'private', 'pending' ),
				'numberposts' => 1,
			) );
			$existing = ! empty( $drafts ) ? $drafts[0] : null;
		}

		if ( $existing ) {
			// Publish if draft and backfill content if empty.
			$updates = array( 'ID' => $existing->ID );
			if ( $existing->post_status !== 'publish' ) {
				$updates['post_status'] = 'publish';
			}
			if ( empty( trim( $existing->post_content ) ) ) {
				$updates['post_content'] = $page_data['content'];
			}
			if ( count( $updates ) > 1 ) {
				wp_update_post( $updates );
			}
		} else {
			wp_insert_post( array(
				'post_title'   => $page_data['title'],
				'post_name'    => $slug,
				'post_status'  => 'publish',
				'post_type'    => 'page',
				'post_content' => $page_data['content'],
			) );
		}
	}

	update_option( 'developer_portfolio_migration_107', true );
}
add_action( 'init', 'developer_portfolio_create_missing_pages' );

/**
 * One-time migration: replace WP default Privacy Policy content with custom content.
 */
function developer_portfolio_fix_privacy_policy() {
	if ( get_option( 'developer_portfolio_migration_108' ) ) {
		return;
	}

	$page = get_page_by_path( 'privacy-policy' );
	if ( ! $page ) {
		$drafts = get_posts( array(
			'name'        => 'privacy-policy',
			'post_type'   => 'page',
			'post_status' => 'any',
			'numberposts' => 1,
		) );
		$page = ! empty( $drafts ) ? $drafts[0] : null;
	}

	if ( $page ) {
		$custom_content = '<!-- wp:paragraph {"textColor":"muted","style":{"typography":{"fontSize":"1.05rem","lineHeight":"1.8"}}} -->
<p class="has-muted-color has-text-color" style="font-size:1.05rem;line-height:1.8">Your privacy is important to us. This Privacy Policy explains how we collect, use, disclose, and safeguard your information when you visit this website.</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":2,"style":{"typography":{"fontStyle":"normal","fontWeight":"600","fontSize":"1.5rem"}}} -->
<h2 class="wp-block-heading" style="font-size:1.5rem;font-style:normal;font-weight:600">Information We Collect</h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"textColor":"muted","style":{"typography":{"fontSize":"1.05rem","lineHeight":"1.8"}}} -->
<p class="has-muted-color has-text-color" style="font-size:1.05rem;line-height:1.8">We may collect information about you in a variety of ways. The information we may collect via the website includes data you voluntarily provide when contacting us.</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":2,"style":{"typography":{"fontStyle":"normal","fontWeight":"600","fontSize":"1.5rem"}}} -->
<h2 class="wp-block-heading" style="font-size:1.5rem;font-style:normal;font-weight:600">Contact Us</h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"textColor":"muted","style":{"typography":{"fontSize":"1.05rem","lineHeight":"1.8"}}} -->
<p class="has-muted-color has-text-color" style="font-size:1.05rem;line-height:1.8">If you have questions or comments about this Privacy Policy, please contact us.</p>
<!-- /wp:paragraph -->';

		wp_update_post( array(
			'ID'           => $page->ID,
			'post_status'  => 'publish',
			'post_content' => $custom_content,
		) );
	}

	update_option( 'developer_portfolio_migration_108', true );
}
add_action( 'init', 'developer_portfolio_fix_privacy_policy' );

/**
 * One-time migration: populate tech_stack_data meta for Home & About pages,
 * and add the tech-stack section to the About page content.
 */
function developer_portfolio_populate_tech_stack() {
	if ( get_option( 'developer_portfolio_migration_109' ) ) {
		return;
	}

	$default_tech_stack = wp_json_encode( array(
		array(
			'name'   => 'Frontend',
			'color'  => 'violet',
			'badges' => array( 'React', 'Next.js', 'TypeScript', 'Tailwind CSS', 'Vue.js', 'Svelte' ),
		),
		array(
			'name'   => 'Backend',
			'color'  => 'cyan',
			'badges' => array( 'Node.js', 'Python', 'Go', 'GraphQL', 'REST APIs', 'PostgreSQL' ),
		),
		array(
			'name'   => 'DevOps & Cloud',
			'color'  => 'emerald',
			'badges' => array( 'AWS', 'Docker', 'Kubernetes', 'CI/CD', 'Terraform', 'Vercel' ),
		),
		array(
			'name'   => 'Tools & Design',
			'color'  => 'amber',
			'badges' => array( 'Figma', 'Git', 'VS Code', 'Storybook', 'Jest', 'Playwright' ),
		),
	) );

	// Populate Home page meta.
	$home = get_page_by_path( 'home' );
	if ( $home ) {
		update_post_meta( $home->ID, 'tech_stack_data', $default_tech_stack );
	}

	// Populate About page meta and add tech-stack section to content.
	$about = get_page_by_path( 'about' );
	if ( $about ) {
		update_post_meta( $about->ID, 'tech_stack_data', $default_tech_stack );

		$tech_stack_section = '<!-- wp:group {"align":"full","className":"tech-stack-section","style":{"spacing":{"padding":{"top":"6rem","bottom":"6rem","left":"1.5rem","right":"1.5rem"}},"color":{"background":"rgba(26, 26, 46, 0.3)"}},"layout":{"type":"constrained","contentSize":"1152px"}} -->
<div class="wp-block-group alignfull tech-stack-section has-background" style="background-color:rgba(26, 26, 46, 0.3);padding-top:6rem;padding-right:1.5rem;padding-bottom:6rem;padding-left:1.5rem">

<!-- wp:group {"style":{"spacing":{"margin":{"bottom":"4rem"},"blockGap":"0.5rem"}},"layout":{"type":"flex","orientation":"vertical","justifyContent":"center"}} -->
<div class="wp-block-group" style="margin-bottom:4rem">
<!-- wp:paragraph {"align":"center","style":{"typography":{"fontSize":"0.875rem"}},"textColor":"primary","fontFamily":"jetbrains-mono"} -->
<p class="has-text-align-center has-primary-color has-text-color has-jetbrains-mono-font-family" style="font-size:0.875rem">// tech stack</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"textAlign":"center","style":{"typography":{"fontStyle":"normal","fontWeight":"700","fontSize":"clamp(1.75rem, 4vw, 2.5rem)"}}} -->
<h2 class="wp-block-heading has-text-align-center" style="font-size:clamp(1.75rem, 4vw, 2.5rem);font-style:normal;font-weight:700">Technologies I <span class="developer-portfolio-gradient-text">Work With</span></h2>
<!-- /wp:heading -->
</div>
<!-- /wp:group -->

<!-- wp:group {"className":"tech-stack-cards","layout":{"type":"default"}} -->
<div class="wp-block-group tech-stack-cards"></div>
<!-- /wp:group -->

</div>
<!-- /wp:group -->';

		$content = $about->post_content;
		// Insert before the contact CTA pattern if present.
		$cta_pattern = '<!-- wp:pattern {"slug":"developer-portfolio/contact-cta"} /-->';
		if ( strpos( $content, $cta_pattern ) !== false ) {
			$content = str_replace( $cta_pattern, $tech_stack_section . "\n\n" . $cta_pattern, $content );
		} else {
			$content .= "\n\n" . $tech_stack_section;
		}

		wp_update_post( array(
			'ID'           => $about->ID,
			'post_content' => $content,
		) );
	}

	update_option( 'developer_portfolio_migration_109', true );
}
add_action( 'init', 'developer_portfolio_populate_tech_stack' );

/**
 * One-time migration: replace static tech-stack pattern / placeholder with the custom block.
 */
function developer_portfolio_use_tech_stack_block() {
	if ( get_option( 'developer_portfolio_migration_110' ) ) {
		return;
	}

	$custom_block = '<!-- wp:developer-portfolio/tech-stack {"align":"full"} /-->';

	// Update Home page.
	$home = get_page_by_path( 'home' );
	if ( $home ) {
		$content = $home->post_content;
		// Replace the static pattern reference.
		$content = str_replace(
			'<!-- wp:pattern {"slug":"developer-portfolio/tech-stack"} /-->',
			$custom_block,
			$content
		);
		wp_update_post( array(
			'ID'           => $home->ID,
			'post_content' => $content,
		) );
	}

	// Update About page.
	$about = get_page_by_path( 'about' );
	if ( $about ) {
		$content = $about->post_content;

		// Replace the static pattern reference if present.
		$content = str_replace(
			'<!-- wp:pattern {"slug":"developer-portfolio/tech-stack"} /-->',
			$custom_block,
			$content
		);

		// Remove the tech-stack-cards placeholder group added by migration 109.
		$placeholder_start = strpos( $content, '<!-- wp:group {"align":"full","className":"tech-stack-section"' );
		if ( $placeholder_start !== false ) {
			$placeholder_end = strpos( $content, '<!-- /wp:group -->', $placeholder_start );
			if ( $placeholder_end !== false ) {
				// Find the closing tag end.
				$placeholder_end += strlen( '<!-- /wp:group -->' );
				$before = substr( $content, 0, $placeholder_start );
				$after  = substr( $content, $placeholder_end );
				$content = trim( $before ) . "\n" . $custom_block . "\n" . trim( $after );
			}
		}

		wp_update_post( array(
			'ID'           => $about->ID,
			'post_content' => $content,
		) );
	}

	update_option( 'developer_portfolio_migration_110', true );
}
add_action( 'init', 'developer_portfolio_use_tech_stack_block' );

/**
 * One-time migration: replace hero-section and contact-cta patterns with custom blocks.
 */
function developer_portfolio_use_hero_cta_blocks() {
	if ( get_option( 'developer_portfolio_migration_111' ) ) {
		return;
	}

	$hero_block = '<!-- wp:developer-portfolio/hero-section {"align":"full"} /-->';
	$cta_block  = '<!-- wp:developer-portfolio/contact-cta {"align":"full"} /-->';

	$pages_to_update = array( 'home', 'about' );

	foreach ( $pages_to_update as $slug ) {
		$page = get_page_by_path( $slug );
		if ( ! $page ) {
			continue;
		}

		$content = $page->post_content;

		// Replace hero-section pattern with custom block.
		$content = str_replace(
			'<!-- wp:pattern {"slug":"developer-portfolio/hero-section"} /-->',
			$hero_block,
			$content
		);

		// Replace contact-cta pattern with custom block.
		$content = str_replace(
			'<!-- wp:pattern {"slug":"developer-portfolio/contact-cta"} /-->',
			$cta_block,
			$content
		);

		wp_update_post( array(
			'ID'           => $page->ID,
			'post_content' => $content,
		) );
	}

	update_option( 'developer_portfolio_migration_111', true );
}
add_action( 'init', 'developer_portfolio_use_hero_cta_blocks' );

/**
 * One-time migration: clean up leftover markup from previous migrations on About page.
 */
function developer_portfolio_cleanup_about_content() {
	if ( get_option( 'developer_portfolio_migration_112' ) ) {
		return;
	}

	$about = get_page_by_path( 'about' );
	if ( $about ) {
		$content = $about->post_content;

		// Remove leftover tech-stack-cards empty group.
		$content = preg_replace(
			'/<!-- wp:group \{[^}]*"tech-stack-cards"[^}]*\} -->\s*<div[^>]*><\/div>\s*<!-- \/wp:group -->/s',
			'',
			$content
		);

		// Remove stray closing </div> and <!-- /wp:post-content --> tags.
		$content = str_replace( "\n</div>\n<!-- /wp:post-content -->\n", "\n", $content );
		$content = str_replace( "</div>\n<!-- /wp:post-content -->", '', $content );

		// Clean up extra blank lines.
		$content = preg_replace( '/\n{3,}/', "\n", $content );
		$content = trim( $content );

		wp_update_post( array(
			'ID'           => $about->ID,
			'post_content' => $content,
		) );
	}

	update_option( 'developer_portfolio_migration_112', true );
}
add_action( 'init', 'developer_portfolio_cleanup_about_content' );

/**
 * One-time migration: second pass cleanup for About page leftover markup.
 */
function developer_portfolio_cleanup_about_v2() {
	if ( get_option( 'developer_portfolio_migration_113' ) ) {
		return;
	}

	$about = get_page_by_path( 'about' );
	if ( $about ) {
		$content = $about->post_content;

		// Remove the exact leftover tech-stack-cards group block.
		$leftover = "<!-- wp:group {\"className\":\"tech-stack-cards\",\"layout\":{\"type\":\"default\"}} -->\n<div class=\"wp-block-group tech-stack-cards\"></div>\n<!-- /wp:group -->";
		$content = str_replace( $leftover, '', $content );

		// Remove stray </div> and <!-- /wp:post-content --> tags.
		$content = str_replace( "\n</div>\n<!-- /wp:post-content -->\n", "\n", $content );
		$content = str_replace( "</div>\n<!-- /wp:post-content -->", '', $content );

		// Clean up multiple blank lines.
		$content = preg_replace( '/\n{3,}/', "\n", $content );
		$content = trim( $content );

		wp_update_post( array(
			'ID'           => $about->ID,
			'post_content' => $content,
		) );
	}

	update_option( 'developer_portfolio_migration_113', true );
}
add_action( 'init', 'developer_portfolio_cleanup_about_v2' );

/**
 * One-time migration: set About page to exact clean content.
 */
function developer_portfolio_fix_about_content() {
	if ( get_option( 'developer_portfolio_migration_114' ) ) {
		return;
	}

	$about = get_page_by_path( 'about' );
	if ( $about ) {
		$clean = developer_portfolio_about_content();
		wp_update_post( array(
			'ID'           => $about->ID,
			'post_content' => $clean,
		) );
	}

	update_option( 'developer_portfolio_migration_114', true );
}
add_action( 'init', 'developer_portfolio_fix_about_content' );

/**
 * One-time migration: replace about-bio + about-stats patterns with about-intro block.
 */
function developer_portfolio_migrate_about_intro_block() {
	if ( get_option( 'developer_portfolio_migration_115' ) ) {
		return;
	}

	$about = get_page_by_path( 'about' );
	if ( $about ) {
		$clean = developer_portfolio_about_content();
		wp_update_post( array(
			'ID'           => $about->ID,
			'post_content' => $clean,
		) );
	}

	update_option( 'developer_portfolio_migration_115', true );
}
add_action( 'init', 'developer_portfolio_migrate_about_intro_block' );

/**
 * One-time migration: replace about-experience + about-education patterns with about-experience block.
 */
function developer_portfolio_migrate_about_experience_block() {
	if ( get_option( 'developer_portfolio_migration_116' ) ) {
		return;
	}

	$about = get_page_by_path( 'about' );
	if ( $about ) {
		$clean = developer_portfolio_about_content();
		wp_update_post( array(
			'ID'           => $about->ID,
			'post_content' => $clean,
		) );
	}

	update_option( 'developer_portfolio_migration_116', true );
}
add_action( 'init', 'developer_portfolio_migrate_about_experience_block' );

/**
 * One-time migration: replace featured-projects + latest-posts patterns with custom blocks on Home page.
 */
function developer_portfolio_migrate_home_blocks() {
	if ( get_option( 'developer_portfolio_migration_117' ) ) {
		return;
	}

	$home = get_page_by_path( 'home' );
	if ( $home ) {
		$clean = developer_portfolio_home_content();
		wp_update_post( array(
			'ID'           => $home->ID,
			'post_content' => $clean,
		) );
	}

	update_option( 'developer_portfolio_migration_117', true );
}
add_action( 'init', 'developer_portfolio_migrate_home_blocks' );

/**
 * One-time migration: reorder home page blocks (services above CTA, blog below CTA).
 */
function developer_portfolio_migrate_home_reorder() {
	if ( get_option( 'developer_portfolio_migration_118' ) ) {
		return;
	}

	$home = get_page_by_path( 'home' );
	if ( $home ) {
		$clean = developer_portfolio_home_content();
		wp_update_post( array(
			'ID'           => $home->ID,
			'post_content' => $clean,
		) );
	}

	update_option( 'developer_portfolio_migration_118', true );
}
add_action( 'init', 'developer_portfolio_migrate_home_reorder' );

/**
 * One-time migration: replace blog, projects, and services pages with editable blocks.
 */
function developer_portfolio_migrate_listing_blocks() {
	if ( get_option( 'developer_portfolio_migration_119' ) ) {
		return;
	}

	$pages = array(
		'blog'     => 'developer_portfolio_blog_content',
		'projects' => 'developer_portfolio_projects_content',
		'services' => 'developer_portfolio_services_content',
	);

	foreach ( $pages as $slug => $fn ) {
		$page = get_page_by_path( $slug );
		if ( $page && function_exists( $fn ) ) {
			wp_update_post( array(
				'ID'           => $page->ID,
				'post_content' => call_user_func( $fn ),
			) );
		}
	}

	update_option( 'developer_portfolio_migration_119', true );
}
add_action( 'init', 'developer_portfolio_migrate_listing_blocks' );

/**
 * One-time migration: create service categories and populate all service posts for the pricing page.
 */
function developer_portfolio_migrate_services_data() {
	if ( get_option( 'developer_portfolio_migration_120' ) ) {
		return;
	}

	// --- Create service categories ---
	$categories = array(
		'hourly-rates'        => 'Hourly Rates',
		'wordpress-packages'  => 'WordPress Packages',
		'ai-packages'         => 'AI Packages',
		'security-packages'   => 'Security Packages',
		'monthly-retainers'   => 'Monthly Retainers',
		'add-ons'             => 'Add-Ons',
	);

	foreach ( $categories as $slug => $name ) {
		if ( ! term_exists( $slug, 'service_category' ) ) {
			wp_insert_term( $name, 'service_category', array( 'slug' => $slug ) );
		}
	}

	// Helper to create a service post.
	$create_service = function ( $title, $category_slug, $meta, $excerpt = '' ) {
		$existing = get_posts( array(
			'post_type'   => 'service',
			'title'       => $title,
			'post_status' => 'any',
			'numberposts' => 1,
		) );
		if ( ! empty( $existing ) ) {
			return $existing[0]->ID;
		}

		$post_id = wp_insert_post( array(
			'post_type'    => 'service',
			'post_title'   => $title,
			'post_status'  => 'publish',
			'post_excerpt' => $excerpt,
			'post_content' => '',
		) );

		if ( is_wp_error( $post_id ) ) {
			return 0;
		}

		$term = get_term_by( 'slug', $category_slug, 'service_category' );
		if ( $term ) {
			wp_set_object_terms( $post_id, array( $term->term_id ), 'service_category' );
		}

		foreach ( $meta as $key => $value ) {
			update_post_meta( $post_id, $key, $value );
		}

		return $post_id;
	};

	// --- Hourly Rates (5 posts) ---
	$create_service( 'WordPress Development', 'hourly-rates', array(
		'service_price'        => '$115/hr',
		'service_price_note'   => 'Standard dev work',
		'service_badge_type'   => 'wp',
		'service_typical_uses' => 'Theme customization, plugin dev, bug fixes, WooCommerce, performance tuning, FSE work',
		'service_min_hours'    => '2 hrs',
		'service_sort_order'   => '1',
		'service_subtitle'     => 'WordPress',
	) );

	$create_service( 'AI Systems & Consulting', 'hourly-rates', array(
		'service_price'        => '$185/hr',
		'service_price_note'   => 'AI strategy + build',
		'service_badge_type'   => 'ai',
		'service_typical_uses' => 'RAG systems, local LLM setup, AI workflow design, prompt engineering, AI tool integration',
		'service_min_hours'    => '2 hrs',
		'service_sort_order'   => '2',
		'service_subtitle'     => 'AI',
	) );

	$create_service( 'Cybersecurity Consulting', 'hourly-rates', array(
		'service_price'        => '$135/hr',
		'service_price_note'   => 'Hardening + audits',
		'service_badge_type'   => 'sec',
		'service_typical_uses' => 'Security audits, WordPress hardening, log monitoring setup, SIEM configuration, compliance review',
		'service_min_hours'    => '2 hrs',
		'service_sort_order'   => '3',
		'service_subtitle'     => 'Security',
	) );

	$create_service( 'Full-Stack / Custom Dev', 'hourly-rates', array(
		'service_price'        => '$125/hr',
		'service_price_note'   => 'Full-stack builds',
		'service_badge_type'   => 'wp',
		'service_typical_uses' => 'React apps, Node.js backends, REST APIs, custom web applications, PWAs',
		'service_min_hours'    => '2 hrs',
		'service_sort_order'   => '4',
		'service_subtitle'     => 'React · Node.js · PHP',
	) );

	$create_service( 'Strategy & Technical Advisory', 'hourly-rates', array(
		'service_price'        => '$165/hr',
		'service_price_note'   => 'Calls + roadmapping',
		'service_badge_type'   => 'ai',
		'service_typical_uses' => 'Architecture planning, tech stack decisions, vendor evaluation, project scoping, AI roadmaps',
		'service_min_hours'    => '1 hr',
		'service_sort_order'   => '5',
		'service_subtitle'     => 'Consulting',
	) );

	// --- WordPress Packages (3 posts) ---
	$create_service( 'Business Site', 'wordpress-packages', array(
		'service_price'      => '$3,500',
		'service_price_note' => 'flat fee · 3–4 week delivery',
		'service_tier'       => 'Starter',
		'service_badge_type' => 'wp',
		'service_featured'   => '',
		'service_cta_text'   => 'Get a Quote →',
		'service_cta_url'    => 'mailto:info@jeremyanderson.tech',
		'service_sort_order' => '1',
		'service_features'   => wp_json_encode( array(
			'Up to 8 pages (Home, About, Services, Contact + 4 more)',
			'Custom child theme or FSE theme configuration',
			'Mobile responsive + Core Web Vitals optimized',
			'On-page SEO setup (Yoast/RankMath, meta, schema)',
			'Contact form + Google Maps integration',
			'Basic security hardening + SSL',
			'30-day post-launch support',
		) ),
	), 'A clean, fast, mobile-friendly WordPress site for small local businesses that need a professional web presence without the agency price tag.' );

	$create_service( 'Custom Platform', 'wordpress-packages', array(
		'service_price'      => '$7,500',
		'service_price_note' => 'flat fee · 5–7 week delivery',
		'service_tier'       => 'Most Requested',
		'service_badge_type' => 'wp',
		'service_featured'   => '1',
		'service_cta_text'   => 'Get a Quote →',
		'service_cta_url'    => 'mailto:info@jeremyanderson.tech',
		'service_sort_order' => '2',
		'service_features'   => wp_json_encode( array(
			'Unlimited pages, custom post types & taxonomies',
			'Custom plugin development (up to 2 features)',
			'Full Site Editing theme with custom block patterns',
			'Advanced SEO + local schema markup',
			'Performance optimization (caching, CDN, image pipeline)',
			'Security hardening + login protection',
			'REST API integration (up to 1 third-party service)',
			'CMS training session for your team',
			'60-day post-launch support',
		) ),
	), 'A fully custom WordPress build with tailored functionality, custom plugin development, and a polished design system. The right fit for growing businesses and funded startups.' );

	$create_service( 'Complex System', 'wordpress-packages', array(
		'service_price'      => '$15,000+',
		'service_price_note' => 'scoped + quoted · 8–12 weeks',
		'service_tier'       => 'Enterprise',
		'service_badge_type' => 'wp',
		'service_featured'   => '',
		'service_cta_text'   => 'Schedule a Call →',
		'service_cta_url'    => 'mailto:info@jeremyanderson.tech',
		'service_sort_order' => '3',
		'service_features'   => wp_json_encode( array(
			'Architecture planning & technical specification',
			'Custom plugin suite (unlimited scope)',
			'Multi-site / network configuration',
			'WooCommerce with custom checkout & integrations',
			'SIEM/SOC dashboard integration for monitoring',
			'Advanced security framework + compliance review',
			'Load testing + scalability planning',
			'Developer documentation + handoff',
			'90-day post-launch support',
		) ),
	), 'Large-scale WordPress platforms, multi-site networks, complex plugin ecosystems, WooCommerce stores, and sites requiring advanced security or compliance requirements.' );

	// --- AI Packages (3 posts) ---
	$create_service( 'AI Discovery', 'ai-packages', array(
		'service_price'      => '$1,200',
		'service_price_note' => 'flat fee · 1-week engagement',
		'service_tier'       => 'Jumpstart',
		'service_badge_type' => 'ai',
		'service_featured'   => '',
		'service_cta_text'   => 'Book Discovery →',
		'service_cta_url'    => 'mailto:info@jeremyanderson.tech',
		'service_sort_order' => '1',
		'service_features'   => wp_json_encode( array(
			'2-hour discovery workshop (remote)',
			'Workflow audit — identify top 3 AI opportunities',
			'Feasibility assessment for each opportunity',
			'Build-vs-buy recommendation',
			'Detailed written roadmap + cost estimates',
			'30-min follow-up Q&A call',
		) ),
	), 'A focused advisory engagement to identify where AI can realistically help your business, what it would cost to build, and what to prioritize first.' );

	$create_service( 'Custom AI Tool', 'ai-packages', array(
		'service_price'      => '$8,500',
		'service_price_note' => 'starting price · scoped per project',
		'service_tier'       => 'Build',
		'service_badge_type' => 'ai',
		'service_featured'   => '1',
		'service_cta_text'   => 'Get a Quote →',
		'service_cta_url'    => 'mailto:info@jeremyanderson.tech',
		'service_sort_order' => '2',
		'service_features'   => wp_json_encode( array(
			'Full scope definition & architecture document',
			'Local or cloud LLM selection + setup',
			'Vector database / RAG pipeline (if applicable)',
			'Custom web interface or API integration',
			'Prompt engineering + testing',
			'Integration with existing systems (WordPress, etc.)',
			'Documentation + staff training session',
			'60-day post-launch support',
		) ),
	), 'Design and build a focused, production-ready AI tool that solves one real operational problem. RAG systems, conversational interfaces, log analyzers, document assistants, and more.' );

	$create_service( 'AI System Integration', 'ai-packages', array(
		'service_price'      => '$20,000+',
		'service_price_note' => 'scoped + quoted · 8–14 weeks',
		'service_tier'       => 'Scale',
		'service_badge_type' => 'ai',
		'service_featured'   => '',
		'service_cta_text'   => 'Schedule a Call →',
		'service_cta_url'    => 'mailto:info@jeremyanderson.tech',
		'service_sort_order' => '3',
		'service_features'   => wp_json_encode( array(
			'Multi-tool AI architecture design',
			'Knowledge base + RAG system at scale',
			'Automation workflow integration (n8n, custom)',
			'AI-assisted monitoring & alerting systems',
			'Data privacy review + local LLM deployment options',
			'Team training + change management support',
			'Infrastructure documentation',
			'90-day support + iteration sprints',
		) ),
	), 'End-to-end AI infrastructure for organizations ready to deploy AI across multiple workflows — including knowledge management, automation pipelines, and monitoring systems.' );

	// --- Security Packages (3 posts) ---
	$create_service( 'Security Audit', 'security-packages', array(
		'service_price'      => '$850',
		'service_price_note' => 'flat fee · delivered in 5 business days',
		'service_tier'       => 'Essential',
		'service_badge_type' => 'sec',
		'service_featured'   => '',
		'service_cta_text'   => 'Order Audit →',
		'service_cta_url'    => 'mailto:info@jeremyanderson.tech',
		'service_sort_order' => '1',
		'service_features'   => wp_json_encode( array(
			'Plugin & theme vulnerability scan',
			'User roles & permissions review',
			'Login security & 2FA assessment',
			'SSL/TLS configuration check',
			'File permission & server config review',
			'Written report with risk ratings + fix priority',
			'30-min debrief call',
		) ),
	), 'A thorough review of your WordPress site\'s security posture with a prioritized remediation report. Know exactly what\'s at risk and what to fix first.' );

	$create_service( 'Hardening Package', 'security-packages', array(
		'service_price'      => '$2,400',
		'service_price_note' => 'flat fee · 1–2 week delivery',
		'service_tier'       => 'Complete',
		'service_badge_type' => 'sec',
		'service_featured'   => '1',
		'service_cta_text'   => 'Get a Quote →',
		'service_cta_url'    => 'mailto:info@jeremyanderson.tech',
		'service_sort_order' => '2',
		'service_features'   => wp_json_encode( array(
			'Full security audit (as above)',
			'WordPress core, plugin & theme hardening',
			'Login protection + brute-force blocking',
			'Two-factor authentication setup',
			'Security headers + WAF configuration',
			'Log monitoring setup (fail2ban, audit logs)',
			'Malware scan + cleanup (if needed)',
			'MDPA / compliance checklist review',
			'Written documentation of all changes',
		) ),
	), 'Audit plus full implementation — we don\'t just tell you what to fix, we fix it. Leaves your site in a measurably more secure state with documentation of every change made.' );

	$create_service( 'SIEM / SOC Setup', 'security-packages', array(
		'service_price'      => '$4,500+',
		'service_price_note' => 'scoped + quoted · 2–4 weeks',
		'service_tier'       => 'Infrastructure',
		'service_badge_type' => 'sec',
		'service_featured'   => '',
		'service_cta_text'   => 'Schedule a Call →',
		'service_cta_url'    => 'mailto:info@jeremyanderson.tech',
		'service_sort_order' => '3',
		'service_features'   => wp_json_encode( array(
			'Architecture design for multi-site monitoring',
			'Centralized logging pipeline setup',
			'Alert rules for common attack patterns',
			'AI-assisted log analysis integration',
			'Uptime + availability monitoring',
			'Incident response runbook',
			'Dashboard for non-technical staff',
			'60-day support + tuning',
		) ),
	), 'Design and deploy a centralized security monitoring system for organizations managing multiple WordPress sites or web applications across shared or distributed infrastructure.' );

	// --- Monthly Retainers (4 posts) ---
	$create_service( 'Watchdog', 'monthly-retainers', array(
		'service_price'          => '$350',
		'service_price_note'     => 'per month',
		'service_tier'           => 'Tier 1',
		'service_badge_type'     => 'maint',
		'service_hours_included' => '~3 hrs/mo included · extra at $115/hr',
		'service_color_bar'      => '#334155',
		'service_sort_order'     => '1',
		'service_features'       => wp_json_encode( array(
			'WordPress core, plugin & theme updates',
			'Weekly uptime monitoring',
			'Automated offsite backups',
			'Monthly security scan',
			'48-hr response time for issues',
			'Monthly status email',
		) ),
	) );

	$create_service( 'Guardian', 'monthly-retainers', array(
		'service_price'          => '$850',
		'service_price_note'     => 'per month',
		'service_tier'           => 'Tier 2',
		'service_badge_type'     => 'maint',
		'service_hours_included' => '~7 hrs/mo included · extra at $110/hr',
		'service_color_bar'      => '#3b82f6',
		'service_sort_order'     => '2',
		'service_features'       => wp_json_encode( array(
			'Everything in Watchdog',
			'Priority 24-hr response time',
			'Performance monitoring + CWV tracking',
			'Monthly 30-min strategy call',
			'Minor content & design edits',
			'Quarterly security report',
			'Plugin conflict resolution',
		) ),
	) );

	$create_service( 'Partner', 'monthly-retainers', array(
		'service_price'          => '$1,800',
		'service_price_note'     => 'per month',
		'service_tier'           => 'Tier 3',
		'service_badge_type'     => 'maint',
		'service_hours_included' => '~15 hrs/mo included · extra at $105/hr',
		'service_color_bar'      => '#34d399',
		'service_sort_order'     => '3',
		'service_features'       => wp_json_encode( array(
			'Everything in Guardian',
			'Priority 12-hr response time',
			'Active dev hours (features, fixes, improvements)',
			'Monthly 1-hr strategy + roadmap call',
			'SEO monitoring + technical SEO fixes',
			'MDPA / compliance monitoring',
			'AI tool maintenance (if applicable)',
			'Quarterly full security audit',
		) ),
	) );

	$create_service( 'Embedded Dev', 'monthly-retainers', array(
		'service_price'          => '$4,200',
		'service_price_note'     => 'per month',
		'service_tier'           => 'Tier 4',
		'service_badge_type'     => 'maint',
		'service_hours_included' => '~40 hrs/mo · effectively part-time',
		'service_color_bar'      => '#f59e0b',
		'service_sort_order'     => '4',
		'service_features'       => wp_json_encode( array(
			'Everything in Partner',
			'Priority same-day response',
			'Dedicated dev capacity for new features',
			'Slack/Teams direct access during business hours',
			'Weekly sync call',
			'Covers WordPress + AI + Security work',
			'Annual architecture review',
			'Best fit for agencies & growing startups',
		) ),
	) );

	// --- Add-Ons (9 posts) ---
	$addons = array(
		array(
			'title'   => 'Performance Optimization',
			'icon'    => '⚡',
			'price'   => '$650',
			'sort'    => '1',
			'excerpt' => 'Core Web Vitals audit + implementation. Target: 90+ PageSpeed on desktop and mobile. Covers caching, image optimization, CDN setup, and asset delivery.',
		),
		array(
			'title'   => 'Local SEO Setup',
			'icon'    => '🗺️',
			'price'   => '$550',
			'sort'    => '2',
			'excerpt' => 'Google Business Profile optimization, local schema markup, NAP consistency audit, and on-page local keyword implementation for Boston/Worcester area businesses.',
		),
		array(
			'title'   => 'Schema & AEO Markup',
			'icon'    => '🤖',
			'price'   => '$475',
			'sort'    => '3',
			'excerpt' => 'Full structured data implementation: Person, Organization, FAQ, HowTo, Article, and BreadcrumbList schema — optimized for Google rich results and AI engine citations.',
		),
		array(
			'title'   => 'Site Migration',
			'icon'    => '🔄',
			'price'   => '$750',
			'sort'    => '4',
			'excerpt' => 'Full WordPress migration between hosts with zero downtime, DNS management, SSL transfer, and post-migration testing. Includes 30-day monitoring period.',
		),
		array(
			'title'   => 'Custom Gutenberg Block',
			'icon'    => '🧩',
			'price'   => '$950',
			'sort'    => '5',
			'excerpt' => 'A single fully custom Gutenberg block with editor controls, frontend rendering, and full FSE compatibility. Additional blocks at $650/each.',
		),
		array(
			'title'   => 'MDPA Compliance Review',
			'icon'    => '📋',
			'price'   => '$675',
			'sort'    => '6',
			'excerpt' => 'WordPress-specific Massachusetts Data Privacy Act compliance audit: cookie consent, plugin telemetry, data handling practices, and remediation checklist.',
		),
		array(
			'title'   => 'AI Readiness Assessment',
			'icon'    => '🧠',
			'price'   => '$495',
			'sort'    => '7',
			'excerpt' => 'A 90-minute structured workshop + written report identifying your top 3 AI automation opportunities, ranked by effort and impact. Great first step before committing to a build.',
		),
		array(
			'title'   => 'WordPress CMS Training',
			'icon'    => '🎓',
			'price'   => '$350',
			'sort'    => '8',
			'excerpt' => 'A 2-hour live training session (remote or in-person, Worcester/Boston area) for your team covering content editing, media management, and site maintenance basics.',
		),
		array(
			'title'   => 'Technical SEO Audit',
			'icon'    => '🔍',
			'price'   => '$575',
			'sort'    => '9',
			'excerpt' => 'Full crawl analysis, metadata audit, Core Web Vitals, structured data gaps, crawlability issues, and a prioritized fix list. Includes Google Search Console setup if needed.',
		),
	);

	foreach ( $addons as $addon ) {
		$create_service( $addon['title'], 'add-ons', array(
			'service_price'      => $addon['price'],
			'service_icon'       => $addon['icon'],
			'service_sort_order' => $addon['sort'],
		), $addon['excerpt'] );
	}

	update_option( 'developer_portfolio_migration_120', true );
}
add_action( 'init', 'developer_portfolio_migrate_services_data' );

/**
 * One-time migration: update Services page content with new services pricing blocks.
 */
function developer_portfolio_migrate_services_page_blocks() {
	if ( get_option( 'developer_portfolio_migration_121' ) ) {
		return;
	}

	$page = get_page_by_path( 'services' );
	if ( $page ) {
		wp_update_post( array(
			'ID'           => $page->ID,
			'post_content' => developer_portfolio_services_content(),
		) );
	}

	update_option( 'developer_portfolio_migration_121', true );
}
add_action( 'init', 'developer_portfolio_migrate_services_page_blocks' );

/**
 * One-time migration: populate service post content with bound blocks so data renders in the editor.
 */
function developer_portfolio_migrate_service_post_content() {
	if ( get_option( 'developer_portfolio_migration_122' ) ) {
		return;
	}

	$block_content = '<!-- wp:group {"style":{"spacing":{"blockGap":"0.25rem"}},"layout":{"type":"flex","orientation":"vertical"}} -->
<div class="wp-block-group">
<!-- wp:paragraph {"style":{"typography":{"fontSize":"0.85rem"}},"metadata":{"bindings":{"content":{"source":"core/post-meta","args":{"key":"service_subtitle"}}}}} -->
<p style="font-size:0.85rem"></p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"style":{"typography":{"fontSize":"0.75rem","textTransform":"uppercase","letterSpacing":"0.06em"},"color":{"text":"#64748b"}},"metadata":{"bindings":{"content":{"source":"core/post-meta","args":{"key":"service_tier"}}}}} -->
<p class="has-text-color" style="color:#64748b;font-size:0.75rem;letter-spacing:0.06em;text-transform:uppercase"></p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->

<!-- wp:group {"style":{"spacing":{"blockGap":"0.15rem","margin":{"top":"1rem"}}},"layout":{"type":"flex","orientation":"vertical"}} -->
<div class="wp-block-group" style="margin-top:1rem">
<!-- wp:heading {"level":3,"metadata":{"bindings":{"content":{"source":"core/post-meta","args":{"key":"service_price"}}}}} -->
<h3 class="wp-block-heading"></h3>
<!-- /wp:heading -->

<!-- wp:paragraph {"style":{"color":{"text":"#64748b"},"typography":{"fontSize":"0.8rem"}},"metadata":{"bindings":{"content":{"source":"core/post-meta","args":{"key":"service_price_note"}}}}} -->
<p class="has-text-color" style="color:#64748b;font-size:0.8rem"></p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->

<!-- wp:separator -->
<hr class="wp-block-separator has-alpha-channel-opacity"/>
<!-- /wp:separator -->

<!-- wp:paragraph {"placeholder":"Service description — visible in the excerpt and package cards..."} -->
<p></p>
<!-- /wp:paragraph -->

<!-- wp:buttons {"style":{"spacing":{"margin":{"top":"1rem"}}}} -->
<div class="wp-block-buttons" style="margin-top:1rem">
<!-- wp:button {"metadata":{"bindings":{"text":{"source":"core/post-meta","args":{"key":"service_cta_text"}},"url":{"source":"core/post-meta","args":{"key":"service_cta_url"}}}}} -->
<div class="wp-block-button"><a class="wp-block-button__link wp-element-button"></a></div>
<!-- /wp:button -->
</div>
<!-- /wp:buttons -->';

	$services = get_posts( array(
		'post_type'      => 'service',
		'post_status'    => 'any',
		'posts_per_page' => -1,
	) );

	foreach ( $services as $service ) {
		// Only update if current content is empty or from the old template.
		$current = trim( $service->post_content );
		if ( empty( $current ) || strpos( $current, 'service_price_range' ) !== false ) {
			wp_update_post( array(
				'ID'           => $service->ID,
				'post_content' => $block_content,
			) );
		}
	}

	update_option( 'developer_portfolio_migration_122', true );
}
add_action( 'init', 'developer_portfolio_migrate_service_post_content' );

/**
 * One-time migration: rebuild service post content with excerpt in description paragraph.
 */
function developer_portfolio_migrate_service_content_with_excerpt() {
	if ( get_option( 'developer_portfolio_migration_123' ) ) {
		return;
	}

	$services = get_posts( array(
		'post_type'      => 'service',
		'post_status'    => 'any',
		'posts_per_page' => -1,
	) );

	foreach ( $services as $service ) {
		$excerpt = trim( $service->post_excerpt );

		$block_content = '<!-- wp:group {"style":{"spacing":{"blockGap":"0.25rem"}},"layout":{"type":"flex","orientation":"vertical"}} -->
<div class="wp-block-group">
<!-- wp:paragraph {"style":{"typography":{"fontSize":"0.85rem"}},"metadata":{"bindings":{"content":{"source":"core/post-meta","args":{"key":"service_subtitle"}}}}} -->
<p style="font-size:0.85rem"></p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"style":{"typography":{"fontSize":"0.75rem","textTransform":"uppercase","letterSpacing":"0.06em"},"color":{"text":"#64748b"}},"metadata":{"bindings":{"content":{"source":"core/post-meta","args":{"key":"service_tier"}}}}} -->
<p class="has-text-color" style="color:#64748b;font-size:0.75rem;letter-spacing:0.06em;text-transform:uppercase"></p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->

<!-- wp:group {"style":{"spacing":{"blockGap":"0.15rem","margin":{"top":"1rem"}}},"layout":{"type":"flex","orientation":"vertical"}} -->
<div class="wp-block-group" style="margin-top:1rem">
<!-- wp:heading {"level":3,"metadata":{"bindings":{"content":{"source":"core/post-meta","args":{"key":"service_price"}}}}} -->
<h3 class="wp-block-heading"></h3>
<!-- /wp:heading -->

<!-- wp:paragraph {"style":{"color":{"text":"#64748b"},"typography":{"fontSize":"0.8rem"}},"metadata":{"bindings":{"content":{"source":"core/post-meta","args":{"key":"service_price_note"}}}}} -->
<p class="has-text-color" style="color:#64748b;font-size:0.8rem"></p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->

<!-- wp:separator -->
<hr class="wp-block-separator has-alpha-channel-opacity"/>
<!-- /wp:separator -->

<!-- wp:paragraph -->
<p>' . esc_html( $excerpt ) . '</p>
<!-- /wp:paragraph -->

<!-- wp:buttons {"style":{"spacing":{"margin":{"top":"1rem"}}}} -->
<div class="wp-block-buttons" style="margin-top:1rem">
<!-- wp:button {"metadata":{"bindings":{"text":{"source":"core/post-meta","args":{"key":"service_cta_text"}},"url":{"source":"core/post-meta","args":{"key":"service_cta_url"}}}}} -->
<div class="wp-block-button"><a class="wp-block-button__link wp-element-button"></a></div>
<!-- /wp:button -->
</div>
<!-- /wp:buttons -->';

		wp_update_post( array(
			'ID'           => $service->ID,
			'post_content' => $block_content,
		) );
	}

	update_option( 'developer_portfolio_migration_123', true );
}
add_action( 'init', 'developer_portfolio_migrate_service_content_with_excerpt' );


/**
 * One-time migration: update Projects page to two category-filtered sections.
 */
function developer_portfolio_migrate_projects_sections() {
	if ( get_option( 'developer_portfolio_migration_126' ) ) {
		return;
	}

	$page = get_page_by_path( 'projects' );
	if ( $page ) {
		wp_update_post( array(
			'ID'           => $page->ID,
			'post_content' => developer_portfolio_projects_content(),
		) );
	}

	update_option( 'developer_portfolio_migration_126', true );
}
add_action( 'init', 'developer_portfolio_migrate_projects_sections' );

/**
 * One-time migration: re-apply Projects page content with correct characters.
 */
function developer_portfolio_migrate_projects_sections_fix() {
	if ( get_option( 'developer_portfolio_migration_127' ) ) {
		return;
	}

	$page = get_page_by_path( 'projects' );
	if ( $page ) {
		wp_update_post( array(
			'ID'           => $page->ID,
			'post_content' => developer_portfolio_projects_content(),
		) );
	}

	update_option( 'developer_portfolio_migration_127', true );
}
add_action( 'init', 'developer_portfolio_migrate_projects_sections_fix' );

/**
 * One-time migration: re-apply Projects page with empty description on proprietary section.
 */
function developer_portfolio_migrate_projects_sections_v2() {
	if ( get_option( 'developer_portfolio_migration_128' ) ) {
		return;
	}

	$page = get_page_by_path( 'projects' );
	if ( $page ) {
		wp_update_post( array(
			'ID'           => $page->ID,
			'post_content' => developer_portfolio_projects_content(),
		) );
	}

	update_option( 'developer_portfolio_migration_128', true );
}
add_action( 'init', 'developer_portfolio_migrate_projects_sections_v2' );

/**
 * One-time migration: populate Home page tech stack data to match live site.
 */
function developer_portfolio_migrate_tech_stack_data() {
	if ( get_option( 'developer_portfolio_migration_129' ) ) {
		return;
	}

	$home = get_page_by_path( 'home' );
	if ( ! $home ) {
		$home = get_page_by_title( 'Home' );
	}
	if ( ! $home ) {
		return;
	}

	$tech_stack = array(
		array(
			'name'   => 'Frontend & UI',
			'color'  => 'violet',
			'badges' => array( 'JavaScript', 'TypeScript', 'React', 'HTML', 'CSS', 'Tailwind CSS' ),
		),
		array(
			'name'   => 'Backend & Platforms',
			'color'  => 'cyan',
			'badges' => array( 'WordPress', 'PHP', 'Node.js', 'Python', 'REST APIs', 'MySQL' ),
		),
		array(
			'name'   => 'AI Systems & Automation',
			'color'  => 'emerald',
			'badges' => array( 'Ollama (Local LLM)', 'RAG', 'Vector Databases', 'AI Workflow Automation', 'Prompt Engineering', 'AI-Assisted Development' ),
		),
		array(
			'name'   => 'Security, Infrastructure & DevOps',
			'color'  => 'rose',
			'badges' => array( 'Linux', 'Security Hardening', 'Log Monitoring', 'Infrastructure Automation', 'Docker', 'GitHub Actions' ),
		),
		array(
			'name'   => 'Tools & Design',
			'color'  => 'amber',
			'badges' => array( 'Figma', 'Git', 'GitHub', 'VS Code', 'Postman', 'Cloudways' ),
		),
	);

	update_post_meta( $home->ID, 'tech_stack_data', wp_json_encode( $tech_stack ) );

	update_option( 'developer_portfolio_migration_129', true );
}
add_action( 'init', 'developer_portfolio_migrate_tech_stack_data' );

/**
 * One-time migration: update About page education section with new content.
 */
function developer_portfolio_update_about_education() {
	if ( get_option( 'developer_portfolio_migration_130' ) ) {
		return;
	}

	$about = get_page_by_path( 'about' );
	if ( ! $about ) {
		return;
	}

	$content = $about->post_content;

	// Replace B.S. Computer Science / UC Berkeley with Harvard content.
	$content = str_replace(
		'B.S. Computer Science</p>',
		'Studying Computer Science at Harvard</p>',
		$content
	);
	$content = str_replace(
		'UC Berkeley, 2018',
		'Boston, MA 2023 - Present',
		$content
	);

	// Replace AWS Solutions Architect with W3C WAI content.
	$content = str_replace(
		'AWS Solutions Architect</p>',
		'W3C / WAI Web Accessibility</p>',
		$content
	);
	$content = str_replace(
		'Certification, 2020',
		'Professional Certification, 2022',
		$content
	);

	if ( $content !== $about->post_content ) {
		wp_update_post( array(
			'ID'           => $about->ID,
			'post_content' => $content,
		) );
	}

	update_option( 'developer_portfolio_migration_130', true );
}
add_action( 'init', 'developer_portfolio_update_about_education' );

/**
 * One-time migration: remove education and achievements section from About page.
 */
function developer_portfolio_remove_about_education() {
	if ( get_option( 'developer_portfolio_migration_131' ) ) {
		return;
	}

	$about = get_page_by_path( 'about' );
	if ( ! $about ) {
		return;
	}

	$content = $about->post_content;

	// Remove the education section pattern block from the content.
	$content = preg_replace(
		'/<!--\s*wp:pattern\s*{\s*"slug":\s*"developer-portfolio\/about-education"\s*}\s*\/\s*-->/',
		'',
		$content
	);

	// Also try to remove any education columns/sections that might be hardcoded.
	// Remove blocks with "Education" or "Achievements" headings.
	$content = preg_replace(
		'/<!--\s*wp:columns[^>]*>.*?Education.*?Achievements.*?<!--\s*\/wp:columns\s*-->/s',
		'',
		$content
	);

	if ( $content !== $about->post_content ) {
		wp_update_post( array(
			'ID'           => $about->ID,
			'post_content' => $content,
		) );
	}

	update_option( 'developer_portfolio_migration_131', true );
}
add_action( 'init', 'developer_portfolio_remove_about_education' );

/**
 * One-time migration: populate About page tech stack data to match live site.
 */
function developer_portfolio_migrate_about_tech_stack() {
	if ( get_option( 'developer_portfolio_migration_132' ) ) {
		return;
	}

	$about = get_page_by_path( 'about' );
	if ( ! $about ) {
		$about = get_page_by_title( 'About' );
	}
	if ( ! $about ) {
		return;
	}

	$tech_stack = array(
		array(
			'name'   => 'Frontend & UI',
			'color'  => 'violet',
			'badges' => array( 'JavaScript', 'TypeScript', 'React', 'HTML', 'CSS', 'Tailwind CSS' ),
		),
		array(
			'name'   => 'Backend & Platforms',
			'color'  => 'cyan',
			'badges' => array( 'WordPress', 'PHP', 'Node.js', 'Python', 'REST APIs', 'MySQL' ),
		),
		array(
			'name'   => 'AI Systems & Automation',
			'color'  => 'emerald',
			'badges' => array( 'Ollama (Local LLM)', 'RAG', 'Vector Databases', 'AI Workflow Automation', 'Prompt Engineering', 'AI-Assisted Development' ),
		),
		array(
			'name'   => 'Security, Infrastructure & DevOps',
			'color'  => 'rose',
			'badges' => array( 'Linux', 'Security Hardening', 'Log Monitoring', 'Infrastructure Automation', 'Docker', 'GitHub Actions' ),
		),
		array(
			'name'   => 'Tools & Design',
			'color'  => 'amber',
			'badges' => array( 'Figma', 'Git', 'GitHub', 'VS Code', 'Postman', 'Cloudways' ),
		),
	);

	update_post_meta( $about->ID, 'tech_stack_data', wp_json_encode( $tech_stack ) );

	update_option( 'developer_portfolio_migration_132', true );
}
add_action( 'init', 'developer_portfolio_migrate_about_tech_stack' );
