<?php

//cw_load('logs');

$location[] = array(cw_get_langvar_by_name("lbl_shop_logs"), "index.php?target=logs");

function logs_convert_date($posted_data) {
	$start_date = false;
	$end_date = time();

	switch ($posted_data['date_period']) {
		case 'D': # Today
			$start_date = time();
			break;

		case 'W': # This week
			$first_weekday = $end_date - (date("w",$end_date) * 86400);
			$start_date = mktime(0,0,0,date("n",$first_weekday),date("j",$first_weekday),date("Y",$first_weekday));
			break;

		case 'M': # This month
			$start_date = mktime(0,0,0,date("n",$end_date),1,date("Y",$end_date));
			break;

		case 'C': # Custom range
			$start_date = $posted_data['start_date'];
			$end_date = $posted_data['end_date'];
			break;
	}

	return array($start_date, $end_date);
}

global $registered_logs, $logs_files_summary_size; 

#
# Log names translation
#
$log_labels = cw_log_get_names();
$logs_search_data = &cw_session_register('logs_search_data', array());

if ($REQUEST_METHOD != 'POST')
	$posted_data = $logs_search_data;

if ($REQUEST_METHOD == 'POST' && !empty($posted_data)) {

	$need_advanced_options = false;
	foreach ($posted_data as $k=>$v) {
		if (!is_array($v) && !is_numeric($v))
			$posted_data[$k] = stripslashes($v);

		if (is_array($v)) {
			$tmp = array();
			foreach ($v as $k1=>$v1) {
				$tmp[$v1] = 1;
			}
			$posted_data[$k] = $tmp;
		}
	}

	if (empty($posted_data['logs'])) {
		$posted_data['logs'] = false;
	}

	if ($StartMonth) {
			$posted_data['start_date'] = mktime(0,0,0,$StartMonth,$StartDay,$StartYear);
			$posted_data['end_date'] = mktime(23,59,59,$EndMonth,$EndDay,$EndYear);
	}

	$logs_search_data = $posted_data;

	if ($action == "clean") {
		list($start_date, $end_date) = logs_convert_date($posted_data);
		$labels = array();
		if (!empty($posted_data['logs']) && is_array($posted_data['logs']))
			$labels = array_keys($posted_data['logs']);

		$error_files = array();
		$_tmp = cw_log_list_files($labels, $start_date, $end_date);
        
		if (is_array($_tmp)) {
			foreach ($_tmp as $l=>$d) {
				foreach ($d as $ts=>$file) {
					$file = $var_dirs['log'].'/'.$file;
					if (ini_get('error_log') !== $file && @unlink($file) === false)
						$error_files[] = $file;
				}
			}
		}

		if (!empty($error_files)) {
			$top_message['type'] = 'E';
			$top_message['content'] = cw_get_langvar_by_name('err_files_delete_perms', array('files'=>implode("\n",$error_files)),$current_language,true);
		}
		else {
			$top_message['type'] = 'I';
			$top_message['content'] = cw_get_langvar_by_name('msg_logs_deleted_ok');
		}
	}

	cw_header_location('index.php?target=logs');
}

if (!empty($posted_data)) {
	if (!isset($posted_data['date_period'])) $posted_data['date_period'] = 'D';

	if (!isset($posted_data['count']) || (int)$posted_data['count'] < 0)
		$posted_data['count'] = 0;
	else
		$posted_data['count'] = (int)$posted_data['count'];

	$logs_search_data = $posted_data;

	list($start_date, $end_date) = logs_convert_date($posted_data);

	$logs_data = "";
	$labels = array();

	if (!empty($posted_data['logs']) && is_array($posted_data['logs'])) {
	    $labels = array_keys($posted_data['logs']);
  	    $_tmp = cw_log_get_contents($labels, $start_date, $end_date, true, $posted_data['count']);
        }

	if (is_array($_tmp) && !empty($_tmp)) {
		foreach ($_tmp as $label=>$_data) {
			$dialog_tools_data['left'][] = array("link" => '#result_'.$label, 'title' => (!empty($log_labels[$label]) ? $log_labels[$label] : $label));
		}
		$logs_data = $_tmp;
	}

	$smarty->assign('show_results', 1);
}
else {
	$posted_data = array();
	$posted_data['date_period'] = 'D';
/*
	foreach ($log_labels as $k=>$v) {
		$posted_data['logs'][$k] = 1;
	}
*/
	$posted_data['count'] = 20;
}

ksort($registered_logs);
$smarty->assign('registered_logs', $registered_logs);
$smarty->assign('summary_size', cw_logs_size_format($logs_files_summary_size));


$smarty->assign('log_labels', $log_labels);
$smarty->assign('search_prefilled', $posted_data);
$smarty->assign('logs', $logs_data);
$smarty->assign('main', 'logs');
