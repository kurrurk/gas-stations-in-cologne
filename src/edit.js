import { __ } from '@wordpress/i18n';
import { useState, useEffect, useRef } from '@wordpress/element';
import { useSelect } from '@wordpress/data';
import {
	useBlockProps,
	InspectorControls,
	BlockControls,
} from '@wordpress/block-editor';
import 'bootstrap/dist/css/bootstrap.min.css';
import './editor.scss';
import { TextControl, SelectControl, PanelBody } from '@wordpress/components';

export default function Edit( { attributes, setAttributes } ) {
	const mapRef = useRef( null );

	const { columns, showMap } = attributes;
	const onChangeColumns = ( newColumns ) => {
		setAttributes( { columns: newColumns } );
	};
	const toggleShowMap = () => {
		setAttributes( { showMap: ! showMap } );
	};

	const [ sortAddress, setSortAddress ] = useState( '' );
	const [ coords, setCoords ] = useState( '' );
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

	function getDistanceKm( lat1, lng1, lat2, lng2 ) {
		const R = 6371;

		const dLat = ( ( lat2 - lat1 ) * Math.PI ) / 180;
		const dLng = ( ( lng2 - lng1 ) * Math.PI ) / 180;

		const a =
			Math.sin( dLat / 2 ) * Math.sin( dLat / 2 ) +
			Math.cos( ( lat1 * Math.PI ) / 180 ) *
				Math.cos( ( lat2 * Math.PI ) / 180 ) *
				Math.sin( dLng / 2 ) *
				Math.sin( dLng / 2 );

		const c = 2 * Math.atan2( Math.sqrt( a ), Math.sqrt( 1 - a ) );

		return R * c;
	}

	const sortedPosts = [ ...filteredPosts ].sort( ( a, b ) => {
		let valueA;
		let valueB;

		switch ( sortBy ) {
			case 'id':
				valueA = Number( a.meta?.[ 'gas-station_object_id' ] || 0 );
				valueB = Number( b.meta?.[ 'gas-station_object_id' ] || 0 );
				break;

			case 'distance':
				if ( coords ) {
					valueA = getDistanceKm(
						coords.lat,
						coords.lng,
						Number( a.meta?.[ 'gas-station_geometry_y' ] || 0 ),
						Number( a.meta?.[ 'gas-station_geometry_x' ] || 0 )
					);

					valueB = getDistanceKm(
						coords.lat,
						coords.lng,
						Number( b.meta?.[ 'gas-station_geometry_y' ] || 0 ),
						Number( b.meta?.[ 'gas-station_geometry_x' ] || 0 )
					);
				}
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

	const fetchCoords = async ( value ) => {
		if ( ! value ) return;

		try {
			const res = await fetch(
				`https://maps.googleapis.com/maps/api/geocode/json?address=${ encodeURIComponent(
					value
				) }&key=AIzaSyCAsek5OKF19JGZuOlAeic5HouACN1A6fw`
			);

			const data = await res.json();

			if ( ! data.results || ! data.results.length ) {
				throw new Error( 'Address not found' );
			}

			const location = data.results[ 0 ].geometry.location;

			setCoords( {
				lat: location.lat,
				lng: location.lng,
			} );
		} catch ( err ) {
			setCoords( null );
		}
	};

	let debounceTimer;
	const handleChange = ( value ) => {
		setSortAddress( value );

		clearTimeout( debounceTimer );
		debounceTimer = setTimeout( () => {
			fetchCoords( value );
		}, 500 );
	};

	useEffect( () => {
		if ( ! window.google || ! mapRef.current || ! showMap ) return;

		const center = { lat: 50.9375, lng: 6.9603 };

		const map = new window.google.maps.Map( mapRef.current, {
			zoom: 11,
			center,
		} );

		sortedPosts.forEach( ( station ) => {
			const marker = new window.google.maps.Marker( {
				position: {
					lat: Number( station.meta[ 'gas-station_geometry_y' ] ),
					lng: Number( station.meta[ 'gas-station_geometry_x' ] ),
				},
				map,
			} );

			marker.addListener( 'click', () => {
				const card = document.getElementById(
					`station-${ station.id }`
				);

				if ( card ) {
					card.scrollIntoView( {
						behavior: 'smooth',
						block: 'start',
					} );
				}
			} );
		} );
	}, [ sortedPosts, showMap ] );

	return (
		<>
			<InspectorControls>
				<PanelBody title="Layout">
					<SelectControl
						label="Columns"
						value={ columns }
						options={ [
							{ label: '1 column', value: 12 },
							{ label: '2 columns', value: 6 },
							{ label: '3 columns', value: 4 },
							{ label: '4 columns', value: 3 },
							{ label: '6 columns', value: 2 },
						] }
						onChange={ onChangeColumns }
					/>
				</PanelBody>
			</InspectorControls>
			<BlockControls
				controls={ [
					{
						icon: 'location-alt',
						title: __( 'Show Map', 'gas-stations' ),
						onClick: toggleShowMap,
						isActive: showMap,
					},
				] }
			></BlockControls>
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
							{ label: 'Distance', value: 'distance' },
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
					{ sortBy === 'distance' && (
						<TextControl
							label="Address to calculate distance"
							value={ sortAddress }
							onChange={ handleChange }
							placeholder="Enter address..."
						/>
					) }
				</div>
				{ /* --- Content --- */ }
				{ ! posts && <p>Loading…</p> }
				{ posts && safePosts.length === 0 && (
					<p>No posts found for this post type.</p>
				) }
				{ showMap && (
					<div ref={ mapRef } style={ { height: '400px' } } />
				) }
				{ posts && safePosts.length > 0 && (
					<div className="gas-stations-grid row w-100 m-0 d-flex flex-wrap">
						{ sortedPosts.map( ( post ) => {
							const meta = post.meta || {};
							return (
								<div
									key={ post.id }
									className={ `col-${ columns } p-1` }
								>
									<div
										id={ `station-${ post.id }` }
										className="card border-info mb-3 p-0"
									>
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
											{ meta[
												'gas-station_geometry_x'
											] && (
												<p className="card-text">
													<strong>X:</strong>{ ' ' }
													{
														meta[
															'gas-station_geometry_x'
														]
													}
												</p>
											) }
											{ meta[
												'gas-station_geometry_y'
											] && (
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
		</>
	);
}
