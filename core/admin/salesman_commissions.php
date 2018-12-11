<?php
if (!$addons['Salesman'])
    cw_header_location('index.php');

if ($action == "apply" && is_array($plans)) {
    foreach ($plans as $k=>$v)
        db_query("REPLACE INTO $tables[salesman_commissions] (salesman_customer_id, plan_id) VALUES ('$k', '$v')");

	cw_header_location("index.php?target=salesman_commissions&mode=go&page=$page&salesman=$salesman&applied");
}
elseif ($action == "apply_global" && $pc) {
// TODO: no SQL, API only
	$salesmans = cw_query_column("SELECT customer_id FROM $tables[customers] WHERE usertype = 'B'".(($salesman && $use_filter) ? " AND email LIKE '%$salesman%'" : ""));
	if ($salesmans)
    foreach ($salesmans as $v)
	    db_query("REPLACE INTO $tables[salesman_commissions] (salesman_customer_id, plan_id) VALUES ('$v', '$pc')");
	$top_message['content'] = cw_get_langvar_by_name("txt_plan_was_successfully_applied");
	$top_message['type'] = "I";

	cw_header_location("index.php?target=salesman_commissions&mode=go&pc=$pc&salesman=$salesman&use_filter=$use_filter");
}
elseif ($action == "go") {
// TODO: no SQL, API only
	$salesman_info = cw_query("SELECT $tables[salesman_commissions].plan_id, $tables[customers].customer_id FROM $tables[customers] LEFT JOIN $tables[salesman_commissions] ON $tables[salesman_commissions].salesman_customer_id=$tables[customers].customer_id WHERE $tables[customers].usertype = 'B' AND $tables[customers].email LIKE '".((empty($salesman) || $use_filter != 'Y') ? "%" : "%$salesman%")."' order by $tables[customers].customer_id");
	$smarty->assign ("salesman_info", $salesman_info);

    $smarty->assign('mode', 'go');
}

$salesmans = cw_user_get_short_list('B');
$salesman_plans = cw_query("SELECT * FROM $tables[salesman_plans] ORDER BY title, plan_id");

$smarty->assign("salesman_plans", $salesman_plans);
$smarty->assign("salesmans", $salesmans);
$smarty->assign("salesman", $salesman);
$smarty->assign("use_filter", $use_filter);

$smarty->assign('main', 'commissions');
?>
