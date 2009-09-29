<?php

//	Globalizing $current_user, $wpdb and calling get_currentuserinfo()
global $current_user;
global $wpdb;
get_currentuserinfo();

//	Set default values for month and year and query timesheet data from $wpdb
if((!isset($_REQUEST['doaction']) || ($_REQUEST['doaction'] == 'View')) && ($_REQUEST['page'] == 'wpts-ts-new.php')) {
	if(!isset($_REQUEST['month'])) $_REQUEST['month'] = date('n');
	if(!isset($_REQUEST['year'])) $_REQUEST['year'] = date('Y');
	if(!isset($_REQUEST['ts_author'])) $_REQUEST['ts_author'] = $current_user->ID;
	if (!current_user_can('manage_options')) $_REQUEST['ts_author'] = $current_user->ID;	
	$ts_month = mktime(23,59,59,$_REQUEST['month'],1,$_REQUEST['year']);
	$ts_d1 = date('Y-m-d', mktime(12,1,1,date('n',$ts_month),1,date('Y',$ts_month)));
	$ts_d2 = date('Y-m-d', mktime(12,1,1,date('n',$ts_month),date('t',$ts_month),date('Y',$ts_month)));
	$ts_timesheet = $wpdb->get_results("SELECT ID, ts_author, ts_date, ts_job_name, ts_description, time_format(ts_time_in, '%h:%i %p'), time_format(ts_time_out, '%h:%i %p'), time_format(ts_hours, '%h:%i') FROM wp_timesheets WHERE ts_author = ".$_REQUEST['ts_author']." AND ts_date >= '".$ts_d1."' AND ts_date <= '".$ts_d2."'", ARRAY_N);
} elseif(($_REQUEST['doaction'] == 'Edit') && ($_REQUEST['page'] == 'wpts-ts-new.php')) {
	$ts_timesheet = $wpdb->get_results("SELECT ID, ts_author, ts_date, ts_job_name, ts_description, ts_time_in, ts_time_out, ts_hours FROM wp_timesheets WHERE ID = ".$_REQUEST['ID'], ARRAY_N);
} elseif ($_REQUEST['page'] == 'wpts-reports.php') {
	if((!isset($_REQUEST['ts_date1'])) || (strtotime($_REQUEST['ts_date1']) === false)) $_REQUEST['ts_date1'] = date('Y-m-d', mktime(12,1,1,date('n'),1,date('Y')));
	if((!isset($_REQUEST['ts_date2'])) || (strtotime($_REQUEST['ts_date2']) === false)) $_REQUEST['ts_date2'] = date('Y-m-d');
	if(!isset($_REQUEST['ts_group_type'])) $_REQUEST['ts_group_type'] = 'list';
	if(!isset($_REQUEST['ts_report_type'])) $_REQUEST['ts_report_type'] = 'display';
	if($_REQUEST['doaction'] == 'View') {
		$ts_d1 = date('Y-m-d', strtotime($_REQUEST['ts_date1']));
		$ts_d2 = date('Y-m-d', strtotime($_REQUEST['ts_date2']));	
		if (current_user_can('manage_options')) {
			if(!isset($_REQUEST['ts_author'])) $_REQUEST['ts_author'] = 0;
			$ts_author = $_REQUEST['ts_author'];
		} else {
			$ts_author = $current_user->ID;
		}
	}
	if($_REQUEST['ts_group_type'] == 'list'){
		if($ts_author == 0) {$ts_timesheet = $wpdb->get_results("SELECT ID, ts_author, date_format(ts_date,'%M %e, %Y'), ts_job_name, ts_description, time_format(ts_time_in, '%h:%i %p'), time_format(ts_time_out, '%h:%i %p'), time_format(ts_hours, '%h:%i') FROM wp_timesheets WHERE ts_date >= '".$ts_d1."' AND ts_date <= '".$ts_d2."' ORDER BY ts_date", ARRAY_N); } 
		else {$ts_timesheet = $wpdb->get_results("SELECT ID, ts_author, date_format(ts_date,'%M %e, %Y'), ts_job_name, ts_description, time_format(ts_time_in, '%h:%i %p'), time_format(ts_time_out, '%h:%i %p'), time_format(ts_hours, '%h:%i') FROM wp_timesheets WHERE ts_author = '".$ts_author."' AND ts_date >= '".$ts_d1."' AND ts_date <= '".$ts_d2."' ORDER BY ts_date", ARRAY_N); }
	} elseif($_REQUEST['ts_group_type'] == 'by_author') {
		if($ts_author == 0) {$ts_timesheet = $wpdb->get_results("SELECT ts_author, Sec_to_Time(Sum(Time_to_Sec(ts_hours))) FROM wp_timesheets WHERE ts_date >= '".$ts_d1."' AND ts_date <= '".$ts_d2."' GROUP BY ts_author ORDER BY ts_date", ARRAY_N); } 
		else {$ts_timesheet = $wpdb->get_results("SELECT ts_author, Sec_to_Time(Sum(Time_to_Sec(ts_hours))) FROM wp_timesheets WHERE ts_author = '".$ts_author."' AND ts_date >= '".$ts_d1."' AND ts_date <= '".$ts_d2."' GROUP BY ts_author ORDER BY ts_date ", ARRAY_N); }	
	} elseif($_REQUEST['ts_group_type'] == 'by_job_name') {
		if($ts_author == 0) {$ts_timesheet = $wpdb->get_results("SELECT ts_job_name, Sec_to_Time(Sum(Time_to_Sec(ts_hours))) FROM wp_timesheets WHERE ts_date >= '".$ts_d1."' AND ts_date <= '".$ts_d2."' GROUP BY ts_job_name ORDER BY ts_date", ARRAY_N); } 
		else {$ts_timesheet = $wpdb->get_results("SELECT ts_job_name, Sec_to_Time(Sum(Time_to_Sec(ts_hours))) FROM wp_timesheets WHERE ts_author = '".$ts_author."' AND ts_date >= '".$ts_d1."' AND ts_date <= '".$ts_d2."' GROUP BY ts_job_name ORDER BY ts_date", ARRAY_N); }			
	} elseif($_REQUEST['ts_group_type'] == 'by_date') {
		if($ts_author == 0) {$ts_timesheet = $wpdb->get_results("SELECT date_format(ts_date,'%M %e, %Y'), Sec_to_Time(Sum(Time_to_Sec(ts_hours))) FROM wp_timesheets WHERE ts_date >= '".$ts_d1."' AND ts_date <= '".$ts_d2."' GROUP BY date_format(ts_date,'%M %e, %Y') ORDER BY ts_date ", ARRAY_N); } 
		else {$ts_timesheet = $wpdb->get_results("SELECT date_format(ts_date,'%M %e, %Y'), Sec_to_Time(Sum(Time_to_Sec(ts_hours))) FROM wp_timesheets WHERE ts_author = '".$ts_author."' AND ts_date >= '".$ts_d1."' AND ts_date <= '".$ts_d2."' GROUP BY date_format(ts_date,'%M %e, %Y') ORDER BY ts_date ", ARRAY_N); }		
	} elseif($_REQUEST['ts_group_type'] == 'by_week') {
		if($ts_author == 0) {$ts_timesheet = $wpdb->get_results("SELECT date_format(ts_date,'Week %u of %Y'), Sec_to_Time(Sum(Time_to_Sec(ts_hours))) FROM wp_timesheets WHERE ts_date >= '".$ts_d1."' AND ts_date <= '".$ts_d2."' GROUP BY WEEK(ts_date,1) ORDER BY ts_date ", ARRAY_N); } 
		else {$ts_timesheet = $wpdb->get_results("SELECT date_format(ts_date,'Week %u of %Y'), Sec_to_Time(Sum(Time_to_Sec(ts_hours))) FROM wp_timesheets WHERE ts_author = '".$ts_author."' AND ts_date >= '".$ts_d1."' AND ts_date <= '".$ts_d2."' GROUP BY WEEK(ts_date,1) ORDER BY ts_date ", ARRAY_N); }		
	} elseif($_REQUEST['ts_group_type'] == 'by_month') {
		if($ts_author == 0) {$ts_timesheet = $wpdb->get_results("SELECT date_format(ts_date,'%M, %Y'), Sec_to_Time(Sum(Time_to_Sec(ts_hours))) FROM wp_timesheets WHERE ts_date >= '".$ts_d1."' AND ts_date <= '".$ts_d2."' GROUP BY date_format(ts_date,'%M, %Y') ORDER BY ts_date", ARRAY_N); } 
		else {$ts_timesheet = $wpdb->get_results("SELECT date_format(ts_date,'%M, %Y'), Sec_to_Time(Sum(Time_to_Sec(ts_hours))) FROM wp_timesheets WHERE ts_author = '".$ts_author."' AND ts_date >= '".$ts_d1."' AND ts_date <= '".$ts_d2."' GROUP BY date_format(ts_date,'%M, %Y') ORDER BY ts_date", ARRAY_N); }		
	}
	//print_r($ts_timesheet);
}

