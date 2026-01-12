<?php

if (! defined('ABSPATH')) {
	exit; // Exit if accessed directly.
}

if (! class_exists('Gas_Stations_Post_Type')) {
	class Gas_Stations_Post_Type
	{
		public function __construct()
		{
			add_action('init', array($this, 'create_post_type'));
			add_action('add_meta_boxes', array($this, 'add_meta_boxes'));
			add_action('save_post', array($this, 'save_post'), 10, 2);
			add_filter('manage_gas-station_posts_columns', array($this, 'gas_stations_cpt_columns'));
			add_action('manage_gas-station_posts_custom_column', array($this, 'gas_stations_custom_columns'), 10, 2);
			add_filter('manage_edit-gas-station_sortable_columns', array($this, 'gas_stations_sortable_columns'));
		}

		public function create_post_type()
		{

			register_post_type(
				'gas-station',
				array(
					'label' => 'Gas Station',
					'description' => 'Custom Post Type for Gas Stations',
					'labels' => array(
						'name' => 'Gas Stations',
						'singular_name' => 'Gas Station'
					),
					'public' => true,
					'supports' => array('title', 'editor', 'thumbnail'),
					'herearchial' => false,
					'show_ui' => true,
					'show_in_menu' => false,
					'menu_position' => 5,
					'show_in_admin_bar' => true,
					'show_in_nav_menus' => true,
					'can_export' => true,
					'has_archive' => false,
					'exclude_from_search' => false,
					'publicly_queryable' => true,
					'show_in_rest' => true,
					'menu_icon' => 'data:image/svg+xml;base64,' . base64_encode(file_get_contents(GAS_STATIONS_URL . 'assets/images/tankstelle.svg'))
					//'register_meta_box_cd' => array( $this, 'add_meta_boxes' ),
				)
			);
		}

		public function add_meta_boxes()
		{
			add_meta_box(
				'gas_station_meta_box',
				'Link Options',
				array($this, 'add_inner_meta_box'),
				'gas-station',
				'normal',
				'high'
			);
		}

		public function add_inner_meta_box($post)
		{
			require_once(GAS_STATIONS_PATH . 'views/gas-station_metabox.php');
		}

		public function save_post($post_id)
		{

			if (isset($_POST['gas-station_nonce'])) {

				if (! wp_verify_nonce($_POST['gas-station_nonce'], 'gas-station_nonce')) {
					return;
				}
			}

			if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
				return;
			}

			if (isset($_POST['post_type']) && $_POST['post_type'] != 'gas-stations') {
				if (! current_user_can('edit_page', $post_id)) {
					return;
				} elseif (! current_user_can('edit_post', $post_id)) {
					return;
				}
			}

			if (isset($_POST['action']) && $_POST['action'] === 'editpost') {

				$old_object_id = get_post_meta($post_id, 'gas-station_object_id', true);
				$new_object_id = !empty(sanitize_text_field($_POST['gas-station_object_id'])) ? sanitize_text_field($_POST['gas-station_object_id']) : 0;
				$old_address = get_post_meta($post_id, 'gas-station_address', true);
				$new_address = !empty(sanitize_text_field($_POST['gas-station_address']))  ? sanitize_text_field($_POST['gas-station_address']) : 'Enter the address.'; // esc_url_raw can also be used here
				$old_geometry_x = get_post_meta($post_id, 'gas-station_geometry_x', true);
				$new_geometry_x = !empty(sanitize_text_field($_POST['gas-station_geometry_x'])) ? sanitize_text_field($_POST['gas-station_geometry_x']) : 0;
				$old_geometry_y = get_post_meta($post_id, 'gas-station_geometry_y', true);
				$new_geometry_y = !empty(sanitize_text_field($_POST['gas-station_geometry_y']))  ? sanitize_text_field($_POST['gas-station_geometry_y']) : 0; // esc_url_raw can also be used here


				update_post_meta($post_id, 'gas-station_object_id', $new_object_id, $old_object_id);
				update_post_meta($post_id, 'gas-station_address', $new_address, $old_address);
				update_post_meta($post_id, 'gas-station_geometry_x', $new_geometry_x, $old_geometry_x);
				update_post_meta($post_id, 'gas-station_geometry_y', $new_geometry_y, $old_geometry_y);
			}
		}

		public function gas_stations_cpt_columns($columns)
		{

			$last = array_slice($columns, -1);
			array_pop($columns);

			$columns['gas-station_address'] = esc_html__('Address', 'gas-station');
			$columns['gas-station_geometry_x'] = esc_html__('Geometry X', 'gas-station');
			$columns['gas-station_geometry_y'] = esc_html__('Geometry Y', 'gas-station');

			$columns[array_keys($last)[0]] = $last[array_keys($last)[0]];

			return $columns;
		}

		public function gas_stations_custom_columns($column, $post_id)
		{
			switch ($column) {
				case 'gas-station_address':
					$address = get_post_meta($post_id, 'gas-station_address', true);
					echo esc_html($address);
					break;
				case 'gas-station_geometry_x':
					$geometry_x = get_post_meta($post_id, 'gas-station_geometry_x', true);
					echo floatval($geometry_x);
					break;
				case 'gas-station_geometry_y':
					$geometry_y = get_post_meta($post_id, 'gas-station_geometry_y', true);
					echo floatval($geometry_y);
					break;
			}
		}

		public function gas_stations_sortable_columns($columns)
		{
			$columns['gas-station_address'] = 'gas-station_address';
			$columns['gas-station_geometry_x'] = 'gas-station_geometry_x';
			$columns['gas-station_geometry_y'] = 'gas-station_geometry_y';
			return $columns;
		}
	}
}
