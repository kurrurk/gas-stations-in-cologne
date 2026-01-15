<?php

if (! defined('ABSPATH')) {
	exit; // Exit if accessed directly.
}

if (! class_exists('Gas_Stations_Block_List')) {
	class Gas_Stations_Block_List
	{

		public function __construct()
		{
			//Registriert einen neuen Gutenberg Block.
			add_action('init', array($this, 'create_block_gas_stations_in_cologne_block_init'));

			//Erstellt einen REST-Endpunkt für den JSON-Download zur dynamischen Block-Generierung
			add_action('rest_api_init', array($this, 'gas_stations_rest_api_init'));

			//Bindet die benötigten Styles und Skripte für den Gutenberg Block ein.
			add_action('enqueue_block_assets', array($this, 'enqueue_block_assets'), 999);
		}

		public function create_block_gas_stations_in_cologne_block_init()
		{

			register_block_type(
				GAS_STATIONS_PATH,
				array('render_callback' => array($this, 'render_gas_stations_block'))
			);
		}

		function render_gas_stations_block($attributes)
		{

			$columns = isset($attributes['columns'])
				? (int) $attributes['columns']
				: 4;

			$showMap = isset($attributes['showMap'])
				? (int) $attributes['showMap']
				: false;

			$wrapper_attributes = get_block_wrapper_attributes([
				'class' => 'wp-block-gas-stations-list border border-info rounded-1 bg-light',
			]);

			$query = new WP_Query([
				'post_type'      => 'gas-station',
				'posts_per_page' => -1,
			]);

			if (! $query->have_posts()) {
				return '<p>No gas stations found.</p>';
			}

			ob_start();

			require(GAS_STATIONS_PATH . 'views/gas_stations_block.php');

			wp_reset_postdata();

			return ob_get_clean();
		}

		public function enqueue_block_assets()
		{
			if (! has_block('create-block/gas-stations-in-cologne')) {
				return;
			}

			wp_enqueue_style('gas-stations-bootstrap-css');
			wp_enqueue_style('gas-stations-style-css');
			wp_enqueue_script('gas-stations-js');
		}

		//Erstellt einen REST-Endpunkt
		public function gas_stations_rest_api_init()
		{

			register_rest_route('gas-stations/v1', '/data', [
				'methods'  => 'GET',
				'callback' => array($this, 'gas_stations_rest_data'),
				'permission_callback' => '__return_true',
			]);
		}

		// Erstellt ein sortiertes Objekt-Array zur Generierung der Karten.
		private function get_gas_stations_data(WP_REST_Request $request)
		{

			$search     = sanitize_text_field($request->get_param('search'));
			$sort_by    = sanitize_text_field($request->get_param('sortBy'));
			$sort_order = sanitize_text_field($request->get_param('sortOrder'));

			$address = sanitize_text_field($request->get_param('address'));

			$user_lat = null;
			$user_lng = null;

			if ($address) {
				$coords = $this->get_coords_from_address($address);
				if ($coords) {
					$user_lat = $coords['lat'];
					$user_lng = $coords['lng'];
				}
			}

			$args = [
				'post_type'      => 'gas-station',
				'posts_per_page' => -1,
				'meta_query'     => [],
			];

			if ($search) {
				$args['meta_query'][] = [
					'key'     => 'gas-station_address',
					'value'   => $search,
					'compare' => 'LIKE',
				];
			}

			$query = new WP_Query($args);

			$data = [];

			if ($query->have_posts()) {
				while ($query->have_posts()) {
					$query->the_post();

					$lat = (float) get_post_meta(get_the_ID(), 'gas-station_geometry_y', true);
					$lng = (float) get_post_meta(get_the_ID(), 'gas-station_geometry_x', true);

					$item = [
						'id'        => get_the_ID(),
						'title'     => get_the_title(),
						'address'   => get_post_meta(get_the_ID(), 'gas-station_address', true),
						'lat'       => $lat,
						'lng'       => $lng,
						'object_id' => (int) get_post_meta(get_the_ID(), 'gas-station_object_id', true),
						'distance' => null,
					];

					if (is_numeric($user_lat) && is_numeric($user_lng)) {
						$item['distance'] = $this->calculate_distance_km(
							$user_lat,
							$user_lng,
							$lat,
							$lng
						);
					}

					$data[] = $item;
				}
			}

			wp_reset_postdata();

			if ($sort_by === 'distance' && $user_lat && $user_lng) {

				usort($data, function ($a, $b) use ($sort_order) {
					if ($a['distance'] === null) return 1;
					if ($b['distance'] === null) return -1;

					return $sort_order === 'desc'
						? $b['distance'] <=> $a['distance']
						: $a['distance'] <=> $b['distance'];
				});
			} else {

				$key = $sort_by === 'id' ? 'object_id' : 'address';

				usort($data, function ($a, $b) use ($key, $sort_order) {
					return $sort_order === 'desc'
						? $b[$key] <=> $a[$key]
						: $a[$key] <=> $b[$key];
				});
			}

			return $data;
		}

		// Wandelt das Array in JSON um und sendet es an den Endpoint.
		public function gas_stations_rest_data(WP_REST_Request $request)
		{
			$data = $this->get_gas_stations_data($request);
			return rest_ensure_response($data);
		}

		// Berechnet die Entfernung zwischen zwei Punkten auf der Erde.
		private function calculate_distance_km($lat1, $lng1, $lat2, $lng2)
		{
			$earth_radius = 6371; // km

			$dLat = deg2rad($lat2 - $lat1);
			$dLng = deg2rad($lng2 - $lng1);

			$a = sin($dLat / 2) * sin($dLat / 2) +
				cos(deg2rad($lat1)) *
				cos(deg2rad($lat2)) *
				sin($dLng / 2) * sin($dLng / 2);

			$c = 2 * atan2(sqrt($a), sqrt(1 - $a));

			return $earth_radius * $c;
		}

		// Ruft Geokoordinaten über die Google API ab.
		private function get_coords_from_address($address)
		{

			$api_key = 'YOUR_API_KEY';

			$url = add_query_arg(
				[
					'address' => urlencode($address),
					'key'     => $api_key,
				],
				'https://maps.googleapis.com/maps/api/geocode/json'
			);

			$response = wp_remote_get($url);

			if (is_wp_error($response)) {
				return null;
			}

			$data = json_decode(wp_remote_retrieve_body($response), true);

			if (empty($data['results'][0]['geometry']['location'])) {
				return null;
			}

			return [
				'lat' => $data['results'][0]['geometry']['location']['lat'],
				'lng' => $data['results'][0]['geometry']['location']['lng'],
			];
		}
	}
}
