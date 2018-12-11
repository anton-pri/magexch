<?php
# kornev, TOFIX
if (!$addons['Salesman'])
    cw_header_location('index.php');

if($action == 'paid' && $paid && $REQUEST_METHOD == 'POST') {
	foreach($paid as $k => $v) {
		$orders = cw_query("SELECT $tables[orders].doc_id FROM $tables[orders], $tables[salesman_payment] WHERE $tables[salesman_payment].salesman_customer_id='$k' AND $tables[salesman_payment].doc_id = $tables[orders].doc_id AND $tables[orders].status IN ('P', 'C') AND $tables[salesman_payment].paid <> 'Y'");
		$ids = array();
		if($orders) {
			for($x = 0; $x < count($orders); $x++)
				$ids[] = $orders[$x]['doc_id'];
			if(!empty($ids))
				db_query("UPDATE $tables[salesman_payment] SET paid = 'Y' WHERE customer_id='$k' AND doc_id IN ('".implode("','", $ids)."')");
		}
	}
	cw_header_location("index.php?target=salesman_report");
} 
elseif($action == 'export') {
	$smarty->assign ("delimiter", $delimiter);
	$report = cw_query("SELECT $tables[customers].*, $tables[salesman_plans].min_paid, SUM(IF($tables[docs].status NOT IN ('P', 'C'), $tables[salesman_payment].commissions, 0)) as sum, SUM(IF($tables[salesman_payment].paid = 'Y' AND $tables[docs].status IN ('P', 'C'), $tables[salesman_payment].commissions, 0)) as sum_paid, SUM(IF($tables[salesman_payment].paid <> 'Y' AND $tables[docs].status IN ('P', 'C'), $tables[salesman_payment].commissions, 0)) as sum_nopaid, IF(SUM(IF(($tables[salesman_payment].paid <> 'Y' AND $tables[docs].status IN ('P', 'C')), $tables[salesman_payment].commissions, 0)) >= $tables[salesman_plans].min_paid, 'Y', '') as is_paid FROM $tables[customers], $tables[salesman_payment], $tables[salesman_commissions], $tables[salesman_plans], $tables[docs] WHERE $tables[salesman_plans].plan_id = $tables[salesman_commissions].plan_id AND $tables[salesman_commissions].salesman_customer_id=$tables[customers].customer_id AND $tables[customers].customer_id=$tables[salesman_payment].salesman_customer_id AND $tables[docs].doc_id = $tables[salesman_payment].doc_id AND $tables[customers].usertype = 'B' AND $tables[customers].status = 'Y' GROUP BY $tables[customers].customer_id".($use_limit == 'Y'?" HAVING is_paid = 'Y'":""));
	if ($report) {
		foreach ($report as $key=>$value) {
			foreach ($value as $rk=>$rv)
				$report[$key][$rk] = '"' . str_replace ("\"", "\"\"", $report[$key][$rk]) . '"';
		}
	}
	$smarty->assign ("report", $report);

	header ("Content-Type: text/csv");
	header("Content-Disposition: attachment; filename=salesman_report.csv");
	cw_display("admin/main/salesman_report_export.tpl",$smarty);
	exit;
}

$result = cw_query("SELECT $tables[customers].customer_id, $tables[salesman_plans].min_paid, SUM(IF($tables[docs].status NOT IN ('P', 'C'), $tables[salesman_payment].commissions, 0)) as sum, SUM(IF($tables[salesman_payment].paid = 'Y' AND $tables[docs].status IN ('P', 'C'), $tables[salesman_payment].commissions, 0)) as sum_paid, SUM(IF($tables[salesman_payment].paid <> 'Y' AND $tables[docs].status IN ('P', 'C'), $tables[salesman_payment].commissions, 0)) as sum_nopaid, IF(SUM(IF(($tables[salesman_payment].paid <> 'Y' AND $tables[docs].status IN ('P', 'C')), $tables[salesman_payment].commissions, 0)) >= $tables[salesman_plans].min_paid, 'Y', '') as is_paid FROM $tables[customers], $tables[salesman_payment], $tables[salesman_commissions], $tables[salesman_plans], $tables[docs] WHERE $tables[salesman_plans].plan_id = $tables[salesman_commissions].plan_id AND $tables[salesman_commissions].salesman_customer_id=$tables[customers].customer_id AND $tables[customers].customer_id=$tables[salesman_payment].salesman_customer_id AND $tables[docs].doc_id = $tables[salesman_payment].doc_id AND $tables[customers].usertype = 'B' AND $tables[customers].status = 'Y' GROUP BY $tables[customers].customer_id".($use_limit == 'Y'?" HAVING is_paid = 'Y'":""));
if($result) {
	foreach($result as $k=>$v) {
		if($v['is_paid'])
			$is_paid = 'Y';
	}
	$smarty->assign ("is_paid", $is_paid);
}
$smarty->assign ("result", $result);
$smarty->assign('use_limit', $use_limit);

$smarty->assign('main', 'report');
