<?php
/**
 * Server-side render for the Services Market Rates block.
 *
 * @param array    $attributes Block attributes.
 * @param string   $content    Block content.
 * @param WP_Block $block      Block instance.
 */

$cards_raw = isset( $attributes['cards'] ) ? $attributes['cards'] : '[]';
$cards     = json_decode( $cards_raw, true );
if ( ! is_array( $cards ) ) {
	$cards = array();
}
?>
<div <?php echo get_block_wrapper_attributes( array( 'class' => 'svc-market-bar alignfull' ) ); ?>>
	<div class="svc-market-inner">
		<?php foreach ( $cards as $card ) : ?>
		<div class="svc-market-card">
			<div class="svc-market-label"><?php echo esc_html( $card['label'] ?? '' ); ?></div>
			<div class="svc-market-range"><?php echo esc_html( $card['range'] ?? '' ); ?></div>
			<div class="svc-market-note"><?php echo esc_html( $card['note'] ?? '' ); ?></div>
		</div>
		<?php endforeach; ?>
	</div>
</div>
