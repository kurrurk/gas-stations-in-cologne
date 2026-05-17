document.addEventListener( 'DOMContentLoaded', () => {
	const blocks = document.querySelectorAll(
		'.wp-block-gas-stations-list, .wp-shortcode-gas-stations-list'
	);

	blocks.forEach( ( block ) => {
		const form = block.querySelector( '.gas-filter-form' );
		const results = block.querySelector( '.gas-results' );
		const columns = block.dataset.columns;

		if ( ! form || ! results ) return;

		const searchInput = form.querySelector( 'input[name="search"]' );
		const sortBySelect = form.querySelector( 'select[name="sortBy"]' );
		const sortOrderSelect = form.querySelector(
			'select[name="sortOrder"]'
		);
		const distanceAddressInput = form.querySelector(
			'input[name="distanceAddress"]'
		);

		let debounceTimer = null;

		// ---------- LOAD HTML ----------
		const loadResults = ( params ) => {
			results.innerHTML = '';

			fetch( '/wp-json/gas-stations/v1/data?' + params.toString() )
				.then( ( res ) => res.json() )
				.then( ( stations ) => {
					stations.forEach( ( data ) => {
						const station = {
							// title: new DOMParser().parseFromString(
							// 	data.title,
							// 	'text/html'
							// ).documentElement.textContent,
							address: data.address,
							x: data.lng,
							y: data.lat,
						};
						results.appendChild(
							createGasStationCard( station, columns )
						);
					} );
				} );
		};

		// ---------- MAIN UPDATE ----------
		const triggerUpdate = async () => {
			const params = {
				search: searchInput.value,
				sortBy: sortBySelect.value,
				sortOrder: sortOrderSelect.value,
			};

			if ( distanceAddressInput?.value ) {
				params.address = distanceAddressInput.value;
			}

			const uRLSearchParams = new URLSearchParams( params );

			// HTML
			loadResults( uRLSearchParams );

			// fetch(
			// 	'/wp-json/gas-stations/v1/data?' + uRLSearchParams.toString()
			// )
			// 	.then( ( res ) => res.json() )
			// 	.then( ( data ) => {
			// 		initMap( data );
			// 	} );
		};

		const debouncedTrigger = () => {
			clearTimeout( debounceTimer );
			debounceTimer = setTimeout( triggerUpdate, 300 );
		};

		searchInput.addEventListener( 'input', debouncedTrigger );
		distanceAddressInput?.addEventListener( 'input', debouncedTrigger );

		sortBySelect.addEventListener( 'change', triggerUpdate );
		sortOrderSelect.addEventListener( 'change', triggerUpdate );

		triggerUpdate();
	} );

	// function initMap( stations ) {
	// 	if ( ! document.getElementById( 'map' ) ) {
	// 		return;
	// 	}

	// 	const center = { lat: 50.9375, lng: 6.9603 }; // Köln

	// 	const map = new google.maps.Map( document.getElementById( 'map' ), {
	// 		zoom: 11,
	// 		center: center,
	// 	} );

	// 	stations.forEach( ( station ) => {
	// 		new google.maps.Marker( {
	// 			position: { lat: station.lat, lng: station.lng },
	// 			map: map,
	// 			title: station.title,
	// 		} );
	// 	} );
	// }

	function createGasStationCard( station, columns = 4 ) {
		const wrapper = document.createElement( 'div' );
		wrapper.className = `card-wrapper col-${ columns } p-1`;

		const card = document.createElement( 'div' );
		card.className = 'card h-100 border-info mb-3 p-0';

		// header
		const header = document.createElement( 'div' );
		header.className = 'card-header';
		header.textContent = station.title;

		// body
		const body = document.createElement( 'div' );
		body.className = 'card-body';

		if ( station.address ) {
			const p = document.createElement( 'p' );
			p.innerHTML = `<strong>Address:</strong> ${ station.address }`;
			body.appendChild( p );
		}

		if ( station.x ) {
			const p = document.createElement( 'p' );
			p.innerHTML = `<strong>X:</strong> ${ station.x }`;
			body.appendChild( p );
		}

		if ( station.y ) {
			const p = document.createElement( 'p' );
			p.innerHTML = `<strong>Y:</strong> ${ station.y }`;
			body.appendChild( p );
		}

		card.appendChild( header );
		card.appendChild( body );
		wrapper.appendChild( card );

		return wrapper;
	}
} );
