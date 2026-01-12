//import { __ } from '@wordpress/i18n';
import { useState } from '@wordpress/element';
import { useSelect } from '@wordpress/data';
import { useBlockProps } from '@wordpress/block-editor';
import 'bootstrap/dist/css/bootstrap.min.css';
import './editor.scss';
import { TextControl, SelectControl } from '@wordpress/components';

export default function Edit() {
	const [ search, setSearch ] = useState( '' );
	const [ sortBy, setSortBy ] = useState( 'address' );
	const [ sortOrder, setSortOrder ] = useState( 'asc' );

	const posts = useSelect( ( select ) =>
		select( 'core' ).getEntityRecords( 'postType', 'gas-station', {
			per_page: -1,
		} )
	);

	const safePosts = Array.isArray( posts ) ? posts : [];

	const filteredPosts = safePosts.filter( ( post ) => {
		const address = String(
			post.meta?.[ 'gas-station_address' ] || ''
		).toLowerCase();

		return address.includes( search.toLowerCase() );
	} );

	const sortedPosts = [ ...filteredPosts ].sort( ( a, b ) => {
		let valueA;
		let valueB;

		switch ( sortBy ) {
			case 'id':
				valueA = Number( a.meta?.[ 'gas-station_object_id' ] || 0 );
				valueB = Number( b.meta?.[ 'gas-station_object_id' ] || 0 );
				break;

			case 'distance':
				// пример: используем X как "distance"
				// valueA = Number( a.meta?.['gas-station_geometry_x'] || 0 );
				// valueB = Number( b.meta?.['gas-station_geometry_x'] || 0 );
				break;

			case 'address':
			default:
				valueA = String(
					a.meta?.[ 'gas-station_address' ] || ''
				).toLowerCase();
				valueB = String(
					b.meta?.[ 'gas-station_address' ] || ''
				).toLowerCase();
		}

		if ( valueA < valueB ) return sortOrder === 'asc' ? -1 : 1;
		if ( valueA > valueB ) return sortOrder === 'asc' ? 1 : -1;
		return 0;
	} );

	return (
		<div
			{ ...useBlockProps( {
				className: `container border border-info rounded-1 bg-light`,
			} ) }
		>
			{ /* --- Controls --- */ }
			<div className="border-bottom border-info p-1">
				<TextControl
					label="Search by address"
					value={ search }
					onChange={ setSearch }
					placeholder="Enter address..."
				/>
				<SelectControl
					label="Sort by"
					value={ sortBy }
					options={ [
						{ label: 'Address', value: 'address' },
						// { label: 'Distance', value: 'distance' },
						{ label: 'ID', value: 'id' },
					] }
					onChange={ setSortBy }
				/>

				<SelectControl
					label="Order"
					value={ sortOrder }
					options={ [
						{ label: 'Ascending', value: 'asc' },
						{ label: 'Descending', value: 'desc' },
					] }
					onChange={ setSortOrder }
				/>
			</div>

			{ /* --- Content --- */ }
			{ ! posts && <p>Loading…</p> }

			{ posts && posts.length === 0 && (
				<p>No posts found for this post type.</p>
			) }
			{ posts && posts.length > 0 && (
				<div className="gas-stations-grid row w-100 m-0 d-flex flex-wrap">
					{ sortedPosts.map( ( post ) => {
						const meta = post.meta || {};
						return (
							<div key={ post.id } className="col-4 p-1">
								<div className="card border-info mb-3 p-0">
									{ /* <img src="..." class="card-img-top" alt="..."> */ }
									<div
										className="card-header"
										dangerouslySetInnerHTML={ {
											__html: post.title.rendered,
										} }
									/>
									<div className="card-body">
										{ meta[ 'gas-station_address' ] && (
											<h5 className="card-title">
												<strong>Address:</strong>{ ' ' }
												{
													meta[
														'gas-station_address'
													]
												}
											</h5>
										) }
										{ meta[ 'gas-station_geometry_x' ] && (
											<p className="card-text">
												<strong>X:</strong>{ ' ' }
												{
													meta[
														'gas-station_geometry_x'
													]
												}
											</p>
										) }
										{ meta[ 'gas-station_geometry_y' ] && (
											<p className="card-text">
												<strong>Y:</strong>{ ' ' }
												{
													meta[
														'gas-station_geometry_y'
													]
												}
											</p>
										) }
									</div>
								</div>
							</div>
						);
					} ) }
				</div>
			) }
		</div>
	);
}
