<div <?php echo $wrapper_attributes; ?>>
	<form method="get" class="border-bottom border-info p-2 mb-2">

		<input
			type="text"
			name="gas_search"
			value="<?php //echo esc_attr($search);
					?>"
			placeholder="Search by address"
			class="form-control mb-2" />

		<select name="gas_sort_by" class="form-select mb-2">
			<option value="address" <?php //selected($sort_by, 'address');
									?>>Address</option>
			<option value="id" <?php //selected($sort_by, 'id');
								?>>ID</option>
		</select>

		<select name="gas_sort_order" class="form-select mb-2">
			<option value="asc" <?php //selected($sort_order, 'asc');
								?>>Ascending</option>
			<option value="desc" <?php //selected($sort_order, 'desc');
									?>>Descending</option>
		</select>

		<button type="submit" class="btn btn-primary">
			Apply
		</button>

	</form>
	<div class="gas-stations-grid row w-100 m-0 d-flex flex-wrap">

		<?php while ($query->have_posts()) : $query->the_post();

			$address = get_post_meta(get_the_ID(), 'gas-station_address', true);
			$x       = get_post_meta(get_the_ID(), 'gas-station_geometry_x', true);
			$y       = get_post_meta(get_the_ID(), 'gas-station_geometry_y', true);
		?>

			<div class="col-<?php echo esc_attr($columns); ?> p-1">
				<div class="card border-info mb-3 p-0">

					<div class="card-header">
						<?php echo esc_html(get_the_title()); ?>
					</div>

					<div class="card-body">

						<?php if ($address) : ?>
							<h5 class="card-title">
								<strong>Address:</strong>
								<?php echo esc_html($address); ?>
							</h5>
						<?php endif; ?>

						<?php if ($x) : ?>
							<p class="card-text">
								<strong>X:</strong>
								<?php echo esc_html($x); ?>
							</p>
						<?php endif; ?>

						<?php if ($y) : ?>
							<p class="card-text">
								<strong>Y:</strong>
								<?php echo esc_html($y); ?>
							</p>
						<?php endif; ?>

					</div>
				</div>
			</div>

		<?php endwhile; ?>

	</div>
</div>