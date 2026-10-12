<?php
/**
 * Server-side render for the Social Links block.
 *
 * @param array    $attributes Block attributes.
 * @param string   $content    Block content.
 * @param WP_Block $block      Block instance.
 */

$github_url   = isset( $attributes['githubUrl'] ) ? $attributes['githubUrl'] : '';
$linkedin_url = isset( $attributes['linkedinUrl'] ) ? $attributes['linkedinUrl'] : '';
$x_url        = isset( $attributes['xUrl'] ) ? $attributes['xUrl'] : '';
$email_url    = isset( $attributes['emailUrl'] ) ? $attributes['emailUrl'] : '';

$links = array();

if ( ! empty( $github_url ) ) {
	$links[] = array(
		'url'   => $github_url,
		'label' => 'GitHub',
		'svg'   => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24"><path d="M12 2C6.477 2 2 6.477 2 12c0 4.418 2.865 8.166 6.839 9.489.5.092.682-.217.682-.482 0-.237-.009-.866-.013-1.7-2.782.603-3.369-1.342-3.369-1.342-.454-1.155-1.11-1.462-1.11-1.462-.908-.62.069-.608.069-.608 1.003.07 1.531 1.03 1.531 1.03.892 1.529 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.11-4.555-4.943 0-1.091.39-1.984 1.029-2.683-.103-.253-.446-1.27.098-2.647 0 0 .84-.269 2.75 1.025A9.564 9.564 0 0 1 12 6.844a9.59 9.59 0 0 1 2.504.337c1.909-1.294 2.747-1.025 2.747-1.025.546 1.377.203 2.394.1 2.647.64.699 1.028 1.592 1.028 2.683 0 3.842-2.339 4.687-4.566 4.935.359.309.678.919.678 1.852 0 1.336-.012 2.415-.012 2.743 0 .267.18.578.688.48C19.138 20.163 22 16.418 22 12c0-5.523-4.477-10-10-10z"/></svg>',
	);
}

if ( ! empty( $linkedin_url ) ) {
	$links[] = array(
		'url'   => $linkedin_url,
		'label' => 'LinkedIn',
		'svg'   => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24"><path d="M19.7 3H4.3A1.3 1.3 0 003 4.3v15.4A1.3 1.3 0 004.3 21h15.4a1.3 1.3 0 001.3-1.3V4.3A1.3 1.3 0 0019.7 3zM8.339 18.338H5.667v-8.59h2.672v8.59zM7.004 8.574a1.548 1.548 0 11-.002-3.096 1.548 1.548 0 01.002 3.096zm11.335 9.764H15.67v-4.177c0-.996-.017-2.278-1.387-2.278-1.389 0-1.601 1.086-1.601 2.206v4.249h-2.667v-8.59h2.559v1.174h.037c.356-.675 1.227-1.387 2.526-1.387 2.703 0 3.203 1.779 3.203 4.092v4.711z"/></svg>',
	);
}

if ( ! empty( $x_url ) ) {
	$links[] = array(
		'url'   => $x_url,
		'label' => 'X',
		'svg'   => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24"><path d="M13.982 10.622L20.54 3h-1.554l-5.693 6.618L8.745 3H3.5l6.876 10.007L3.5 21h1.554l6.012-6.989L15.868 21h5.245l-7.131-10.378zm-2.128 2.474l-.697-.997L5.653 4.16h2.386l4.474 6.4.697.996 5.815 8.318h-2.387l-4.745-6.787z"/></svg>',
	);
}

if ( ! empty( $email_url ) ) {
	$links[] = array(
		'url'   => $email_url,
		'label' => 'Email',
		'svg'   => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24"><path d="M20 4H4a2 2 0 00-2 2v12a2 2 0 002 2h16a2 2 0 002-2V6a2 2 0 00-2-2zm0 4.236l-8 4.882-8-4.882V6h16v2.236z"/></svg>',
	);
}

if ( empty( $links ) ) {
	return;
}
?>
<div <?php echo get_block_wrapper_attributes( array( 'class' => 'footer-social-links' ) ); ?>>
	<ul class="wp-block-social-links has-icon-color">
		<?php foreach ( $links as $link ) : ?>
		<li class="wp-social-link">
			<a href="<?php echo esc_url( $link['url'] ); ?>" aria-label="<?php echo esc_attr( $link['label'] ); ?>" rel="noopener noreferrer" target="_blank">
				<?php echo $link['svg']; ?>
			</a>
		</li>
		<?php endforeach; ?>
	</ul>
</div>
