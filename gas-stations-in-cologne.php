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

			require_once(GAS_STATIONS_PATH . 'post-types/class.gas-stations-cpt.php');
			$Gas_Stations_Post_Type = new Gas_Stations_Post_Type();

			add_action('init', array($this, 'create_block_gas_stations_in_cologne_block_init'));
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
			//unregister_post_type( 'vs-slider' );
		}

		public static function uninstall()
		{
			//     delete_option( 'vs_slider_options' );

			//     $posts = get_posts( array(
			//         'post_type' => 'vs-slider',
			//         'numberposts' => -1,
			//         'post_status' => 'any'
			//     ) );

			//     foreach ( $posts as $post ) {
			//         wp_delete_post( $post->ID, true );
			//     }
		}

		public function create_block_gas_stations_in_cologne_block_init()
		{
			register_block_type_from_metadata(__DIR__);
		}
	}
}

if (class_exists('Gas_Stations')) {

	//register_activation_hook(__FILE__, array('Gas_Stations', 'activate'));
	//register_deactivation_hook(__FILE__, array('Gas_Stations', 'deactivate'));
	//register_uninstall_hook(__FILE__, array('Gas_Stations', 'uninstall'));

	$vs_slider = new Gas_Stations();
}
