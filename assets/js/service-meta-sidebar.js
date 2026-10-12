( function( wp ) {
	var registerPlugin = wp.plugins.registerPlugin;
	var PluginDocumentSettingPanel = wp.editPost.PluginDocumentSettingPanel;
	var TextControl = wp.components.TextControl;
	var TextareaControl = wp.components.TextareaControl;
	var SelectControl = wp.components.SelectControl;
	var ToggleControl = wp.components.ToggleControl;
	var PanelBody = wp.components.PanelBody;
	var useSelect = wp.data.useSelect;
	var useEntityProp = wp.coreData.useEntityProp;
	var el = wp.element.createElement;
	var Fragment = wp.element.Fragment;

	function ServiceMetaPanel() {
		var postType = useSelect( function( select ) {
			return select( 'core/editor' ).getCurrentPostType();
		}, [] );

		var result = useEntityProp( 'postType', postType, 'meta' );
		var meta = result[ 0 ];
		var setMeta = result[ 1 ];

		if ( postType !== 'service' ) {
			return null;
		}

		function updateMeta( key, value ) {
			var updated = {};
			updated[ key ] = value;
			setMeta( Object.assign( {}, meta, updated ) );
		}

		// Parse features from JSON string for editing
		var featuresStr = '';
		try {
			var parsed = JSON.parse( meta.service_features || '[]' );
			if ( Array.isArray( parsed ) ) {
				featuresStr = parsed.join( '\n' );
			}
		} catch( e ) {
			featuresStr = meta.service_features || '';
		}

		return el( Fragment, null,
			el(
				PluginDocumentSettingPanel,
				{
					name: 'service-pricing-panel',
					title: 'Pricing',
					icon: 'money-alt',
				},
				el( TextControl, {
					label: 'Price',
					help: 'e.g. "$115/hr", "$2,500", "$500/mo"',
					value: meta.service_price || '',
					onChange: function( val ) { updateMeta( 'service_price', val ); },
				} ),
				el( TextControl, {
					label: 'Price Note',
					help: 'Subtitle under the price, e.g. "per hour" or "one-time fee"',
					value: meta.service_price_note || '',
					onChange: function( val ) { updateMeta( 'service_price_note', val ); },
				} ),
				el( TextControl, {
					label: 'Price Range (legacy)',
					help: 'Used by the services grid block, e.g. "Starting at $500"',
					value: meta.service_price_range || '',
					onChange: function( val ) { updateMeta( 'service_price_range', val ); },
				} )
			),
			el(
				PluginDocumentSettingPanel,
				{
					name: 'service-details-panel',
					title: 'Service Details',
					icon: 'info-outline',
					initialOpen: false,
				},
				el( TextControl, {
					label: 'Subtitle',
					help: 'Short tagline, e.g. "Custom WordPress Development"',
					value: meta.service_subtitle || '',
					onChange: function( val ) { updateMeta( 'service_subtitle', val ); },
				} ),
				el( TextControl, {
					label: 'Tier',
					help: 'Tier label, e.g. "STARTER", "PROFESSIONAL", "ENTERPRISE"',
					value: meta.service_tier || '',
					onChange: function( val ) { updateMeta( 'service_tier', val ); },
				} ),
				el( SelectControl, {
					label: 'Badge Type',
					help: 'Color badge for category identification',
					value: meta.service_badge_type || '',
					options: [
						{ label: '— None —', value: '' },
						{ label: 'WordPress (blue)', value: 'wp' },
						{ label: 'AI (green)', value: 'ai' },
						{ label: 'Security (amber)', value: 'sec' },
						{ label: 'Maintenance (violet)', value: 'maint' },
					],
					onChange: function( val ) { updateMeta( 'service_badge_type', val ); },
				} ),
				el( ToggleControl, {
					label: 'Featured (Most Popular)',
					checked: meta.service_featured === '1',
					onChange: function( val ) { updateMeta( 'service_featured', val ? '1' : '' ); },
				} ),
				el( TextControl, {
					label: 'Icon (emoji)',
					help: 'Single emoji for add-on cards',
					value: meta.service_icon || '',
					onChange: function( val ) { updateMeta( 'service_icon', val ); },
				} ),
				el( TextControl, {
					label: 'Sort Order',
					help: 'Numeric sort order within the category',
					value: meta.service_sort_order || '',
					onChange: function( val ) { updateMeta( 'service_sort_order', val ); },
				} )
			),
			el(
				PluginDocumentSettingPanel,
				{
					name: 'service-features-panel',
					title: 'Features List',
					icon: 'list-view',
					initialOpen: false,
				},
				el( TextareaControl, {
					label: 'Features (one per line)',
					help: 'Each line becomes a checkmark item in the package card.',
					value: featuresStr,
					rows: 8,
					onChange: function( val ) {
						var items = val.split( '\n' ).filter( function( line ) { return line.trim() !== ''; } );
						updateMeta( 'service_features', JSON.stringify( items ) );
					},
				} )
			),
			el(
				PluginDocumentSettingPanel,
				{
					name: 'service-hourly-panel',
					title: 'Hourly Rate Details',
					icon: 'clock',
					initialOpen: false,
				},
				el( TextControl, {
					label: 'Typical Use Cases',
					help: 'Comma-separated use cases for the pricing table',
					value: meta.service_typical_uses || '',
					onChange: function( val ) { updateMeta( 'service_typical_uses', val ); },
				} ),
				el( TextControl, {
					label: 'Minimum Hours',
					help: 'e.g. "2 hrs" or "1 hr"',
					value: meta.service_min_hours || '',
					onChange: function( val ) { updateMeta( 'service_min_hours', val ); },
				} )
			),
			el(
				PluginDocumentSettingPanel,
				{
					name: 'service-retainer-panel',
					title: 'Retainer Details',
					icon: 'backup',
					initialOpen: false,
				},
				el( TextControl, {
					label: 'Hours Included',
					help: 'e.g. "5 hrs/mo included"',
					value: meta.service_hours_included || '',
					onChange: function( val ) { updateMeta( 'service_hours_included', val ); },
				} ),
				el( TextControl, {
					label: 'Color Bar (hex)',
					help: 'Top color bar for retainer card, e.g. "#334155"',
					value: meta.service_color_bar || '',
					onChange: function( val ) { updateMeta( 'service_color_bar', val ); },
				} )
			),
			el(
				PluginDocumentSettingPanel,
				{
					name: 'service-cta-panel',
					title: 'CTA Button',
					icon: 'external',
					initialOpen: false,
				},
				el( TextControl, {
					label: 'CTA Text',
					help: 'e.g. "Get Started →"',
					value: meta.service_cta_text || '',
					onChange: function( val ) { updateMeta( 'service_cta_text', val ); },
				} ),
				el( TextControl, {
					label: 'CTA URL',
					help: 'e.g. "mailto:info@jeremyanderson.tech"',
					value: meta.service_cta_url || '',
					onChange: function( val ) { updateMeta( 'service_cta_url', val ); },
				} )
			)
		);
	}

	registerPlugin( 'service-meta-sidebar', {
		render: ServiceMetaPanel,
	} );
} )( window.wp );
