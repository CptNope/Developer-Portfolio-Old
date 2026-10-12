( function( wp ) {
	var el = wp.element.createElement;
	var useBlockProps = wp.blockEditor.useBlockProps;
	var RichText = wp.blockEditor.RichText;
	var InspectorControls = wp.blockEditor.InspectorControls;
	var PanelBody = wp.components.PanelBody;
	var TextControl = wp.components.TextControl;
	var TextareaControl = wp.components.TextareaControl;
	var registerBlockType = wp.blocks.registerBlockType;

	function HeroSectionEdit( props ) {
		var attributes = props.attributes;
		var setAttributes = props.setAttributes;

		var blockProps = useBlockProps( {
			className: 'hero-section',
		} );

		return el( 'div', blockProps,
			el( InspectorControls, null,
				el( PanelBody, { title: 'Rotating Titles', initialOpen: true },
					el( TextareaControl, {
						label: 'Roles (comma-separated)',
						help: 'These cycle under your name on the frontend. e.g. "Full-Stack Developer, WordPress Engineer"',
						value: attributes.roles,
						onChange: function( val ) { setAttributes( { roles: val } ); },
					} )
				),
				el( PanelBody, { title: 'Button Settings', initialOpen: false },
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
			el( 'div', { className: 'hero-background' },
				el( 'div', { className: 'hero-inner' },
					el( 'div', { className: 'hero-badge-wrapper' },
						el( RichText, {
							tagName: 'span',
							className: 'hero-badge-text',
							value: attributes.badgeText,
							onChange: function( val ) { setAttributes( { badgeText: val } ); },
							placeholder: 'Badge text...',
						} )
					),
					el( RichText, {
						tagName: 'h1',
						className: 'hero-title developer-portfolio-gradient-text-light',
						value: attributes.name,
						onChange: function( val ) { setAttributes( { name: val } ); },
						placeholder: 'Your Name',
					} ),
					el( RichText, {
						tagName: 'span',
						className: 'hero-role-text has-jetbrains-mono-font-family developer-portfolio-gradient-text',
						value: attributes.role,
						onChange: function( val ) { setAttributes( { role: val } ); },
						placeholder: '{ Your Role }',
					} ),
					el( RichText, {
						tagName: 'p',
						className: 'hero-description has-muted-color has-text-color',
						value: attributes.description,
						onChange: function( val ) { setAttributes( { description: val } ); },
						placeholder: 'Your description...',
					} ),
					el( 'div', { className: 'hero-buttons' },
						el( 'div', { className: 'hero-btn-primary' },
							el( RichText, {
								tagName: 'span',
								value: attributes.primaryButtonText,
								onChange: function( val ) { setAttributes( { primaryButtonText: val } ); },
								placeholder: 'Button text',
							} )
						),
						el( 'div', { className: 'hero-btn-secondary' },
							el( RichText, {
								tagName: 'span',
								value: attributes.secondaryButtonText,
								onChange: function( val ) { setAttributes( { secondaryButtonText: val } ); },
								placeholder: 'Button text',
							} )
						)
					)
				)
			)
		);
	}

	registerBlockType( 'developer-portfolio/hero-section', {
		edit: HeroSectionEdit,
		save: function() {
			return null;
		},
	} );
} )( window.wp );
