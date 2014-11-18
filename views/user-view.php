<div class="wrap">
	<h2><?=_e( 'View Timedata' , 'wp-timesheets' )?></h2>
	
<?php if ($add_id === false) : ?>
	<div id="setting-error-settings_updated" class="error settings-error"><p><strong><?=$error_msg?></strong></p></div>	
<?php elseif ( isset($add_id) ) : ?>
	<div id="setting-error-settings_updated" class="updated settings-error"><p><strong><?=_e('Timedata saved.', 'wp-timesheets' )?></strong></p></div>		
<?php endif; ?>
	
<?php if ($view_id !== false) : ?>	
	<table class="form-table">
<?php if (WP_Timesheets_Admin::current_user_can('manage_options')) : 
	$user = get_userdata($user)?>		
		<tr>
			<th scope="row"><?=_e('User', 'wp-timesheets' )?></th>
			<td><?=$user->display_name.' ('.$user->user_login.')'?></td>
		</tr>
<?php endif; ?>	
		<tr>
			<th scope="row"><?=_e('Job Name', 'wp-timesheets' )?></th>
			<td><?=$job_name?></td>
		</tr>
		<tr>
			<th scope="row"><?=_e('Job Date, Time', 'wp-timesheets' )?></th>
			<td><?=_e('On', 'wp-timesheets' )?> <?=$date?> <?=_e('from', 'wp-timesheets' )?> <?=$time1?> <?=_e('to', 'wp-timesheets' )?> <?=$time2?></td>
		</tr>
<?php if ( $show_description == 1 || !empty($description) ) : ?>		
		<tr>
			<th scope="row"><?=_e('Description', 'wp-timesheets' )?></th>
			<td><?=$description?></td>
		</tr>
<?php endif; ?>			
<?=WP_Timesheets_Admin::display_other_fields($other_field, true)?>
	</table>
<?php endif; ?>
	
	<p class="submit">
		<a class="button button-secondary" href="?page=wp_timesheets_user_report"><?=_e('View Timesheets', 'wp-timesheets' )?></a>
		<a class="button button-secondary" href="?page=wp_timesheets_user_manage"><?=_e('Add Timedata', 'wp-timesheets' )?></a>
	</p>
	
</div>