<?php
require_once('wpts-lib.php');

/*
 * Page specific request variables, database queries etc.
 */
if((!isset($_REQUEST['ts_date1'])) || (strtotime($_REQUEST['ts_date1']) === false)) $_REQUEST['ts_date1'] = date('Y-m-d', mktime(0,0,1,date('n'),1,date('Y')));
if((!isset($_REQUEST['ts_date2'])) || (strtotime($_REQUEST['ts_date2']) === false)) $_REQUEST['ts_date2'] = date('Y-m-d');
if(!isset($_REQUEST['ts_report_type'])) $_REQUEST['ts_report_type'] = 'list';
if(!isset($_REQUEST['ts_report_display'])) $_REQUEST['ts_report_display'] = 'display';
if(!isset($_REQUEST['doaction'])) $_REQUEST['doaction'] = 'View';
if (current_user_can('manage_options')) {
	if(!isset($_REQUEST['ts_author'])) $_REQUEST['ts_author'] = 0;
} else {
	$_REQUEST['ts_author'] = $current_user->ID;
}
if($_REQUEST['doaction'] == 'View') {
	$temp_d1 = strtotime($_REQUEST['ts_date1']);
	$temp_d2 = strtotime($_REQUEST['ts_date2']);
	$ts_d1 = date('Y-m-d H:i:s', mktime(0,0,1,date('n',$temp_d1),date('j',$temp_d1),date('Y',$temp_d1)));
	$ts_d2 = date('Y-m-d H:i:s', mktime(23,59,59,date('n',$temp_d2),date('j',$temp_d2),date('Y',$temp_d2)));
	$ts_author = $_REQUEST['ts_author'];
	if($_REQUEST['ts_report_type'] == 'list'){
		if($ts_author == 0) {$ts_timesheet = $wpdb->get_results("SELECT ID, ts_author, date_format(ts_time_in,'%M %e, %Y'), ts_job_name, ts_description, date_format(ts_time_in, '%h:%i %p'), date_format(ts_time_out, '%h:%i %p'), SEC_TO_TIME(TIMESTAMPDIFF(SECOND,ts_time_in,ts_time_out)/60) FROM ".$wpts_db_table_name." WHERE ts_time_in >= '".$ts_d1."' AND ts_time_out <= '".$ts_d2."' ORDER BY ts_time_in", ARRAY_N); } 
		else {$ts_timesheet = $wpdb->get_results("SELECT ID, ts_author, date_format(ts_time_in,'%M %e, %Y'), ts_job_name, ts_description, date_format(ts_time_in, '%h:%i %p'), date_format(ts_time_out, '%h:%i %p'), SEC_TO_TIME(TIMESTAMPDIFF(SECOND,ts_time_in,ts_time_out)/60) FROM ".$wpts_db_table_name." WHERE ts_author = '".$ts_author."' AND ts_time_in >= '".$ts_d1."' AND ts_time_out <= '".$ts_d2."' ORDER BY ts_time_in", ARRAY_N); }
	} elseif($_REQUEST['ts_report_type'] == 'by_author') {
		if($ts_author == 0) {$ts_timesheet = $wpdb->get_results("SELECT ts_author, SEC_TO_TIME(SUM(TIMESTAMPDIFF(SECOND,ts_time_in,ts_time_out)/60)) FROM ".$wpts_db_table_name." WHERE ts_time_in >= '".$ts_d1."' AND ts_time_out <= '".$ts_d2."' GROUP BY ts_author ORDER BY ts_time_in", ARRAY_N); } 
		else {$ts_timesheet = $wpdb->get_results("SELECT ts_author, SEC_TO_TIME(SUM(TIMESTAMPDIFF(SECOND,ts_time_in,ts_time_out)/60)) FROM ".$wpts_db_table_name." WHERE ts_author = '".$ts_author."' AND ts_time_in >= '".$ts_d1."' AND ts_time_out <= '".$ts_d2."' GROUP BY ts_author ORDER BY ts_time_in", ARRAY_N); }	
	} elseif($_REQUEST['ts_report_type'] == 'by_job_name') {
		if($ts_author == 0) {$ts_timesheet = $wpdb->get_results("SELECT ts_job_name, SEC_TO_TIME(SUM(TIMESTAMPDIFF(SECOND,ts_time_in,ts_time_out)/60)) FROM ".$wpts_db_table_name." WHERE ts_time_in >= '".$ts_d1."' AND ts_time_out <= '".$ts_d2."' GROUP BY ts_job_name ORDER BY ts_time_in", ARRAY_N); } 
		else {$ts_timesheet = $wpdb->get_results("SELECT ts_job_name, SEC_TO_TIME(SUM(TIMESTAMPDIFF(SECOND,ts_time_in,ts_time_out)/60)) FROM ".$wpts_db_table_name." WHERE ts_author = '".$ts_author."' AND ts_time_in >= '".$ts_d1."' AND ts_time_out <= '".$ts_d2."' GROUP BY ts_job_name ORDER BY ts_time_in", ARRAY_N); }			
	} elseif($_REQUEST['ts_report_type'] == 'by_date') {
		if($ts_author == 0) {$ts_timesheet = $wpdb->get_results("SELECT date_format(ts_time_in,'%M %e, %Y'), SEC_TO_TIME(SUM(TIMESTAMPDIFF(SECOND,ts_time_in,ts_time_out)/60)) FROM ".$wpts_db_table_name." WHERE ts_time_in >= '".$ts_d1."' AND ts_time_out <= '".$ts_d2."' GROUP BY date_format(ts_time_in,'%M %e, %Y') ORDER BY ts_time_in", ARRAY_N); } 
		else {$ts_timesheet = $wpdb->get_results("SELECT date_format(ts_time_in,'%M %e, %Y'), SEC_TO_TIME(SUM(TIMESTAMPDIFF(SECOND,ts_time_in,ts_time_out)/60)) FROM ".$wpts_db_table_name." WHERE ts_author = '".$ts_author."' AND ts_time_in >= '".$ts_d1."' AND ts_time_out <= '".$ts_d2."' GROUP BY date_format(ts_time_in,'%M %e, %Y') ORDER BY ts_time_in", ARRAY_N); }		
	} elseif($_REQUEST['ts_report_type'] == 'by_week') {
		if($ts_author == 0) {$ts_timesheet = $wpdb->get_results("SELECT date_format(ts_time_in,'Week %u of %Y'), SEC_TO_TIME(SUM(TIMESTAMPDIFF(SECOND,ts_time_in,ts_time_out)/60)) FROM ".$wpts_db_table_name." WHERE ts_time_in >= '".$ts_d1."' AND ts_time_out <= '".$ts_d2."' GROUP BY WEEK(ts_time_in,1) ORDER BY ts_time_in", ARRAY_N); } 
		else {$ts_timesheet = $wpdb->get_results("SELECT date_format(ts_time_in,'Week %u of %Y'), SEC_TO_TIME(SUM(TIMESTAMPDIFF(SECOND,ts_time_in,ts_time_out)/60)) FROM ".$wpts_db_table_name." WHERE ts_author = '".$ts_author."' AND ts_time_in >= '".$ts_d1."' AND ts_time_out <= '".$ts_d2."' GROUP BY WEEK(ts_time_in,1) ORDER BY ts_time_in", ARRAY_N); }		
	} elseif($_REQUEST['ts_report_type'] == 'by_month') {
		if($ts_author == 0) {$ts_timesheet = $wpdb->get_results("SELECT date_format(ts_time_in,'%M, %Y'), SEC_TO_TIME(SUM(TIMESTAMPDIFF(SECOND,ts_time_in,ts_time_out)/60)) FROM ".$wpts_db_table_name." WHERE ts_time_in >= '".$ts_d1."' AND ts_time_out <= '".$ts_d2."' GROUP BY date_format(ts_time_in,'%M, %Y') ORDER BY ts_time_in", ARRAY_N); } 
		else {$ts_timesheet = $wpdb->get_results("SELECT date_format(ts_time_in,'%M, %Y'), SEC_TO_TIME(SUM(TIMESTAMPDIFF(SECOND,ts_time_in,ts_time_out)/60)) FROM ".$wpts_db_table_name." WHERE ts_author = '".$ts_author."' AND ts_time_in >= '".$ts_d1."' AND ts_time_out <= '".$ts_d2."' GROUP BY date_format(ts_time_in,'%M, %Y') ORDER BY ts_time_in", ARRAY_N); }		
	}
	
}
?>
<div class="wrap">

