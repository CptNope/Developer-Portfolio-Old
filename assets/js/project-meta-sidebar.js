( function( wp ) {
	const { registerPlugin } = wp.plugins;
	const { PluginDocumentSettingPanel } = wp.editPost;
	const { TextControl } = wp.components;
	const { useSelect, useDispatch } = wp.data;
	const { useEntityProp } = wp.coreData;
	const el = wp.element.createElement;

	function ProjectMetaPanel() {
		const postType = useSelect( function( select ) {
			return select( 'core/editor' ).getCurrentPostType();
		}, [] );

		const [ meta, setMeta ] = useEntityProp( 'postType', postType, 'meta' );

		if ( postType !== 'project' ) {
			return null;
		}

		return el(
			PluginDocumentSettingPanel,
			{
				name: 'project-meta-panel',
				title: 'Project Details',
				icon: 'portfolio',
			},
			el( TextControl, {
				label: 'Subtitle',
				help: 'Short tagline shown below the title (e.g. "AI-Assisted Training Platform")',
				value: meta.project_subtitle || '',
				onChange: function( val ) {
					setMeta( Object.assign( {}, meta, { project_subtitle: val } ) );
				},
			} ),
			el( TextControl, {
				label: 'GitHub URL',
				help: 'Full URL to the GitHub repository',
				value: meta.project_github_url || '',
				onChange: function( val ) {
					setMeta( Object.assign( {}, meta, { project_github_url: val } ) );
				},
			} ),
			el( TextControl, {
				label: 'Live Site URL',
				help: 'Full URL to the live project',
				value: meta.project_live_url || '',
				onChange: function( val ) {
					setMeta( Object.assign( {}, meta, { project_live_url: val } ) );
				},
			} )
		);
	}

	registerPlugin( 'project-meta-sidebar', {
		render: ProjectMetaPanel,
	} );
} )( window.wp );
