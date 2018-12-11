<?php
$shipping = cw_func_call('cw_shipping_get_list', array('cart' => $aom_orders[$doc_id], 'products' => $aom_orders[$doc_id]['products'], 'userinfo' => $aom_orders[$doc_id]['userinfo']));

if (!empty($_saved_data))
    extract($_saved_data);

if (is_array($shipping)) {
    $found = false;
    foreach($shipping as $k=>$v)
        if ($doc_data['order']['shipping_id'] == $v['shipping_id']) {
            $found = true;
            break;
        }
    if (!$found && empty($doc_data['order']['shipping_id'])) {
    }
    else {
        if (!$found && $aom_orders[$doc_id]['shipping_id'] == $doc_data['order']['shipping_id']) {
            $aom_orders[$doc_id]['shipping_id'] = $shipping[0]['shipping_id'];
            $aom_orders[$doc_id]['shipping'] = $shipping[0]['shipping'];
        }
        if (!$found)
            $smarty->assign('shipping_lost', $shipping);
    }
}
$smarty->assign('shipping', $shipping);
