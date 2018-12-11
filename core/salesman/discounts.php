<?php
cw_load('salesman');
$new_discount_saved = &cw_session_register('new_discount_saved');

if ($action == 'update' && is_array($data)) {
    foreach($data as $id=>$val) {
        if (empty($id)) continue;
        if ($val['del'])
            cw_call('cw_salesman_delete_discount', array($customer_id, $id));
    }
    cw_header_location("index.php?target=discounts");
}


if ($action == 'add' && is_array($new_discount)) {
    $new_discount_saved = $new_discount;

    $counted = cw_query_first_cell("select * from $tables[discount_coupons] where coupon='$new_discount[coupon]'");
    if ($new_discount['discount'] > 100 || $new_discount['discount'] <= 0 || empty($new_discount['coupon'])) $counted = 1;
 
    if ($counted) {
        $top_message = array('content' => cw_get_langvar_by_name('msg_err_discount_coupons_add'), 'type' => 'E');
    }
    else {
        $new_discount['salesman_customer_id'] = $customer_id;
        $new_discount['coupon_type'] = "percent";
        $new_discount['status']= 3;
        cw_array2insert('discount_coupons', $new_discount);
        if ($new_discount['from_account'])
            cw_salesman_change_discount_status($new_discount['coupon'], 1);
    }
    cw_header_location('index.php?target='.$target);
}
if (empty($new_discount['coupon'])) {
    while(true) {
        $pc = substr(strtoupper(md5(uniqid(rand()))), 0, 9);
        if (!cw_query_first_cell("select count(*) from $tables[discount_coupons] where coupon='$pc'")) break;
    }
    $new_discount['coupon'] = $pc;
}
$smarty->assign('new_discount', $new_discount);
cw_session_unregister('new_discount_saved');

$smarty->assign('salesman_users', cw_salesman_get_customers($customer_id));
$smarty->assign('discounts', cw_salesman_get_discounts($customer_id));

$smarty->assign('main', 'discounts');
