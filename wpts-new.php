<?php 
require_once('wpts-lib.php');

/*
 * Page specific request variables, database queries etc.
 */
if(!isset($_REQUEST['date'])) $_REQUEST['date'] = date('Y-m-d');
if (current_user_can('manage_options')) {
	if(!isset($_REQUEST['ts_author'])) $_REQUEST['ts_author'] = $current_user->ID;
} else {
	$_REQUEST['ts_author'] = $current_user->ID;
}
if(!isset($_REQUEST['doaction']) || ($_REQUEST['doaction'] == 'Add') || ($_REQUEST['doaction'] == 'Insert')) {
	$title = 'Add Timedata';
	if(!isset($_REQUEST['doaction']) || $_REQUEST['doaction'] == 'Add') {
		$ts_author = $_REQUEST['ts_author'];
		$ts_date = date('Y-m-d', strtotime($_REQUEST['date']));
		$ts_job_name = '';
		$ts_description = '';
		$ts_time_in = '09:00 AM';
		$ts_time_out = '05:00 PM';	
		$doaction = 'Insert';	
	} elseif($_REQUEST['doaction'] == 'Insert') {
		$ts_author = $_REQUEST['ts_author'];
		$ts_date = date('Y-m-d', strtotime($_REQUEST['date']));
		$ts_job_name = $_REQUEST['ts_job_name'];
		$ts_description = $_REQUEST['ts_description'];
		$ts_time_in = date('Y-m-d H:i:s',strtotime($_REQUEST['ts_date'] . ' ' . $_REQUEST['ts_time_in']));
		$ts_time_out = date('Y-m-d H:i:s',strtotime($_REQUEST['ts_date'] . ' ' . $_REQUEST['ts_time_out']));	
		$ts_validate = $wpdb->get_results("SELECT ts_time_in, ts_time_out FROM ".$wpts_db_table_name." WHERE ts_author = ".$ts_author." AND date_format(ts_time_in,'%Y-%m-%d') = '".date('Y-m-d',strtotime($ts_time_in))."'", ARRAY_N);
		$ts_validate_result = true;
		for($i = 0; $i < sizeof($ts_validate); $i++ ) {
			if($ts_validate_result === true) $ts_validate_result = wpts_validate_overlap($ts_validate[$i][0],$ts_validate[$i][1],$ts_time_in,$ts_time_out);
		}
		if($ts_validate_result === false){
			$class = 'error';
			$message = 'Time data for the specified time already exists! <a href="javascript:history.go(-1)">Go back and try again</a>';
		} else {
			$wpdb->query("INSERT INTO ".$wpts_db_table_name." (`ID`, `ts_author`, `ts_job_name`, `ts_description`, `ts_time_in`, `ts_time_out`) VALUES (NULL, ".$ts_author.", '".$ts_job_name."', '".$ts_description."', '".$ts_time_in."', '".$ts_time_out."')");		
			$class = 'updated';
			$message = 'Time data saved.';
		}
	}
} elseif(($_REQUEST['doaction'] == 'Edit') || ($_REQUEST['doaction'] == 'Update')) {
	$title = 'Update Timedata';	
	if($_REQUEST['doaction'] == 'Edit') {
		$ts_timesheet = $wpdb->get_results("SELECT ID, ts_author, ts_job_name, ts_description, ts_time_in, ts_time_out FROM ".$wpts_db_table_name." WHERE ID = ".$_REQUEST['ID'], ARRAY_A);
		$ID = $ts_timesheet[0]['ID'];
		$ts_author = $ts_timesheet[0]['ts_author'];
		$ts_date = date('Y-m-d', strtotime($ts_timesheet[0]['ts_time_in']));
		$ts_job_name = $ts_timesheet[0]['ts_job_name'];
		$ts_description = $ts_timesheet[0]['ts_description'];
		$ts_time_in = date('g:i A', strtotime($ts_timesheet[0]['ts_time_in']));
		$ts_time_out = date('g:i A', strtotime($ts_timesheet[0]['ts_time_out']));	
		$doaction = 'Update';
	} elseif ($_REQUEST['doaction'] == 'Update') {
		$ts_author = $_REQUEST['ts_author'];
		$ts_date = date('Y-m-d', strtotime($_REQUEST['date']));
		$ts_job_name = $_REQUEST['ts_job_name'];
		$ts_description = $_REQUEST['ts_description'];
		$ts_time_in = date('Y-m-d H:i:s',strtotime($_REQUEST['ts_date'] . ' ' . $_REQUEST['ts_time_in']));
		$ts_time_out = date('Y-m-d H:i:s',strtotime($_REQUEST['ts_date'] . ' ' . $_REQUEST['ts_time_out']));
		$ts_validate = $wpdb->get_results("SELECT ts_time_in, ts_time_out FROM ".$wpts_db_table_name." WHERE ts_author = ".$ts_author." AND date_format(ts_time_in,'%Y-%m-%d') = '".date('Y-m-d',strtotime($ts_time_in))."' AND ID != '".$_REQUEST['ID']."'", ARRAY_N);
		$ts_validate_result = true;
		for($i = 0; $i < sizeof($ts_validate); $i++ ) {
			if($ts_validate_result === true) $ts_validate_result = wpts_validate_overlap($ts_validate[$i][0],$ts_validate[$i][1],$ts_time_in,$ts_time_out);
		}				
		if ($ts_validate_result === true && current_user_can('manage_options')) {
			$wpdb->query("UPDATE ".$wpts_db_table_name." SET `ts_job_name` = '".$ts_job_name."', `ts_description` = '".$ts_description."', `ts_time_in` = '".$ts_time_in."', `ts_time_out` = '".$ts_time_out."' WHERE ID = '".$_REQUEST['ID']."'");
			$class = 'updated';
			$message = 'Time data updated.';
		} elseif($ts_validate_result === false) {
			$class = 'error';
			$message = 'Time data for the specified time already exists! <a href="javascript:history.go(-1)">Go back and try again</a>';	
		} elseif(!current_user_can('manage_options')) {
			$class = 'error';
			$message = 'You are not authorised to modify timesheet!';			
		}	
	}
}
?>
<div class="wrap">

