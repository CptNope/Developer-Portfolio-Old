( function( wp ) {
	var registerPlugin = wp.plugins.registerPlugin;
	var PluginDocumentSettingPanel = wp.editPost.PluginDocumentSettingPanel;
	var TextControl = wp.components.TextControl;
	var useSelect = wp.data.useSelect;
	var useEntityProp = wp.coreData.useEntityProp;
	var el = wp.element.createElement;

	function PostMetaPanel() {
		var postType = useSelect( function( select ) {
			return select( 'core/editor' ).getCurrentPostType();
		}, [] );

		var entityProps = useEntityProp( 'postType', postType, 'meta' );
		var meta = entityProps[0];
		var setMeta = entityProps[1];

		if ( postType !== 'post' ) {
			return null;
		}

		return el(
			PluginDocumentSettingPanel,
			{
				name: 'post-meta-panel',
				title: 'Post Details',
				icon: 'edit',
			},
			el( TextControl, {
				label: 'Subtitle',
				help: 'Short tagline shown below the title on the blog listing (e.g. "Deep dive into WordPress security")',
				value: meta.post_subtitle || '',
				onChange: function( val ) {
					setMeta( Object.assign( {}, meta, { post_subtitle: val } ) );
				},
			} )
		);
	}

	registerPlugin( 'post-meta-sidebar', {
		render: PostMetaPanel,
	} );
} )( window.wp );
