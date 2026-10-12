( function( wp ) {
	var el = wp.element.createElement;
	var useBlockProps = wp.blockEditor.useBlockProps;
	var InspectorControls = wp.blockEditor.InspectorControls;
	var PanelBody = wp.components.PanelBody;
	var TextControl = wp.components.TextControl;
	var TextareaControl = wp.components.TextareaControl;
	var SelectControl = wp.components.SelectControl;
	var registerBlockType = wp.blocks.registerBlockType;

	function PackagesEdit( props ) {
		var attributes = props.attributes;
		var setAttributes = props.setAttributes;

		var blockProps = useBlockProps( { className: 'svc-section' } );

		var tagClass = 'svc-tag-' + ( attributes.sectionTagType || 'wp' );

		return el( 'div', blockProps,
			el( InspectorControls, null,
				el( PanelBody, { title: 'Section Settings', initialOpen: true },
					el( TextControl, {
						label: 'Section Tag',
						value: attributes.sectionTag,
						onChange: function( val ) { setAttributes( { sectionTag: val } ); },
					} ),
					el( SelectControl, {
						label: 'Tag Color',
						value: attributes.sectionTagType,
						options: [
							{ label: 'WordPress (blue)', value: 'wp' },
							{ label: 'AI (green)', value: 'ai' },
							{ label: 'Security (amber)', value: 'sec' },
							{ label: 'Maintenance (violet)', value: 'maint' },
						],
						onChange: function( val ) { setAttributes( { sectionTagType: val } ); },
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
					el( SelectControl, {
						label: 'Category',
						value: attributes.category,
						options: [
							{ label: 'WordPress Packages', value: 'wordpress-packages' },
							{ label: 'AI Packages', value: 'ai-packages' },
							{ label: 'Security Packages', value: 'security-packages' },
						],
						onChange: function( val ) { setAttributes( { category: val } ); },
					} )
				)
			),
			el( 'div', { className: 'svc-section-inner' },
				el( 'span', { className: 'svc-section-tag ' + tagClass }, attributes.sectionTag ),
				el( 'h2', { className: 'svc-section-heading' }, attributes.heading ),
				el( 'p', { className: 'svc-section-intro' }, attributes.intro ),
				el( 'div', { className: 'svc-pkg-grid', style: { display: 'grid', gridTemplateColumns: 'repeat(3, 1fr)', gap: '16px', marginTop: '24px' } },
					el( 'div', { style: { padding: '24px', background: 'rgba(255,255,255,0.03)', border: '1px solid rgba(139,92,246,0.15)', borderRadius: '12px', textAlign: 'center', color: '#9696ae' } }, 'Package 1' ),
					el( 'div', { style: { padding: '24px', background: 'rgba(255,255,255,0.03)', border: '1px solid #3b82f6', borderRadius: '12px', textAlign: 'center', color: '#9696ae' } }, 'Package 2 (Featured)' ),
					el( 'div', { style: { padding: '24px', background: 'rgba(255,255,255,0.03)', border: '1px solid rgba(139,92,246,0.15)', borderRadius: '12px', textAlign: 'center', color: '#9696ae' } }, 'Package 3' )
				),
				el( 'p', { style: { textAlign: 'center', marginTop: '12px', fontSize: '12px', color: '#64748b' } }, 'Pulls from "' + attributes.category + '" service posts (rendered on frontend)' )
			)
		);
	}

	registerBlockType( 'developer-portfolio/services-packages', {
		edit: PackagesEdit,
		save: function() { return null; },
	} );
} )( window.wp );
