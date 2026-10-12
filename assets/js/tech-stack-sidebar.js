( function( wp ) {
	var registerPlugin = wp.plugins.registerPlugin;
	var PluginDocumentSettingPanel = wp.editPost.PluginDocumentSettingPanel;
	var useSelect = wp.data.useSelect;
	var useEntityProp = wp.coreData.useEntityProp;
	var el = wp.element.createElement;
	var useState = wp.element.useState;
	var TextControl = wp.components.TextControl;
	var Button = wp.components.Button;
	var SelectControl = wp.components.SelectControl;
	var PanelRow = wp.components.PanelRow;
	var FormTokenField = wp.components.FormTokenField;

	var COLOR_OPTIONS = [
		{ label: 'Violet', value: 'violet' },
		{ label: 'Cyan', value: 'cyan' },
		{ label: 'Emerald', value: 'emerald' },
		{ label: 'Rose', value: 'rose' },
		{ label: 'Amber', value: 'amber' },
	];

	var COLOR_HEX = {
		violet: '#8b5cf6',
		cyan: '#06b6d4',
		emerald: '#10b981',
		rose: '#f43f5e',
		amber: '#f59e0b',
	};

	function TechStackPanel() {
		var postType = useSelect( function( select ) {
			return select( 'core/editor' ).getCurrentPostType();
		}, [] );

		var entityProps = useEntityProp( 'postType', postType, 'meta' );
		var meta = entityProps[0];
		var setMeta = entityProps[1];

		var expandedState = useState( -1 );
		var expandedIndex = expandedState[0];
		var setExpandedIndex = expandedState[1];

		if ( postType !== 'page' ) {
			return null;
		}

		var raw = meta.tech_stack_data || '[]';
		var categories;
		try {
			categories = JSON.parse( raw );
		} catch ( e ) {
			categories = [];
		}
		if ( ! Array.isArray( categories ) ) {
			categories = [];
		}

		function saveCategories( updated ) {
			setMeta( Object.assign( {}, meta, {
				tech_stack_data: JSON.stringify( updated ),
			} ) );
		}

		function addCategory() {
			var updated = categories.concat( [ {
				name: 'New Category',
				color: 'violet',
				badges: [],
			} ] );
			saveCategories( updated );
			setExpandedIndex( updated.length - 1 );
		}

		function removeCategory( index ) {
			var updated = categories.filter( function( _, i ) {
				return i !== index;
			} );
			saveCategories( updated );
			if ( expandedIndex === index ) {
				setExpandedIndex( -1 );
			}
		}

		function updateCategory( index, key, value ) {
			var updated = categories.map( function( cat, i ) {
				if ( i !== index ) return cat;
				var newCat = Object.assign( {}, cat );
				newCat[ key ] = value;
				return newCat;
			} );
			saveCategories( updated );
		}

		function moveCategory( index, direction ) {
			var newIndex = index + direction;
			if ( newIndex < 0 || newIndex >= categories.length ) return;
			var updated = categories.slice();
			var temp = updated[ index ];
			updated[ index ] = updated[ newIndex ];
			updated[ newIndex ] = temp;
			saveCategories( updated );
			setExpandedIndex( newIndex );
		}

		if ( categories.length === 0 ) {
			return el(
				PluginDocumentSettingPanel,
				{ name: 'tech-stack-panel', title: 'Tech Stack', icon: 'editor-code' },
				el( 'p', { style: { color: '#757575', fontStyle: 'italic' } },
					'No tech stack categories. Add one to get started.'
				),
				el( Button, {
					variant: 'primary',
					onClick: addCategory,
					style: { marginTop: '8px' },
				}, '+ Add Category' )
			);
		}

		var categoryElements = categories.map( function( cat, index ) {
			var isExpanded = expandedIndex === index;
			var dotColor = COLOR_HEX[ cat.color ] || '#8b5cf6';

			var header = el( 'div', {
				style: {
					display: 'flex',
					alignItems: 'center',
					justifyContent: 'space-between',
					padding: '8px 0',
					cursor: 'pointer',
					borderBottom: '1px solid #e0e0e0',
				},
				onClick: function() {
					setExpandedIndex( isExpanded ? -1 : index );
				},
			},
				el( 'div', { style: { display: 'flex', alignItems: 'center', gap: '8px' } },
					el( 'span', {
						style: {
							width: '10px',
							height: '10px',
							borderRadius: '50%',
							backgroundColor: dotColor,
							display: 'inline-block',
							flexShrink: 0,
						},
					} ),
					el( 'strong', null, cat.name ),
					el( 'span', { style: { color: '#757575', fontSize: '12px' } },
						' (' + ( cat.badges ? cat.badges.length : 0 ) + ')'
					)
				),
				el( 'span', { style: { fontSize: '18px', color: '#757575' } },
					isExpanded ? '▾' : '▸'
				)
			);

			if ( ! isExpanded ) {
				return el( 'div', { key: index }, header );
			}

			var details = el( 'div', {
				style: {
					padding: '12px 0',
					borderBottom: '1px solid #e0e0e0',
				},
			},
				el( TextControl, {
					label: 'Category Name',
					value: cat.name,
					onChange: function( val ) {
						updateCategory( index, 'name', val );
					},
				} ),
				el( SelectControl, {
					label: 'Color',
					value: cat.color,
					options: COLOR_OPTIONS,
					onChange: function( val ) {
						updateCategory( index, 'color', val );
					},
				} ),
				el( FormTokenField, {
					label: 'Badges (press Enter to add)',
					value: cat.badges || [],
					onChange: function( tokens ) {
						updateCategory( index, 'badges', tokens );
					},
				} ),
				el( 'div', {
					style: {
						display: 'flex',
						gap: '4px',
						marginTop: '8px',
					},
				},
					el( Button, {
						isSmall: true,
						variant: 'secondary',
						onClick: function() { moveCategory( index, -1 ); },
						disabled: index === 0,
					}, '↑' ),
					el( Button, {
						isSmall: true,
						variant: 'secondary',
						onClick: function() { moveCategory( index, 1 ); },
						disabled: index === categories.length - 1,
					}, '↓' ),
					el( Button, {
						isSmall: true,
						isDestructive: true,
						onClick: function() {
							if ( window.confirm( 'Remove "' + cat.name + '"?' ) ) {
								removeCategory( index );
							}
						},
					}, 'Remove' )
				)
			);

			return el( 'div', { key: index }, header, details );
		} );

		return el(
			PluginDocumentSettingPanel,
			{ name: 'tech-stack-panel', title: 'Tech Stack', icon: 'editor-code' },
			el( 'div', null, categoryElements ),
			el( Button, {
				variant: 'secondary',
				onClick: addCategory,
				style: { marginTop: '12px', width: '100%' },
			}, '+ Add Category' )
		);
	}

	registerPlugin( 'tech-stack-sidebar', {
		render: TechStackPanel,
	} );
} )( window.wp );
