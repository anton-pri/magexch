<?php
if ($customer_id) {
    cw_load('salesman', 'doc');
    cw_cleanup_target($customer_id);
    $current_level = cw_salesman_current_level($customer_id);
    $target = cw_salesman_get_target($customer_id);
    $smarty->assign('current_level', $current_level);
    $smarty->assign('salesman_target', $target);
    $smarty->assign('salesman_reach', $target['target'] - $current_level);
    $smarty->assign('salesman_reached', cw_salesman_is_reached($customer_id));
    $premiums_selected = cw_salesman_is_selected($customer_id);
    $smarty->assign('salesman_selected', $premiums_selected);
    $smarty->assign('salesman_premiums', cw_salesman_get_premiums($customer_id, $current_language, " and active=1".($premiums_selected?" and selected=1":"")));

    if ($action == 'premiums' && !$premiums_selected && is_array($choosed_premium)) {
        foreach($choosed_premium as $id=>$val)
            db_query("update $tables[salesman_premiums] set selected=1 where id='$id' and customer_id='$customer_id'");
    }

# kornev, select orders
    $previous_customer_id_date = &cw_session_register('previous_customer_id_date');
    $curtime = cw_core_get_time();
    $start_dates[] = $previous_customer_id_date;  # Since last customer_id
    $start_dates[] = mktime(0,0,0,date("m",$curtime),date("d",$curtime),date("Y",$curtime));
    $start_week = $curtime - (date("w",$curtime))*24*3600; # Week starts since Sunday
    $start_dates[] = mktime(0,0,0,date("m",$start_week),date("d",$start_week),date("Y",$start_week));
    $start_dates[] = mktime(0,0,0,date("m",$curtime),1,date("Y",$curtime));

    foreach($start_dates as $start_date) {
        $date_condition = "and d.doc_info_id=di.doc_info_id and d.date>='$start_date' and d.date<='$curtime' and di.salesman_customer_id='$customer_id'";
    
        $orders['P'][] = cw_query_first_cell("select count(*) from $tables[docs] as d, $tables[docs_info] as di where d.type='O' and d.status='P' $date_condition");
        $orders['F'][] = cw_query_first_cell("select count(*) from $tables[docs] as d, $tables[docs_info] as di where d.type='O' and (d.status='F' OR d.status='D') $date_condition");
        $orders['I'][] = cw_query_first_cell("select count(*) from $tables[docs] as d, $tables[docs_info] as di where d.type='O' and d.status='I' $date_condition");
        $orders['Q'][] = cw_query_first_cell("select count(*) from $tables[docs] as d, $tables[docs_info] as di where d.type='O' and d.status='Q' $date_condition");
        $gross_total[] = price_format(cw_query_first_cell("select sum(total) from $tables[docs] as d, $tables[docs_info] as di where 1 $date_condition"));
        $total_paid[] = price_format(cw_query_first_cell("select sum(total) from $tables[docs] as d, $tables[docs_info] as di where (status='P' OR status='C') $date_condition"));
    }
    $smarty->assign('orders', $orders);
    $smarty->assign('gross_total', $gross_total);
    $smarty->assign('total_paid', $total_paid);

# kornev, last order
    $last_order_id = cw_query_first_cell("select doc_id from $tables[docs] as d, $tables[docs_info] as di where d.doc_info_id=di.doc_info_id and di.salesman_customer_id='$customer_id' order by date desc limit 1");
    if ($last_order_id) {
        $last_order = cw_doc_get($last_order_id);
        $smarty->assign('last_order', $last_order);
    }

# kornev, child salesmans
    include $app_main_dir."/include/affiliates.php";

# kornev, salesman general stats
    include $app_main_dir."/include/stats.php";

# kornev, last 10 orders
    cw_load('doc');
    $last_orders = cw_query("select doc_id FROM $tables[orders] $tables[docs] as d, $tables[docs_info] as di where d.doc_info_id=di.doc_info_id and di.salesman_customer_id='$customer_id' ORDER BY date DESC LIMIT 10");
    if ($last_orders)
        foreach($last_orders as $k=>$val)
            $last_orders[$k] = cw_doc_get($val['doc_id']);
    $smarty->assign('last_orders', $last_orders);
    $smarty->assign('main', 'main');
}
else 
    $smarty->assign('main', 'welcome');

$smarty->assign('mode', $mode);
?>
