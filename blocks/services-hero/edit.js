( function( wp ) {
	var el = wp.element.createElement;
	var useBlockProps = wp.blockEditor.useBlockProps;
	var InspectorControls = wp.blockEditor.InspectorControls;
	var RichText = wp.blockEditor.RichText;
	var PanelBody = wp.components.PanelBody;
	var TextControl = wp.components.TextControl;
	var TextareaControl = wp.components.TextareaControl;
	var registerBlockType = wp.blocks.registerBlockType;

	function ServicesHeroEdit( props ) {
		var attributes = props.attributes;
		var setAttributes = props.setAttributes;

		var chips = [];
		try { chips = JSON.parse( attributes.chips ); } catch( e ) { chips = []; }

		var blockProps = useBlockProps( {
			className: 'svc-hero-section',
		} );

		return el( 'div', blockProps,
			el( InspectorControls, null,
				el( PanelBody, { title: 'Hero Settings', initialOpen: true },
					el( TextControl, {
						label: 'Eyebrow Text',
						value: attributes.eyebrow,
						onChange: function( val ) { setAttributes( { eyebrow: val } ); },
					} ),
					el( TextControl, {
						label: 'Heading',
						value: attributes.heading,
						onChange: function( val ) { setAttributes( { heading: val } ); },
					} ),
					el( TextControl, {
						label: 'Heading Highlight (italic)',
						value: attributes.headingHighlight,
						onChange: function( val ) { setAttributes( { headingHighlight: val } ); },
					} ),
					el( TextareaControl, {
						label: 'Subtitle',
						value: attributes.subtitle,
						onChange: function( val ) { setAttributes( { subtitle: val } ); },
					} ),
					el( TextareaControl, {
						label: 'Chips (one per line)',
						value: chips.join( '\n' ),
						onChange: function( val ) {
							setAttributes( { chips: JSON.stringify( val.split( '\n' ).filter( function( c ) { return c.trim(); } ) ) } );
						},
						help: 'Enter one chip label per line.',
					} )
				)
			),
			el( 'div', { className: 'svc-hero-inner' },
				el( 'div', { className: 'svc-hero-orb svc-hero-orb--1' } ),
				el( 'div', { className: 'svc-hero-orb svc-hero-orb--2' } ),
				el( 'p', { className: 'svc-hero-eyebrow' }, attributes.eyebrow ),
				el( 'h1', { className: 'svc-hero-heading' },
					attributes.heading,
					el( 'br' ),
					el( 'span', { className: 'developer-portfolio-gradient-text' }, attributes.headingHighlight )
				),
				el( 'p', { className: 'svc-hero-subtitle' }, attributes.subtitle ),
				el( 'div', { className: 'svc-hero-chips' },
					chips.map( function( chip, i ) {
						return el( 'span', { key: i, className: 'svc-chip' }, chip );
					} )
				)
			)
		);
	}

	registerBlockType( 'developer-portfolio/services-hero', {
		edit: ServicesHeroEdit,
		save: function() { return null; },
	} );
} )( window.wp );
