<?php
if (! defined('ABSPATH')) {
	exit; // Exit if accessed directly
}

if (! class_exists('Gas_Stations_Settings')) {
	class Gas_Stations_Settings
	{

		public static $options;

		public function __construct()
		{
			self::$options = get_option('gas-stations_options');

			//Verarbeitet die Eingabe, lädt die JSON-Daten und erstellt daraus ‚gas-station‘-Posts.
			add_action('admin_init', array($this, 'handle_import_from_json'), 9);

			//Konfiguriert das Formular auf der Einstellungsseite des Plugins.
			add_action('admin_init', array($this, 'admin_init'));
		}

		public function admin_init()
		{

			register_setting(
				'gas_stations_group',
				'gas_stations_options',
				array($this, 'gas_stations_validate')
			);

			add_settings_section(
				'gas_stations_main_section',
				'How does it work?',
				null,
				'gas-stations-page1'
			);

			add_settings_section(
				'gas_stations_second_section',
				'Other Plugin Options',
				null,
				'gas-stations-page2'
			);

			add_settings_field(
				'gas_stations_shortcode',
				'Shortcode',
				array($this, 'gas_stations_shortcode_callBack'),
				'gas-stations-page1',
				'gas_stations_main_section'
			);

			add_settings_field(
				'gas_stations_json_info',
				'JSON file requirements',
				array($this, 'gas_stations_json_info_callBack'),
				'gas-stations-page2',
				'gas_stations_second_section',
			);

			add_settings_field(
				'gas_stations_json_link',
				'JSON URL',
				array($this, 'gas_stations_json_link_callBack'),
				'gas-stations-page2',
				'gas_stations_second_section',
				array(
					'label_for' => 'gas_stations_json_link'
				)
			);
		}

		public function handle_import_from_json()
		{
			if (! isset($_POST['import_from_json'])) {
				return;
			}

			if (
				!isset($_POST['import_json_nonce']) || !wp_verify_nonce($_POST['import_json_nonce'], 'import_json_posts')
			) {
				return;
			}

			if (! current_user_can('edit_posts')) {
				return;
			}

			$json_url = $_POST['gas_stations_json_link'];

			$response = wp_remote_get($json_url);

			if (is_wp_error($response)) {
				return;
			}

			$body = wp_remote_retrieve_body($response);
			$data = json_decode($body, true);

			if (! is_array($data)) {
				return;
			}

			if (! array_key_exists('features', $data)) {
				return;
			}

			$posts = get_posts(array(
				'post_type' => 'gas-station',
				'numberposts' => -1,
				'post_status' => 'any'
			));

			foreach ($posts as $post) {
				wp_delete_post($post->ID, true);
			}

			foreach ($data['features'] as $item) {
				if (empty($item['attributes'])) {
					continue;
				}

				$post_id = wp_insert_post([
					'post_title'  => sanitize_text_field('Gas Station - ' . $item['attributes']['objectid']),
					'post_status' => 'publish',
					'post_type'   => 'gas-station',
				]);

				if (! $post_id || is_wp_error($post_id)) {
					continue;
				}

				if (isset($item['attributes']) && isset($item['geometry'])) {
					update_post_meta($post_id, 'gas-station_object_id', absint($item['attributes']['objectid']));
					update_post_meta($post_id, 'gas-station_address', esc_html($item['attributes']['adresse']));
					update_post_meta($post_id, 'gas-station_geometry_x', floatval($item['geometry']['x']));
					update_post_meta($post_id, 'gas-station_geometry_y', floatval($item['geometry']['y']));
				}
			}

			add_action('admin_notices', function () {
				add_settings_error('gas_stations_options', 'gas_stations_message', 'Posts imported and coordinates saved successfully', 'success');
			});
		}
		public function gas_stations_shortcode_callBack()
		{ ?>

			<span>To add the list of gas stations to your website, you can use the shortcode <strong>[gas_stations]</strong> or insert the <strong>“Gas Stations”</strong> block in the Gutenberg editor.</span>

		<?php
		}

		public function gas_stations_json_info_callBack()
		{ ?>

			<span>
				Please upload a JSON file that contains a <code>features</code> block in the following format:
			</span>

			<pre style="background:#f6f7f7;padding:10px;border:1px solid #ccd0d4;">
"features": [
 {
   "attributes": {
      "objectid": id,
      "adresse": "address"
   },
   "geometry": {
      "x": 1.1111...,
      "y": 2.2222....
   }
 }
]
			</pre>
		<?php
		}

		public function gas_stations_json_link_callBack($args)
		{ ?>
			<input
				type="url"
				name="gas_stations_json_link"
				id="gas_stations_json_link"
				<?php
			}

			public function gas_stations_validate($input)
			{

				$new_input = array();

				foreach ($input as $key => $value) {

					$new_input[$key] = sanitize_text_field($value);

					switch ($key) {
						case 'gas_stations_json_link':
							if (empty($value)) {
								add_settings_error('gas_stations_options', 'gas_stations_message', 'The title field cannot be empty', 'error');
								$value = 'Please, type some text.';
							}
							$new_input[$key] = sanitize_text_field($value);
							break;
						default:
							$new_input[$key] = sanitize_text_field($value);
							break;
					}
				}

				return $new_input;
			}
		}
	}
