<?php

if (! defined('ABSPATH')) {
	exit; // Exit if accessed directly.
}

if (! class_exists('Gas_Stations_Block_List')) {
	class Gas_Stations_Block_List
	{

		public function __construct()
		{
			add_action('init', array($this, 'create_block_gas_stations_in_cologne_block_init'));
			add_action('rest_api_init', array($this, 'gas_stations_rest_api_init'));

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

			require_once(GAS_STATIONS_PATH . 'views/gas_stations_block.php');

			wp_reset_postdata();

			return ob_get_clean();
		}

		public function enqueue_block_assets()
		{
			if (! has_block('create-block/gas-stations-in-cologne')) {
				return;
			}

			wp_enqueue_style('gas-stations-bootstrap-css');
			wp_enqueue_script('gas-stations-js');
		}

		public function gas_stations_rest_api_init()
		{
			register_rest_route('gas-stations/v1', '/filter', [
				'methods'  => 'GET',
				'callback' =>  array($this, 'gas_stations_rest_filter'),
			]);
		}

		public function gas_stations_rest_filter(WP_REST_Request $request)
		{

			$search     = sanitize_text_field($request->get_param('search'));
			$sort_by    = sanitize_text_field($request->get_param('sortBy'));
			$sort_order = sanitize_text_field($request->get_param('sortOrder'));
			$columns    = (int) $request->get_param('columns');

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

			if ($sort_by === 'id') {
				$args['orderby']  = 'meta_value_num';
				$args['meta_key'] = 'gas-station_object_id';
			} else {
				$args['orderby']  = 'meta_value';
				$args['meta_key'] = 'gas-station_address';
			}

			$args['order'] = strtoupper($sort_order) === 'DESC' ? 'DESC' : 'ASC';

			$query = new WP_Query($args);

			ob_start();

			if ($query->have_posts()) {
				while ($query->have_posts()) {
					$query->the_post();

					$address = get_post_meta(get_the_ID(), 'gas-station_address', true);
					$x = get_post_meta(get_the_ID(), 'gas-station_geometry_x', true);
					$y = get_post_meta(get_the_ID(), 'gas-station_geometry_y', true);

					require(GAS_STATIONS_PATH .  'views/gas_stations_card.php');
				}
			} else {
				echo '<p>No results found.</p>';
			}

			wp_reset_postdata();

			return ob_get_clean();
		}
	}
}
