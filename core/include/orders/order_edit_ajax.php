<?php
define('AOM', 1);

$aom_orders = &cw_session_register('aom_orders');
cw_load('doc', 'aom', 'taxes', 'warehouse', 'ean', 'product', 'cart', 'cart_process', 'category','web');

$error_message = '';

if ($action == 'functions') {
    cw_display('addons/pos/printer_functions_ajax.tpl', $smarty);
    exit(0);
}

if ($action == 'search_products') {
    cw_core_process_date_fields($posted_data, null, array(''=>array('manufacturers', 'categories', 'suppliers')));

    $aom_data = array();
    $aom_data['categories_orig'] = $posted_data['categories_orig'];
    $aom_data['categories'] = $posted_data['categories'];

    if ($posted_data['attribute_names']['manufacturer_id'] && !empty($posted_data['attribute_names']['manufacturer_id'][0]))
        $aom_data['attribute_names']['manufacturer_id'] = $posted_data['attribute_names']['manufacturer_id'];

    $aom_data['product_id'] = $posted_data['product_id'];

    $aom_data['by_title'] = true;
    $aom_data['by_keywords'] = true;
    $aom_data['by_shortdescr'] = true;
    $aom_data['by_fulldescr'] = true;
    $aom_data['by_eancode'] = true;
    $aom_data['by_sku'] = true;
    $aom_data['substring'] = $posted_data['substring'];
    $aom_data['substring_exact'] = $posted_data['substring_exact'];

    $aom_data['limit'] = 30;
    $aom_data['flat_search'] = 1;
    $aom_data['sort_field'] = 'product';
    $aom_data['sort_direction'] = 1;

    list($products, $navigation) = cw_func_call('cw_product_search', array('data' => $aom_data, 'user_account' => $user_account, 'current_area' => $current_area, 'info_type' => 8));
    $smarty->assign('products', $products);

    $smarty->assign('doc_id', $doc_id);
    $smarty->assign('is_old_products', $is_old_products);

    cw_display('addons/advanced_order_management/search_products_ajax.tpl', $smarty);
    exit(0);
}


if ($aom_orders[$doc_id]['type'] == 'G') {
    $config['unlimited_products'] = true;
    $config['Taxes']['display_taxed_order_totals'] = 'Y'; 
}
elseif(in_array($aom_orders[$doc_id]['type'], array('P', 'Q', 'R', 'D')))
    $config['unlimited_products'] = true;

if (($current_area == 'G' && $aom_orders[$doc_id]['type'] == 'G') || $current_area == 'C')
    $config['unlimited_products'] = cw_query_first_cell("select count(*) from $tables[warehouse_divisions] where division_id='".$aom_orders[$doc_id]['info']['warehouse_customer_id']."' and backorder & 2");

if ($action == 'print') {
    if ($aom_orders[$doc_id]['saved'] != 2) {
        $smarty->assign('doc_id', $doc_id);
        if (is_array($aom_orders[$doc_id]['products']))
        foreach($aom_orders[$doc_id]['products'] as $k=>$v)
            $aom_orders[$doc_id]['products'][$k]['supplier_code'] = cw_query_first_cell("select productcode from $tables[products_supplied_amount] where product_id='$v[product_id]' order by avail desc, date desc limit 1");
        $smarty->assign('doc', $aom_orders[$doc_id]);
        $aom_orders[$doc_id]['saved'] == 2;
        cw_display('addons/advanced_order_management/printer_ajax.tpl', $smarty);
    }
    exit(0);
}

if ($action == 'delete_item' && isset($index) && !$aom_orders[$doc_id]['saved']) {
    unset($aom_orders[$doc_id]['products'][$index]);
    $aom_orders[$doc_id]['info']['use_shipping_cost_alt'] = 'N';
    $doc_data = cw_doc_get($doc_id, 0);
    $aom_orders[$doc_id] = cw_aom_normalize_after_update($aom_orders[$doc_id], $doc_data);
}

