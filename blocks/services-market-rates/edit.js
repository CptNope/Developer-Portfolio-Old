( function( wp ) {
	var el = wp.element.createElement;
	var useBlockProps = wp.blockEditor.useBlockProps;
	var InspectorControls = wp.blockEditor.InspectorControls;
	var PanelBody = wp.components.PanelBody;
	var TextControl = wp.components.TextControl;
	var Button = wp.components.Button;
	var registerBlockType = wp.blocks.registerBlockType;

	function MarketRatesEdit( props ) {
		var attributes = props.attributes;
		var setAttributes = props.setAttributes;

		var cards = [];
		try { cards = JSON.parse( attributes.cards ); } catch( e ) { cards = []; }

		function updateCard( index, key, value ) {
			var updated = cards.slice();
			updated[ index ] = Object.assign( {}, updated[ index ] );
			updated[ index ][ key ] = value;
			setAttributes( { cards: JSON.stringify( updated ) } );
		}

		function addCard() {
			var updated = cards.concat( { label: 'New Rate', range: '$0/hr', note: 'Description' } );
			setAttributes( { cards: JSON.stringify( updated ) } );
		}

		function removeCard( index ) {
			var updated = cards.filter( function( _, i ) { return i !== index; } );
			setAttributes( { cards: JSON.stringify( updated ) } );
		}

		var blockProps = useBlockProps( { className: 'svc-market-bar' } );

		return el( 'div', blockProps,
			el( InspectorControls, null,
				el( PanelBody, { title: 'Market Rate Cards', initialOpen: true },
					cards.map( function( card, i ) {
						return el( 'div', { key: i, style: { marginBottom: '16px', paddingBottom: '16px', borderBottom: '1px solid #333' } },
							el( 'p', { style: { fontWeight: 600, fontSize: '12px', marginBottom: '4px' } }, 'Card ' + ( i + 1 ) ),
							el( TextControl, { label: 'Label', value: card.label, onChange: function( v ) { updateCard( i, 'label', v ); } } ),
							el( TextControl, { label: 'Range', value: card.range, onChange: function( v ) { updateCard( i, 'range', v ); } } ),
							el( TextControl, { label: 'Note', value: card.note, onChange: function( v ) { updateCard( i, 'note', v ); } } ),
							el( Button, { isDestructive: true, isSmall: true, onClick: function() { removeCard( i ); } }, 'Remove' )
						);
					} ),
					el( Button, { isSecondary: true, isSmall: true, onClick: addCard, style: { marginTop: '8px' } }, '+ Add Card' )
				)
			),
			el( 'div', { className: 'svc-market-inner' },
				cards.map( function( card, i ) {
					return el( 'div', { key: i, className: 'svc-market-card' },
						el( 'div', { className: 'svc-market-label' }, card.label ),
						el( 'div', { className: 'svc-market-range' }, card.range ),
						el( 'div', { className: 'svc-market-note' }, card.note )
					);
				} )
			)
		);
	}

	registerBlockType( 'developer-portfolio/services-market-rates', {
		edit: MarketRatesEdit,
		save: function() { return null; },
	} );
} )( window.wp );
