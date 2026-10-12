( function( wp ) {
	var el = wp.element.createElement;
	var useBlockProps = wp.blockEditor.useBlockProps;
	var InspectorControls = wp.blockEditor.InspectorControls;
	var MediaUpload = wp.blockEditor.MediaUpload;
	var PanelBody = wp.components.PanelBody;
	var TextControl = wp.components.TextControl;
	var Button = wp.components.Button;
	var registerBlockType = wp.blocks.registerBlockType;
	var useSelect = wp.data.useSelect;

	function parseJSON( str, fallback ) {
		try { return JSON.parse( str ); } catch( e ) { return fallback; }
	}

	function SiteHeaderEdit( props ) {
		var a = props.attributes;
		var set = props.setAttributes;
		var blockProps = useBlockProps( { className: 'site-header-block' } );
		var navItems = parseJSON( a.navItems, [] );

		/* Fetch logo image URL if we have a logoId */
		var logoUrl = useSelect( function( select ) {
			if ( ! a.logoId ) return '';
			var media = select( 'core' ).getMedia( a.logoId );
			if ( ! media ) return '';
			var sizes = media.media_details && media.media_details.sizes;
			if ( sizes && sizes.thumbnail ) return sizes.thumbnail.source_url;
			return media.source_url || '';
		}, [ a.logoId ] );

		/* Fetch favicon image URL if we have a faviconId */
		var faviconUrl = useSelect( function( select ) {
			if ( ! a.faviconId ) return '';
			var media = select( 'core' ).getMedia( a.faviconId );
			if ( ! media ) return '';
			var sizes = media.media_details && media.media_details.sizes;
			if ( sizes && sizes.thumbnail ) return sizes.thumbnail.source_url;
			return media.source_url || '';
		}, [ a.faviconId ] );

		/* Fetch WP site title as placeholder */
		var wpSiteTitle = useSelect( function( select ) {
			var settings = select( 'core' ).getEntityRecord( 'root', 'site' );
			return settings ? settings.title : '';
		}, [] );

		var displayTitle = a.siteTitle || wpSiteTitle || 'Site Title';

		function updateItem( idx, key, value ) {
			var copy = navItems.slice();
			copy[ idx ] = Object.assign( {}, copy[ idx ] );
			copy[ idx ][ key ] = value;
			set( { navItems: JSON.stringify( copy ) } );
		}

		function addItem() {
			var copy = navItems.slice();
			copy.push( { label: 'New Link', url: '/' } );
			set( { navItems: JSON.stringify( copy ) } );
		}

		function removeItem( idx ) {
			var copy = navItems.slice();
			copy.splice( idx, 1 );
			set( { navItems: JSON.stringify( copy ) } );
		}

		/* Brand Panel */
		var brandPanel = el( PanelBody, { title: 'Brand / Identity', initialOpen: true },
			el( TextControl, {
				label: 'Site Title',
				value: a.siteTitle,
				placeholder: wpSiteTitle || 'Site Title',
				help: 'Leave empty to use the WordPress site title.',
				onChange: function( v ) { set( { siteTitle: v } ); }
			} ),
			el( 'div', { style: { marginBottom: '12px' } },
				el( 'label', { style: { display: 'block', marginBottom: '8px', fontWeight: 600 } }, 'Logo' ),
				a.logoId && logoUrl
					? el( 'div', { style: { marginBottom: '8px' } },
						el( 'img', { src: logoUrl, alt: 'Logo', style: { maxWidth: '80px', maxHeight: '80px', borderRadius: '6px', display: 'block' } } ),
						el( Button, { isDestructive: true, variant: 'secondary', onClick: function() { set( { logoId: 0 } ); }, style: { marginTop: '6px' } }, 'Remove Logo' )
					)
					: null,
				el( MediaUpload, {
					onSelect: function( media ) { set( { logoId: media.id } ); },
					allowedTypes: [ 'image' ],
					value: a.logoId,
					render: function( obj ) {
						return el( Button, { variant: 'secondary', onClick: obj.open },
							a.logoId ? 'Replace Logo' : 'Select Logo'
						);
					}
				} ),
				el( 'p', { style: { fontSize: '12px', color: '#757575', marginTop: '4px' } }, 'Leave empty to use the WordPress custom logo.' )
			),
			el( 'div', { style: { marginBottom: '12px' } },
				el( 'label', { style: { display: 'block', marginBottom: '8px', fontWeight: 600 } }, 'Favicon' ),
				a.faviconId && faviconUrl
					? el( 'div', { style: { marginBottom: '8px' } },
						el( 'img', { src: faviconUrl, alt: 'Favicon', style: { width: '32px', height: '32px', borderRadius: '4px', display: 'block' } } ),
						el( Button, { isDestructive: true, variant: 'secondary', onClick: function() { set( { faviconId: 0 } ); }, style: { marginTop: '6px' } }, 'Remove Favicon' )
					)
					: null,
				el( MediaUpload, {
					onSelect: function( media ) { set( { faviconId: media.id } ); },
					allowedTypes: [ 'image' ],
					value: a.faviconId,
					render: function( obj ) {
						return el( Button, { variant: 'secondary', onClick: obj.open },
							a.faviconId ? 'Replace Favicon' : 'Select Favicon'
						);
					}
				} ),
				el( 'p', { style: { fontSize: '12px', color: '#757575', marginTop: '4px' } }, 'Leave empty to use the WordPress site icon. Recommended: 512×512px.' )
			)
		);

		var navPanel = el( PanelBody, { title: 'Navigation Items (' + navItems.length + ')', initialOpen: false },
			navItems.map( function( item, i ) {
				return el( PanelBody, { key: i, title: 'Link ' + ( i + 1 ) + ': ' + item.label, initialOpen: false },
					el( TextControl, { label: 'Label', value: item.label, onChange: function( v ) { updateItem( i, 'label', v ); } } ),
					el( TextControl, { label: 'URL', value: item.url, onChange: function( v ) { updateItem( i, 'url', v ); } } ),
					el( Button, { isDestructive: true, variant: 'secondary', onClick: function() { removeItem( i ); }, style: { marginTop: '8px' } }, 'Remove' )
				);
			} ),
			el( Button, { variant: 'primary', onClick: addItem, style: { marginTop: '12px' } }, '+ Add Nav Item' )
		);

		var ctaPanel = el( PanelBody, { title: 'CTA Button', initialOpen: false },
			el( TextControl, { label: 'Button Text', value: a.ctaText, onChange: function( v ) { set( { ctaText: v } ); } } ),
			el( TextControl, { label: 'Button URL', value: a.ctaUrl, onChange: function( v ) { set( { ctaUrl: v } ); } } )
		);

		/* Preview */
		var logoEl = logoUrl
			? el( 'img', { src: logoUrl, alt: 'Logo', style: { width: '24px', height: '24px', borderRadius: '4px', objectFit: 'cover' } } )
			: el( 'span', null, '\uD83C\uDF10' );

		var preview = el( 'div', { className: 'sh-preview' },
			el( 'div', { className: 'sh-logo-area' },
				logoEl,
				el( 'span', { className: 'sh-site-title' }, displayTitle )
			),
			el( 'nav', { className: 'sh-nav' },
				navItems.map( function( item, i ) {
					return el( 'span', { key: i, className: 'sh-nav-link' }, item.label );
				} )
			),
			el( 'span', { className: 'sh-cta' }, a.ctaText )
		);

		return el( 'div', blockProps,
			el( InspectorControls, null, brandPanel, navPanel, ctaPanel ),
			preview
		);
	}

	registerBlockType( 'developer-portfolio/site-header', {
		edit: SiteHeaderEdit,
		save: function() { return null; },
	} );
} )( window.wp );