<h2><?php echo $title?></h2>
<small>Powered by <a href="http://webdlabs.com" target="_blank">webdlabs.com</a>. Please <a href="http://webdlabs.com/projects/donate/" target="_blank">donate (by paypal)</a> if you found this useful.</small>
<?php
if(!isset($_REQUEST['doaction']) || ($_REQUEST['doaction'] == 'Add') || ($_REQUEST['doaction'] == 'Edit')) {
?>
<!--HTML code for TimeSheet Add and Edit-->
<form action="" method="post">
	<input type="hidden" name="page" value="wpts-new.php" />
	<input type="hidden" name="ID" value="<?php echo $ID;?>" />
	<table class="form-table">
	<?php if (current_user_can('manage_options')) { ?>
	<tr valign="top">
	<th scope="row"><label for="ts_date">For user</label></th>
	<td><?php wp_dropdown_users('name=ts_author&selected='.$ts_author)?></td>
	</tr>
	<? } else { ?>
	<input type="hidden" name="ts_author" value="<?php echo $ts_author;?>" />
	<? } ?>
	<tr valign="top">
	<th scope="row"><label for="ts_date">Date</label></th>
	<td><input name="ts_date" type="text" id="ts_date" value="<?php echo $ts_date?>" class="regular-text"/>
	<span class="description">Format: YYYY-MM-DD</span></td>
	</tr>
	<tr valign="top">
	<th scope="row"><label for="ts_job_name">Job Name</label></th>
	<td><input name="ts_job_name" type="text" id="ts_job_name" value="<?php echo $ts_job_name;?>" class="regular-text" autocomplete="off"/>
	<span class="description">Project / Client name</span></td>
	</tr>
	<tr valign="top">
	<th scope="row"><label for="ts_job_description">Description</label></th>
	<td><input name="ts_description" type="text" id="ts_description"  value="<?php echo $ts_description;?>" class="regular-text" />
	<span class="description">In a few words, explain about the job done.</span></td>
	</tr>
	<th scope="row"><label for="ts_time">Time (From - To)</label></th>
	<td>From: <input type="text" name="ts_time_in" id="ts_time_in" value="<?php echo $ts_time_in;?>" class="small-text" style="width:80px"/> To: <input type="text" name="ts_time_out" id="ts_time_out" value="<?php echo $ts_time_out;?>" class="small-text" style="width:80px" />
	<span class="description" id="ts_hours_display"></span></td></td>
	</tr>
	</table>
	<p class="submit">
	<input type="submit" name="doaction" id="doaction" class="button-primary" value="<?php echo $doaction;?>" /> 
	<a href="?page=wpts-reports.php" class="button-secondary action" style="display:inline">Cancel</a>
	</p>
</form>
<?php
} elseif(($_REQUEST['doaction'] == 'Insert') || ($_REQUEST['doaction'] == 'Update')) {
?>
<!--HTML code for TimeSheet Insert and Update-->
<div id="message" class="<?php echo $class; ?>"><p><strong><?php echo $message; ?></strong></p></div>
<?php if($class == 'updated') { ?>
	<table class="form-table">
	<tr valign="top">
	<th scope="row"><label for="ts_date">Time</label></th>
	<td><span class="description"><?php echo date('D, j M Y', strtotime($ts_time_in))?> - From <?php echo date('g:i A', strtotime($ts_time_in))?> To <?php echo date('g:i A', strtotime($ts_time_out))?></span></td>
	</tr>
	<tr valign="top">
	<th scope="row"><label for="ts_job_name">Job Name</label></th>
	<td><span class="description"><?php echo $ts_job_name?></span></td>
	</tr>
	<tr valign="top">
	<th scope="row"><label for="ts_job_description">Description</label></th>
	<td><span class="description"><?php echo $ts_description?></span></td>
	</tr>
	</table>
	<br />
	<a href="?page=wpts-new.php&doaction=Add" class="button-secondary action" style="display:inline">Add Timedata</a>
	<a href="?page=wpts-reports.php" class="button-secondary action" style="display:inline">View Timesheets</a>
<?php } ?>
<?php } ?>
</div>