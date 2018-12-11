<?php
$search_data = &cw_session_register('search_data', array());
if ($action == 'go') {
    $date_fields = array (
        '' =>array('start_date' => 0, 'end_date' => 1),
    );
    cw_core_process_date_fields($posted_data, $date_fields);

    $search_data['payment_history'] = $posted_data;
    cw_header_location('index.php?target='.$target);
}

$paid_total_result = cw_query_first ("SELECT SUM(commissions) AS numba FROM $tables[salesman_payment] WHERE paid='Y' AND salesman_customer_id='$customer_id'");
$smarty->assign('paid_total', $paid_total);

$data = $search_data['payment_history'];
$where = array();
$from_tbls = array('salesman_payment');
$where[] = 'paid="Y"';
if ($data['start_date'])
    $where[] = "$tables[salesman_payment].add_date>='$data[start_date]'";
if ($data['end_date'])
    $where[] = "$tables[salesman_payment].add_date>='$data[end_date]'";

$total_payments = cw_query_first_cell(cw_db_generate_query('count(*)', $from_tbls, '', $where, '', '', '', 0));
if ($total_payments)
    $smarty->assign('payments', cw_query(cw_db_generate_query('*', $from_tbls, '', $where, '', '', array('add_date'))));

$navigation = cw_core_get_navigation($target, $total_payments, $page);
$navigation['script'] = 'index.php?target='.$target;
$smarty->assign('navigation', $navigation);

$smarty->assign('search_prefilled', $data);
 
$smarty->assign('main', 'payment_history');
