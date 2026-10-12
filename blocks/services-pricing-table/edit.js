( function( wp ) {
	var el = wp.element.createElement;
	var useBlockProps = wp.blockEditor.useBlockProps;
	var InspectorControls = wp.blockEditor.InspectorControls;
	var PanelBody = wp.components.PanelBody;
	var TextControl = wp.components.TextControl;
	var TextareaControl = wp.components.TextareaControl;
	var registerBlockType = wp.blocks.registerBlockType;

	function PricingTableEdit( props ) {
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
						help: 'Service category slug to query (e.g. "hourly-rates").',
					} )
				),
				el( PanelBody, { title: 'Volume Discount Callout', initialOpen: false },
					el( TextControl, {
						label: 'Callout Title',
						value: attributes.volumeDiscountTitle,
						onChange: function( val ) { setAttributes( { volumeDiscountTitle: val } ); },
					} ),
					el( TextareaControl, {
						label: 'Callout Text (HTML allowed)',
						value: attributes.volumeDiscountText,
						onChange: function( val ) { setAttributes( { volumeDiscountText: val } ); },
					} )
				)
			),
			el( 'div', { className: 'svc-section-inner' },
				el( 'span', { className: 'svc-section-tag svc-tag-rate' }, attributes.sectionTag ),
				el( 'h2', { className: 'svc-section-heading' }, attributes.heading ),
				el( 'p', { className: 'svc-section-intro' }, attributes.intro ),
				el( 'div', { className: 'svc-pricing-table-placeholder', style: { padding: '24px', background: 'rgba(255,255,255,0.05)', borderRadius: '10px', textAlign: 'center', color: '#9696ae' } },
					'Pricing table — pulls from "' + attributes.category + '" service posts (rendered on frontend)'
				),
				attributes.volumeDiscountText ? el( 'div', { className: 'svc-callout', style: { marginTop: '20px', padding: '18px 22px', background: 'rgba(255,255,255,0.03)', border: '1px solid rgba(139,92,246,0.15)', borderRadius: '10px' } },
					el( 'span', { className: 'svc-callout-title' }, attributes.volumeDiscountTitle ),
					el( 'p', { className: 'svc-callout-text', style: { marginTop: '6px', fontSize: '13px', color: '#94a3b8' } }, 'Volume discount details (rendered with HTML on frontend)' )
				) : null
			)
		);
	}

	registerBlockType( 'developer-portfolio/services-pricing-table', {
		edit: PricingTableEdit,
		save: function() { return null; },
	} );
} )( window.wp );
