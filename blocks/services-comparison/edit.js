( function( wp ) {
	var el = wp.element.createElement;
	var useBlockProps = wp.blockEditor.useBlockProps;
	var InspectorControls = wp.blockEditor.InspectorControls;
	var PanelBody = wp.components.PanelBody;
	var TextControl = wp.components.TextControl;
	var TextareaControl = wp.components.TextareaControl;
	var registerBlockType = wp.blocks.registerBlockType;

	function ComparisonEdit( props ) {
		var attributes = props.attributes;
		var setAttributes = props.setAttributes;

		var columns = [];
		var rows = [];
		try { columns = JSON.parse( attributes.columns ); } catch( e ) {}
		try { rows = JSON.parse( attributes.rows ); } catch( e ) {}

		var blockProps = useBlockProps( { className: 'svc-section' } );

		return el( 'div', blockProps,
			el( InspectorControls, null,
				el( PanelBody, { title: 'Section Settings', initialOpen: true },
					el( TextControl, {
						label: 'Section Tag',
						value: attributes.sectionTag,
						onChange: function( val ) { setAttributes( { sectionTag: val } ); },
					} ),
					el( TextControl, {
						label: 'Heading',
						value: attributes.heading,
						onChange: function( val ) { setAttributes( { heading: val } ); },
					} ),
					el( TextareaControl, {
						label: 'Intro Text',
						value: attributes.intro,
						onChange: function( val ) { setAttributes( { intro: val } ); },
					} )
				),
				el( PanelBody, { title: 'Table Data (JSON)', initialOpen: false },
					el( TextareaControl, {
						label: 'Columns (JSON array)',
						value: attributes.columns,
						onChange: function( val ) { setAttributes( { columns: val } ); },
						help: 'JSON array of column header strings.',
					} ),
					el( TextareaControl, {
						label: 'Rows (JSON array)',
						value: attributes.rows,
						onChange: function( val ) { setAttributes( { rows: val } ); },
						help: 'JSON array of row objects with label, cells, and types arrays.',
						rows: 8,
					} )
				)
			),
			el( 'div', { className: 'svc-section-inner' },
				el( 'span', { className: 'svc-section-tag svc-tag-rate' }, attributes.sectionTag ),
				el( 'h2', { className: 'svc-section-heading' }, attributes.heading ),
				el( 'p', { className: 'svc-section-intro' }, attributes.intro ),
				el( 'div', { style: { overflowX: 'auto', marginTop: '24px' } },
					el( 'table', { className: 'svc-compare-table', style: { width: '100%', borderCollapse: 'collapse', fontSize: '13px' } },
						el( 'thead', null,
							el( 'tr', null,
								columns.map( function( col, i ) {
									return el( 'th', {
										key: i,
										style: { padding: '10px 14px', textAlign: 'left', color: i === 1 ? '#8b5cf6' : '#94a3b8', borderBottom: '1px solid rgba(255,255,255,0.06)' }
									}, col );
								} )
							)
						),
						el( 'tbody', null,
							rows.map( function( row, ri ) {
								return el( 'tr', { key: ri },
									el( 'td', { style: { padding: '10px 14px', color: '#e2e8f0', borderBottom: '1px solid rgba(255,255,255,0.04)' } }, row.label ),
									( row.cells || [] ).map( function( cell, ci ) {
										var color = '#94a3b8';
										if ( row.types && row.types[ ci ] === 'gold' ) color = '#f59e0b';
										if ( row.types && row.types[ ci ] === 'check' ) color = '#34d399';
										if ( row.types && row.types[ ci ] === 'cross' ) color = '#ef4444';
										if ( row.types && row.types[ ci ] === 'partial' ) color = '#94a3b8';
										return el( 'td', {
											key: ci,
											style: { padding: '10px 14px', color: color, borderBottom: '1px solid rgba(255,255,255,0.04)' }
										}, cell );
									} )
								);
							} )
						)
					)
				)
			)
		);
	}

	registerBlockType( 'developer-portfolio/services-comparison', {
		edit: ComparisonEdit,
		save: function() { return null; },
	} );
} )( window.wp );
