<?php

/*
 * Globalizing $current_user, $wpdb and calling get_currentuserinfo()
 */
global $current_user;
global $wpdb;
get_currentuserinfo();

$wpts_db_table_name = $wpdb->prefix.'wpts';

/*
 * List display
 */
function wpts_report_type_list($ts_timesheet){
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
			<td class="date column-date"><?php echo $ts_timesheet[$i][4]?>
			<div class="row-actions" style="display:inline; margin-left:10px;"><span class='add'><a href="?page=wpts-new.php&doaction=Add&date=<?php echo date('Y-m-d',strtotime($ts_timesheet[$i][2]))?>&ts_author=<?php echo $curauth->ID?>" title="Add">Add</a></span><?php if (current_user_can('manage_options')) { ?> | <span class='edit'><a href="?page=wpts-new.php&doaction=Edit&ID=<?php echo $ts_timesheet[$i][0]?>" title="Edit">Edit</a></span><?php } ?></div>
			</td>
			<td class="date column-date"><?php echo $ts_timesheet[$i][5]?></td>
			<td class="date column-date"><?php echo $ts_timesheet[$i][6]?></td>
			<td class="date column-date"><?php echo $ts_timesheet[$i][7]?></td>
		</tr>
<?php		
		}
	}
}

/*
 * List download
 */
function wpts_report_type_list_download($ts_timesheet){
	$filedata = '"User","Date","Job Name","Description","Time In","Time Out","Hours"'."\n";
	for ($i = 0; $i < sizeof($ts_timesheet); $i++) {
		$curauth = get_userdata($ts_timesheet[$i][1]);
		$filedata .= '"'.$curauth->user_login.'","'.$ts_timesheet[$i][2].'","'.$ts_timesheet[$i][3].'","'.$ts_timesheet[$i][4].'","'.$ts_timesheet[$i][5].'","'.$ts_timesheet[$i][6].'","'.$ts_timesheet[$i][7].'"'."\n";		
	}
	$filename = $_REQUEST['ts_author'].'_'.$_REQUEST['ts_date1'].'_'.$_REQUEST['ts_date2'].'_'.$_REQUEST['ts_group_type'].'.csv';
	file_put_contents(dirname(__FILE__).'/reports/'.$filename,$filedata);
	return $filename;
}

/*
 * Report display
 */
function wpts_report($ts_timesheet){
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
			if($_REQUEST['ts_report_type'] == 'by_author') {
				$curauth = get_userdata($ts_timesheet[$i][0]);
				$data = $curauth->user_login . ' (' . $curauth->user_email . ')';
			} else {
				$data = $ts_timesheet[$i][0];
			}
?>
		<tr class="iedit <?php echo $alternate;?> <?php echo $complete;?>">
			<td class="date column-date"><?php echo $data?></td>
			<td class="date column-date"><?php echo $ts_timesheet[$i][1]?></td>
		</tr>
<?php		
		}
	}
}

/*
 * Report download
 */
function wpts_report_download($ts_timesheet, $ts_report_type){
	$filedata = '"'.$ts_report_type.'","Hours"'."\n";
	for ($i = 0; $i < sizeof($ts_timesheet); $i++) {
		if($_REQUEST['ts_report_type'] == 'by_author') {
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

/*
 * Timestamp overlap validation
 */
function wpts_validate_overlap($a1,$b1,$a2,$b2){
	if(($a2 <= $a1) && ($b2 >= $b1)) { return false;
	} elseif (($a2 >= $a1) && ($b2 <= $b1)) { return false;
	} elseif (($a2 < $a1) && ($b2 > $a1) && ($b2 < $b1)) { return false;
	} elseif (($a2 > $a1) && ($a2 < $b1) && ($b2 > $b1)) { return false;
	} else { return true;
	}
}

?>