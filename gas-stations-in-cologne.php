<?php

/**
 * Plugin Name:       Gas Stations in Cologne
 * Description:       Test assignment for Scopevisio.
 * Requires at least: 5.7
 * Requires PHP:      7.0
 * Version:           0.1.0
 * Author:            Vasily Shatalkin
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       gas-stations-in-cologne
 *
 * @package           create-block
 */

/**
 * Registers the block using the metadata loaded from the `block.json` file.
 * Behind the scenes, it registers also all assets so they can be enqueued
 * through the block editor in the corresponding context.
 *
 * @see https://developer.wordpress.org/block-editor/tutorials/block-tutorial/writing-your-first-block-type/
 */

if (! defined('ABSPATH')) {
	exit; // Exit if accessed directly.
}

if (! class_exists('Gas_Stations')) {
	class Gas_Stations
	{

		public function __construct()
		{
			$this->define_constants();

			require_once(GAS_STATIONS_PATH . 'functions/functions.php');

			add_action('admin_menu', array($this, 'add_menu'));

			require_once(GAS_STATIONS_PATH . 'post-types/class.gas-stations-cpt.php');
			$Gas_Stations_Post_Type = new Gas_Stations_Post_Type();

			require_once(GAS_STATIONS_PATH . 'class.gas-stations-settings.php');
			$Gas_Stations_Settings = new Gas_Stations_Settings();

			require_once(GAS_STATIONS_PATH . 'blocks/class.gas-stations-list-block.php');
			$Gas_Stations_Block_List = new Gas_Stations_Block_List();

			add_action('wp_enqueue_scripts', array($this, 'register_scripts'));
			add_action('admin_enqueue_scripts', array($this, 'register_admin_scripts'), 999);
		}

		public function define_constants()
		{

			define('GAS_STATIONS_PATH', plugin_dir_path(__FILE__));
			define('GAS_STATIONS_URL', plugin_dir_url(__FILE__));
			define('GAS_STATIONS_VERSION', '1.0.0');
		}

		public static function activate()
		{

			//flush_rewrite_rules();
			update_option('rewrite_rules', ''); // This method is similar to the function above, but it works better.

		}

		public static function deactivate()
		{
			flush_rewrite_rules();
			unregister_post_type('gas-station');
		}

		public static function uninstall()
		{
			delete_option('gas_stations_options');

			$posts = get_posts(array(
				'post_type' => 'gas-station',
				'numberposts' => -1,
				'post_status' => 'any'
			));

			foreach ($posts as $post) {
				wp_delete_post($post->ID, true);
			}
		}

		public function add_menu()
		{
			add_menu_page( // add_theme_page // add_options_page
				'Gas Stations Options',
				'Gas Stations',
				'manage_options',
				'gas-stations-admin',
				array($this, 'gas_stations_settings_page'),
				'data:image/svg+xml;base64,' . base64_encode(file_get_contents(GAS_STATIONS_URL . 'assets/images/tankstelle.svg')),

				10
			);

			add_submenu_page(
				'gas-stations-admin', // 'edit-comments.php' Example of existing parent slug
				'Manage Gas Stations',
				'Manage Gas Stations',
				'manage_options',
				'edit.php?post_type=gas-station',
				null,
				null
			);

			add_submenu_page(
				'gas-stations-admin',
				'Add New Gas Station',
				'Add New Gas Station',
				'manage_options',
				'post-new.php?post_type=gas-station',
				null,
				null
			);
		}

		public function gas_stations_settings_page()
		{

			if (! current_user_can('manage_options')) {
				return;
			}
			if (isset($_GET['settings-updated'])) {
				add_settings_error('gas_stations_options', 'gas_stations_message', 'Settings Saved', 'updated');
			}
			settings_errors('gas_stations_options');
			require_once(GAS_STATIONS_PATH . 'views/settings-page.php');
		}

		public function register_scripts()
		{

			wp_register_script(
				'gas-stations-js',
				GAS_STATIONS_URL . 'assets/js/gas-stations-frontend.js',
				array(),
				filemtime(GAS_STATIONS_PATH . 'assets/js/gas-stations-frontend.js'),
				true
			);

			wp_register_style(
				'gas-stations-style-css',
				GAS_STATIONS_URL . 'assets/css/frontend.css',
				array(),
				filemtime(GAS_STATIONS_PATH . 'assets/css/frontend.css'),
				'all'
			);

			wp_register_style(
				'gas-stations-bootstrap-css',
				GAS_STATIONS_URL . 'assets/css/bootstrap.min.css',
				array(),
				filemtime(GAS_STATIONS_PATH . 'assets/css/bootstrap.min.css'),
				'all'
			);

			wp_enqueue_script(
				'google-maps',
				'https://maps.googleapis.com/maps/api/js?key=' . 'AIzaSyCAsek5OKF19JGZuOlAeic5HouACN1A6fw' . '&callback=initMap',
				[],
				null,
				true
			);
		}

		public register_admin_scripts()
		{

			wp_enqueue_script(
				'google-maps',
				'https://maps.googleapis.com/maps/api/js?key=' . 'AIzaSyCAsek5OKF19JGZuOlAeic5HouACN1A6fw' . '&callback=initMap',
				[],
				null,
				true
			);

		}
	}
}

if (class_exists('Gas_Stations')) {

	register_activation_hook(__FILE__, array('Gas_Stations', 'activate'));
	register_deactivation_hook(__FILE__, array('Gas_Stations', 'deactivate'));
	register_uninstall_hook(__FILE__, array('Gas_Stations', 'uninstall'));

	$gas_stations = new Gas_Stations();
}
