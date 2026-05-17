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
			add_action('init', array($this, 'create_block_gas_stations_list_block_block_init'));
		}
		public function create_block_gas_stations_list_block_block_init()
		{
			wp_register_block_types_from_metadata_collection(GAS_STATIONS_PATH . '/blocks/gas-stations-list-block/build', GAS_STATIONS_PATH . '/blocks/gas-stations-list-block/build/blocks-manifest.php');
		}
	}
}
