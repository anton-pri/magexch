<?php
if (!$addons['Salesman'])
    cw_header_location("index.php?target=error_message&error=access_denied&id=27");

# Define data for the navigation within section

if ($StartDay)
	$search['start_date'] = mktime(0, 0, 0, $StartMonth, $StartDay, $StartYear);

if ($EndDay)
	$search['end_date'] = mktime(23, 59, 59, $EndMonth, $EndDay, $EndYear);

if ($search) {
	$where = array();

	if ($search['start_date'] && $search['end_date'])
		$where[] = $search['end_date']." > $tables[orders].date AND $tables[orders].date > ".$search['start_date'];
	if ($search['customer_id'])
		$where[] = "$tables[salesman_payment].customer_id='$search[customer_id]'";

	if($search['status'])
		$where[] = "$tables[orders].status = '$search[status]'";

	if($search['doc_id'])
		$where[] = "$tables[orders].display_id = '$search[doc_id]'";

	if ($search['paid'])
		$where[] = " IF($tables[salesman_payment].paid = 'Y', 'Y', IF($tables[orders].status IN ('C','P'), 'A', 'N')) = '$search[paid]'";

	if ($where)
		$where_condition = " AND ".implode(" AND ", $where);

//	$report = cw_query("SELECT $tables[salesman_payment].*, $tables[customers].*, ($tables[orders].subtotal-$tables[orders].discount-$tables[orders].coupon_discount) as subtotal, $tables[orders].date, $tables[orders].status AS order_status, IF($tables[salesman_payment].paid = 'Y', 'Y', IF($tables[orders].status IN ('C','P'), 'A', '')) as paid FROM $tables[salesman_payment], $tables[orders], $tables[customers] WHERE $tables[salesman_payment].customer_id=$tables[customers].customer_id AND $tables[salesman_payment].doc_id=$tables[orders].doc_id AND $tables[customers].status = 'Y' AND $tables[customers].usertype = 'B'".$where_condition." ORDER BY $tables[salesman_payment].add_date, $tables[customers].customer_id");
    $report = cw_query("SELECT $tables[salesman_payment].*, $tables[customers].*, ($tables[orders].subtotal-$tables[orders].discount-$tables[orders].coupon_discount) as subtotal, $tables[orders].date, $tables[orders].status AS order_status, $tables[orders].display_id, IF($tables[salesman_payment].paid = 'Y', 'Y', IF($tables[orders].status IN ('C','P'), 'A', '')) as paid FROM $tables[salesman_payment], $tables[orders], $tables[customers] WHERE $tables[salesman_payment].customer_id=$tables[customers].customer_id AND $tables[salesman_payment].doc_id=$tables[orders].doc_id AND $tables[customers].status = 'Y' AND $tables[customers].usertype = 'B'".$where_condition." ORDER BY $tables[salesman_payment].add_date, $tables[customers].customer_id");

	if ($action == 'export') {
		$smarty->assign ("delimiter", $delimiter);
		if ($report) {
			foreach ($report as $key=>$value) {
				foreach ($value as $rk=>$rv) {
					$report[$key][$rk] = '"' . str_replace ("\"", "\"\"", $report[$key][$rk]) . '"';
				}
			}
		}

		$smarty->assign ("report", $report);

		header ("Content-Type: text/csv");
		header("Content-Disposition: attachment; filename=salesman_orders.csv");
		cw_display("admin/main/salesman_orders_export.tpl",$smarty);
		exit;
	}

	$smarty->assign ("orders", $report);
}

$smarty->assign ("main", "salesman_orders");

$smarty->assign('salesmans', cw_query("SELECT customer_id FROM $tables[customers] WHERE usertype = 'B' AND status = 'Y' ORDER BY customer_id"));

$smarty->assign('search', $search);
$smarty->assign ("month_begin", mktime(0,0,0,date('m'),1,date('Y')));
