<div class="wrap">
	<h2><?php _e( 'Timesheets' , 'wp-timesheets' )?> <a href="?page=wp_timesheets_user_manage&action=add" class="add-new-h2"><?php _e( 'Add New' , 'wp-timesheets' )?></a></h2>
	
	<div class="tablenav top">
		<form action="" method="post" id="user-report">		
			<?php _e( 'For' , 'wp-timesheets' )?> 
<?php if (WP_Timesheets_Admin::current_user_can('manage_options')) : 
			wp_dropdown_users( 'name=user&show_option_all=All Users&selected='.$user );
else : 
			echo '<b>'.WP_Timesheets::$current_user->display_name.'</b>';
endif; ?>	
			<?php _e( 'for' , 'wp-timesheets' )?>
			<select name="report_range" id="wpts-report-range">
				<option data-date1="<?php echo $report_range_data['l7d'][0]?>" data-date2="<?php echo $report_range_data['l7d'][1]?>" value="l7d" <?php echo selected( 'l7d', $report_range)?>><?php _e( 'Last 7 days' , 'wp-timesheets' )?></option>
				<option data-date1="<?php echo $report_range_data['l14d'][0]?>" data-date2="<?php echo $report_range_data['l14d'][1]?>" value="l14d" <?php echo selected( 'l14d', $report_range)?>><?php _e( 'Last 14 days' , 'wp-timesheets' )?></option>
				<option data-date1="<?php echo $report_range_data['l30d'][0]?>" data-date2="<?php echo $report_range_data['l30d'][1]?>" value="l30d" <?php echo selected( 'l30d', $report_range)?>><?php _e( 'Last 30 days' , 'wp-timesheets' )?></option>
				<option data-date1="<?php echo $report_range_data['tm'][0]?>" data-date2="<?php echo $report_range_data['tm'][1]?>" value="tm" <?php echo selected( 'tm', $report_range)?>><?php _e( 'This month' , 'wp-timesheets' )?></option>
				<option data-date1="<?php echo $report_range_data['lm'][0]?>" data-date2="<?php echo $report_range_data['lm'][1]?>" value="lm" <?php echo selected( 'lm', $report_range)?>><?php _e( 'Last month' , 'wp-timesheets' )?></option>
			</select>			
			<?php _e( 'from' , 'wp-timesheets' )?>
			<input name="date1" type="text" id="wpts-date1" value="<?php echo $date1?>" class="regular-text datepicker"/> <?php _e( 'to' , 'wp-timesheets' )?> <input name="date2" type="text" id="wpts-date2"  value="<?php echo $date2?>" class="regular-text datepicker"/>
			<?php _e( 'grouped by' , 'wp-timesheets' )?>
			<select name="report_type">
				<option value="list" <?php echo selected( 'list', $report_type)?>><?php _e( 'all records' , 'wp-timesheets' )?></option>
<?php if (WP_Timesheets_Admin::current_user_can('manage_options')) : ?>
				<option value="by_user" <?php echo selected( 'by_user', $report_type)?>><?php _e( 'user' , 'wp-timesheets' )?></option>
<?php endif; ?>
				<option value="by_job_name" <?php echo selected( 'by_job_name', $report_type)?>><?php _e( 'job name' , 'wp-timesheets' )?></option>
				<option value="by_date" <?php echo selected( 'by_date', $report_type)?>><?php _e( 'date' , 'wp-timesheets' )?></option>
				<option value="by_week" <?php echo selected( 'by_week', $report_type)?>><?php _e( 'week' , 'wp-timesheets' )?></option>
				<option value="by_month" <?php echo selected( 'by_month', $report_type)?>><?php _e( 'month' , 'wp-timesheets' )?></option>
			</select>
			<input type="submit" value="View" name="action" class="button button-primary" />
			<input type="button" value="Download" name="action" class="button" id="user-report-download" data-download="<?php echo wp_nonce_url( plugins_url( 'wp-timesheets/download.php' ), 'View' )?>" />
		</form>
	</div>
<?php WP_Timesheets_Admin::display_timesheet_report($timesheet, $report_type)?>
</div>