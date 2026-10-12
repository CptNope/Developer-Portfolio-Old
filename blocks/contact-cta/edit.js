( function( wp ) {
	var el = wp.element.createElement;
	var useBlockProps = wp.blockEditor.useBlockProps;
	var RichText = wp.blockEditor.RichText;
	var InspectorControls = wp.blockEditor.InspectorControls;
	var PanelBody = wp.components.PanelBody;
	var TextControl = wp.components.TextControl;
	var registerBlockType = wp.blocks.registerBlockType;

	function ContactCtaEdit( props ) {
		var attributes = props.attributes;
		var setAttributes = props.setAttributes;

		var blockProps = useBlockProps( {
			className: 'cta-section',
		} );

		return el( 'div', blockProps,
			el( InspectorControls, null,
				el( PanelBody, { title: 'Button Settings', initialOpen: true },
					el( TextControl, {
						label: 'Button URL',
						value: attributes.buttonUrl,
						onChange: function( val ) { setAttributes( { buttonUrl: val } ); },
					} )
				)
			),
			el( 'div', { className: 'cta-card' },
				el( 'div', { className: 'cta-icon' },
					el( 'span', null, '✉' )
				),
				el( 'div', { className: 'cta-heading-wrapper' },
					el( RichText, {
						tagName: 'span',
						className: 'cta-heading-text',
						value: attributes.heading,
						onChange: function( val ) { setAttributes( { heading: val } ); },
						placeholder: 'Heading text...',
					} ),
					el( 'span', null, ' ' ),
					el( RichText, {
						tagName: 'span',
						className: 'developer-portfolio-gradient-text',
						value: attributes.headingHighlight,
						onChange: function( val ) { setAttributes( { headingHighlight: val } ); },
						placeholder: 'Highlight',
					} )
				),
				el( RichText, {
					tagName: 'p',
					className: 'cta-description has-muted-color has-text-color',
					value: attributes.description,
					onChange: function( val ) { setAttributes( { description: val } ); },
					placeholder: 'Description text...',
				} ),
				el( 'div', { className: 'cta-button-wrapper' },
					el( 'div', { className: 'wp-block-button hero-cta-primary' },
						el( RichText, {
							tagName: 'span',
							className: 'wp-block-button__link has-violet-600-to-cyan-600-gradient-background has-background wp-element-button',
							value: attributes.buttonText,
							onChange: function( val ) { setAttributes( { buttonText: val } ); },
							placeholder: 'Button text',
						} )
					)
				)
			)
		);
	}

	registerBlockType( 'developer-portfolio/contact-cta', {
		edit: ContactCtaEdit,
		save: function() {
			return null;
		},
	} );
} )( window.wp );
