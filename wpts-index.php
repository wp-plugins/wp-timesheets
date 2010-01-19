<?php
/*
Plugin Name: WP TimeSheets
Plugin URI: http://webdlabs.com/projects/wp-timesheets/
Description: A simple timesheet software for WordPress
Author: Akshay Raje
Version: 0.4
Author URI: http://webdlabs.com
*/

global $wpdb;
register_activation_hook(__FILE__,'wpts_install');
add_action('admin_menu', 'wpts_dashboard');

/*
 * wpts_install() is our installation routine. Does the MySQL table setup.
 */
function wpts_install() {
	global $wpdb;
	$wpts_db_table_name = $wpdb->prefix.'wpts';
	$wpts_db_version = '0.3';
	if($wpdb->get_var("SHOW TABLES LIKE '$wpts_db_table_name'") != $wpts_db_table_name) {
		$sql = "CREATE TABLE " . $wpts_db_table_name . " (
		  `ID` bigint(20) unsigned NOT NULL auto_increment,
		  `ts_author` bigint(20) NOT NULL,
		  `ts_job_name` text NOT NULL,
		  `ts_description` text NOT NULL,
		  `ts_time_in` datetime NOT NULL,
		  `ts_time_out` datetime NOT NULL,
		  PRIMARY KEY  (`ID`)
		) ENGINE=MyISAM  DEFAULT CHARSET=utf8;";
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta($sql);
		$welcome_name = "Mr. Wordpress";
		$welcome_text = "Congratulations, you just completed the installation!";
		add_option('wpts_db_version', $wpts_db_version);
	}
	// To move data from old table format to new one
	$wpts_db_table_name_old = $wpdb->prefix.'timesheets';
	if($wpdb->get_var("show tables like '$wpts_db_table_name_old'") == $wpts_db_table_name_old) {
		$old_date = $wpdb->get_results("SELECT ID, ts_author, ts_job_name, ts_description, ts_date, ts_time_in, ts_time_out FROM ".$wpts_db_table_name_old, ARRAY_N);
		for ($i = 0; $i < sizeof($old_date); $i++) {
			$wpdb->query("INSERT INTO ".$wpts_db_table_name." (`ID`, `ts_author`, `ts_job_name`, `ts_description`, `ts_time_in`, `ts_time_out`) VALUES (NULL, ".$old_date[$i][1].", '".$old_date[$i][2]."', '".$old_date[$i][3]."', '".$old_date[$i][4]." ".$old_date[$i][5]."', '".$old_date[$i][4]." ".$old_date[$i][6]."')");		
		}
	}
	@mkdir(dirname( __FILE__ ).'/reports', 0777); 
}

/*
 * wpts_dashboard() sets up various dashbord menus.
 */
function wpts_dashboard() {
	global $wpdb;
	global $wpts_db_version;
	global $wpts_db_table_name;
 	$wpts_home = add_menu_page('TimeSheets', 'TimeSheets', 1, 'wpts-reports.php', 'wpts_view_page');
 	$wpts_view = add_submenu_page('wpts-reports.php', 'View Timesheets', 'View Timesheets', 1, 'wpts-reports.php', 'wpts_view_page');
 	$wpts_add = add_submenu_page('wpts-reports.php', 'Manage Timedata', 'Manage Timedata', 1, 'wpts-new.php', 'wpts_add_page'); 
	add_action('admin_head-'.$wpts_add, 'wpts_admin_header' );
}

function wpts_view_page() {
	require_once('wpts-reports.php');
}

function wpts_add_page() {
	require_once('wpts-new.php');
}

function wpts_admin_header() {
	if(!isset($_REQUEST['doaction']) || $_REQUEST['doaction'] == 'Add' || $_REQUEST['doaction'] == 'Edit') {
	global $wpdb;
	$wpts_db_table_name = $wpdb->prefix.'wpts';
	$ts_job_name_distinct = $wpdb->get_results("SELECT DISTINCT `ts_job_name` FROM ".$wpts_db_table_name, ARRAY_N);
	for($i = 0; $i < sizeof($ts_job_name_distinct); $i++ ) {
		$ts_job_name_distinct_str .= '"'.$ts_job_name_distinct[$i][0].'",';
	}
?>
<link rel="stylesheet" type="text/css" media="all" href="<?php echo WP_PLUGIN_URL ?>/wp-timesheets/style.css" />
<script type="text/javascript" src="<?php echo WP_PLUGIN_URL ?>/wp-timesheets/js/jquery.timePicker.js"></script>
<script type="text/javascript" src="<?php echo WP_PLUGIN_URL ?>/wp-timesheets/js/jquery.autocomplete.js"></script>		
<script type="text/javascript" src="<?php echo WP_PLUGIN_URL ?>/wp-timesheets/js/wpts-jslib.js"></script>
<script type="text/javascript">
jQuery(document).ready(function($) {
	$("#ts_job_name").autocompleteArray([<?php echo $ts_job_name_distinct_str ?>], {matchSubset:1, matchContains:1, autoFill:true, minChars:1})
})
</script>
<?php
	}
}


?>
