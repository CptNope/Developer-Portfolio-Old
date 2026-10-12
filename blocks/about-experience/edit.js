( function( wp ) {
	var el = wp.element.createElement;
	var useBlockProps = wp.blockEditor.useBlockProps;
	var InspectorControls = wp.blockEditor.InspectorControls;
	var PanelBody = wp.components.PanelBody;
	var TextControl = wp.components.TextControl;
	var TextareaControl = wp.components.TextareaControl;
	var Button = wp.components.Button;
	var registerBlockType = wp.blocks.registerBlockType;

	function parseJSON( str, fallback ) {
		try { return JSON.parse( str ); } catch( e ) { return fallback; }
	}

	function AboutExperienceEdit( props ) {
		var a = props.attributes;
		var set = props.setAttributes;
		var blockProps = useBlockProps( { className: 'about-experience-block' } );

		var jobs = parseJSON( a.jobs, [] );
		var eduItems = parseJSON( a.eduItems, [] );
		var achieveItems = parseJSON( a.achieveItems, [] );

		/* --- helpers to update JSON arrays --- */
		function setJobs( newJobs ) { set( { jobs: JSON.stringify( newJobs ) } ); }
		function setEduItems( newItems ) { set( { eduItems: JSON.stringify( newItems ) } ); }
		function setAchieveItems( newItems ) { set( { achieveItems: JSON.stringify( newItems ) } ); }

		function updateJob( idx, key, val ) {
			var copy = jobs.slice();
			copy[ idx ] = Object.assign( {}, copy[ idx ] );
			copy[ idx ][ key ] = val;
			setJobs( copy );
		}
		function addJob() {
			setJobs( jobs.concat( { role: '', period: '', company: '', description: '' } ) );
		}
		function removeJob( idx ) {
			setJobs( jobs.filter( function( _, i ) { return i !== idx; } ) );
		}

		function updateEdu( idx, key, val ) {
			var copy = eduItems.slice();
			copy[ idx ] = Object.assign( {}, copy[ idx ] );
			copy[ idx ][ key ] = val;
			setEduItems( copy );
		}
		function addEdu() {
			setEduItems( eduItems.concat( { title: '', subtitle: '' } ) );
		}
		function removeEdu( idx ) {
			setEduItems( eduItems.filter( function( _, i ) { return i !== idx; } ) );
		}

		function updateAchieve( idx, key, val ) {
			var copy = achieveItems.slice();
			copy[ idx ] = Object.assign( {}, copy[ idx ] );
			copy[ idx ][ key ] = val;
			setAchieveItems( copy );
		}
		function addAchieve() {
			setAchieveItems( achieveItems.concat( { title: '', subtitle: '' } ) );
		}
		function removeAchieve( idx ) {
			setAchieveItems( achieveItems.filter( function( _, i ) { return i !== idx; } ) );
		}

		/* --- Inspector panels --- */
		var headingPanel = el( PanelBody, { title: 'Heading', initialOpen: true },
			el( TextControl, { label: 'Section Label', value: a.sectionLabel, onChange: function( v ) { set( { sectionLabel: v } ); } } ),
			el( TextControl, { label: 'Heading', value: a.heading, onChange: function( v ) { set( { heading: v } ); } } ),
			el( TextControl, { label: 'Highlight Word', help: 'Gets gradient styling.', value: a.headingHighlight, onChange: function( v ) { set( { headingHighlight: v } ); } } )
		);

		var jobPanels = jobs.map( function( job, idx ) {
			return el( PanelBody, { key: 'job-' + idx, title: 'Job ' + ( idx + 1 ) + ( job.role ? ': ' + job.role : '' ), initialOpen: false },
				el( TextControl, { label: 'Role', value: job.role, onChange: function( v ) { updateJob( idx, 'role', v ); } } ),
				el( TextControl, { label: 'Period', value: job.period, onChange: function( v ) { updateJob( idx, 'period', v ); } } ),
				el( TextControl, { label: 'Company', value: job.company, onChange: function( v ) { updateJob( idx, 'company', v ); } } ),
				el( TextareaControl, { label: 'Description', value: job.description, onChange: function( v ) { updateJob( idx, 'description', v ); } } ),
				el( Button, { variant: 'secondary', isDestructive: true, onClick: function() { removeJob( idx ); }, style: { marginTop: '8px' } }, 'Remove Job' )
			);
		} );

		var eduPanel = el( PanelBody, { title: 'Education (' + eduItems.length + ')', initialOpen: false },
			el( TextControl, { label: 'Card Emoji', value: a.eduEmoji, onChange: function( v ) { set( { eduEmoji: v } ); } } ),
			el( TextControl, { label: 'Card Title', value: a.eduTitle, onChange: function( v ) { set( { eduTitle: v } ); } } ),
			eduItems.map( function( item, idx ) {
				return el( 'div', { key: 'edu-' + idx, style: { borderTop: idx > 0 ? '1px solid rgba(139,92,246,0.15)' : 'none', paddingTop: idx > 0 ? '12px' : '0', marginTop: idx > 0 ? '12px' : '0' } },
					el( TextControl, { label: 'Title ' + ( idx + 1 ), value: item.title, onChange: function( v ) { updateEdu( idx, 'title', v ); } } ),
					el( TextControl, { label: 'Subtitle ' + ( idx + 1 ), value: item.subtitle, onChange: function( v ) { updateEdu( idx, 'subtitle', v ); } } ),
					el( Button, { variant: 'secondary', isDestructive: true, onClick: function() { removeEdu( idx ); }, size: 'small' }, 'Remove' )
				);
			} ),
			el( Button, { variant: 'secondary', onClick: addEdu, style: { marginTop: '12px' } }, '+ Add Education Item' )
		);

		var achievePanel = el( PanelBody, { title: 'Achievements (' + achieveItems.length + ')', initialOpen: false },
			el( TextControl, { label: 'Card Emoji', value: a.achieveEmoji, onChange: function( v ) { set( { achieveEmoji: v } ); } } ),
			el( TextControl, { label: 'Card Title', value: a.achieveTitle, onChange: function( v ) { set( { achieveTitle: v } ); } } ),
			achieveItems.map( function( item, idx ) {
				return el( 'div', { key: 'ach-' + idx, style: { borderTop: idx > 0 ? '1px solid rgba(139,92,246,0.15)' : 'none', paddingTop: idx > 0 ? '12px' : '0', marginTop: idx > 0 ? '12px' : '0' } },
					el( TextControl, { label: 'Title ' + ( idx + 1 ), value: item.title, onChange: function( v ) { updateAchieve( idx, 'title', v ); } } ),
					el( TextControl, { label: 'Subtitle ' + ( idx + 1 ), value: item.subtitle, onChange: function( v ) { updateAchieve( idx, 'subtitle', v ); } } ),
					el( Button, { variant: 'secondary', isDestructive: true, onClick: function() { removeAchieve( idx ); }, size: 'small' }, 'Remove' )
				);
			} ),
			el( Button, { variant: 'secondary', onClick: addAchieve, style: { marginTop: '12px' } }, '+ Add Achievement' )
		);

		/* --- Block Preview --- */
		var timelinePreview = el( 'div', { className: 'experience-timeline' },
			jobs.map( function( job, idx ) {
				return el( 'div', { key: idx, className: 'timeline-item' },
					el( 'div', { className: 'exp-card' },
						el( 'div', { className: 'exp-card-header' },
							el( 'span', { className: 'exp-role' }, job.role ),
							el( 'span', { className: 'exp-period' }, job.period )
						),
						el( 'p', { className: 'exp-company' }, '💼 ' + job.company ),
						el( 'p', { className: 'exp-desc' }, job.description )
					)
				);
			} )
		);

		function renderInfoCard( emoji, title, items ) {
			return el( 'div', { className: 'edu-achieve-card' },
				el( 'div', { className: 'edu-achieve-header' },
					el( 'span', { className: 'edu-achieve-emoji' }, emoji ),
					el( 'span', { className: 'edu-achieve-title' }, title )
				),
				items.map( function( item, idx ) {
					return el( 'div', { key: idx, className: 'edu-achieve-item' },
						el( 'p', { className: 'edu-achieve-item-title' }, item.title ),
						el( 'p', { className: 'edu-achieve-item-sub' }, item.subtitle )
					);
				} )
			);
		}

		return el( 'div', blockProps,
			el( InspectorControls, null,
				headingPanel,
				el( PanelBody, { title: 'Jobs (' + jobs.length + ')', initialOpen: false },
					el( 'p', { style: { fontSize: '12px', color: '#999', marginBottom: '12px' } }, 'Expand individual job panels below to edit.' ),
					el( Button, { variant: 'secondary', onClick: addJob }, '+ Add Job' )
				),
				jobPanels,
				eduPanel,
				achievePanel
			),

			el( 'div', { className: 'about-exp-header' },
				el( 'p', { className: 'about-exp-label has-primary-color has-text-color has-jetbrains-mono-font-family' }, a.sectionLabel ),
				el( 'h2', { className: 'about-exp-heading wp-block-heading' }, a.heading )
			),

			timelinePreview,

			el( 'div', { className: 'edu-achieve-grid' },
				renderInfoCard( a.eduEmoji, a.eduTitle, eduItems ),
				renderInfoCard( a.achieveEmoji, a.achieveTitle, achieveItems )
			)
		);
	}

	registerBlockType( 'developer-portfolio/about-experience', {
		edit: AboutExperienceEdit,
		save: function() { return null; },
	} );
} )( window.wp );
