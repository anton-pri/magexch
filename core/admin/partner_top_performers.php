<?php
# kornev, TOFIX
if(!$addons['Salesman'])
    cw_header_location("index.php?target=error_message&error=access_denied&id=24");

$location[] = array(cw_get_langvar_by_name("lbl_top_performers"), "");

if($StartDay)
	$search['start_date'] = mktime(0, 0, 0, $StartMonth, $StartDay, $StartYear);

if($EndDay)
    $search['end_date'] = mktime(23, 59, 59, $EndMonth, $EndDay, $EndYear);

if($search) {
	$where = array();
    if($search['start_date'] && $search['end_date'])
        $where[] = $search['end_date']." > $tables[salesman_clicks].add_date AND $tables[salesman_clicks].add_date > ".$search['start_date'];
	if($where)
		$where_condition = " AND ".implode(" AND ", $where);
	$result = cw_query("SELECT $tables[salesman_clicks].*, $tables[salesman_clicks].$search[report] as name, COUNT($tables[salesman_clicks].$search[report]) as clicks, SUM(($tables[orders].subtotal - $tables[orders].discount - $tables[orders].coupon_discount)) as sales, COUNT($tables[orders].subtotal) as num_sales  FROM $tables[salesman_clicks] LEFT JOIN $tables[orders] ON $tables[salesman_clicks].click_id = $tables[orders].click_id WHERE 1 ".$where_condition." GROUP BY $tables[salesman_clicks].$search[report] ORDER BY ".$search['sort']." DESC");
	if($result) {
		$smarty->assign('result', $result);
	}
}

$smarty->assign('search', $search);
$smarty->assign ("month_begin", mktime(0,0,0,date('m'),1,date('Y')));

$smarty->assign ('main', 'salesman_top_performers');
