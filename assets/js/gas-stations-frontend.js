document.addEventListener( 'DOMContentLoaded', () => {
	const blocks = document.querySelectorAll( '.wp-block-gas-stations-list' );

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
		const loadResults = async ( params ) => {
			results.innerHTML = '<p>Loading…</p>';

			const url = new URL(
				'/wp-json/gas-stations/v1/filter',
				window.location.origin
			);
			Object.keys( params ).forEach( ( key ) =>
				url.searchParams.append( key, params[ key ] )
			);

			url.searchParams.append( 'columns', columns );

			const response = await fetch( url );
			const html = await response.text();

			results.innerHTML = html
				.replace( /^["']|["']$/g, '' )
				.replace( /\\n/g, '' )
				.replace( /\\t/g, '' )
				.replace( /\\\//g, '/' )
				.replace( /\\"/g, '"' );
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

			// const uRLSearchParams = new URLSearchParams( params );

			// HTML
			loadResults( params );

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
} );
