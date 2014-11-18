<?php

class WP_Timesheets {
	
	public static $current_user;
	public static $wpts_table;
	public static $error_msg;

	public static function init() {
		
		global $wpdb;
		WP_Timesheets::$current_user = wp_get_current_user();
		WP_Timesheets::$wpts_table = $wpdb->prefix.'wpts';
		WP_Timesheets::$error_msg = null;
		
	}
	
	public static function view( $name, $vars = array() ) {
		
		load_plugin_textdomain( 'wp-timesheets' );
		$file = WPTS__PLUGIN_DIR . 'views/'. $name . '.php';
		if( !empty($vars) )
			extract( $vars, EXTR_OVERWRITE );
		unset( $vars );
		include( $file );
		
	}	

	/**
	 * Attached to activate_{ plugin_basename( __FILES__ ) } by register_activation_hook()
	 * @static
	 */
	public static function plugin_activate() {
		
		global $wpdb;
		$wpts_db_table_name = $wpdb->prefix.'wpts';
		$sql = "CREATE TABLE " . $wpts_db_table_name . " (
		  `ID` bigint(20) unsigned NOT NULL auto_increment,
		  `ts_author` bigint(20) NOT NULL,
		  `ts_job_name` varchar(1000) NOT NULL,
		  `ts_description` text NOT NULL,
		  `ts_other_fields` text NOT NULL,
		  `ts_time_in` datetime NOT NULL,
		  `ts_time_out` datetime NOT NULL,
		  PRIMARY KEY  (`ID`),
		  KEY `ts_author` (`ts_author`),
		  KEY `ts_job_name` (`ts_job_name`(333)),
		  KEY `ts_time_in` (`ts_time_in`),
		  KEY `ts_time_out` (`ts_time_out`)		  
		) ENGINE=MyISAM  DEFAULT CHARSET=utf8;";		
		
		if($wpdb->get_var("SHOW TABLES LIKE '$wpts_db_table_name'") != $wpts_db_table_name) {
			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
			dbDelta( $sql );
			add_option( 'wpts_db_version', WPTS__DB_VERSION );
		}
		
		$installed_db_version = get_option( 'wpts_db_version' );
		if ( $installed_db_version != WPTS__DB_VERSION ) {
			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
			dbDelta( $sql );
			update_option( 'wpts_db_version', WPTS__DB_VERSION );
		}		
			
		$wpts_options = get_option( 'wpts_options' );
		if ( $wpts_options === false || !isset($wpts_options['job_list_display']) ){
			$default_wpts_options = array(
				'edit_timedata' => 1,
				'job_list_display' => 'autocomplete',
				'show_description' => 1
			);				
			add_option( 'wpts_options', $default_wpts_options );
		}
		
