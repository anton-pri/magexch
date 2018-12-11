<?php
# kornev. TOFIX
if (!$addons['Salesman'])
    cw_header_location('index.php');

cw_load('salesman');

if ($action == 'update' && is_array($data)) {
    foreach($data as $id=>$val) {
        if (empty($id)) continue;
        if ($val['del'])
            cw_call('cw_salesman_delete_discount', array($val['salesman_customer_id'], $id));
        else
            cw_call('cw_salesman_change_discount_status', array($id, $val['status']));
    }
    cw_header_location("index.php?target=discounts");
}

$smarty->assign('discounts', cw_salesman_get_discounts_all());

$smarty->assign('main', 'discounts');
