<?php 
require_once('wpts-lib.php');
?>

<div class="wrap">

<h2>TimeSheet Reports</h2>
<small>Powered by <a href="http://webdlabs.com" target="_blank">webdlabs.com</a>. Please <a href="http://webdlabs.com/projects/donate/" target="_blank">donate (by paypal)</a> if you found this useful.</small>


<!--HTML code for TimeSheet Add-->
<form action="" method="get">
	<input type="hidden" name="page" value="wpts-reports.php" />
	<table class="form-table">
	<?php if (current_user_can('manage_options')) { ?>
	<tr valign="top">
	<th scope="row"><label for="ts_author">User</label></th>
	<td>
	<?php wp_dropdown_users('name=ts_author&show_option_all=All Users&selected='.$_REQUEST['ts_author']);?>
	</td>
	</tr>
	<?php } ?>
	<tr valign="top">
	<th scope="row"><label for="ts_date1">Period</label></th>
	<td>From <input name="ts_date1" type="text" id="ts_date1" value="<?php echo $_REQUEST['ts_date1'];?>" class="regular-text" style="width:100px"/> To <input name="ts_date2" type="text" id="ts_date2"  value="<?php echo $_REQUEST['ts_date2'];?>" class="regular-text" style="width:100px"/>
	<span class="description">Format YYYY-MM-DD</span></td>
	</tr>
	<tr valign="top">
	<th scope="row"><label for="ts_group_type">Reporting</label></th>
	<td>
	<input type="radio" name="ts_group_type" value="list"<?php if($_REQUEST['ts_group_type'] == 'list') echo ' checked="checked"';?> /> Display as a list<br />
	<?php if(current_user_can('manage_options')) { ?><input type="radio" name="ts_group_type" value="by_author"<?php if($_REQUEST['ts_group_type'] == 'by_author') echo ' checked="checked"';?> /> By User <?php } ?>
	<input type="radio" name="ts_group_type" value="by_job_name"<?php if($_REQUEST['ts_group_type'] == 'by_job_name') echo ' checked="checked"';?> /> By Job Name<br />
	<input type="radio" name="ts_group_type" value="by_date"<?php if($_REQUEST['ts_group_type'] == 'by_date') echo ' checked="checked"';?> /> By Date <input type="radio" name="ts_group_type" value="by_week"<?php if($_REQUEST['ts_group_type'] == 'by_week') echo ' checked="checked"';?> /> By Week <input type="radio" name="ts_group_type" value="by_month"<?php if($_REQUEST['ts_group_type'] == 'by_month') echo ' checked="checked"';?> /> By Month
	</td>
	</tr>	
	<tr valign="top">
	<th scope="row"><label for="ts_report_type">Report option</label></th>
	<td>
	<label title="On screen display"><input type="radio" name="ts_report_type" value="display"<?php if($_REQUEST['ts_report_type'] == 'display') echo ' checked="checked"';?> /> On screen display</label>
	<label title="Excel / CSV download"><input type="radio" name="ts_report_type" value="download"<?php if($_REQUEST['ts_report_type'] == 'download') echo ' checked="checked"';?> /> Excel (CSV) download</label>
	</td>
	</tr>
	</table>
	<p class="submit">
	<input type="submit" name="doaction" id="doaction" class="button-primary" value="View" />
	</p>
</form>

<?php
if(($_REQUEST['doaction'] == 'View') && ($_REQUEST['ts_group_type'] == 'list') && ($_REQUEST['ts_report_type'] == 'display')) {
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
<?php echo timesheet_detail($ts_timesheet);?>
	  </tbody> 
	</table>

<?php
} elseif(($_REQUEST['doaction'] == 'View') && ($_REQUEST['ts_group_type'] != 'list') && ($_REQUEST['ts_report_type'] == 'display')) {
	$header = ucwords(str_replace(array('by_','_'),array('',' '),$_REQUEST['ts_group_type']));
?>

<!--HTML code for TimeSheet Reports-->
	<table class="widefat page fixed" cellspacing="0" style="width:500px">
	  <thead>
	  <tr>
		<th scope="col" id="user" class="manage-column column-user" style="width:70%"><?php echo $header?></th>
		<th scope="col" id="hours" class="manage-column column-hours" style="width:30%">Hours</th>								
	  </tr>
	  </thead>
	  <tfoot>
	  <tr>
		<th scope="col" id="user" class="manage-column column-user" style="width:70%"><?php echo $header?></th>	  
		<th scope="col" id="hours" class="manage-column column-hours" style="width:30%">Hours</th>
	  </tr>
	  </tfoot>	  
	  <tbody>
<?php echo timesheet_report($ts_timesheet);?>
	  </tbody> 
	</table>

<?php
} elseif(($_REQUEST['doaction'] == 'View') && ($_REQUEST['ts_group_type'] == 'list') && ($_REQUEST['ts_report_type'] == 'download')) {
	echo '<a href="'.get_bloginfo('url').'/wp-content/plugins/wp-timesheets/reports/'.timesheet_detail_download($ts_timesheet).'">Click here to download Excel (csv) report</a>';
} elseif(($_REQUEST['doaction'] == 'View') && ($_REQUEST['ts_group_type'] != 'list') && ($_REQUEST['ts_report_type'] == 'download')) {
	echo '<a href="'.get_bloginfo('url').'/wp-content/plugins/wp-timesheets/reports/'.timesheet_report_download($ts_timesheet, ucwords(str_replace(array('by_','_'),array('',' '),$_REQUEST['ts_group_type']))).'">Click here to download Excel (csv) report</a>';
}
?>
</div>