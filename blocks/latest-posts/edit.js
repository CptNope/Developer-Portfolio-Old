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

	function LatestPostsEdit( props ) {
		var a = props.attributes;
		var set = props.setAttributes;
		var blockProps = useBlockProps( { className: 'latest-posts-block' } );
		var selectedIds = parseJSON( a.postIds, [] );

		var allPosts = useSelect( function( select ) {
			return select( 'core' ).getEntityRecords( 'postType', 'post', {
				per_page: 50,
				status: 'publish',
				orderby: 'date',
				order: 'desc',
			} );
		}, [] );

		var displayPosts = useSelect( function( select ) {
			if ( selectedIds.length > 0 ) {
				return select( 'core' ).getEntityRecords( 'postType', 'post', {
					include: selectedIds,
					per_page: selectedIds.length,
					orderby: 'include',
				} );
			}
			return select( 'core' ).getEntityRecords( 'postType', 'post', {
				per_page: a.count,
				status: 'publish',
				orderby: 'date',
				order: 'desc',
			} );
		}, [ a.postIds, a.count ] );

		function togglePost( id ) {
			var idx = selectedIds.indexOf( id );
			var copy = selectedIds.slice();
			if ( idx === -1 ) {
				copy.push( id );
			} else {
				copy.splice( idx, 1 );
			}
			set( { postIds: JSON.stringify( copy ) } );
		}

		var postPicker = el( PanelBody, { title: 'Select Posts', initialOpen: true },
			el( 'p', { style: { fontSize: '12px', color: '#999', marginBottom: '8px' } },
				selectedIds.length > 0
					? selectedIds.length + ' selected. Uncheck all to show latest ' + a.count + '.'
					: 'Showing latest ' + a.count + '. Check specific posts to pin them.'
			),
			el( RangeControl, {
				label: 'Count (when none selected)',
				value: a.count,
				onChange: function( v ) { set( { count: v } ); },
				min: 1,
				max: 12,
			} ),
			! allPosts
				? el( Spinner )
				: allPosts.length === 0
					? el( 'p', { style: { color: '#999' } }, 'No posts found.' )
					: allPosts.map( function( p ) {
						return el( CheckboxControl, {
							key: p.id,
							label: p.title.rendered,
							checked: selectedIds.indexOf( p.id ) !== -1,
							onChange: function() { togglePost( p.id ); },
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
		var cards = ! displayPosts
			? el( Spinner )
			: displayPosts.length === 0
				? el( 'p', { style: { color: '#999' } }, 'No posts to display.' )
				: el( 'div', { className: 'lp-list' },
					displayPosts.map( function( p ) {
						return el( 'div', { key: p.id, className: 'lp-card' },
							el( 'div', { className: 'lp-card-body' },
								el( 'div', { className: 'lp-card-meta' },
									el( 'span', { className: 'lp-card-date' }, p.date ? new Date( p.date ).toLocaleDateString() : '' )
								),
								el( 'h3', { className: 'lp-card-title' }, p.title.rendered ),
								p.excerpt && p.excerpt.rendered
									? el( 'p', { className: 'lp-card-excerpt', dangerouslySetInnerHTML: { __html: p.excerpt.rendered } } )
									: null
							),
							el( 'span', { className: 'lp-card-arrow' }, '→' )
						);
					} )
				);

		return el( 'div', blockProps,
			el( InspectorControls, null, postPicker, headingPanel ),
			el( 'div', { className: 'lp-header' },
				el( 'p', { className: 'lp-label has-primary-color has-text-color has-jetbrains-mono-font-family' }, a.sectionLabel ),
				el( 'div', { className: 'lp-header-row' },
					el( 'h2', { className: 'lp-heading wp-block-heading' }, a.heading ),
					el( 'span', { className: 'lp-link' }, a.linkText )
				)
			),
			cards
		);
	}

	registerBlockType( 'developer-portfolio/latest-posts', {
		edit: LatestPostsEdit,
		save: function() { return null; },
	} );
} )( window.wp );
