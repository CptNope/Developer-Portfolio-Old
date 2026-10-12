( function( wp ) {
	var el = wp.element.createElement;
	var useBlockProps = wp.blockEditor.useBlockProps;
	var InspectorControls = wp.blockEditor.InspectorControls;
	var PanelBody = wp.components.PanelBody;
	var TextControl = wp.components.TextControl;
	var TextareaControl = wp.components.TextareaControl;
	var RangeControl = wp.components.RangeControl;
	var CheckboxControl = wp.components.CheckboxControl;
	var Spinner = wp.components.Spinner;
	var useSelect = wp.data.useSelect;
	var registerBlockType = wp.blocks.registerBlockType;

	function parseJSON( str, fallback ) {
		try { return JSON.parse( str ); } catch( e ) { return fallback; }
	}

	function ServicesGridEdit( props ) {
		var a = props.attributes;
		var set = props.setAttributes;
		var blockProps = useBlockProps( { className: 'services-grid-block' } );
		var selectedIds = parseJSON( a.postIds, [] );

		var allServices = useSelect( function( select ) {
			return select( 'core' ).getEntityRecords( 'postType', 'service', {
				per_page: 50,
				status: 'publish',
				orderby: 'date',
				order: 'desc',
			} );
		}, [] );

		var displayServices = useSelect( function( select ) {
			if ( selectedIds.length > 0 ) {
				return select( 'core' ).getEntityRecords( 'postType', 'service', {
					include: selectedIds,
					per_page: selectedIds.length,
					orderby: 'include',
				} );
			}
			return select( 'core' ).getEntityRecords( 'postType', 'service', {
				per_page: a.count,
				status: 'publish',
				orderby: 'date',
				order: 'desc',
			} );
		}, [ a.postIds, a.count ] );

		function toggleService( id ) {
			var idx = selectedIds.indexOf( id );
			var copy = selectedIds.slice();
			if ( idx === -1 ) {
				copy.push( id );
			} else {
				copy.splice( idx, 1 );
			}
			set( { postIds: JSON.stringify( copy ) } );
		}

		var servicePicker = el( PanelBody, { title: 'Select Services', initialOpen: true },
			el( 'p', { style: { fontSize: '12px', color: '#999', marginBottom: '8px' } },
				selectedIds.length > 0
					? selectedIds.length + ' selected. Uncheck all to show latest ' + a.count + '.'
					: 'Showing latest ' + a.count + '. Check specific services to pin them.'
			),
			el( RangeControl, {
				label: 'Count (when none selected)',
				value: a.count,
				onChange: function( v ) { set( { count: v } ); },
				min: 1,
				max: 12,
			} ),
			! allServices
				? el( Spinner )
				: allServices.length === 0
					? el( 'p', { style: { color: '#999' } }, 'No services found.' )
					: allServices.map( function( s ) {
						return el( CheckboxControl, {
							key: s.id,
							label: s.title.rendered,
							checked: selectedIds.indexOf( s.id ) !== -1,
							onChange: function() { toggleService( s.id ); },
						} );
					} )
		);

		var headingPanel = el( PanelBody, { title: 'Heading & Link', initialOpen: false },
			el( TextControl, { label: 'Section Label', value: a.sectionLabel, onChange: function( v ) { set( { sectionLabel: v } ); } } ),
			el( TextControl, { label: 'Heading', value: a.heading, onChange: function( v ) { set( { heading: v } ); } } ),
			el( TextControl, { label: 'Highlight Word', value: a.headingHighlight, onChange: function( v ) { set( { headingHighlight: v } ); } } ),
			el( TextareaControl, { label: 'Description', value: a.description, onChange: function( v ) { set( { description: v } ); } } ),
			el( TextControl, { label: 'Link Text', value: a.linkText, onChange: function( v ) { set( { linkText: v } ); } } ),
			el( TextControl, { label: 'Link URL', value: a.linkUrl, onChange: function( v ) { set( { linkUrl: v } ); } } )
		);

		/* Preview */
		var cards = ! displayServices
			? el( Spinner )
			: displayServices.length === 0
				? el( 'p', { style: { color: '#999' } }, 'No services to display.' )
				: el( 'div', { className: 'sg-grid' },
					displayServices.map( function( s ) {
						return el( 'div', { key: s.id, className: 'sg-card' },
							el( 'h3', { className: 'sg-card-title' }, s.title.rendered ),
							s.excerpt && s.excerpt.rendered
								? el( 'p', { className: 'sg-card-excerpt', dangerouslySetInnerHTML: { __html: s.excerpt.rendered } } )
								: null
						);
					} )
				);

		return el( 'div', blockProps,
			el( InspectorControls, null, servicePicker, headingPanel ),
			el( 'div', { className: 'sg-header' },
				el( 'p', { className: 'sg-label has-primary-color has-text-color has-jetbrains-mono-font-family' }, a.sectionLabel ),
				el( 'div', { className: 'sg-header-row' },
					el( 'h2', { className: 'sg-heading wp-block-heading' }, a.heading ),
					el( 'span', { className: 'sg-link' }, a.linkText )
				),
				a.description ? el( 'p', { className: 'sg-desc' }, a.description ) : null
			),
			cards
		);
	}

	registerBlockType( 'developer-portfolio/services-grid', {
		edit: ServicesGridEdit,
		save: function() { return null; },
	} );
} )( window.wp );
