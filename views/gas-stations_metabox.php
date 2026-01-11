<?php

//$meta = get_post_meta($post->ID);
$object_id = get_post_meta($post->ID, 'gas-station_link_text', true);
$adresse = get_post_meta($post->ID, 'gas-station_adresse', true);
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
				value="<?= (isset($object_id)) ? esc_html($object_id) : ''; ?>"
				required>
		</td>
	</tr>
	<tr>
		<th>
			<label for="gas-station_adresse">Adresse</label>
		</th>
		<td>
			<input
				type="text"
				name="gas-station_adresse"
				id="gas-station_adresse"
				class="regular-text adresse"
				value="<?= (isset($adresse)) ? esc_url($adresse) : ''; ?>"
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
				value="<?= (isset($geometry_x)) ? esc_html($geometry_x) : ''; ?>"
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
				value="<?= (isset($geometry_y)) ? esc_url($geometry_y) : ''; ?>"
				required>
		</td>
	</tr>

</table>