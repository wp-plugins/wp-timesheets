<?php

$parse_uri = explode( 'wp-content', $_SERVER['SCRIPT_FILENAME'] );
require_once( $parse_uri[0] . 'wp-load.php' );

check_admin_referer( 'View' );

require_once( 'class.wpts.php' );

$vars = array_merge($_GET, $_POST);

if( (!isset($vars['date1'])) || (strtotime($vars['date1']) === false) ) 
	$vars['date1'] = date('j M Y', mktime(0,0,1,date('n'),date('d')-6,date('Y')));
if( (!isset($vars['date2'])) || (strtotime($vars['date2']) === false) ) 
	$vars['date2'] = date('j M Y');
if( !isset($vars['report_type']) ) 
	$vars['report_type'] = 'list';

$filename = 'wpts-'.$vars['report_type'].'-report-'.str_replace(' ','-',$vars['date1']).'-'.str_replace(' ','-',$vars['date2']).'.csv';
$reportdata = WP_Timesheets::get_report($vars);

if(empty($reportdata)){
	load_plugin_textdomain( 'wp_timesheets' );
	wp_die(_e('There seem to be no data for the selected date range.', 'wp_timesheets' ));
}

header("Content-type: text/csv; charset=utf-8",true,200);
header("Content-Disposition: attachment; filename=$filename");

$fp = fopen('php://output', 'w');

if($vars['report_type'] === 'list'){
	$wpts_options = get_option( 'wpts_options' );
	$other_fields_text = trim( $wpts_options['other_fields'] );
	if( !empty($other_fields_text) )
		$other_fields = explode(PHP_EOL, $other_fields_text);
	else 
		$other_fields = array();
	$header_fields = array_merge(array('User','Date','Job','Description','From','To','Hours'), $other_fields);
	fputcsv($fp, $header_fields);
	foreach ($reportdata as $data){
		$row = array();
		foreach($header_fields as $column){
			if(in_array($column,$other_fields)){
				$row[] = (!empty($data[$column]) ? date('H:i',$data[$column]*3600) : '');
			} else {
				$row[] = $data[$column];
			}
		}
		fputcsv($fp, $row);
	}
} else {
	fputcsv($fp, array_keys($reportdata[0]));
	foreach ($reportdata as $data)
		fputcsv($fp, $data);
}
fclose($fp);
exit();	