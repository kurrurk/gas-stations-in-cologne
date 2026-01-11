<?php

//$meta = get_post_meta($post->ID);
$object_id = get_post_meta($post->ID, 'gas-station_link_text', true);
$address = get_post_meta($post->ID, 'gas-station_address', true);
$geometry_x = get_post_meta($post->ID, 'gas-station_geometry_x', true);
$geometry_y = get_post_meta($post->ID, 'gas-station_geometry_y', true);
?>
<table class="form-table gas-stations-metabox">
	<input type="hidden" name="gas-station_nonce" value="<?= wp_create_nonce('gas-station_nonce'); ?>">
	<tr>
		<th>
			<label for="gas-station_object_id">Object id</label>
		</th>
		<td>
			<input
				type="number"
				name="gas-station_object_id"
				id="gas-station_object_id"
				class="regular-text object-id"
				value="<?= (isset($object_id)) ? absint($object_id) : ''; ?>"
				required>
		</td>
	</tr>
	<tr>
		<th>
			<label for="gas-station_address">Address</label>
		</th>
		<td>
			<input
				type="text"
				name="gas-station_address"
				id="gas-station_address"
				class="regular-text address"
				value="<?= (isset($address)) ? esc_html($address) : ''; ?>"
				required>
		</td>
	</tr>
	<tr>
		<th>
			<label for="gas-station_geometry_x">X</label>
		</th>
		<td>
			<input
				type="number"
				name="gas-station_geometry_x"
				id="gas-station_geometry_x"
				class="regular-text geometry-x"
				value="<?= (isset($geometry_x)) ? floatval($geometry_x) : ''; ?>"
				required>
		</td>
	</tr>
	<tr>
		<th>
			<label for="gas-station_geometry_y">Y</label>
		</th>
		<td>
			<input
				type="number"
				name="gas-station_geometry_y"
				id="gas-station_geometry_y"
				class="regular-text geometry-y"
				value="<?= (isset($geometry_y)) ? floatval($geometry_y) : ''; ?>"
				required>
		</td>
	</tr>

</table>