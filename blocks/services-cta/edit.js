( function( wp ) {
	var el = wp.element.createElement;
	var useBlockProps = wp.blockEditor.useBlockProps;
	var InspectorControls = wp.blockEditor.InspectorControls;
	var RichText = wp.blockEditor.RichText;
	var PanelBody = wp.components.PanelBody;
	var TextControl = wp.components.TextControl;
	var registerBlockType = wp.blocks.registerBlockType;

	function ServicesCtaEdit( props ) {
		var attributes = props.attributes;
		var setAttributes = props.setAttributes;

		var blockProps = useBlockProps( { className: 'svc-section' } );

		return el( 'div', blockProps,
			el( InspectorControls, null,
				el( PanelBody, { title: 'Button Settings', initialOpen: true },
					el( TextControl, {
						label: 'Primary Button URL',
						value: attributes.primaryButtonUrl,
						onChange: function( val ) { setAttributes( { primaryButtonUrl: val } ); },
					} ),
					el( TextControl, {
						label: 'Secondary Button URL',
						value: attributes.secondaryButtonUrl,
						onChange: function( val ) { setAttributes( { secondaryButtonUrl: val } ); },
					} )
				)
			),
			el( 'div', { className: 'svc-section-inner' },
				el( 'div', { className: 'svc-cta-box' },
					el( RichText, {
						tagName: 'h3',
						className: 'svc-cta-heading',
						value: attributes.heading,
						onChange: function( val ) { setAttributes( { heading: val } ); },
						placeholder: 'CTA heading...',
					} ),
					el( RichText, {
						tagName: 'p',
						className: 'svc-cta-desc',
						value: attributes.description,
						onChange: function( val ) { setAttributes( { description: val } ); },
						placeholder: 'CTA description...',
					} ),
					el( 'div', { className: 'svc-cta-buttons' },
						el( RichText, {
							tagName: 'span',
							className: 'svc-cta-btn-primary',
							value: attributes.primaryButtonText,
							onChange: function( val ) { setAttributes( { primaryButtonText: val } ); },
							placeholder: 'Primary button',
						} ),
						el( RichText, {
							tagName: 'span',
							className: 'svc-cta-btn-secondary',
							value: attributes.secondaryButtonText,
							onChange: function( val ) { setAttributes( { secondaryButtonText: val } ); },
							placeholder: 'Secondary button',
						} )
					)
				)
			)
		);
	}

	registerBlockType( 'developer-portfolio/services-cta', {
		edit: ServicesCtaEdit,
		save: function() { return null; },
	} );
} )( window.wp );