if (
	$action == 'update_item_info' 
	&& isset($index) 
	&& !$aom_orders[$doc_id]['saved']
) {
    $aom_orders[$doc_id]['info']['use_shipping_cost_alt'] = 'N';
    $doc_data = cw_doc_get($doc_id, 0);

    $product_id = $aom_orders[$doc_id]['products'][$index]['product_id'];
    $v = $product_details[$index];
    $v['amount'] = intval($v['amount']);

    $count_product_in_stock = cw_aom_get_quantity_in_stock($aom_orders[$doc_id]['products'][$index]['warehouse_customer_id'], $product_id, $doc_data['order']['status'], $v['product_options'], $doc_data['products'][$index]);
    if ($is_old) $count_product_in_stock += $v['amount'];

# kornev
# pos orders && supplier orders are unlimited
    if ($v['amount'] > 0) {
        $aom_orders[$doc_id]['products'][$index]['amount'] = $config['unlimited_products'] ? $v['amount'] : min($v['amount'], $count_product_in_stock);
    }

    $v['price'] = cw_aom_validate_price($v['price']);
    if ($config['Taxes']['display_taxed_order_totals'] == 'Y') {
        $v['price'] = cw_taxes_price_without_tax($v['price'], $aom_orders[$doc_id]['products'][$index]['taxes']);
    }

    $product_options_result = array();
# kornev, TOFIX
    if ($v['product_options'] && $addons['product_options']) {

        if (!cw_check_product_options ($product_id, $v['product_options'])) {
            $v['product_options'] = cw_get_default_options($product_id, $v['amount'], $aom_orders[$doc_id]['userinfo']['membership_id']);
        }

        list($variant, $product_options_result) = cw_get_product_options_data($product_id, $v['product_options'], $aom_orders[$doc_id]['userinfo']['membership_id']);
        $aom_orders[$doc_id]['products'][$index]['options_surcharge'] = 0;

        if ($product_options_result) {
	        foreach($product_options_result as $key=>$o) {
	            $aom_orders[$doc_id]['products'][$index]['options_surcharge'] += ($o['modifier_type'] == '%' ? ($v['price']*$o['price_modifier']/100) : $o['price_modifier']);
	        }
        }

    }

    $is_copy_price = false;
    if (
    	in_array($aom_orders[$doc_id]['type'], array('P', 'Q', 'R')) 
    	&& !$aom_orders[$doc_id]['products'][$index]['is_net_price']
    ) {
        $is_copy_price = true;
    }

    if ($current_area == 'G' && !$accl['100001']) {
        $v['price'] = $aom_orders[$doc_id]['products'][$index]['net_price'];
    }

    // net_price is a price before update, I hope
    $aom_orders[$doc_id]['products'][$index]['net_price'] = $aom_orders[$doc_id]['products'][$index]['price'];

    if (
    	($current_area == 'G' && $accl['100000']) 
    	|| $current_area != 'G'
    ) {    	
        if ($v['use_discount']) {
            $aom_orders[$doc_id]['products'][$index]['discount_formula'] = $v['discount'];            
            $v['price'] = cw_user_apply_discount_by_formula($v['discount'], $v['price']);
            $is_copy_price = false;
        }
        elseif ($aom_orders[$doc_id]['products'][$index]['price'] != $v['price']) {
            $aom_orders[$doc_id]['products'][$index]['discount_formula'] = '';
        }
    }
    else {
        $aom_orders[$doc_id]['products'][$index]['discount_formula'] = '';
    }

    $aom_orders[$doc_id]['products'][$index]['price'] 							= $v['price'];
    $aom_orders[$doc_id]['products'][$index]['product_options'] 				= $product_options_result;
    $aom_orders[$doc_id]['products'][$index]['extra_data']['product_options'] 	= $v['product_options'];

    if ($is_copy_price) {
        $aom_orders[$doc_id]['products'][$index]['net_price'] = $aom_orders[$doc_id]['products'][$index]['price'];
    }

    if ($v['productcode']) {

        if (
        	$aom_orders[$doc_id]['type'] == 'D' 
        	&& $aom_orders[$doc_id]['products'][$index]['productcode'] != $v['productcode']
        ) {
        	$sql = "select supplier_price 
        			from $tables[products_supplied_amount] 
        			where avail > 0 
        				and productcode='" . $v['productcode'] . "' 
        				and product_id='" . $product_id . "'";
            $aom_orders[$doc_id]['products'][$index]['net_price'] = cw_query_first_cell($sql); 
            $aom_orders[$doc_id]['products'][$index]['price'] = $aom_orders[$doc_id]['products'][$index]['net_price'];
        }

        $aom_orders[$doc_id]['products'][$index]['productcode'] = $v['productcode'];
    }
    
    if (in_array($aom_orders[$doc_id]['type'], array('P', 'Q', 'R', 'D'))) {
        $aom_orders[$doc_id]['products'][$index]['is_auto_calc'] = $v['is_auto_calc'];
        $aom_orders[$doc_id]['products'][$index]['end_price'] = $v['end_price'];
    }

    $aom_orders[$doc_id] = cw_aom_normalize_after_update($aom_orders[$doc_id], $doc_data);
}

