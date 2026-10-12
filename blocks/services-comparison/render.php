<?php
/**
 * Server-side render for the Services Comparison block.
 *
 * @param array    $attributes Block attributes.
 * @param string   $content    Block content.
 * @param WP_Block $block      Block instance.
 */

$section_tag = isset( $attributes['sectionTag'] ) ? $attributes['sectionTag'] : 'Market Position';
$heading     = isset( $attributes['heading'] ) ? $attributes['heading'] : '';
$intro       = isset( $attributes['intro'] ) ? $attributes['intro'] : '';
$columns     = json_decode( isset( $attributes['columns'] ) ? $attributes['columns'] : '[]', true );
$rows        = json_decode( isset( $attributes['rows'] ) ? $attributes['rows'] : '[]', true );

if ( ! is_array( $columns ) ) { $columns = array(); }
if ( ! is_array( $rows ) ) { $rows = array(); }

$type_classes = array(
	'gold'    => 'svc-cmp-gold',
	'check'   => 'svc-cmp-check',
	'cross'   => 'svc-cmp-cross',
	'partial' => 'svc-cmp-partial',
	'muted'   => 'svc-cmp-muted',
);
?>
<div <?php echo get_block_wrapper_attributes( array( 'class' => 'svc-section alignfull' ) ); ?>>
	<div class="svc-section-inner">
		<span class="svc-section-tag svc-tag-rate"><?php echo esc_html( $section_tag ); ?></span>
		<h2 class="svc-section-heading"><?php echo esc_html( $heading ); ?></h2>
		<?php if ( $intro ) : ?>
		<p class="svc-section-intro"><?php echo esc_html( $intro ); ?></p>
		<?php endif; ?>

		<?php if ( ! empty( $columns ) && ! empty( $rows ) ) : ?>
		<div class="svc-compare-wrapper">
			<table class="svc-compare-table">
				<thead>
					<tr>
						<?php foreach ( $columns as $i => $col ) : ?>
						<th<?php echo $i === 1 ? ' class="svc-cmp-jeremy"' : ''; ?>><?php echo esc_html( $col ); ?></th>
						<?php endforeach; ?>
					</tr>
				</thead>
				<tbody>
					<?php foreach ( $rows as $row ) : ?>
					<tr>
						<td><?php echo esc_html( $row['label'] ?? '' ); ?></td>
						<?php
						$cells = isset( $row['cells'] ) ? $row['cells'] : array();
						$types = isset( $row['types'] ) ? $row['types'] : array();
						foreach ( $cells as $ci => $cell ) :
							$type  = isset( $types[ $ci ] ) ? $types[ $ci ] : 'muted';
							$class = isset( $type_classes[ $type ] ) ? $type_classes[ $type ] : 'svc-cmp-muted';
							$is_jeremy = ( $ci === 0 );
						?>
						<td class="<?php echo esc_attr( $class ); ?><?php echo $is_jeremy ? ' svc-cmp-jeremy' : ''; ?>"><?php echo esc_html( $cell ); ?></td>
						<?php endforeach; ?>
					</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		</div>
		<?php endif; ?>
	</div>
</div>
