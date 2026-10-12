( function( wp ) {
	var el = wp.element.createElement;
	var useBlockProps = wp.blockEditor.useBlockProps;
	var InspectorControls = wp.blockEditor.InspectorControls;
	var PanelBody = wp.components.PanelBody;
	var TextControl = wp.components.TextControl;
	var TextareaControl = wp.components.TextareaControl;
	var RangeControl = wp.components.RangeControl;
	var ToggleControl = wp.components.ToggleControl;
	var Spinner = wp.components.Spinner;
	var useSelect = wp.data.useSelect;
	var registerBlockType = wp.blocks.registerBlockType;

	function ProjectsListingEdit( props ) {
		var a = props.attributes;
		var set = props.setAttributes;
		var blockProps = useBlockProps( { className: 'projects-listing-block' } );

		var projects = useSelect( function( select ) {
			return select( 'core' ).getEntityRecords( 'postType', 'project', {
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
			el( ToggleControl, { label: 'Hero Header', help: a.isHero ? 'Large hero heading (h1).' : 'Smaller section heading (h2).', checked: a.isHero, onChange: function( v ) { set( { isHero: v } ); } } ),
			el( TextControl, { label: 'Category Slug', help: 'Filter by project_category slug. Leave empty for all.', value: a.category, onChange: function( v ) { set( { category: v } ); } } ),
			el( RangeControl, { label: 'Projects Per Page', value: a.perPage, onChange: function( v ) { set( { perPage: v } ); }, min: 1, max: 30 } ),
			el( RangeControl, { label: 'Columns', value: a.columns, onChange: function( v ) { set( { columns: v } ); }, min: 1, max: 4 } )
		);

		var cards = ! projects
			? el( Spinner )
			: projects.length === 0
				? el( 'p', { style: { color: '#999', textAlign: 'center' } }, 'No projects found.' )
				: el( 'div', { className: 'fp-grid', style: { gridTemplateColumns: 'repeat(' + a.columns + ', 1fr)' } },
					projects.map( function( p ) {
						var imgUrl = null;
						if ( p.featured_media && p._embedded && p._embedded['wp:featuredmedia'] ) {
							imgUrl = p._embedded['wp:featuredmedia'][0].source_url;
						}
						return el( 'div', { key: p.id, className: 'fp-card' },
							el( 'div', { className: 'fp-card-img' + ( imgUrl ? '' : ' fp-card-img-empty' ), style: imgUrl ? { backgroundImage: 'url(' + imgUrl + ')' } : {} } ),
							el( 'div', { className: 'fp-card-body' },
								el( 'h3', { className: 'fp-card-title' }, p.title.rendered )
							)
						);
					} )
				);

		return el( 'div', blockProps,
			el( InspectorControls, null, headingPanel, displayPanel ),
			el( 'div', { className: 'bl-hero' },
				el( 'p', { className: 'bl-label has-primary-color has-text-color has-jetbrains-mono-font-family' }, a.sectionLabel ),
				el( a.isHero ? 'h1' : 'h2', { className: 'bl-heading wp-block-heading' }, a.heading ),
				a.description ? el( 'p', { className: 'bl-desc' }, a.description ) : null
			),
			cards
		);
	}

	registerBlockType( 'developer-portfolio/projects-listing', {
		edit: ProjectsListingEdit,
		save: function() { return null; },
	} );
} )( window.wp );
