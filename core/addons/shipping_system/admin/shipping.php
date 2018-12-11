<?php
cw_load('attributes');

$top_message = &cw_session_register('top_message', array());
$shipping_modified_data = &cw_session_register('shipping_modified_data', array());

if ($action == 'list') {
	if (!empty($data)) {
		foreach ($data as $id => $arr) {
            if (!$arr['active']) $arr['active'] = 0;
			$arr['weight_min'] = cw_convert_number($arr['weight_min']);
			$arr['weight_limit'] = cw_convert_number($arr['weight_limit']);
			cw_array2update("shipping", $arr, "shipping_id = '$id'");
		}
	}

	if (!empty($add['shipping'])) {
		$add['weight_min'] = cw_convert_number($add['weight_min']);
		$add['weight_limit'] = cw_convert_number($add['weight_limit']);
		$id = cw_array2insert("shipping", $add);
    }

	$top_message['content'] = cw_get_langvar_by_name("msg_adm_shipping_methods_upd");

	cw_header_location("index.php?target=$target");
}

if ($action == 'update') {
    $rules = array(
        'shipping' => '',
    );
    $update['shipping_id'] = $shipping_id;
    $update['attributes'] = $attributes;
	$fillerror = cw_error_check($update, $rules, 'D');

    if (!$fillerror) {
        if (!$shipping_id)
            $update['shipping_id'] = $shipping_id = cw_array2insert('shipping', $update, 1, array('shipping', 'active', 'orderby'));

        cw_array2update('shipping', $update, "shipping_id='$shipping_id'", array('carrier_id', 'shipping', 'shipping_time', 'destination', 'active', 'weight_min', 'weight_limit', 'insurance', 'fee_basic', 'fee_basic_limit', 'fee_ex_flat', 'fee_ex_percent'));
        cw_shipping_update_cods($shipping_id, $update['cod_type_id']);
        cw_call('cw_attributes_save', array('item_id' => $shipping_id, 'item_type' => 'D', 'attributes' => $attributes, 'language' => $edited_language));
    }
    else {
        $top_message = array('content' => $fillerror, 'type' => 'E');
        $shipping_modified_data = $update;
    }
    cw_header_location("index.php?target=$target&shipping_id=$shipping_id");
}

if ($action == 'delete' && is_array($del_shippings)) {
    foreach($del_shippings as $shipping_id=>$v)
        cw_shipping_delete($shipping_id);

	$top_message['content'] = cw_get_langvar_by_name("msg_adm_shipping_method_del");
	cw_header_location("index.php?target=$target");
}

if (isset($shipping_id)) {
    $shipping = cw_shipping_get($shipping_id);
    if ($shipping_modified_data) $shipping = array_merge($shipping, $shipping_modified_data);
    $smarty->assign('shipping', $shipping);

    $attributes = cw_func_call('cw_attributes_get', array('item_id' => $shipping_id, 'item_type' => 'D', 'prefilled' => $shipping_modified_data['attributes'], 'language' => $edited_language));
    $shipping_modified_data = array();

    $smarty->assign('attributes', $attributes);

    $carriers = cw_shipping_get_carriers(true);
    $smarty->assign('carriers', $carriers);
    $smarty->assign('cod_types', cw_shipping_get_cod_types());
    $smarty->assign('mode', 'modify');
}
else {
    $carriers = cw_shipping_get_carriers(true);
    if (!empty($carriers))
    foreach ($carriers as $k=>$v) {
        $carriers[$k]['total_methods'] = cw_query_first_cell("select count(*) from $tables[shipping] where carrier_id='$v[carrier_id]'");
        $carriers[$k]['total_enabled'] = cw_query_first_cell("select count(*) from $tables[shipping] where carrier_id='$v[carrier_id]' and active=1");
        $carriers[$k]['shipping'] = cw_func_call('cw_shipping_search', array('data' => array('carrier_id' => $v['carrier_id'])));
    }
    $smarty->assign('carriers', $carriers);
}

$location[] = array(cw_get_langvar_by_name('lbl_shipping_methods'), '');
$smarty->assign('main', 'shippings');

$smarty->assign('current_main_dir', 'addons/shipping_system');
$smarty->assign('current_section_dir', 'admin');
