<?php
if ($action == 'wholesales_modify' && !empty($product_info) && is_array($wprices)) {
	foreach($wprices as $k=>$v) {
        if (!$addons['wholesale_trading']) $v['quantity'] = 1;
	if (isset($v['membership_id_old']) && $v['membership_id_old']==0 && $v['quantity_old']==1) $v['membership_id']=0;
	if ($v['quantity_old']==1 && $v['membership_id']==0) $v['quantity']=1;
        if ($v['quantity'] <= 0 || (!$v['quantity_old'] && $v['price'] <= 0)) continue;
        if (!$k) {
            $v['quantity_old'] = $v['quantity'];
            $v['membership_id_old'] = $v['membership_id'];
        }

        cw_product_update_price($product_id, $v['variant_id'], $v['membership_id'], $v['membership_id_old'], $v['quantity'], $v['quantity_old'], $v['price'], $v['list_price'], $k);
        if ($ge_id && $fields['w_price'][$k] && $info)
            while($pid = cw_ge_each($ge_id, 1, $product_id))
                cw_product_update_price($pid, $v['variant_id'], $v['membership_id'], $v['membership_id_old'], $v['quantity'], $v['quantity_old'], $v['price'], $v['list_price']);
	}
    $top_message = array('content' => cw_get_langvar_by_name('msg_adm_product_wholesale_upd'), 'type' => 'I');
    cw_refresh($product_id, 'wholesale');
} 

if ($action == 'wholesales_delete' && is_array($wprices)) {
    foreach ($wprices as $id => $v) {
        if (!$v['del']) continue;
	if ($v['quantity_old']>1 || $v['membership_id']!=0)
        db_query("delete from $tables[products_prices] where price_id='$id'");
    }

    $top_message = array('content' => cw_get_langvar_by_name('msg_adm_product_wholesale_del'), 'type' => 'I');
    cw_refresh($product_id, 'wholesale');
}

$smarty->assign('products_prices', cw_wholesale_get_prices($product_id));
?>
