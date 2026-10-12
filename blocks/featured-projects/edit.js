( function( wp ) {
	var el = wp.element.createElement;
	var useBlockProps = wp.blockEditor.useBlockProps;
	var InspectorControls = wp.blockEditor.InspectorControls;
	var PanelBody = wp.components.PanelBody;
	var TextControl = wp.components.TextControl;
	var RangeControl = wp.components.RangeControl;
	var CheckboxControl = wp.components.CheckboxControl;
	var Spinner = wp.components.Spinner;
	var useSelect = wp.data.useSelect;
	var registerBlockType = wp.blocks.registerBlockType;

	function parseJSON( str, fallback ) {
		try { return JSON.parse( str ); } catch( e ) { return fallback; }
	}

	function FeaturedProjectsEdit( props ) {
		var a = props.attributes;
		var set = props.setAttributes;
		var blockProps = useBlockProps( { className: 'featured-projects-block' } );
		var selectedIds = parseJSON( a.postIds, [] );

		var allProjects = useSelect( function( select ) {
			return select( 'core' ).getEntityRecords( 'postType', 'project', {
				per_page: 50,
				status: 'publish',
				orderby: 'date',
				order: 'desc',
			} );
		}, [] );

		var displayProjects = useSelect( function( select ) {
			if ( selectedIds.length > 0 ) {
				return select( 'core' ).getEntityRecords( 'postType', 'project', {
					include: selectedIds,
					per_page: selectedIds.length,
					orderby: 'include',
				} );
			}
			return select( 'core' ).getEntityRecords( 'postType', 'project', {
				per_page: a.count,
				status: 'publish',
				orderby: 'date',
				order: 'desc',
			} );
		}, [ a.postIds, a.count ] );

		function toggleProject( id ) {
			var idx = selectedIds.indexOf( id );
			var copy = selectedIds.slice();
			if ( idx === -1 ) {
				copy.push( id );
			} else {
				copy.splice( idx, 1 );
			}
			set( { postIds: JSON.stringify( copy ) } );
		}

		var projectPicker = el( PanelBody, { title: 'Select Projects', initialOpen: true },
			el( 'p', { style: { fontSize: '12px', color: '#999', marginBottom: '8px' } },
				selectedIds.length > 0
					? selectedIds.length + ' selected. Uncheck all to show latest ' + a.count + '.'
					: 'Showing latest ' + a.count + '. Check specific projects to pin them.'
			),
			el( RangeControl, {
				label: 'Count (when none selected)',
				value: a.count,
				onChange: function( v ) { set( { count: v } ); },
				min: 1,
				max: 12,
			} ),
			! allProjects
				? el( Spinner )
				: allProjects.length === 0
					? el( 'p', { style: { color: '#999' } }, 'No projects found.' )
					: allProjects.map( function( p ) {
						return el( CheckboxControl, {
							key: p.id,
							label: p.title.rendered,
							checked: selectedIds.indexOf( p.id ) !== -1,
							onChange: function() { toggleProject( p.id ); },
						} );
					} )
		);

		var headingPanel = el( PanelBody, { title: 'Heading & Link', initialOpen: false },
			el( TextControl, { label: 'Section Label', value: a.sectionLabel, onChange: function( v ) { set( { sectionLabel: v } ); } } ),
			el( TextControl, { label: 'Heading', value: a.heading, onChange: function( v ) { set( { heading: v } ); } } ),
			el( TextControl, { label: 'Highlight Word', value: a.headingHighlight, onChange: function( v ) { set( { headingHighlight: v } ); } } ),
			el( TextControl, { label: 'Link Text', value: a.linkText, onChange: function( v ) { set( { linkText: v } ); } } ),
			el( TextControl, { label: 'Link URL', value: a.linkUrl, onChange: function( v ) { set( { linkUrl: v } ); } } )
		);

		/* Preview */
		var cards = ! displayProjects
			? el( Spinner )
			: displayProjects.length === 0
				? el( 'p', { style: { color: '#999' } }, 'No projects to display.' )
				: el( 'div', { className: 'fp-grid' },
					displayProjects.map( function( p ) {
						var thumb = p.featured_media_src_url || p._embedded && p._embedded['wp:featuredmedia'] && p._embedded['wp:featuredmedia'][0] && p._embedded['wp:featuredmedia'][0].source_url;
						return el( 'div', { key: p.id, className: 'fp-card' },
							thumb ? el( 'div', { className: 'fp-card-img', style: { backgroundImage: 'url(' + thumb + ')' } } ) : el( 'div', { className: 'fp-card-img fp-card-img-empty' } ),
							el( 'div', { className: 'fp-card-body' },
								el( 'h3', { className: 'fp-card-title' }, p.title.rendered ),
								p.excerpt && p.excerpt.rendered
									? el( 'p', { className: 'fp-card-excerpt', dangerouslySetInnerHTML: { __html: p.excerpt.rendered } } )
									: null
							)
						);
					} )
				);

		return el( 'div', blockProps,
			el( InspectorControls, null, projectPicker, headingPanel ),
			el( 'div', { className: 'fp-header' },
				el( 'p', { className: 'fp-label has-primary-color has-text-color has-jetbrains-mono-font-family' }, a.sectionLabel ),
				el( 'div', { className: 'fp-header-row' },
					el( 'h2', { className: 'fp-heading wp-block-heading' }, a.heading ),
					el( 'span', { className: 'fp-link' }, a.linkText )
				)
			),
			cards
		);
	}

	registerBlockType( 'developer-portfolio/featured-projects', {
		edit: FeaturedProjectsEdit,
		save: function() { return null; },
	} );
} )( window.wp );