$not_found = array();
if ($action == 'add_item' && !empty($newproduct_id) && !$aom_orders[$doc_id]['saved']) {
    $newproduct_ids = explode(" ", trim($newproduct_id));
    $amounts = explode(' ', trim($newamount));

    $aom_orders[$doc_id]['info']['use_shipping_cost_alt'] = 'N';

    $out_products = cw_aom_add_new_products($aom_orders[$doc_id], $newproduct_ids, array(), $amounts,  array(), array(), $is_old);

    $doc_data = cw_doc_get($doc_id, 0);
    $aom_orders[$doc_id] = cw_aom_normalize_after_update($aom_orders[$doc_id], $doc_data);
}

if ($action == 'add_item_by_ean' && is_array($eans) && !$aom_orders[$doc_id]['saved']) {
    $pids = array();
    $amounts = array();
    $discounts = array();
    foreach($eans as $ean) {
        if (empty($ean['ean'])) continue;
        $product_info = cw_ean_get_product_info(trim($ean['ean']));
        if (!$product_info) {
            $not_found[] = $ean['ean'];
            continue;
        }

        $pids[] = $product_info['product_id'];
        $vars[] = $product_info['variant_id'];
        $amounts[] = $default_use_only_ean == 'Y'?intval($default_number):$ean['amount'];
        $discounts[] = $ean['discount'];
    }
    $out_products = cw_aom_add_new_products($aom_orders[$doc_id], $pids, $vars, $amounts, $discounts, array(), $is_old);

    $doc_data = cw_doc_get($doc_id, 0);
    $aom_orders[$doc_id] = cw_aom_normalize_after_update($aom_orders[$doc_id], $doc_data);
}

if ($action == 'update_discount' && !$aom_orders[$doc_id]['saved']) {
    if ($param == 'gd_value' && (($current_area == 'G' && $accl['100002']) || $current_area != 'G'))
        $aom_orders[$doc_id]['pos']['gd_value'] = $value;    
    if ($param == 'gd_type' && (($current_area == 'G' && $accl['100002']) || $current_area != 'G'))
        $aom_orders[$doc_id]['pos']['gd_type'] = $value;
    if ($param == 'vd_value' && (($current_area == 'G' && $accl['100003']) || $current_area != 'G'))
        $aom_orders[$doc_id]['pos']['vd_value'] = $value;

    $doc_data = cw_doc_get($doc_id, 0);
    $aom_orders[$doc_id] = cw_aom_normalize_after_update($aom_orders[$doc_id], $doc_data);
}

if ($action == 'update_payment' && !$aom_orders[$doc_id]['saved']) {
    if ($param == 'gp_payment')
        $aom_orders[$doc_id]['pos']['payment'] = $value;
    if ($param == 'gp_paid_by_cc')
        $aom_orders[$doc_id]['pos']['paid_by_cc'] = $value;

    if (!$aom_orders[$doc_id]['pos']['paid_by_cc']) {
        if ($aom_orders[$doc_id]['pos']['payment']) $aom_orders[$doc_id]['pos']['change'] = $aom_orders[$doc_id]['pos']['payment'] - $aom_orders[$doc_id]['info']['total'];
    }
    else
        $aom_orders[$doc_id]['pos']['payment'] = $aom_orders[$doc_id]['pos']['change'] = 0;
}

