<?php

/*
Plugin Name: WP Timesheets
Plugin URI: http://webdlabs.com/projects/wp-timesheets/
Description: Simple timesheet app for WordPress
Version: 1.1
Author: Akshay Raje
Author URI: http://webdlabs.com
*/

// Make sure we don't expose any info if called directly. Silence is golden.
if (!function_exists('add_action'))
	exit;

define('WPTS__PLUGIN_DIR', plugin_dir_path(__FILE__));
define('WPTS__PLUGIN_FILE', plugin_basename(__FILE__));
define('WPTS__VERSION', '1.0');

require_once( WPTS__PLUGIN_DIR . 'class.wpts.php' );

register_activation_hook(__FILE__, array('WP_Timesheets', 'plugin_activate'));
register_deactivation_hook(__FILE__, array('WP_Timesheets', 'plugin_deactivate'));

add_action('init', array('WP_Timesheets', 'init'));

if (is_admin()) {
	require_once( WPTS__PLUGIN_DIR . 'class.wpts-admin.php' );
	add_action('init', array('WP_Timesheets_Admin', 'init'));
}