( function( wp ) {
	var el = wp.element.createElement;
	var useBlockProps = wp.blockEditor.useBlockProps;
	var RichText = wp.blockEditor.RichText;
	var InspectorControls = wp.blockEditor.InspectorControls;
	var MediaUpload = wp.blockEditor.MediaUpload;
	var PanelBody = wp.components.PanelBody;
	var TextControl = wp.components.TextControl;
	var TextareaControl = wp.components.TextareaControl;
	var Button = wp.components.Button;
	var registerBlockType = wp.blocks.registerBlockType;

	function AboutIntroEdit( props ) {
		var a = props.attributes;
		var set = props.setAttributes;

		var blockProps = useBlockProps( {
			className: 'about-intro-block',
		} );

		function statPanel( n, label ) {
			return el( PanelBody, { title: label, initialOpen: false },
				el( TextControl, {
					label: 'Emoji',
					value: a[ 'stat' + n + 'Emoji' ],
					onChange: function( v ) { var o = {}; o[ 'stat' + n + 'Emoji' ] = v; set( o ); },
				} ),
				el( TextControl, {
					label: 'Number',
					value: a[ 'stat' + n + 'Number' ],
					onChange: function( v ) { var o = {}; o[ 'stat' + n + 'Number' ] = v; set( o ); },
				} ),
				el( TextControl, {
					label: 'Label',
					value: a[ 'stat' + n + 'Label' ],
					onChange: function( v ) { var o = {}; o[ 'stat' + n + 'Label' ] = v; set( o ); },
				} )
			);
		}

		function renderStatCard( n ) {
			return el( 'div', { key: n, className: 'about-stat-card' },
				el( 'span', { className: 'about-stat-emoji' }, a[ 'stat' + n + 'Emoji' ] ),
				el( 'span', { className: 'about-stat-number' }, a[ 'stat' + n + 'Number' ] ),
				el( 'span', { className: 'about-stat-label' }, a[ 'stat' + n + 'Label' ] )
			);
		}

		return el( 'div', blockProps,
			el( InspectorControls, null,
				el( PanelBody, { title: 'Image & Location', initialOpen: true },
					el( MediaUpload, {
						onSelect: function( media ) { set( { imageUrl: media.url, imageAlt: media.alt || a.imageAlt } ); },
						allowedTypes: [ 'image' ],
						render: function( obj ) {
							return el( 'div', null,
								a.imageUrl
									? el( 'img', { src: a.imageUrl, style: { maxWidth: '100%', marginBottom: '8px', borderRadius: '8px' } } )
									: null,
								el( Button, { variant: 'secondary', onClick: obj.open, style: { width: '100%' } },
									a.imageUrl ? 'Replace Image' : 'Select Image'
								)
							);
						},
					} ),
					el( TextControl, {
						label: 'Image Alt Text',
						value: a.imageAlt,
						onChange: function( v ) { set( { imageAlt: v } ); },
					} ),
					el( TextControl, {
						label: 'Location',
						value: a.location,
						onChange: function( v ) { set( { location: v } ); },
					} )
				),
				el( PanelBody, { title: 'Heading', initialOpen: false },
					el( TextControl, {
						label: 'Section Label',
						value: a.sectionLabel,
						onChange: function( v ) { set( { sectionLabel: v } ); },
					} ),
					el( TextControl, {
						label: 'Heading Highlight Word',
						help: 'This word will get gradient styling.',
						value: a.headingHighlight,
						onChange: function( v ) { set( { headingHighlight: v } ); },
					} )
				),
				el( PanelBody, { title: 'Bio Text', initialOpen: false },
					el( TextareaControl, {
						label: 'Paragraph 1',
						value: a.bio1,
						onChange: function( v ) { set( { bio1: v } ); },
					} ),
					el( TextareaControl, {
						label: 'Paragraph 2',
						value: a.bio2,
						onChange: function( v ) { set( { bio2: v } ); },
					} ),
					el( TextareaControl, {
						label: 'Paragraph 3',
						value: a.bio3,
						onChange: function( v ) { set( { bio3: v } ); },
					} ),
					el( TextareaControl, {
						label: 'Paragraph 4',
						value: a.bio4,
						onChange: function( v ) { set( { bio4: v } ); },
					} ),
					el( TextareaControl, {
						label: 'Paragraph 5',
						value: a.bio5,
						onChange: function( v ) { set( { bio5: v } ); },
					} ),
					el( TextareaControl, {
						label: 'Paragraph 6',
						value: a.bio6,
						onChange: function( v ) { set( { bio6: v } ); },
					} ),
					el( TextareaControl, {
						label: 'Paragraph 7',
						value: a.bio7,
						onChange: function( v ) { set( { bio7: v } ); },
					} ),
					el( TextareaControl, {
						label: 'Paragraph 8',
						value: a.bio8,
						onChange: function( v ) { set( { bio8: v } ); },
					} )
				),
				statPanel( '1', 'Stat 1' ),
				statPanel( '2', 'Stat 2' ),
				statPanel( '3', 'Stat 3' ),
				statPanel( '4', 'Stat 4' )
			),

			/* ---- Block Preview ---- */
			el( 'div', { className: 'about-intro-header' },
				el( 'p', { className: 'about-intro-label has-primary-color has-text-color has-jetbrains-mono-font-family' }, a.sectionLabel ),
				el( RichText, {
					tagName: 'h1',
					className: 'about-intro-heading wp-block-heading',
					value: a.heading,
					onChange: function( v ) { set( { heading: v } ); },
					placeholder: 'Heading...',
				} )
			),

			el( 'div', { className: 'about-intro-columns' },
				el( 'div', { className: 'about-intro-image-col' },
					el( 'div', { className: 'about-image-wrapper' },
						a.imageUrl
							? el( 'img', { src: a.imageUrl, alt: a.imageAlt, className: 'about-intro-img' } )
							: el( 'div', { className: 'about-intro-img-placeholder' }, 'Select an image in the sidebar' ),
						el( 'div', { className: 'location-badge' },
							el( 'span', null, '📍 ' + a.location )
						)
					)
				),

				el( 'div', { className: 'about-intro-text-col' },
					el( RichText, {
						tagName: 'h2',
						className: 'about-intro-greeting',
						value: a.greeting,
						onChange: function( v ) { set( { greeting: v } ); },
						placeholder: 'Greeting...',
					} ),
					el( 'p', { className: 'about-intro-bio' }, a.bio1 ),
					el( 'p', { className: 'about-intro-bio' }, a.bio2 ),
					el( 'p', { className: 'about-intro-bio' }, a.bio3 ),
					el( 'p', { className: 'about-intro-bio' }, a.bio4 ),
					el( 'p', { className: 'about-intro-bio' }, a.bio5 ),
					el( 'p', { className: 'about-intro-bio' }, a.bio6 ),
					el( 'p', { className: 'about-intro-bio' }, a.bio7 ),
					el( 'p', { className: 'about-intro-bio' }, a.bio8 )
				)
			),

			el( 'div', { className: 'about-stats-grid' },
				renderStatCard( '1' ),
				renderStatCard( '2' ),
				renderStatCard( '3' ),
				renderStatCard( '4' )
			)
		);
	}

	registerBlockType( 'developer-portfolio/about-intro', {
		edit: AboutIntroEdit,
		save: function() {
			return null;
		},
	} );
} )( window.wp );
