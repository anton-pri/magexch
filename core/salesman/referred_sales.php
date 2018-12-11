<?php
include $app_main_dir.'/include/referred_sales.php';

if ($search && is_array($sales)) {
    $orders = array();
    foreach($sales as $val) {
        if (!$orders[$val['doc_id']]) $orders[$val['doc_id']]['order'] = cw_select_order($val['doc_id']);
        $orders[$val['doc_id']]['paid'] = $val['paid'];
        $orders[$val['doc_id']]['total'] += $val['total'];
        $orders[$val['doc_id']]['amount'] += $val['amount'];
        $orders[$val['doc_id']]['product_commission'] += $val['product_commission'];

        $orders[$val['doc_id']]['products'][] = $val;
    }
    $smarty->assign('sales', $orders);
}

$smarty->assign('main', 'referred_sales');
