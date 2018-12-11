<?php
$top_message = &cw_session_register('top_message');

if ($action == 'list') {
    cw_load('group_edit');
    if (empty($product_ids)) {
        $top_message = array('content' => cw_get_langvar_by_name('lbl_please_select_products_for_editing'), 'type' => 'I');
        cw_header_location('index.php?target='.$target.'&mode=search');
    }
    else {
        $product_ids = array_keys($product_ids);
        $ge_id = cw_group_edit_add($product_ids);
        $product_id = $product_ids[0];
        cw_header_location('index.php?target=products&mode=details&product_id='.$product_id.'&ge_id='.$ge_id);
    }
}

if ($action == 'update' && is_array($posted_data)) {
    cw_load('product');

    foreach ($posted_data as $k=>$v) {
        if (isset($v['orderby']) && is_numeric($v['orderby'])) {
            $cat = intval($cat);
            db_query("update $tables[products_categories] set orderby='".$v['orderby']."' where product_id='$k' and category_id='$cat'");
        }
        if (AREA_TYPE == 'P') {
            $wu = array(
                'avail' => $v['avail'],
                'product_id' => $k,
                'warehouse_customer_id' => $customer_id,
                'variant_id' => 0,
            );
            cw_warehouse_insert_avail($wu, true);
        }
        elseif (!$addons['warehouse'])
            cw_array2update('products', array('avail' => $v['avail']), "product_id='$k'"); // TODO, TOFIX: There is no avail field in products
        else
            cw_array2update('products_warehouses_amount', array('avail' => $v['avail']), "product_id='$k'"); // TODO, TOFIX: Does it set avail for all warehouses? Maybe use warehouses=0 as condition?

        if (AREA_TYPE == 'A') {
# kornev, variant product is possible here.
#            cw_product_replace_price($k, $v['price'], 0, false, $v['is_manual_price']);
            cw_call('cw_product_update_status', array($k, $v['status']));
        }
    }
    $top_message = array('content' => cw_get_langvar_by_name('msg_adm_products_upd'), 'type' => 'I');

    if ($cat)
        cw_header_location('index.php?target=categories&mode=products&cat='.$cat.'&sort='.$sort.'&sort_direction='.$sort_direction);
    else
        cw_header_location("index.php?target=$target&mode=search".(intval($navpage)>1 ? "&page=$navpage" : ""));
}

if ($action == "delete" && AREA_TYPE == 'A') {
    $products_to_delete = &cw_session_register("products_to_delete");

    if ($confirmed=="Y") {
        cw_load('product');
        if (is_array($products_to_delete['products'])) {
            foreach ($products_to_delete['products'] as $k=>$v)
			    cw_call('cw_delete_product', array('product_id' => $k, 'update_categories' => true));

            $force_return = $products_to_delete['search_return'];

            $top_message = array('content' => cw_get_langvar_by_name('msg_adm_products_del'), 'type' => 'I');

            cw_log_flag('log_products_delete', 'PRODUCTS', "customer_id: $customer_id\nIP: $REMOTE_ADDR\nOperation: delete products (".implode(',', array_keys($products_to_delete['products'])).")", true);
        }
		else
		    $top_message = array('content' => cw_get_langvar_by_name('msg_adm_warn_products_del'), 'type' => 'W');
    }
    elseif(is_array($product_ids) || $product_id) {
		if ($product_id) {
			$products_to_delete['products'][$product_id] = 'on';
		}
		else {
    		$products_to_delete['products'] = $product_ids;
		}
		$products_to_delete['navpage'] = $navpage;
		$products_to_delete['section'] = @$section;
		if ($REQUEST_METHOD == 'POST')
		    $products_to_delete['search_return'] = $HTTP_REFERER;

        $products_to_delete['cat'] = @$cat;
        cw_header_location("index.php?target=$target&mode=delete");
    }
}

if ($action == 'clone' && is_array($product_ids) && AREA_TYPE == 'A') {
    cw_load('product', 'warehouse', 'category');
    //foreach($product_ids as $product_id=>$tmp)
    $new_product_id = cw_call('cw_product_clone', array(intval(key($product_ids))));
    
    cw_header_location("index.php?target=products&mode=details&product_id=" . $new_product_id);
}

if ($mode == 'clone' && $product_id && AREA_TYPE == 'A') {
    cw_load('product', 'warehouse', 'category');
    $new_product_id = cw_call('cw_product_clone', array(intval($product_id)));
    
    cw_header_location("index.php?target=products&mode=details&product_id=" . $new_product_id);
}

if ($mode == 'delete' && AREA_TYPE == 'A') {
    cw_load('product');
	$products_to_delete = &cw_session_register('products_to_delete');
	$force_return = $products_to_delete['search_return'];

	if (is_array($products_to_delete['products'])) {
		$location[] = array(cw_get_langvar_by_name('lbl_products_management'), 'index.php?target=search');
		$location[] = array(cw_get_langvar_by_name('lbl_delete_products'), '');

        $data = array('product_id' => array_keys($products_to_delete['products']), 'flat_search' => 1);
        list($products, $navigation) = cw_func_call('cw_product_search', array('data' => $data, 'user_account' => $user_account, 'current_area' => $current_area));
        $smarty->assign('products', $products);
        $smarty->assign('search_return', $products_to_delete['search_return']);

        $smarty->assign('main', 'product_delete_confirmation');

        if ($current_area == 'A') cw_display('admin/index.tpl', $smarty);
        else cw_display('warehouse/index.tpl', $smarty);
        exit;
    }

	$top_message = array('content' => cw_get_langvar_by_name('msg_adm_warn_products_del'), 'type' => 'W');
}

cw_header_location("index.php?target=$target&mode=search".(intval($navpage)>1 ? "&page=$navpage" : "").'#process_product_form');
?>