if ($action == 'update_warehouse' && $warehouse_info && AREA_TYPE == 'A') {
    if ($aom_orders[$doc_id]['type'] == 'D') {
        $aom_orders[$doc_id]['info']['company_id'] = $warehouse_info['company_id'];
        cw_aom_update_warehouse($aom_orders[$doc_id], $warehouse_info['customer_id']);
        cw_aom_update_customer($aom_orders[$doc_id], $warehouse_info['dest_customer_id']);
        if ($aom_orders[$doc_id]['info']['warehouse_customer_id'] == $aom_orders[$doc_id]['userinfo']['customer_id']) {
            cw_aom_update_customer($aom_orders[$doc_id], 0);
            $top_message = array('content' => cw_get_langvar_by_name('lbl_dest_source_equals'), 'type' => 'E');
        }
    }
    else {
        $aom_orders[$doc_id]['info']['company_id'] = $warehouse_info['company_id'];
        cw_aom_update_warehouse($aom_orders[$doc_id], $warehouse_info['customer_id']);
    }
}


if ($action && $aom_orders[$doc_id]['saved'])
    $error_message = cw_get_langvar_by_name('txt_aom_already_printed');

if ($action == 'save_doc' && !$aom_orders[$doc_id]['saved']) {
    if ($aom_orders[$doc_id] && $aom_orders[$doc_id]['info']['total'] >= 0 && count($aom_orders[$doc_id]['products'])) {
        if ($aom_orders[$doc_id]['type'] == 'G' && $config['pos']['is_use_printer'] != 'Y' && AREA_TYPE == 'G')
            $aom_orders[$doc_id]['type'] = 'I';
        $aom_orders[$doc_id]['doc_id'] = cw_doc_create_empty($aom_orders[$doc_id]['type']);
        $doc_info = cw_doc_get_basic_info($aom_orders[$doc_id]['doc_id']);
        $aom_orders[$doc_id]['info']['doc_info_id'] = $doc_info['doc_info_id'];
        $aom_orders[$doc_id]['userinfo']['main_address']['address_id'] = $doc_info['main_address_id'];
        $aom_orders[$doc_id]['userinfo']['current_address']['address_id'] = $doc_info['current_address_id'];
        cw_aom_update_order($aom_orders[$doc_id], $is_invoice);
        $aom_orders[$doc_id]['saved'] = 1;

# for pos, generate invoice if required
        if ($aom_orders[$doc_id]['type'] == 'G' && $is_invoice)
            cw_doc_make_full_relation('I', $aom_orders[$doc_id]['doc_id']);
    }
    else
        $error_message = cw_get_langvar_by_name('txt_aom_total_is_incorrect');
}

if (!$aom_orders[$doc_id]['saved'])
    $smarty->assign('reset_error', 1);

$smarty->assign('update_time', cw_core_get_time());

if (count($not_found))
    $error_message .= cw_get_langvar_by_name('txt_aom_product_eans_not_found').implode('<br/>', $not_found).'<br/>';
if (count($out_products))
foreach($out_products as $pr)
    if ($pr[0] == 1)
        $error_message .= cw_get_langvar_by_name('txt_aom_products_out_of_stock').' '.$pr[1].'<br/>';
    elseif ($pr[0] == 2)
        $error_message .= cw_get_langvar_by_name('txt_aom_products_not_added').' '.$pr[1].'<br/>';
    elseif ($pr[0] == 3)
        $error_message .= cw_get_langvar_by_name('txt_aom_choose_supplier').' '.$pr[1].'<br/>';

$smarty->assign('errors', $error_message);

$smarty->assign('doc_id', $doc_id);
$smarty->assign('doc', $aom_orders[$doc_id]);
$smarty->assign('product_layout_elements', cw_call('cw_web_get_product_layout_elements'));
$smarty->assign('layout_data', cw_web_get_layout('docs_' . $aom_orders[$doc_id]['type'][0]));
//$smarty->assign('debug_data', print_r($dd, true));

cw_include("addons/shipping_system/include/orders/order_edit.php");

$result = cw_display('addons/advanced_order_management/order_info_ajax.tpl', $smarty, FALSE);
exit($result);
?>