<h2>TimeSheet Reports</h2>
<small>Powered by <a href="http://webdlabs.com" target="_blank">webdlabs.com</a>. Please <a href="http://webdlabs.com/projects/donate/" target="_blank">donate (by paypal)</a> if you found this useful.</small>

<div class="tablenav">
<form action="" method="post">
	<input type="hidden" name="page" value="wpts-reports.php" />
	Timesheet for 
	<?php 
	if(!isset($_REQUEST['ts_author'])) $_REQUEST['ts_author'] = $current_user->ID;
	if (current_user_can('manage_options')) {
		wp_dropdown_users('name=ts_author&show_option_all=All Users&selected='.$_REQUEST['ts_author']);
	} else { ?>
	<input type="hidden" name="ts_author" value="<?php echo $_REQUEST['ts_author'] ?>" /> 
	<?php echo "'$current_user->user_login'"; } ?>
	for the period 
	<input name="ts_date1" type="text" id="ts_date1" value="<?php echo $_REQUEST['ts_date1'];?>" class="regular-text" style="width:100px"/> to <input name="ts_date2" type="text" id="ts_date2"  value="<?php echo $_REQUEST['ts_date2'];?>" class="regular-text" style="width:100px"/>
	to display  
	<select name="ts_report_type">
		<option value="list"<?php if($_REQUEST['ts_report_type'] == 'list') echo ' selected="selected"';?>>detailed list</option>
		<?php if(current_user_can('manage_options')) { ?>
		<option value="by_author"<?php if($_REQUEST['ts_report_type'] == 'by_author') echo ' selected="selected"';?>>by user</option>
		<?php } ?>
		<option value="by_job_name"<?php if($_REQUEST['ts_report_type'] == 'by_job_name') echo ' selected="selected"';?>>by job name</option>
		<option value="by_date"<?php if($_REQUEST['ts_report_type'] == 'by_date') echo ' selected="selected"';?>>by date</option>
		<option value="by_week"<?php if($_REQUEST['ts_report_type'] == 'by_week') echo ' selected="selected"';?>>by week</option>
		<option value="by_month"<?php if($_REQUEST['ts_report_type'] == 'by_month') echo ' selected="selected"';?>>by month</option>
	</select>
	<select name="ts_report_display">
		<option value="display"<?php if($_REQUEST['ts_report_display'] == 'display') echo ' selected="selected"';?>>on screen</option>
		<option value="download"<?php if($_REQUEST['ts_report_display'] == 'download') echo ' selected="selected"';?>>as excel download</option>
	</select>	
	<input type="submit" value="View" name="doaction" id="doaction" class="button-primary action" />
	<a href="?page=wpts-new.php&doaction=Add" class="button-secondary action" style="display:inline">Add Timedata</a>
