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

	function BlogListingEdit( props ) {
		var a = props.attributes;
		var set = props.setAttributes;
		var blockProps = useBlockProps( { className: 'blog-listing-block' } );

		var posts = useSelect( function( select ) {
			return select( 'core' ).getEntityRecords( 'postType', 'post', {
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
			el( RangeControl, { label: 'Posts Per Page', value: a.perPage, onChange: function( v ) { set( { perPage: v } ); }, min: 1, max: 30 } )
		);

		var cards = ! posts
			? el( Spinner )
			: posts.length === 0
				? el( 'p', { style: { color: '#999', textAlign: 'center' } }, 'No posts found.' )
				: el( 'div', { className: 'bl-list' },
					posts.map( function( p ) {
						return el( 'div', { key: p.id, className: 'lp-card' },
							el( 'div', { className: 'lp-card-body' },
								el( 'div', { className: 'lp-card-meta' },
									el( 'span', { className: 'lp-card-date' }, p.date ? new Date( p.date ).toLocaleDateString() : '' )
								),
								el( 'h3', { className: 'lp-card-title' }, p.title.rendered )
							),
							el( 'span', { className: 'lp-card-arrow' }, '→' )
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

	registerBlockType( 'developer-portfolio/blog-listing', {
		edit: BlogListingEdit,
		save: function() { return null; },
	} );
} )( window.wp );
