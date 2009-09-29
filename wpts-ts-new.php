<?php 
require_once('wpts-lib.php');
if(!isset($_REQUEST['doaction']) || ($_REQUEST['doaction'] == 'View')) {
	$title = 'View TimeSheets';
} elseif(($_REQUEST['doaction'] == 'Add') || ($_REQUEST['doaction'] == 'Insert')) {
	$title = 'Add TimeSheet';	
} elseif(($_REQUEST['doaction'] == 'Edit') || ($_REQUEST['doaction'] == 'Update')) {
	$title = 'Update TimeSheet';	
}
?>

<div class="wrap">

<h2><?php echo $title?></h2>
<small>Powered by <a href="http://webdlabs.com" target="_blank">webdlabs.com</a>. Please <a href="http://webdlabs.com/projects/donate/" target="_blank">donate (by paypal)</a> if you found this useful.</small>


<?php
if(!isset($_REQUEST['doaction']) || ($_REQUEST['doaction'] == 'View')) {
?>

<!--HTML code for TimeSheet View-->
<div class="tablenav">
<form action="" method="get">
	<input type="hidden" name="page" value="wpts-ts-new.php" />
	Timesheet for 
	<?php 
	if(!isset($_REQUEST['ts_author'])) $_REQUEST['ts_author'] = $current_user->ID;
	if (current_user_can('manage_options')) {
		wp_dropdown_users('name=ts_author&selected='.$_REQUEST['ts_author']);
	} else { ?>
	<input type="hidden" name="ts_author" value="<?php echo $_REQUEST['ts_author'] ?>" /> 
	<?php echo "'$current_user->user_login'"; } ?>
	for the period 
	<select name="month">
	<?php options_month($ts_month); ?>
	</select>
	<select name="year">
	<?php options_year($ts_month); ?>
	</select>
	<input type="submit" value="View" name="doaction" id="doaction" class="button-secondary action" />
</form>
</div>

	<table class="widefat page fixed" cellspacing="0">
	  <thead>
	  <tr>
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
		<th scope="col" class="manage-column column-date" style="width:125px">Date</th>
		<th scope="col" class="manage-column column-job" style="width:150px">Job Name</th>
		<th scope="col" class="manage-column column-description" style="">Description</th>
		<th scope="col" class="manage-column column-in" style="width:100px">Time In</th>
		<th scope="col" class="manage-column column-out" style="width:100px">Time Out</th>
		<th scope="col" class="manage-column column-hours" style="width:100px">Hours</th>
	  </tr>
	  </tfoot>	  
	  <tbody>
<?php echo timesheet_month($ts_month, $ts_timesheet);?>		
	  </tbody> 
	</table>

<?php
} elseif(($_REQUEST['doaction'] == 'Add') || ($_REQUEST['doaction'] == 'Edit')) { 
?>

<!--HTML code for TimeSheet Add and Edit-->
<form action="" method="get">
<?php
if($_REQUEST['doaction'] == 'Edit') {
	$ID = $_REQUEST['ID'];
	$ts_author =  $ts_timesheet[0][1];
	$ts_date = $ts_timesheet[0][2];
	$ts_job_name = $ts_timesheet[0][3];
	$ts_description = $ts_timesheet[0][4];
	$ts_time_in = date('h:i A', strtotime($ts_timesheet[0][5]));
	$ts_time_out =date('h:i A', strtotime( $ts_timesheet[0][6]));
	$ts_hours = $ts_timesheet[0][7];
	$doaction = 'Update';
	if(!current_user_can('manage_options') && ($ts_author != $current_user->ID)) $user_check = false;
} elseif($_REQUEST['doaction'] == 'Add') {
	$ts_author = $_REQUEST['ts_author'];
	$ts_date = date('Y-m-d', strtotime($_REQUEST['date']));
	$ts_job_name = '';
	$ts_description = '';
	$ts_time_in = '09:00 AM';
	$ts_time_out = '05:00 PM';	
	$ts_hours = '';
	$doaction = 'Insert';	
}
if($user_check === false) {
?>
<div id="message" class="error"><p><strong>You are not authorised to view this timesheet.</strong></p></div>
<?php 
} else {
?>
	<input type="hidden" name="page" value="wpts-ts-new.php" />
	<input type="hidden" name="ID" value="<?php echo $ID;?>" />
	<input type="hidden" name="ts_author" value="<?php echo $ts_author;?>" />
	<input type="hidden" name="ts_date" value="<?php echo $ts_date;?>" />
	<table class="form-table">
	<tr valign="top">
	<th scope="row"><label for="ts_date">Date</label></th>
	<td><input name="ts_date_display" type="text" id="ts_date_display" value="<?php echo date('D, j M Y', strtotime($ts_date));?>" class="regular-text" disabled="disabled"/></td>
	</tr>
	<tr valign="top">
	<th scope="row"><label for="ts_job_name">Job Name</label></th>
	<td><input name="ts_job_name" type="text" id="ts_job_name" value="<?php echo $ts_job_name;?>" class="regular-text" />
	<span class="description">Project / Client name</span></td>
	</tr>
	<tr valign="top">
	<th scope="row"><label for="ts_job_description">Description</label></th>
	<td><input name="ts_description" type="text" id="ts_description"  value="<?php echo $ts_description;?>" class="regular-text" />
	<span class="description">In a few words, explain about the job done.</span></td>
	</tr>
	<th scope="row"><label for="ts_time">Time (From - To)</label></th>
	<td>From: <input type="text" name="ts_time_in" id="ts_time_in" value="<?php echo $ts_time_in;?>" class="small-text" style="width:80px"/> To: <input type="text" name="ts_time_out" id="ts_time_out" value="<?php echo $ts_time_out;?>" class="small-text" style="width:80px" /></td>
	</tr>
	<th scope="row"><label for="ts_time">Duration</label></th>
	<td><input type="text" name="ts_hours_display" id="ts_hours_display" value="" disabled="disabled" class="small-text"/> <input type="hidden" name="ts_hours" id="ts_hours" value="" />
	<span class="description">Auto calculated (hh:mm)</span></td>
	</tr>
	</table>
	<br />
	<p class="submit">
	<input type="submit" name="doaction" id="doaction" class="button-primary" value="<?php echo $doaction;?>" /> <input type="button" value="Cancel" class="button-secondary" onclick="history.back()" />
	</p>
</form>

<?php
	} // for $user_check
} elseif (($_REQUEST['doaction'] == 'Insert') || ($_REQUEST['doaction'] == 'Update')) {
if($_REQUEST['doaction'] == 'Insert') {
	if (!current_user_can('manage_options')) $_REQUEST['ts_author'] =  $current_user->ID;
	$ts_validate = $wpdb->get_results("SELECT * FROM wp_timesheets WHERE ts_author = ".$_REQUEST['ts_author']." AND ts_date = '".date('Y-m-d',strtotime($_REQUEST['ts_date']))."'", ARRAY_N);
	if(sizeof($ts_validate) > 0){
		$class = 'error';
		$message = 'Timesheet data for the same day already exists.';
	} else {
		if (!current_user_can('manage_options')) $_REQUEST['ts_author'] =  $current_user->ID;
		$wpdb->query("INSERT INTO wp_timesheets (`ID`, `ts_author`, `ts_date`, `ts_job_name`, `ts_description`, `ts_time_in`, `ts_time_out`, `ts_hours`) VALUES (NULL, ".$_REQUEST['ts_author'].", '".date('Y-m-d',strtotime($_REQUEST['ts_date']))."', '".$_REQUEST['ts_job_name']."', '".$_REQUEST['ts_description']."', '".date('G:i',strtotime($_REQUEST['ts_time_in']))."', '".date('G:i',strtotime($_REQUEST['ts_time_out']))."', '".$_REQUEST['ts_hours']."')");		
		$class = 'updated';
		$message = 'Timesheet saved.';
	}
} elseif($_REQUEST['doaction'] == 'Update') {
	if (current_user_can('manage_options')) {
		$wpdb->query("UPDATE wp_timesheets SET `ts_job_name` = '".$_REQUEST['ts_job_name']."', `ts_description` = '".$_REQUEST['ts_description']."', `ts_time_in` = '".date('G:i',strtotime($_REQUEST['ts_time_in']))."', `ts_time_out` = '".date('G:i',strtotime($_REQUEST['ts_time_out']))."',`ts_hours` = '".$_REQUEST['ts_hours']."' WHERE ID = '".$_REQUEST['ID']."'");
		$class = 'updated';
		$message = 'Timesheet updated.';
	} else {
		$class = 'error';
		$message = 'You are not authorised to modify timesheet.';	
	}
}
?>

<!--HTML code for TimeSheet Insert / Update-->
<div id="message" class="<?php echo $class; ?>"><p><strong><?php echo $message; ?></strong></p></div>
	<table class="form-table">
	<tr valign="top">
	<th scope="row"><label for="ts_date">Date & Duration</label></th>
	<td><span class="description"><?php echo date('D, j M Y', strtotime($_REQUEST['ts_date']));?> From: <?php echo $_REQUEST['ts_time_in'];?> To: <?php echo $_REQUEST['ts_time_out'];?> (<?php echo $_REQUEST['ts_hours'];?> hrs)</span></td>
	</tr>
	<tr valign="top">
	<th scope="row"><label for="ts_job_name">Job Name</label></th>
	<td><span class="description"><?php echo $_REQUEST['ts_job_name'];?></span></td>
	</tr>
	<tr valign="top">
	<th scope="row"><label for="ts_job_description">Description</label></th>
	<td><span class="description"><?php echo $_REQUEST['ts_description'];?></span></td>
	</tr>
	</table>
	<br />
	<a href="admin.php?page=wpts-ts-new.php&ts_author=<?php echo $_REQUEST['ts_author']?>">Back to TimeSheet View &raquo;</a>
<?php
}
?>

</div>