//	Create select options for month and year 
function options_month($ts_month){
	for ($i = 1; $i <= 12; $i++) {
		if (date('n', $ts_month) == $i) {$selected = ' selected="selected"';} else {$selected = '';}
		echo '<option value="'.$i.'"'.$selected.'>'.date('F',mktime(0,0,0,$i,1,1))."</option>\n";
	}	
}

function options_year($ts_month){
	for ($i = 2009; $i <= 2015; $i++) {
		if (date('Y', $ts_month) == $i) {$selected = ' selected="selected"';} else {$selected = '';}
		echo '<option value="'.$i.'"'.$selected.'>'.$i."</option>\n";
	}	
}

//	Render Timesheet in View / Add mode
function timesheet_month($ts_month, $ts_timesheet){
	$num_days = date('t',$ts_month);
	for ($i = 1; $i <= $num_days; $i++) {
		if ($i&1) {$alternate = 'alternate';} else {$alternate = '';}
		$ts_date = mktime(23,59,59,date('n',$ts_month),$i,date('Y',$ts_month));
		$complete = 'blank';
		$ts_job_name[$i] = '<a href="admin.php?page=wpts-ts-new.php&doaction=Add&date='.date('Y-m-d',$ts_date).'&ts_author='.$_REQUEST["ts_author"].'">Add details &raquo;</a>';
		$ts_description[$i] = '-';
		$ts_time_in[$i] = '-';
		$ts_time_out[$i] = '-';
		$ts_hours[$i] = '-';		
		for ($j = 0; $j <= sizeof($ts_timesheet); $j++) {
			if ($ts_timesheet[$j][2] == date('Y-m-d', $ts_date)) {
				$complete = 'complete';
				if (current_user_can('manage_options')) {
					$ts_job_name[$i] = '<a href="admin.php?page=wpts-ts-new.php&doaction=Edit&ID='.$ts_timesheet[$j][0].'">'.$ts_timesheet[$j][3].'</a>';
				} else {
					$ts_job_name[$i] = $ts_timesheet[$j][3];
				}
				$ts_description[$i] = $ts_timesheet[$j][4];
				$ts_time_in[$i] = $ts_timesheet[$j][5];
				$ts_time_out[$i] = $ts_timesheet[$j][6];
				$ts_hours[$i] = $ts_timesheet[$j][7].' hrs';
			}
		}
?>
		<tr class="iedit <?php echo $alternate;?> <?php echo $complete;?>">
			<td class="date column-date"><?php echo date('D, j M Y', $ts_date);?></td>
			<td class="date column-date"><?php echo $ts_job_name[$i];?></td>
			<td class="date column-date"><?php echo $ts_description[$i];?></td>
			<td class="date column-date"><?php echo $ts_time_in[$i];?></td>
			<td class="date column-date"><?php echo $ts_time_out[$i];?></td>
			<td class="date column-date"><?php echo $ts_hours[$i];?></td>
		</tr>
<?php		
	}	
}

