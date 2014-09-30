<div class="wrap">
	<h2><?=_e( 'Timesheets' , 'wp-timesheets' )?> <a href="?page=wp_timesheets_user_manage&action=add" class="add-new-h2"><?=_e( 'Add New' , 'wp-timesheets' )?></a></h2>
	
	<div class="tablenav top">
		<form action="" method="post" id="user-report">		
			<?=_e( 'For' , 'wp-timesheets' )?> 
<?php if (current_user_can('manage_options')) : 
			wp_dropdown_users( 'name=user&show_option_all=All Users&selected='.$user );
else : 
			echo '<b>'.WP_Timesheets::$current_user->display_name.'</b>';
endif; ?>	
			<?=_e( 'for' , 'wp-timesheets' )?>
			<select name="report_range" id="wpts-report-range">
				<option data-date1="<?=$report_range_data['l7d'][0]?>" data-date2="<?=$report_range_data['l7d'][1]?>" value="l7d" <?=selected( 'l7d', $report_range)?>><?=_e( 'Last 7 days' , 'wp-timesheets' )?></option>
				<option data-date1="<?=$report_range_data['l14d'][0]?>" data-date2="<?=$report_range_data['l14d'][1]?>" value="l14d" <?=selected( 'l14d', $report_range)?>><?=_e( 'Last 14 days' , 'wp-timesheets' )?></option>
				<option data-date1="<?=$report_range_data['l30d'][0]?>" data-date2="<?=$report_range_data['l30d'][1]?>" value="l30d" <?=selected( 'l30d', $report_range)?>><?=_e( 'Last 30 days' , 'wp-timesheets' )?></option>
				<option data-date1="<?=$report_range_data['tm'][0]?>" data-date2="<?=$report_range_data['tm'][1]?>" value="tm" <?=selected( 'tm', $report_range)?>><?=_e( 'This month' , 'wp-timesheets' )?></option>
				<option data-date1="<?=$report_range_data['lm'][0]?>" data-date2="<?=$report_range_data['lm'][1]?>" value="lm" <?=selected( 'lm', $report_range)?>><?=_e( 'Last month' , 'wp-timesheets' )?></option>
			</select>			
			<?=_e( 'from' , 'wp-timesheets' )?>
			<input name="date1" type="text" id="wpts-date1" value="<?=$date1?>" class="regular-text datepicker"/> <?=_e( 'to' , 'wp-timesheets' )?> <input name="date2" type="text" id="wpts-date2"  value="<?=$date2?>" class="regular-text datepicker"/>
			<?=_e( 'grouped by' , 'wp-timesheets' )?>
			<select name="report_type">
				<option value="list" <?=selected( 'list', $report_type)?>><?=_e( 'all records' , 'wp-timesheets' )?></option>
<?php if (current_user_can('manage_options')) : ?>
				<option value="by_user" <?=selected( 'by_user', $report_type)?>><?=_e( 'user' , 'wp-timesheets' )?></option>
<?php endif; ?>
				<option value="by_job_name" <?=selected( 'by_job_name', $report_type)?>><?=_e( 'job name' , 'wp-timesheets' )?></option>
				<option value="by_date" <?=selected( 'by_date', $report_type)?>><?=_e( 'date' , 'wp-timesheets' )?></option>
				<option value="by_week" <?=selected( 'by_week', $report_type)?>><?=_e( 'week' , 'wp-timesheets' )?></option>
				<option value="by_month" <?=selected( 'by_month', $report_type)?>><?=_e( 'month' , 'wp-timesheets' )?></option>
			</select>
			<input type="submit" value="View" name="action" class="button button-primary" />
			<input type="button" value="Download" name="action" class="button" id="user-report-download" data-download="<?=wp_nonce_url( plugins_url( 'wp-timesheets/download.php' ), 'View' )?>" />
		</form>
	</div>
<?php WP_Timesheets_Admin::display_timesheet_report($timesheet, $report_type)?>
</div>