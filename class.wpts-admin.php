<?php

class WP_Timesheets_Admin {
	
	public static $settings;

	public static function init() {
		
		add_action( 'admin_init', array( 'WP_Timesheets_Admin', 'admin_init' ) );
		add_action( 'admin_menu', array( 'WP_Timesheets_Admin', 'admin_menu' ) );

	}

	public static function admin_init() {
		
		// Load text domain
		load_plugin_textdomain( 'wp-timesheets' );
		
		// Load settings
		WP_Timesheets_Admin::$settings = 
		array(
			'sections' => array(
				array( 'id' => 'section_1', 'title' => '', 
					'callback' => array('WP_Timesheets_Admin', 'section_1_cb'), 'page' => 'wp_timesheets_settings' ),			
			),
			'fields' => array(
				array( 'id' => 'users_can', 'title' => __('Users can', 'wp-timesheets'), 
					'callback' => array('WP_Timesheets_Admin', 'fields_cb'), 'page' => 'wp_timesheets_settings', 
					'section' => 'section_1',  
					'args' => array( 'type' => 'group', 'data' => 'group_1' ) ),
				array( 'id' => 'job_list_display', 'title' => __('Job list display', 'wp-timesheets'), 
					'callback' => array('WP_Timesheets_Admin', 'fields_cb'), 'page' => 'wp_timesheets_settings', 
					'section' => 'section_1', 
					'args' => array( 'id' => 'job_list_display', 'type' => 'radio', 
						'options' => array( 
							array('value' => 'autocomplete', 'text' => __('Autocomplete — suggests existing jobs and allows adding new', 'wp-timesheets') ), 
							array('value' => 'dropdown', 'text' => __('Dropdown — restricted job list (entered below)', 'wp-timesheets') ) ), 
						'description' => __('Job list display options while managing timedata.', 'wp-timesheets') ) ),
				array( 'id' => 'job_list', 'title' => __('Job list', 'wp-timesheets'), 
					'callback' => array('WP_Timesheets_Admin', 'fields_cb'), 'page' => 'wp_timesheets_settings', 
					'section' => 'section_1', 
					'args' => array( 'id' => 'job_list', 'type' => 'textarea', 
						'description' => __('Used if job list is displayed as dropdown. Enter one job name per line.', 'wp-timesheets') ) ),
				array( 'id' => 'show_description', 'title' => __('Description visibility', 'wp-timesheets'), 
					'callback' => array('WP_Timesheets_Admin', 'fields_cb'), 'page' => 'wp_timesheets_settings', 
					'section' => 'section_1', 
					'args' => array( 'id' => 'show_description', 'type' => 'checkbox', 'text' => __('Show description while adding/editing timedata.', 'wp-timesheets') ) ),					
				array( 'id' => 'other_fields', 'title' => __('Other fields', 'wp-timesheets'), 
					'callback' => array('WP_Timesheets_Admin', 'fields_cb'), 'page' => 'wp_timesheets_settings', 
					'section' => 'section_1', 
					'args' => array( 'id' => 'other_fields', 'type' => 'textarea', 
						'description' => __('Other hour input fields for users. This can be used for capturing hours spent on lunch, travel, breaks etc. Enter one field name per line. Leave blank for no other fields.', 'wp-timesheets') ) ),				
			),
			'group_1' => array(
				array( 'id' => 'sc_posts',  
					'args' => array( 'id' => 'edit_timedata', 'type' => 'checkbox', 'text' => __('Edit own timedata', 'wp-timesheets') ) ),
				array( 'id' => 'sc_widgets', 
					'args' => array( 'id' => 'delete_timedata', 'type' => 'checkbox', 'text' => __('Delete own timedata', 'wp-timesheets') ) ),
				'description' => __('Applicable for users below Admin level only. Admins have all rights.', 'wp-timesheets')
			),
			'option_group' => 'wpts_options'
		);
		WP_Timesheets_Admin::settings_api( WP_Timesheets_Admin::$settings );
		
		// Settings page link
		add_filter( 'plugin_action_links_' . WPTS__PLUGIN_FILE, array('WP_Timesheets_Admin', 'plugin_settings_link'), 10, 2 );		
		
	}
	
