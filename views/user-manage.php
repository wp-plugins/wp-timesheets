<div class="wrap">
	<h2><?php echo sprintf( __( '%s Timedata' , 'wp-timesheets' ), ucfirst($verb) )?></h2>
	
<?php if ($add_id === false || $edit_id === false) : ?>
	<div id="setting-error-settings_updated" class="error settings-error"><p><strong><?php echo $error_msg?></strong></p></div>	
<?php endif; ?>	
	
	<form action="?page=wp_timesheets_user_manage" method="post">
<?php if (isset($id)) : ?>
		<input type="hidden" name="id" value="<?php echo $id?>" />
<?php endif; ?>			
		<?php wp_nonce_field( 'Add' ); ?>	
		<table class="form-table">
<?php if (WP_Timesheets_Admin::current_user_can('manage_options')) : ?>		
			<tr>
				<th scope="row"><?php _e('User', 'wp-timesheets' )?></th>
				<td><?php wp_dropdown_users( 'name=user&selected='.$user );?></td>
			</tr>
<?php endif; ?>	
			<tr>
				<th scope="row"><?php _e('Job Name', 'wp-timesheets' )?></th>
				<td><?php echo WP_Timesheets_Admin::display_job_list($job_name)?></td>
			</tr>
			<tr>
				<th scope="row"><?php _e('Job Date, Time', 'wp-timesheets' )?></th>
				<td>
					<fieldset>
						<?php _e('On', 'wp-timesheets' )?> 
						<input name="date" type="text" id="date" value="<?php echo $date?>" class="small-text datepicker" required /> 
						<?php _e('from', 'wp-timesheets' )?>
						<input name="time1" type="text" id="time1" class="small-text automplete-time timepicker" value="<?php echo $time1?>" required />
						<?php _e('to', 'wp-timesheets' )?> 
						<input name="time2" type="text" id="time2" class="small-text automplete-time timepicker" value="<?php echo $time2?>" required />
						<code id="wpts-duration"></code>
					</fieldset>
				</td>
			</tr>
<?php if ( $show_description == 1 || !empty($description) ) : ?>		
			<tr>
				<th scope="row"><?php _e('Description', 'wp-timesheets' )?></th>
				<td><input name="description" id="description" class="regular-text" value="<?php echo $description?>"/><p class="description"><?php _e('In a few words, explain about the job done.', 'wp-timesheets' )?></p></fieldset></td>
			</tr>
<?php endif; ?>			
<?php echo WP_Timesheets_Admin::display_other_fields($other_field)?>
		</table>
		<p class="submit">
			<input type="submit" name="action" id="action" class="button button-primary" value="<?php _e('Save', 'wp-timesheets' )?>">
			<a class="button button-secondary" href="?page=wp_timesheets_user_report"><?php _e('Cancel', 'wp-timesheets' )?></a>
		</p>
	</form>
	
</div>