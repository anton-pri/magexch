<?php
cw_load('ean', 'product');

$ean_settings = &cw_session_register('ean_settings');
$settings_vars = array('default_number', 'default_use_only_ean');

if ($addons['sn'] && $action == 'update_range' && is_array($eans_range)) {
    $eans = $settings_vars = array();
    foreach($eans_range as $range) {
        $tmp = array('ean' => $range['ean']);
        $serials = cw_ean_get_range($range['from'], $range['to']);
        foreach($serials as $serial)
            $eans[] = array('ean' => $range['ean'], 'serial' => $serial);
    }
    $action = 'update_inventory';
}

if ($action == 'update_inventory' && count($eans)) {
    cw_load('serials', 'warehouse');

    foreach($settings_vars as $val)
        $ean_settings[$val] = $$val;
   
    $ean_products = array();
    $saved_products_info = array();
    foreach($eans as $ean) {
        if (empty($ean['ean'])) continue;

        $result = $ean;
        if (!$saved_products_info[$ean['ean']])
            $saved_products_info[$ean['ean']] = cw_ean_get_product_info($ean['ean']);
        $product_info = &$saved_products_info[$ean['ean']];

        if ($product_info['product_id']) {
            $result['product_info'] = $product_info;
            $product_id = $product_info['product_id'];

            if ($addons['sn']) {
                $result['auto_generated'] = cw_serials_is_auto_generated($product_id);

                if ($result['auto_generated']) 
                    $result['numbers'] = implode(" ", cw_serials_check_product($product_id));
                elseif($ean['serial'] && $ean['serial'] != $default_number) {
                    $res = cw_serials_add($user_account['warehouse_customer_id'], $product_id, $ean['serial']);
                    $result['numbers'] = $ean['serial'];
                    if (!$res)
                        $result['error'] = 2;
                }
            }

            if (!$result['error']) {
                $current_avail = cw_warehouse_get_warehouse_avail($user_account['warehouse_customer_id'], $product_info['product_id'], null, $product_info['variant_id']);
                $insert = array();
                $insert['avail'] = intval($current_avail) + 1;
                $insert['warehouse_customer_id'] = $user_account['warehouse_customer_id'];
                $insert['variant_id'] = $product_info['variant_id'];
                $insert['product_id'] = $product_info['product_id'];
                cw_warehouse_insert_avail($insert, true);
            }
        }
        else 
            $result['error'] = 1;
        
        $ean_products[] = $result;
    }
   
    if (count($ean_products))
        $mode = 'result';
    else
        cw_header_location('index.php?target=ean_serials');
}

if ($mode == 'result' && is_array($ean_products)) {
    $errors_arr = array(
        '1' => cw_get_langvar_by_name('lbl_not_found'),
        '2' => cw_get_langvar_by_name('lbl_err_serial_already_exists'),
    );
    foreach($ean_products as $k=>$v) {
        $ean_products[$k] = cw_stripslashes($v);
        $ean_products[$k]['product_info'] = cw_func_call('cw_product_get', array('id' => $v['product_info']['product_id'], 'user_account' => $user_account, 'info_type' => 0));
        $ean_products[$k]['error_descr'] = $errors_arr[$v['error']];
    }
    $smarty->assign('ean_products', $ean_products);

}

foreach($settings_vars as $val) {
    $value = $ean_settings[$val]?$ean_settings[$val]:$config['sn'][$val];
    $smarty->assign($val, $value);
}

$smarty->assign('mode', $mode);

$location[] = array(cw_get_langvar_by_name('lbl_add_serial_number'), '');
$smarty->assign('main', 'ean_serials');
?>
