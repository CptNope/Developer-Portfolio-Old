<?php
/**
 * Server-side render for the Services FAQ block.
 *
 * @param array    $attributes Block attributes.
 * @param string   $content    Block content.
 * @param WP_Block $block      Block instance.
 */

$section_tag = isset( $attributes['sectionTag'] ) ? $attributes['sectionTag'] : 'FAQ';
$heading     = isset( $attributes['heading'] ) ? $attributes['heading'] : '';
$intro       = isset( $attributes['intro'] ) ? $attributes['intro'] : '';
$items       = json_decode( isset( $attributes['items'] ) ? $attributes['items'] : '[]', true );
if ( ! is_array( $items ) ) {
	$items = array();
}

// Build FAQPage structured data.
$faq_schema = array(
	'@context'   => 'https://schema.org',
	'@type'      => 'FAQPage',
	'mainEntity' => array(),
);
foreach ( $items as $item ) {
	$faq_schema['mainEntity'][] = array(
		'@type'          => 'Question',
		'name'           => $item['q'] ?? '',
		'acceptedAnswer' => array(
			'@type' => 'Answer',
			'text'  => $item['a'] ?? '',
		),
	);
}
?>
<div <?php echo get_block_wrapper_attributes( array( 'class' => 'svc-section alignfull' ) ); ?>>
	<div class="svc-section-inner">
		<span class="svc-section-tag svc-tag-rate"><?php echo esc_html( $section_tag ); ?></span>
		<h2 class="svc-section-heading"><?php echo esc_html( $heading ); ?></h2>
		<?php if ( $intro ) : ?>
		<p class="svc-section-intro"><?php echo esc_html( $intro ); ?></p>
		<?php endif; ?>

		<?php if ( ! empty( $items ) ) : ?>
		<div class="svc-faq-list">
			<?php foreach ( $items as $item ) : ?>
			<details class="svc-faq-item">
				<summary class="svc-faq-q"><?php echo esc_html( $item['q'] ?? '' ); ?></summary>
				<div class="svc-faq-a"><?php echo esc_html( $item['a'] ?? '' ); ?></div>
			</details>
			<?php endforeach; ?>
		</div>
		<?php endif; ?>
	</div>

	<?php if ( ! empty( $faq_schema['mainEntity'] ) ) : ?>
	<script type="application/ld+json"><?php echo wp_json_encode( $faq_schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ); ?></script>
	<?php endif; ?>
</div>
