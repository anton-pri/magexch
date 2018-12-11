<?php
if (!defined('APP_START')) die('Access denied');

if ($current_area == 'A' && $customer_id)
    $where_condition = '1';
if($current_area == 'C' || empty($customer_id))
	$where_condition = " usertype IN ('C', 'R')";
elseif($current_area == 'P')
	$where_condition = " usertype IN ('C', 'R', 'P')";
elseif($current_area == 'B')
	$where_condition = " usertype IN ('C', 'R', 'B')";
elseif($current_area == 'G')
    $where_condition = " usertype IN ('G')";

if ($where_condition) {
    $users_online = cw_query("SELECT usertype, COUNT(*) as count, IF(customer_id, 'Y', 'N') as is_registered FROM $tables[sessions_data] WHERE ".$where_condition." GROUP BY usertype, is_registered");
    $smarty->assign('users_online', $users_online);
}
?>
