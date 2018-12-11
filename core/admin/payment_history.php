<?php
$ctime = cw_core_get_time();

$start_date = mktime (0,0,0,date("m",$ctime),1,date("Y",$ctime));
$end_date = $ctime;

if ($action =="go") {
	$salesman_condition = ($salesman ? " AND $tables[salesman_payment].customer_id='$salesman'" : "");

	if($StartMonth) {
		$start_date=mktime(0,0,0,$StartMonth,$StartDay,$StartYear);
		$end_date=mktime(23,59,59,$EndMonth,$EndDay,$EndYear);
	}

	$query = "SELECT $tables[salesman_payment].*, $tables[customers].* FROM $tables[salesman_payment], $tables[customers] WHERE $tables[salesman_payment].paid='Y' AND $tables[salesman_payment].customer_id=$tables[customers].customer_id AND $tables[salesman_payment].add_date>='$start_date' AND $tables[salesman_payment].add_date<='$end_date' $salesman_condition ORDER BY $tables[salesman_payment].add_date desc";

	$total_history = count(cw_query($query));

    $navigation = cw_core_get_navigation($target, $total_history, $page);
    $navigation['script'] = "index.php?target=payment_history&StartMonth=$StartMonth&StartDay=$StartDay&StartYear=$StartYear&EndMonth=$EndMonth&EndDay=$EndDay&EndYear=$EndYear&salesman=$salesman&mode=go";
    $smarty->assign('navigation', $navigation);

	$smarty->assign ("history", cw_query ("$query LIMIT $navigation[first_page], $navigation[objects_per_page]"));
}

$salesmans = cw_query ("SELECT * FROM $tables[customers] WHERE usertype='B' ORDER BY lastname");
$smarty->assign ("salesmans", $salesmans);

$smarty->assign('history', $history);
$smarty->assign('salesman', $salesman);
$smarty->assign('start_date', $start_date);
$smarty->assign('end_date', $end_date);

$smarty->assign ('main', 'payment_history');
