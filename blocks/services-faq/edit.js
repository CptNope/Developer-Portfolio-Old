( function( wp ) {
	var el = wp.element.createElement;
	var useBlockProps = wp.blockEditor.useBlockProps;
	var InspectorControls = wp.blockEditor.InspectorControls;
	var PanelBody = wp.components.PanelBody;
	var TextControl = wp.components.TextControl;
	var TextareaControl = wp.components.TextareaControl;
	var Button = wp.components.Button;
	var registerBlockType = wp.blocks.registerBlockType;

	function FaqEdit( props ) {
		var attributes = props.attributes;
		var setAttributes = props.setAttributes;

		var items = [];
		try { items = JSON.parse( attributes.items ); } catch( e ) {}

		function updateItem( index, key, value ) {
			var updated = items.slice();
			updated[ index ] = Object.assign( {}, updated[ index ] );
			updated[ index ][ key ] = value;
			setAttributes( { items: JSON.stringify( updated ) } );
		}

		function addItem() {
			var updated = items.concat( { q: 'New question?', a: 'Answer here.' } );
			setAttributes( { items: JSON.stringify( updated ) } );
		}

		function removeItem( index ) {
			setAttributes( { items: JSON.stringify( items.filter( function( _, i ) { return i !== index; } ) ) } );
		}

		var blockProps = useBlockProps( { className: 'svc-section' } );

		return el( 'div', blockProps,
			el( InspectorControls, null,
				el( PanelBody, { title: 'Section Settings', initialOpen: true },
					el( TextControl, {
						label: 'Section Tag',
						value: attributes.sectionTag,
						onChange: function( val ) { setAttributes( { sectionTag: val } ); },
					} ),
					el( TextControl, {
						label: 'Heading',
						value: attributes.heading,
						onChange: function( val ) { setAttributes( { heading: val } ); },
					} ),
					el( TextareaControl, {
						label: 'Intro Text',
						value: attributes.intro,
						onChange: function( val ) { setAttributes( { intro: val } ); },
					} )
				),
				el( PanelBody, { title: 'FAQ Items', initialOpen: false },
					items.map( function( item, i ) {
						return el( 'div', { key: i, style: { marginBottom: '16px', paddingBottom: '16px', borderBottom: '1px solid #333' } },
							el( 'p', { style: { fontWeight: 600, fontSize: '12px', marginBottom: '4px' } }, 'Q' + ( i + 1 ) ),
							el( TextControl, { label: 'Question', value: item.q, onChange: function( v ) { updateItem( i, 'q', v ); } } ),
							el( TextareaControl, { label: 'Answer', value: item.a, onChange: function( v ) { updateItem( i, 'a', v ); } } ),
							el( Button, { isDestructive: true, isSmall: true, onClick: function() { removeItem( i ); } }, 'Remove' )
						);
					} ),
					el( Button, { isSecondary: true, isSmall: true, onClick: addItem, style: { marginTop: '8px' } }, '+ Add FAQ' )
				)
			),
			el( 'div', { className: 'svc-section-inner' },
				el( 'span', { className: 'svc-section-tag svc-tag-rate' }, attributes.sectionTag ),
				el( 'h2', { className: 'svc-section-heading' }, attributes.heading ),
				el( 'p', { className: 'svc-section-intro' }, attributes.intro ),
				el( 'div', { className: 'svc-faq-list', style: { marginTop: '24px' } },
					items.map( function( item, i ) {
						return el( 'div', { key: i, className: 'svc-faq-item' },
							el( 'div', { className: 'svc-faq-q' }, item.q ),
							el( 'div', { className: 'svc-faq-a' }, item.a )
						);
					} )
				)
			)
		);
	}

	registerBlockType( 'developer-portfolio/services-faq', {
		edit: FaqEdit,
		save: function() { return null; },
	} );
} )( window.wp );
