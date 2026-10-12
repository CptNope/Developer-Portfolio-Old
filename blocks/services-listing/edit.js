( function( wp ) {
	var el = wp.element.createElement;
	var useBlockProps = wp.blockEditor.useBlockProps;
	var InspectorControls = wp.blockEditor.InspectorControls;
	var PanelBody = wp.components.PanelBody;
	var TextControl = wp.components.TextControl;
	var TextareaControl = wp.components.TextareaControl;
	var RangeControl = wp.components.RangeControl;
	var Spinner = wp.components.Spinner;
	var useSelect = wp.data.useSelect;
	var registerBlockType = wp.blocks.registerBlockType;

	function ServicesListingEdit( props ) {
		var a = props.attributes;
		var set = props.setAttributes;
		var blockProps = useBlockProps( { className: 'services-listing-block' } );

		var services = useSelect( function( select ) {
			return select( 'core' ).getEntityRecords( 'postType', 'service', {
				per_page: a.perPage,
				status: 'publish',
				orderby: 'date',
				order: 'desc',
			} );
		}, [ a.perPage ] );

		var headingPanel = el( PanelBody, { title: 'Heading', initialOpen: true },
			el( TextControl, { label: 'Section Label', value: a.sectionLabel, onChange: function( v ) { set( { sectionLabel: v } ); } } ),
			el( TextControl, { label: 'Heading', value: a.heading, onChange: function( v ) { set( { heading: v } ); } } ),
			el( TextControl, { label: 'Highlight Word', value: a.headingHighlight, onChange: function( v ) { set( { headingHighlight: v } ); } } ),
			el( TextareaControl, { label: 'Description', value: a.description, onChange: function( v ) { set( { description: v } ); } } )
		);

		var displayPanel = el( PanelBody, { title: 'Display Settings', initialOpen: false },
			el( RangeControl, { label: 'Services Per Page', value: a.perPage, onChange: function( v ) { set( { perPage: v } ); }, min: 1, max: 30 } ),
			el( RangeControl, { label: 'Columns', value: a.columns, onChange: function( v ) { set( { columns: v } ); }, min: 1, max: 4 } )
		);

		var cards = ! services
			? el( Spinner )
			: services.length === 0
				? el( 'p', { style: { color: '#999', textAlign: 'center' } }, 'No services found.' )
				: el( 'div', { className: 'sg-grid', style: { gridTemplateColumns: 'repeat(' + a.columns + ', 1fr)' } },
					services.map( function( s ) {
						return el( 'div', { key: s.id, className: 'sg-card' },
							el( 'h3', { className: 'sg-card-title' }, s.title.rendered ),
							s.excerpt && s.excerpt.rendered
								? el( 'p', { className: 'sg-card-excerpt', dangerouslySetInnerHTML: { __html: s.excerpt.rendered } } )
								: null
						);
					} )
				);

		return el( 'div', blockProps,
			el( InspectorControls, null, headingPanel, displayPanel ),
			el( 'div', { className: 'bl-hero' },
				el( 'p', { className: 'bl-label has-primary-color has-text-color has-jetbrains-mono-font-family' }, a.sectionLabel ),
				el( 'h1', { className: 'bl-heading wp-block-heading' }, a.heading ),
				a.description ? el( 'p', { className: 'bl-desc' }, a.description ) : null
			),
			cards
		);
	}

	registerBlockType( 'developer-portfolio/services-listing', {
		edit: ServicesListingEdit,
		save: function() { return null; },
	} );
} )( window.wp );