//	Render Timesheet for detail reports
function timesheet_detail($ts_timesheet){
	if(sizeof($ts_timesheet) == 0) {
?>
		<tr class="iedit">
			<td class="date column-date"></td>
			<td class="date column-date"></td>
			<td class="date column-date"></td>
			<td class="date column-date">No data for selected combination of user and date range</td>
			<td class="date column-date"></td>
			<td class="date column-date"></td>
			<td class="date column-date"></td>
		</tr>
<?php		
	} else {
		for ($i = 0; $i < sizeof($ts_timesheet); $i++) {
			if ($i&1) {$alternate = 'alternate';} else {$alternate = '';}
			$curauth = get_userdata($ts_timesheet[$i][1]);
?>
		<tr class="iedit <?php echo $alternate;?> <?php echo $complete;?>">
			<td class="date column-date"><?php echo $curauth->user_login?></td>
			<td class="date column-date"><?php echo $ts_timesheet[$i][2]?></td>
			<td class="date column-date"><?php echo $ts_timesheet[$i][3]?></td>
			<td class="date column-date"><?php echo $ts_timesheet[$i][4]?></td>
			<td class="date column-date"><?php echo $ts_timesheet[$i][5]?></td>
			<td class="date column-date"><?php echo $ts_timesheet[$i][6]?></td>
			<td class="date column-date"><?php echo $ts_timesheet[$i][7]?></td>
		</tr>
<?php		
		}
	}
}

