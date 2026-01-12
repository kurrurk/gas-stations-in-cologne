<div class="wrap">
	<h1><?= esc_html(get_admin_page_title()); ?></h1>
	<form method="post" id="json-import-form">
		<div id="import-loading"
			class="notice notice-info"
			style="display:none;">
			<p>Import in progress, please wait...</p>
		</div>
		<?php
		wp_nonce_field('import_json_posts', 'import_json_nonce');

		settings_fields('gas_stations_group');
		do_settings_sections('gas-stations-page1');

		settings_fields('gas_stations_group');
		do_settings_sections('gas-stations-page2');

		//submit_button('Update the list of gas stations');
		?>
		<input type="submit" name="import_from_json" class="button button-primary" value="Import posts from JSON">
	</form>
	<script>
		document.getElementById('json-import-form').addEventListener('submit', function() {
			const loadingNotice = document.getElementById('import-loading');
			const button = document.getElementById('import-btn');

			loadingNotice.style.display = 'block';
			button.disabled = true;
			button.value = 'Importing...';
		});
	</script>
</div>