		$installed_version = get_option( 'wpts_version' );
		if ($installed_version != WPTS__VERSION)
			update_option( 'wpts_version', WPTS__VERSION );
	}
	
	public static function maybe_plugin_update() {
		
		$installed_db_version = get_option( 'wpts_db_version' );
		$installed_version = get_option( 'wpts_version' );
		
		if ( $installed_version != WPTS__VERSION || $installed_db_version != WPTS__DB_VERSION ){
          echo '<!-- WPTS__OUTDATED -->';
          WP_Timesheets::plugin_activate();
        }
		
	}

	/**
	 * Removes all connection options
	 * @static
	 */
	public static function plugin_deactivate() {
		
	}
	
	public static function get_report($args) {
		
		if( wp_verify_nonce( $args['_wpnonce'], 'View' ) === false)
			return false;
		
		global $wpdb;
		
		if ( WP_Timesheets_Admin::current_user_can('manage_options') ){
			if (!isset($args['user']))
				$args['user'] = 0;
		} else {
			$args['user'] = WP_Timesheets::$current_user->ID;
		}
		
		$args['date1'] = date('Y-m-d', strtotime($args['date1']));
		$args['date2'] = date('Y-m-d', strtotime($args['date2']));
		
		if($args['user'] != 0)
			$sql[] = "ts_author = '".$args['user']."'";
		$sql[] = "ts_time_in >= '".$args['date1']." 00:00:00' AND ts_time_out <= '".$args['date2']." 23:59:59'";
		$where_sql = implode(' AND ', $sql);
		
		switch ( strtolower($args['report_type']) ) {
			case 'list':
				$timesheet = $wpdb->get_results("SELECT ID, ts_author as 'User', date_format(ts_time_in,'%M %e, %Y') as 'Date', ts_job_name as 'Job', ts_description as 'Description', date_format(ts_time_in, '%h:%i %p') as 'From', date_format(ts_time_out, '%h:%i %p') as 'To', TIME_FORMAT(SEC_TO_TIME(TIMESTAMPDIFF(SECOND,ts_time_in,ts_time_out)),'%H:%i') as 'Hours', ts_other_fields as 'Other' FROM ".WP_Timesheets::$wpts_table." WHERE $where_sql ORDER BY ts_time_in DESC", ARRAY_A);
				foreach($timesheet as $key => $value){
					if( is_array( unserialize( $value['Other'] ) ) )
						foreach(unserialize( $value['Other'] ) as $o_key => $o_value)
							$timesheet[$key][$o_key] = $o_value;
					$user = get_userdata($value['User']);
					$timesheet[$key]['User ID'] = $user->ID;
					$timesheet[$key]['User'] = $user->display_name.' ('.$user->user_login.')';					
					unset($timesheet[$key]['Other']);
				}
				break;
			case 'by_user':
				$timesheet = $wpdb->get_results("SELECT ts_author as 'User', TIME_FORMAT(SEC_TO_TIME(SUM(TIMESTAMPDIFF(SECOND,ts_time_in,ts_time_out))),'%H:%i') as 'Hours' FROM ".WP_Timesheets::$wpts_table." WHERE $where_sql GROUP BY ts_author ORDER BY ts_time_in DESC", ARRAY_A);
				foreach($timesheet as $key => $value){
					$user = get_userdata($value['User']);
					$timesheet[$key]['User ID'] = $user->ID;
					$timesheet[$key]['User'] = $user->display_name.' ('.$user->user_login.')';
				}
				break;
			case 'by_job_name':
				$timesheet = $wpdb->get_results("SELECT ts_job_name as 'Job', TIME_FORMAT(SEC_TO_TIME(SUM(TIMESTAMPDIFF(SECOND,ts_time_in,ts_time_out))),'%H:%i') as 'Hours' FROM ".WP_Timesheets::$wpts_table." WHERE $where_sql GROUP BY ts_job_name ORDER BY ts_time_in DESC", ARRAY_A);
				break;
			case 'by_date':
				$timesheet = $wpdb->get_results("SELECT date_format(ts_time_in,'%M %e, %Y') as 'Date', TIME_FORMAT(SEC_TO_TIME(SUM(TIMESTAMPDIFF(SECOND,ts_time_in,ts_time_out))),'%H:%i') as 'Hours' FROM ".WP_Timesheets::$wpts_table." WHERE $where_sql GROUP BY date_format(ts_time_in,'%M %e, %Y') ORDER BY ts_time_in DESC", ARRAY_A);
				break;
			case 'by_week':
				$timesheet = $wpdb->get_results("SELECT date_format(ts_time_in,'Week %u of %Y') as 'Week', TIME_FORMAT(SEC_TO_TIME(SUM(TIMESTAMPDIFF(SECOND,ts_time_in,ts_time_out))),'%H:%i') as 'Hours' FROM ".WP_Timesheets::$wpts_table." WHERE $where_sql GROUP BY WEEK(ts_time_in,1) ORDER BY ts_time_in DESC", ARRAY_A);
				break;	
			case 'by_month':
				$timesheet = $wpdb->get_results("SELECT date_format(ts_time_in,'%M, %Y') as 'Month', TIME_FORMAT(SEC_TO_TIME(SUM(TIMESTAMPDIFF(SECOND,ts_time_in,ts_time_out))),'%H:%i') as 'Hours' FROM ".WP_Timesheets::$wpts_table." WHERE $where_sql GROUP BY date_format(ts_time_in,'%M, %Y') ORDER BY ts_time_in DESC", ARRAY_A);
				break;
			default:
				$timesheet = false;
				
		}
		
		return $timesheet;
	}
	
	public static function add_timedata($args) {
		
		check_admin_referer( 'Add' );
		
		global $wpdb;
		$wpts_options = get_option( WP_Timesheets_Admin::$settings['option_group'] );
		
		if( WP_Timesheets_Admin::current_user_can('manage_options') === false )
			$args['user'] = WP_Timesheets::$current_user->ID;
		
		if( !isset($args['description']) )
			$args['description'] = '';
		
		$datetime1 = strtotime($args['date'] . ' ' . $args['time1']);
		$datetime2 = strtotime($args['date'] . ' ' . $args['time2']);
		
		if( $datetime1 === false || $datetime2 === false ){
			WP_Timesheets::$error_msg = __('Error saving timedata: Invalid dates', 'wp-timesheets');
			return false;
		}
		
		$args['datetime1'] = date( 'Y-m-d H:i:s', $datetime1 );
		$args['datetime2'] = date( 'Y-m-d H:i:s', $datetime2 );
		
		$overlap = $wpdb->get_results("SELECT ID FROM ".WP_Timesheets::$wpts_table." WHERE ts_author = '".$args['user']."' AND (('".$args['datetime1']."' < ts_time_in AND '".$args['datetime2']."' > ts_time_in) OR ('".$args['datetime1']."' = ts_time_in) OR('".$args['datetime1']."' > ts_time_in AND '".$args['datetime1']."' < ts_time_out))", ARRAY_A);
		
		if( !isset($args['id']) ){
			if( !empty($overlap) ){
				WP_Timesheets::$error_msg = __('Error saving timedata: Job timedata already exists for the selected date and time', 'wp-timesheets');
				return false;
			}
			$wpdb->insert(
					WP_Timesheets::$wpts_table,
					array(
						'ts_author' => $args['user'], 
						'ts_job_name' => $args['job_name'], 
						'ts_description' => $args['description'], 
						'ts_time_in' => $args['datetime1'], 
						'ts_time_out' => $args['datetime2'],
						'ts_other_fields' => serialize( $args['other_field'] ),
					),
					array('%d','%s','%s','%s','%s','%s')
					);
			return $wpdb->insert_id;			
		} else {
			if( !isset($wpts_options['edit_timedata']) || $wpts_options['edit_timedata'] != 1 && WP_Timesheets_Admin::current_user_can('manage_options') === false ){
				WP_Timesheets::$error_msg = __('Error updating timedata: Insufficient access', 'wp-timesheets');
				return false;			
			}			
			if( count($overlap) === 1 && $overlap[0]['ID'] !== $args['id'] ){
				WP_Timesheets::$error_msg = __('Error updating timedata: Job timedata already exists for the selected date and time', 'wp-timesheets');
				return false;				
			}
			$wpdb->update(
					WP_Timesheets::$wpts_table,
					array(
						'ts_author' => $args['user'], 
						'ts_job_name' => $args['job_name'], 
						'ts_description' => $args['description'], 
						'ts_time_in' => $args['datetime1'], 
						'ts_time_out' => $args['datetime2'],
						'ts_other_fields' => serialize( $args['other_field'] ),
					),
					array( 'ID' => $args['id'] ),
					array('%d','%s','%s','%s','%s','%s')
					);
			return $args['id'];			
		}
		
	}
	
	public static function get_timedata($id, $wpnonce = '') {
		
		if( wp_verify_nonce( $wpnonce, 'Get_'.$id ) === false){
			WP_Timesheets::$error_msg = __('Error fetching timedata: Unauthorised access', 'wp-timesheets');
			return false;
		}
		
		global $wpdb;
		$args['id'] = $id;
		
		if ( WP_Timesheets_Admin::current_user_can('manage_options') ){
			if (!isset($args['user']))
				$args['user'] = 0;
		} else {
			$args['user'] = WP_Timesheets::$current_user->ID;
		}
		
		if($args['user'] != 0)
			$sql[] = "ts_author = '".$args['user']."'";
		$sql[] = "ID = '".$args['id']."'";
		$where_sql = implode(' AND ', $sql);
		
		$timesheet = $wpdb->get_results("SELECT ID as 'edit_id', ts_author as 'user', date_format(ts_time_in,'%e %b %Y') as 'date', ts_job_name as 'job_name', ts_description as 'description', date_format(ts_time_in, '%h:%i %p') as 'time1', date_format(ts_time_out, '%h:%i %p') as 'time2', ts_other_fields as 'Other' FROM ".WP_Timesheets::$wpts_table." WHERE $where_sql", ARRAY_A);
		
		if( empty($timesheet) ){
			WP_Timesheets::$error_msg = __('Error fetching timedata: Invalid timedata or unauthorised access', 'wp-timesheets');
			return false;
		}		
		
		foreach($timesheet as $key => $value){
			if( !empty($value['Other']) ){
				$other = unserialize( $value['Other'] );
				foreach($other as $o_key => $o_value)
					$timesheet[$key]['other_field'][$o_key] = $o_value;
			}				
			unset($timesheet[$key]['Other']);
		}		
		
		return $timesheet;
		
	}
}