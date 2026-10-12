( function( wp ) {
	var el = wp.element.createElement;
	var useBlockProps = wp.blockEditor.useBlockProps;
	var InspectorControls = wp.blockEditor.InspectorControls;
	var PanelBody = wp.components.PanelBody;
	var TextControl = wp.components.TextControl;
	var TextareaControl = wp.components.TextareaControl;
	var registerBlockType = wp.blocks.registerBlockType;

	function AddonsEdit( props ) {
		var attributes = props.attributes;
		var setAttributes = props.setAttributes;

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
					} ),
					el( TextControl, {
						label: 'Category Slug',
						value: attributes.category,
						onChange: function( val ) { setAttributes( { category: val } ); },
					} )
				)
			),
			el( 'div', { className: 'svc-section-inner' },
				el( 'span', { className: 'svc-section-tag svc-tag-rate' }, attributes.sectionTag ),
				el( 'h2', { className: 'svc-section-heading' }, attributes.heading ),
				el( 'p', { className: 'svc-section-intro' }, attributes.intro ),
				el( 'div', { style: { display: 'grid', gridTemplateColumns: 'repeat(3, 1fr)', gap: '12px', marginTop: '24px' } },
					[ 1, 2, 3, 4, 5, 6, 7, 8, 9 ].map( function( n ) {
						return el( 'div', { key: n, style: { padding: '16px 18px', background: 'rgba(255,255,255,0.03)', border: '1px solid rgba(139,92,246,0.15)', borderRadius: '10px', color: '#9696ae', fontSize: '12px' } }, 'Add-on ' + n );
					} )
				),
				el( 'p', { style: { textAlign: 'center', marginTop: '12px', fontSize: '12px', color: '#64748b' } },
					'Pulls from "' + attributes.category + '" service posts (rendered on frontend)'
				)
			)
		);
	}

	registerBlockType( 'developer-portfolio/services-addons', {
		edit: AddonsEdit,
		save: function() { return null; },
	} );
} )( window.wp );
