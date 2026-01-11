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
		}

		public function create_post_type()
		{

			register_post_type(
				'gas-stations',
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
					'show_in_menu' => true,
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
	}
}