</form>
</div>

<?php
if(($_REQUEST['doaction'] == 'View') && ($_REQUEST['ts_report_type'] == 'list') && ($_REQUEST['ts_report_display'] == 'display')) {
?>

<!--HTML code for TimeSheet List View-->
	<table class="widefat page fixed" cellspacing="0">
	  <thead>
	  <tr>
		<th scope="col" id="user" class="manage-column column-user" style="width:100px">User</th>
		<th scope="col" id="date" class="manage-column column-date" style="width:125px">Date</th>
		<th scope="col" id="job" class="manage-column column-job" style="width:150px">Job Name</th>
		<th scope="col" id="description" class="manage-column column-description" style="">Description</th>
		<th scope="col" id="in" class="manage-column column-in" style="width:60px">Time In</th>
		<th scope="col" id="out" class="manage-column column-out" style="width:60px">Time Out</th>
		<th scope="col" id="hours" class="manage-column column-hours" style="width:60px">Hours</th>								
	  </tr>
	  </thead>
	  <tfoot>
	  <tr>
		<th scope="col" id="user" class="manage-column column-user" style="width:100px">User</th>	  
		<th scope="col" class="manage-column column-date" style="width:125px">Date</th>
		<th scope="col" class="manage-column column-job" style="width:150px">Job Name</th>
		<th scope="col" class="manage-column column-description" style="">Description</th>
		<th scope="col" class="manage-column column-in" style="width:100px">Time In</th>
		<th scope="col" class="manage-column column-out" style="width:100px">Time Out</th>
		<th scope="col" class="manage-column column-hours" style="width:100px">Hours</th>
	  </tr>
	  </tfoot>	  
	  <tbody>
<?php echo wpts_report_type_list($ts_timesheet);?>
	  </tbody> 
	</table>
<?php
} elseif(($_REQUEST['doaction'] == 'View') && ($_REQUEST['ts_report_type'] != 'list') && ($_REQUEST['ts_report_display'] == 'display')) {
	$header = ucwords(str_replace(array('by_','_'),array('',' '),$_REQUEST['ts_report_type']));
?>

<!--HTML code for TimeSheet Reports-->
	<table class="widefat page fixed" cellspacing="0" style="width:600px">
	  <thead>
	  <tr>
		<th scope="col" id="user" class="manage-column column-user"><?php echo $header?></th>
		<th scope="col" id="hours" class="manage-column column-hours" style="width:100px">Hours</th>								
	  </tr>
	  </thead>
	  <tfoot>
	  <tr>
		<th scope="col" id="user" class="manage-column column-user"><?php echo $header?></th>	  
		<th scope="col" id="hours" class="manage-column column-hours" style="width:100px">Hours</th>
	  </tr>
	  </tfoot>	  
	  <tbody>
<?php echo wpts_report($ts_timesheet);?>
	  </tbody> 
	</table>
<?php
} elseif(($_REQUEST['doaction'] == 'View') && ($_REQUEST['ts_report_type'] == 'list') && ($_REQUEST['ts_report_display'] == 'download')) {
	echo '<a href="'.get_bloginfo('url').'/wp-content/plugins/wp-timesheets/reports/'.wpts_report_type_list_download($ts_timesheet).'">Click here to download Excel (csv) report</a>';
} elseif(($_REQUEST['doaction'] == 'View') && ($_REQUEST['ts_report_type'] != 'list') && ($_REQUEST['ts_report_display'] == 'download')) {
	echo '<a href="'.get_bloginfo('url').'/wp-content/plugins/wp-timesheets/reports/'.wpts_report_download($ts_timesheet, ucwords(str_replace(array('by_','_'),array('',' '),$_REQUEST['ts_group_type']))).'">Click here to download Excel (csv) report</a>';
}
?>
<br />
<br />
<a href="?page=wpts-new.php&doaction=Add" class="button-secondary action" style="display:inline">Add Timedata</a>
</div>