function timesheet_detail_download($ts_timesheet){
	$filedata = '"User","Date","Job Name","Description","Time In","Time Out","Hours"'."\n";
	for ($i = 0; $i < sizeof($ts_timesheet); $i++) {
		$curauth = get_userdata($ts_timesheet[$i][1]);
		$filedata .= '"'.$curauth->user_login.'","'.$ts_timesheet[$i][2].'","'.$ts_timesheet[$i][3].'","'.$ts_timesheet[$i][4].'","'.$ts_timesheet[$i][5].'","'.$ts_timesheet[$i][6].'","'.$ts_timesheet[$i][7].'"'."\n";		
	}
	$filename = $_REQUEST['ts_author'].'_'.$_REQUEST['ts_date1'].'_'.$_REQUEST['ts_date2'].'_'.$_REQUEST['ts_group_type'].'.csv';
	file_put_contents(dirname(__FILE__).'/reports/'.$filename,$filedata);
	return $filename;
}

//	Render Timesheet for Reports
function timesheet_report($ts_timesheet){
	if(sizeof($ts_timesheet) == 0) {
?>
		<tr class="iedit">
			<td class="date column-date">No data for selected combination of user and date range</td>
			<td class="date column-date"></td>
		</tr>
<?php		
	} else {
		for ($i = 0; $i < sizeof($ts_timesheet); $i++) {
			if ($i&1) {$alternate = 'alternate';} else {$alternate = '';}
			if($_REQUEST['ts_group_type'] == 'by_author') {
				$curauth = get_userdata($ts_timesheet[$i][0]);
				$data = $curauth->user_login;
			} else {
				$data = $ts_timesheet[$i][0];
			}
?>
		<tr class="iedit <?php echo $alternate;?> <?php echo $complete;?>">
			<td class="date column-date"><?php echo $data?></td>
			<td class="date column-date"><?php echo $ts_timesheet[$i][1]?> hrs</td>
		</tr>
<?php		
		}
	}
}

function timesheet_report_download($ts_timesheet, $ts_group_type){
	$filedata = '"'.$ts_group_type.'","Hours"'."\n";
	for ($i = 0; $i < sizeof($ts_timesheet); $i++) {
		if($_REQUEST['ts_group_type'] == 'by_author') {
			$curauth = get_userdata($ts_timesheet[$i][0]);
			$data = $curauth->user_login;
		} else {
			$data = $ts_timesheet[$i][0];
		}
		$filedata .= '"'.$data.'","'.$ts_timesheet[$i][1].'"'."\n";		
	}
	$filename = $_REQUEST['ts_author'].'_'.$_REQUEST['ts_date1'].'_'.$_REQUEST['ts_date2'].'_'.$_REQUEST['ts_group_type'].'.csv';
	file_put_contents(dirname(__FILE__).'/reports/'.$filename,$filedata);
	return $filename;
}
?>