	public static function settings_api( $settings ) {
		foreach ($settings['sections'] as $section)
			add_settings_section( $section['id'], $section['title'], $section['callback'], $section['page'] );
		foreach ($settings['fields'] as $field)
			add_settings_field( $field['id'], $field['title'], $field['callback'], $field['page'], $field['section'], $field['args'] );
		register_setting( $settings['option_group'], $settings['option_group'] );
	}
	
	public static function section_1_cb() {
		
	}
	
	public static function fields_cb( $option ) {
		
		$wpts_options = get_option( WP_Timesheets_Admin::$settings['option_group'] );
		
		$options = array( $option );
		if( $option['type'] === 'group' ){
			foreach (WP_Timesheets_Admin::$settings[$option['data']] as $custom_key => $custom_field)
				if(is_string($custom_key))
					$options[$custom_key] = $custom_field;
				else 
					$options[] = $custom_field['args'];
		}
		
		echo '<fieldset>';
		foreach ($options as $option) {
			if( $option['type'] === 'text' ){
				echo '<input name="'.WP_Timesheets_Admin::$settings['option_group'].'['.$option['id'].']" type="text" id="'.$option['id'].'" class="regular-text" value="'.$wpts_options[$option['id']].'" />';
			}
			if( $option['type'] === 'textarea' ){
				echo '<textarea name="'.WP_Timesheets_Admin::$settings['option_group'].'['.$option['id'].']" id="'.$option['id'].'" rows="5" cols="50" class="regular-text">'.$wpts_options[$option['id']].'</textarea>';
			}			
			if( $option['type'] === 'number' ){
				echo '<input name="'.WP_Timesheets_Admin::$settings['option_group'].'['.$option['id'].']" type="number" id="'.$option['id'].'" step="'.$option['step'].'" min="'.$option['min'].'" class="small-text" value="'.$wpts_options[$option['id']].'" />';
			}
			if( $option['type'] === 'checkbox' ){
				echo '<p><label><input name="'.WP_Timesheets_Admin::$settings['option_group'].'['.$option['id'].']" type="checkbox" id="'.$option['id'].'" value="1" '.checked( 1, $wpts_options[$option['id']], false ).' /> '.$option['text'].'</label></p>';
			}	
			if( $option['type'] === 'radio' ){
				foreach ($option['options'] as $value)
					echo '<p><label><input name="'.WP_Timesheets_Admin::$settings['option_group'].'['.$option['id'].']" type="radio" id="'.$option['id'].'" value="'.$value['value'].'" '.checked( $value['value'], $wpts_options[$option['id']], false ).' /> '.$value['text'].'</label></p>';	
			}
			if( $option['type'] === 'select' ){
				echo '<select name="'.WP_Timesheets_Admin::$settings['option_group'].'['.$option['id'].']" id="'.$option['id'].'">';
				foreach ($option['options'] as $value)
					echo '<option value="'.$value['value'].'" '.selected( $value['value'], $wpts_options[$option['id']], false ).'>'.$value['text'].'</option>';
				echo '</select>';
			}
			if(isset($option['description'])) echo '<p class="description">'.$option['description'].'</p>';
		}
		if(isset($options['description'])) echo '<p class="description">'.$options['description'].'</p>';
		echo '</fieldset>';
		
	}
	
	public static function admin_menu() {
		
		$settings_page = add_options_page(
			__('WP Timesheets Settings', 'wp-timesheets'), 
			__('WP Timesheets', 'wp-timesheets'), 
			'manage_options', 
			'wp_timesheets', 
			array('WP_Timesheets_Admin', 'plugin_settings_page')
		);
		
		$user_view_page = add_menu_page( 
			__('Timesheets', 'wp-timesheets'), 
			__('Timesheets', 'wp-timesheets'), 
			'read', 
			'wp_timesheets_user_report', 
			array('WP_Timesheets_Admin', 'plugin_user_report_page')
		);
		add_submenu_page(
			'wp_timesheets_user_report',
			__('View Timesheets', 'wp-timesheets'), 
			__('View Timesheets', 'wp-timesheets'), 
			'read', 
			'wp_timesheets_user_report', 
			array('WP_Timesheets_Admin', 'plugin_user_report_page')
		);
		$user_manage_page = add_submenu_page(
			'wp_timesheets_user_report',
			__('Manage Timedata', 'wp-timesheets'), 
			__('Manage Timedata', 'wp-timesheets'), 
			'read', 
			'wp_timesheets_user_manage', 
			array('WP_Timesheets_Admin', 'plugin_user_manage_page')
		);		
		
		add_action('admin_print_scripts-' . $settings_page, array( 'WP_Timesheets_Admin', 'admin_scripts' ));
		add_action('admin_print_scripts-' . $user_view_page, array( 'WP_Timesheets_Admin', 'admin_scripts' ));
		add_action('admin_print_scripts-' . $user_manage_page, array( 'WP_Timesheets_Admin', 'admin_scripts' ));
		
	}
	
