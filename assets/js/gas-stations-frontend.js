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

		let debounceTimer = null;

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

		const triggerUpdate = () => {
			const params = {
				search: searchInput.value,
				sortBy: sortBySelect.value,
				sortOrder: sortOrderSelect.value,
			};

			loadResults( params );
		};

		const debouncedTrigger = () => {
			clearTimeout( debounceTimer );
			debounceTimer = setTimeout( triggerUpdate, 300 );
		};

		searchInput.addEventListener( 'input', debouncedTrigger );

		sortBySelect.addEventListener( 'change', triggerUpdate );
		sortOrderSelect.addEventListener( 'change', triggerUpdate );

		triggerUpdate();
	} );
} );
