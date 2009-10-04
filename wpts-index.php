<?php
/*
Plugin Name: WP TimeSheets
Plugin URI: http://webdlabs.com/projects/wp-timesheets/
Description: A simple timesheet software for WordPress
Author: Akshay Raje
Version: 0.2
Author URI: http://webdlabs.com
*/

add_action('admin_menu', 'wpts_setup');
register_activation_hook(__FILE__,'wpts_install');

function wpts_install() {
	global $wpdb;
	$table_name = $wpdb->prefix . 'timesheets';
	if($wpdb->get_var("show tables like '$table_name'") != $table_name) {
		$sql = "CREATE TABLE " . $table_name . " (
		  `ID` bigint(20) unsigned NOT NULL auto_increment,
		  `ts_author` bigint(20) NOT NULL,
		  `ts_date` date NOT NULL,
		  `ts_job_name` text NOT NULL,
		  `ts_description` text NOT NULL,
		  `ts_time_in` time NOT NULL,
		  `ts_time_out` time NOT NULL,
		  `ts_hours` time NOT NULL,
		  PRIMARY KEY  (`ID`)
		) ENGINE=MyISAM  DEFAULT CHARSET=utf8;";
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta($sql);
		$welcome_name = "Mr. Wordpress";
		$welcome_text = "Congratulations, you just completed the installation!";
	}
}

function wpts_setup() {
  $wpts_home = add_menu_page('TimeSheets', 'TimeSheets', 1, 'wpts-ts-new.php', 'wpts_view_add_page');
  $wpts_view_add = add_submenu_page('wpts-ts-new.php', 'Manage', 'Manage', 1, 'wpts-ts-new.php', 'wpts_view_add_page');
  $wpts_reports = add_submenu_page('wpts-ts-new.php', 'Reports', 'Reports', 1, 'wpts-reports.php', 'wpts_reports_page'); 
  add_action('admin_head-'.$wpts_home, 'wpts_admin_header' );
  add_action('admin_head-'.$wpts_view_add, 'wpts_admin_header' );
}

function wpts_admin_header() {
	if($_REQUEST['doaction'] == 'Add' || $_REQUEST['doaction'] == 'Edit') {
		$url = get_settings('siteurl');
		$url = $url . '/wp-content/plugins/wp-timesheets/';
		echo '<link rel="stylesheet" type="text/css" media="all" href="'.$url.'timePicker.css" />'."\n";
		echo '<script type="text/javascript" src="'.$url.'jquery.timePicker.js"></script>'."\n";
		echo '<script type="text/javascript" src="'.$url.'wpts-jslib.js"></script>'."\n";
	}
}

function wpts_view_add_page() {
	require_once('wpts-ts-new.php');
}

function wpts_reports_page() {
	require_once('wpts-reports.php');
}
?>