	public static function admin_scripts(){
		
		wp_enqueue_script( 'wpts-js', plugins_url( '/views/js/wpts.js', __FILE__ ), array('jquery-ui-datepicker','jquery-ui-autocomplete'), WPTS__VERSION );
		wp_enqueue_style( 'jquery-ui-theme-smoothness', '//ajax.googleapis.com/ajax/libs/jqueryui/1.11.1/themes/smoothness/jquery-ui.min.css', array(), '1.11.1' );
		wp_enqueue_style( 'wpts-css', plugins_url( '/views/css/wpts.css', __FILE__ ), array(), WPTS__VERSION );
		
	}
    
    public static function current_user_can( $capability ){
        
        $cu = wp_get_current_user();
        $cuc = current_user_can( $capability );
        $cugrc = $cu->get_role_caps();
        if($cuc === true && isset( $cugrc[$capability] ) && $cugrc[$capability] === true )
          return true;
        return false;
    }
	
	public static function plugin_settings_page() {
		WP_Timesheets::view( 'settings' );
	}	
	
	public static function plugin_user_report_page(){
		
		if( !empty($_POST) )
			$vars = $_POST;	
		
		if( !isset($vars['_wpnonce']) )
			$vars['_wpnonce'] = wp_create_nonce( 'View' );
		if( (!isset($vars['date1'])) || (strtotime($vars['date1']) === false) ) 
			$vars['date1'] = date('j M Y', mktime(0,0,1,date('n'),date('d')-7,date('Y')));
		if( (!isset($vars['date2'])) || (strtotime($vars['date2']) === false) ) 
			$vars['date2'] = date('j M Y', mktime(0,0,1,date('n'),date('d')-1,date('Y')));
		if( !isset($vars['report_type']) ) 
			$vars['report_type'] = 'list';
		$vars['report_range_data'] = array(
			'l7d' => array( date('j M Y', mktime(0,0,1,date('n'),date('d')-7,date('Y'))), date('j M Y', mktime(0,0,1,date('n'),date('d')-1,date('Y'))) ),
			'l14d' => array( date('j M Y', mktime(0,0,1,date('n'),date('d')-14,date('Y'))), date('j M Y', mktime(0,0,1,date('n'),date('d')-1,date('Y'))) ),
			'l30d' => array( date('j M Y', mktime(0,0,1,date('n'),date('d')-30,date('Y'))), date('j M Y', mktime(0,0,1,date('n'),date('d')-1,date('Y'))) ),
			'tm' => array( date('j M Y', mktime(0,0,1,date('n'),1,date('Y'))), date('j M Y') ),
			'lm' => array( date('j M Y', mktime(0,0,1,date('n')-1,1,date('Y'))), date('j M Y', mktime(0,0,1,date('n'),0,date('Y'))) ),
		);
		
		$vars['timesheet'] = WP_Timesheets::get_report($vars);
		
		WP_Timesheets::view( 'user-report', $vars );
	}	

