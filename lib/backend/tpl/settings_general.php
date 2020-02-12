<div class="sv_setting_subpage">
	<h2><?php _e('General', 'sv_tracking_manager'); ?></h2>
	<h3 class="divider"><?php _e( 'Display styles', 'sv_tracking_manager' ); ?></h3>
	<div class="sv_setting_flex">
	<?php
		echo $module->get_setting('activate')->run_type()->form();
		echo $module->get_setting('user_identification')->run_type()->form();
		echo $module->get_setting('anonymize_ip')->run_type()->form();
	?>
	</div>
	<div class="sv_setting_flex">
		<?php
			echo $module->get_setting('tracking_id')->run_type()->form();
		?>
	</div>
</div>