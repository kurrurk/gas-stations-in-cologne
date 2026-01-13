<div class="card-wrapper col-<?php echo esc_attr($columns); ?> p-1">
	<div class="card h-100 border-info mb-3 p-0">
		<div class="card-header"><?php echo esc_html(get_the_title()); ?></div>
		<div class="card-body">
			<?php if ($address) : ?>
				<p><strong>Address:</strong> <?php echo esc_html($address); ?></p>
			<?php endif; ?>
			<?php if ($x) : ?>
				<p><strong>X:</strong> <?php echo esc_html($x); ?></p>
			<?php endif; ?>
			<?php if ($y) : ?>
				<p><strong>Y:</strong> <?php echo esc_html($y); ?></p>
			<?php endif; ?>
		</div>
	</div>
</div>