	public static function plugin_user_manage_page(){
		
		$vars = array_merge($_POST, $_GET);
		if( !isset($vars['date']) ) 
			$vars['date'] = date('j M Y');
		if( !isset($vars['time1']) ) 
			$vars['time1'] = '9:00 AM';
		if( !isset($vars['time2']) ) 
			$vars['time2'] = '5:00 PM';	
		if( !isset($vars['verb']) ) 
			$vars['verb'] = 'add';			
		
		$wpts_options = get_option( WP_Timesheets_Admin::$settings['option_group'] );
		$vars['show_description'] = $wpts_options['show_description'];
		
		// Add logic
		if( !empty($_POST) ){
			$vars['add_id'] = WP_Timesheets::add_timedata($_POST);
			$vars['error_msg'] = WP_Timesheets::$error_msg;
			if($vars['add_id'] !== false){
				WP_Timesheets::view( 'user-view', $vars );
				return;
			}		
		}
		
		// Edit logic
		if( $vars['action'] === 'edit' && isset($vars['id']) ){
			$vars['verb'] = 'Edit';
			$timedata =  WP_Timesheets::get_timedata( $vars['id'], $_GET['_wpnonce'] );
			$vars['error_msg'] = WP_Timesheets::$error_msg;
			if($timedata !== false)
				$vars = array_merge($vars, $timedata[0]);
			if($timedata === false)
				$vars['edit_id'] = false;			
		}
		
		// View logic
		if( $vars['action'] === 'view' && isset($vars['id']) ){
			$vars['verb'] = 'View';
			$timedata =  WP_Timesheets::get_timedata( $vars['id'], $_GET['_wpnonce'] );
			$vars['error_msg'] = WP_Timesheets::$error_msg;
			if($timedata !== false){
				$vars = array_merge($vars, $timedata[0]);
				WP_Timesheets::view( 'user-view', $vars );
				return;				
			}
		}		
		
		// For Add and Edit logic
		WP_Timesheets::view( 'user-manage', $vars );
		
	}	
	
	public static function plugin_settings_link($links) {
		$settings_link = '<a href="options-general.php?page=wp_timesheets">' . __('Settings', 'wp-timesheets') . '</a>';
		array_unshift($links, $settings_link);
		return $links;
	}
	
	public static function display_job_list($job_name){
		
		global $wpdb;
		
		$wpts_options = get_option( WP_Timesheets_Admin::$settings['option_group'] );
		$vars['display'] = $wpts_options['job_list_display'];
		
		$existing_jobs = $wpdb->get_results("SELECT ts_job_name as 'job_name' FROM ".WP_Timesheets::$wpts_table." GROUP BY ts_job_name ORDER BY ts_job_name ASC", ARRAY_A);
		$existing_job_list = array();
		foreach($existing_jobs as $job)
			$existing_job_list[] = $job['job_name'];	
	
		$admin_job_list = explode(PHP_EOL, $wpts_options['job_list']);
		
		if($vars['display'] === 'autocomplete')
			$vars['data'] = array_merge( $existing_job_list, array_diff( $admin_job_list, $existing_job_list ) );
		elseif($vars['display'] === 'dropdown')
			$vars['data'] = $admin_job_list;
		
		$vars['value'] = $job_name;
		
		WP_Timesheets::view( 'job-list', $vars );
		
	}	
	
	public static function display_other_fields($other_field, $view = false){
		
		$wpts_options = get_option( WP_Timesheets_Admin::$settings['option_group'] );
		$other_fields = trim( $wpts_options['other_fields'] );
		
		if( empty($other_fields) )
			return;
		
		$vars['data'] = explode(PHP_EOL, $other_fields);
		$vars['values'] = $other_field;
		$vars['view'] = $view;
		
		WP_Timesheets::view( 'other-fields', $vars );
		
	}
	
	public static function display_timesheet_report($timesheet, $report_type){
		
		if(empty($timesheet)){
			WP_Timesheets::view( 'report-empty');
			return;
		}
		
		$vars['timesheet'] = $timesheet;
		
		if($report_type === 'list'){
			
			$wpts_options = get_option( WP_Timesheets_Admin::$settings['option_group'] );
			$other_fields = trim( $wpts_options['other_fields'] );
			if( !empty($other_fields) )
				$vars['other_fields'] = explode(PHP_EOL, $other_fields);
			else 
				$vars['other_fields'] = array();
			
			WP_Timesheets::view( 'report-list', $vars );
			
		} else {
			
			WP_Timesheets::view( 'report-other', $vars );
            
        }
		
	}

}