<div class="wrap">
	<h2><?php _e( 'View Timedata' , 'wp-timesheets' )?></h2>
	
<?php if ($add_id === false) : ?>
	<div id="setting-error-settings_updated" class="error settings-error"><p><strong><?php echo $error_msg?></strong></p></div>	
<?php elseif ( isset($add_id) ) : ?>
	<div id="setting-error-settings_updated" class="updated settings-error"><p><strong><?php _e('Timedata saved.', 'wp-timesheets' )?></strong></p></div>		
<?php endif; ?>
	
<?php if ($view_id !== false) : ?>	
	<table class="form-table">
<?php if (WP_Timesheets_Admin::current_user_can('manage_options')) : 
	$user = get_userdata($user)?>		
		<tr>
			<th scope="row"><?php _e('User', 'wp-timesheets' )?></th>
			<td><?php echo $user->display_name.' ('.$user->user_login.')'?></td>
		</tr>
<?php endif; ?>	
		<tr>
			<th scope="row"><?php _e('Job Name', 'wp-timesheets' )?></th>
			<td><?php echo $job_name?></td>
		</tr>
		<tr>
			<th scope="row"><?php _e('Job Date, Time', 'wp-timesheets' )?></th>
			<td><?php _e('On', 'wp-timesheets' )?> <?php echo $date?> <?php _e('from', 'wp-timesheets' )?> <?php echo $time1?> <?php _e('to', 'wp-timesheets' )?> <?php echo $time2?></td>
		</tr>
<?php if ( $show_description == 1 || !empty($description) ) : ?>		
		<tr>
			<th scope="row"><?php _e('Description', 'wp-timesheets' )?></th>
			<td><?php echo $description?></td>
		</tr>
<?php endif; ?>			
<?php echo WP_Timesheets_Admin::display_other_fields($other_field, true)?>
	</table>
<?php endif; ?>
	
	<p class="submit">
		<a class="button button-secondary" href="?page=wp_timesheets_user_report"><?php _e('View Timesheets', 'wp-timesheets' )?></a>
		<a class="button button-secondary" href="?page=wp_timesheets_user_manage"><?php _e('Add Timedata', 'wp-timesheets' )?></a>
	</p>
	
</div>