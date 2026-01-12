<?php

if (! defined('ABSPATH')) {
	exit; // Exit if accessed directly.
}

if (! function_exists('gas_station_get_placeholder_image')) {
	function gas_station_get_placeholder_image()
	{
		return '<img src="' . esc_url(GAS_STATIONS_URL . 'assets/images/placeholder.png') . '" class="img-fluid" alt="' . esc_attr(get_the_title()) . '"/>';
	}
}

if (! function_exists('gas_stations_option')) {
	function gas_stations_option()
	{

		//$show_bullets = isset(gas_stations_Settings::$options['vs_slider_bullets']) && VS_Slider_Settings::$options['vs_slider_bullets'] == 1 ? true : false;

		// wp_register_script(
		//     'vs-slider-options-js',
		//     VS_SLIDER_URL . 'vendor/flexslider/flexslider.js',
		//     array(),
		//     filemtime( VS_SLIDER_PATH . 'vendor/flexslider/jquery.flexslider-min.js' ),
		//     true
		// );

		// wp_localize_script(
		// 	'vs-slider-options-js',
		// 	'SLIDER_OPTIONS',
		// 	array(
		// 		'controlNav' => $show_bullets
		// 	)
		// );
	}
}
