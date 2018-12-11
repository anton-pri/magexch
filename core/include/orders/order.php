<?php
cw_load('mail', 'doc', 'accounting', 'product', 'web', 'attributes');

$smarty->assign('product_layout_elements', cw_call('cw_web_get_product_layout_elements'));

if ($mode == 'print_aom_pdf') {
# kornev, delay is possible
    $print_doc_id = 0;
    while(empty($print_doc_id)) {
        cw_session_read();
        $aom_orders = &cw_session_register('aom_orders', array());
        $print_doc_id = $aom_orders[$doc_id]['doc_id'];
    }
    $current_language = &cw_session_register('current_language');
    $doc_id = $print_doc_id;
}
if ($action == 'set_template' and $current_area == 'A') {
    if (is_array($label_data['elements']))
    foreach($label_data['elements'] as $k=>$el) 
        if (empty($el)) unset($label_data['elements'][$k]);

    $data = addslashes(serialize($label_data));
    cw_array2update('layouts', array('data' => $data), "layout='$target'");
    cw_header_location("index.php?target=$target&mode=layout&doc_id=$doc_id");
}

if ($action == 'create_template' && $current_area == 'A' && $template['title']) {
    $layout_id = cw_array2insert('layouts', array('title' => $template['title'], 'layout' => 'docs_'.$docs_type));
    db_query("update $tables[docs_info] set layout_id='$layout_id'");
    cw_header_location("index.php?target=$target&mode=layout&doc_id=$doc_id");
}
if ($action == 'copy_layout_template' && $current_area == 'A' && $template['source_layout_id'] && $template['source_layout_id'] != $template['layout_id']) {
    cw_web_copy_layout($template['source_layout_id'], $template['layout_id']);
    cw_header_location("index.php?target=$target&mode=layout&doc_id=$doc_id");
}

if ($action == 'delete_template' && $current_area == 'A') {
    cw_web_delete_layout($template['layout_id']);
    db_query("update $tables[docs_info] set layout_id=0 where layout_id='".$template['layout_id']."'");
    cw_header_location("index.php?target=$target&mode=layout&doc_id=$doc_id");
}

$info_type = 1;
if(in_array($current_area, array('A', 'P'))) $info_type += 512 + 8192;
if($docs_type == 'G') $info_type += 1024;
if(in_array($docs_type, array('P', 'Q', 'R'))) $info_type += 2048;
global $doc_data;
$doc_data = cw_call('cw_doc_get', array($doc_id, $info_type));

if ($mode == 'quote') {
	
	$empty_products = empty($doc_data['products']);
	if (
		$addons['estore_gift'] 
		&& !empty($doc_data['giftcerts']) 
		&& $empty_products
	) {
		$empty_products = FALSE;
	}

	if (
		empty($customer_id) 
		|| $doc_data['type'][0] != 'I' 
		|| $doc_data['status'] != 'P'
		|| $empty_products
		|| empty($doc_data['userinfo']['customer_id'])
		|| $doc_data['userinfo']['customer_id'] != $customer_id
	) {
		$top_message['content'] = cw_get_langvar_by_name("txt_antifraud_service_generror");
		$top_message['type'] = "E";
		cw_header_location("index.php?target=$target&mode=details&doc_id=$doc_id");
	}

	cw_load('cart', 'cart_process');
	
	$cart = &cw_session_register('cart', array());

	if (isset($doc_data['info']['applied_taxes'])) {
		$doc_data['info']['taxes'] = $doc_data['info']['applied_taxes'];
	}

	$cart = $doc_data;
	$cart['orders'][] = $doc_data;

    if ($addons['accessories']) {
        cw_include('addons/accessories/add_to_cart.php');
    }

	$cart['info']['quote_doc_id'] = $doc_data['doc_id'];

	cw_header_location("index.php?target=cart");
}

if ($action == 'change_template' && $current_area == 'A') {
    db_query("update $tables[docs_info] set layout_id='$template[layout_id]' where doc_info_id='$doc_data[doc_info_id]'");
    cw_header_location("index.php?target=$target&mode=layout&doc_id=$doc_id");
}

$smarty->assign('order_details_fields_labels', cw_doc_details_fields_as_labels());

if ($doc_data) {
    $owner_condition = " and $tables[docs].type = '".$doc_data['type']."'";
    if (AREA_TYPE == 'C')
        $owner_condition = " AND $tables[docs_user_info].customer_id='$customer_id'";
    elseif (AREA_TYPE == 'P' || AREA_TYPE == 'V')
        $owner_condition = " AND $tables[docs_info].warehouse_customer_id='$customer_id'";
    elseif (AREA_TYPE == 'B')
        $owner_condition = " AND $tables[docs_info].salesman_customer_id='$customer_id'";

    $tmp = cw_query_first("SELECT $tables[docs].* FROM $tables[docs], $tables[docs_info], $tables[docs_user_info] WHERE $tables[docs_info].doc_info_id=$tables[docs].doc_info_id and $tables[docs_user_info].doc_info_id=$tables[docs].doc_info_id and $tables[docs].doc_id > '$doc_id' $owner_condition ORDER BY $tables[docs].doc_id ASC LIMIT 1");
    $smarty->assign('doc_id_next', $tmp);

    $tmp = cw_query_first("SELECT $tables[docs].* FROM $tables[docs], $tables[docs_info], $tables[docs_user_info] WHERE $tables[docs_info].doc_info_id=$tables[docs].doc_info_id and $tables[docs_user_info].doc_info_id=$tables[docs].doc_info_id and $tables[docs].doc_id <'$doc_id' $owner_condition order by $tables[docs].doc_id DESC LIMIT 1");
    $smarty->assign('doc_id_prev', $tmp);
}
$smarty->assign('doc', $doc_data);

if ($current_area == 'C' && $doc_data['userinfo']['customer_id'] != $customer_id)
    cw_header_location('index.php');

if ($current_area == 'B' && $doc_data['info']['salesman_customer_id'] != $customer_id)
    cw_header_location("index.php?target=error_message&error=access_denied&id=40");

if ($current_area == 'P' && in_array($docs_type, array('I', 'O', 'S')) && $doc_data['info']['warehouse_customer_id'] != $user_account['warehouse_customer_id']) 
    cw_header_location("index.php?target=error_message&error=access_denied&id=40");

if ($action == 'print_barcode' && $addons['barcode']) {
    if (!$print['template_id'])
        cw_header_location("index.php?target=$target&mode=details&doc_id=$doc_id");
    cw_load('web', 'product', 'barcode', 'pdf');
    cw_barcode_print_doc($doc_id, $print);
}

if (in_array($mode, array('print', 'print_pdf', 'print_aom_pdf')) || $action == 'print') {
	if ($action == 'print') {
		$mode = $action;
	}
    $docs_id = explode(',', $doc_id);
    $slips = array();
    foreach($docs_id as $id){
        $doc_info = cw_call('cw_doc_get', array($id, $info_type));
        $slips[] = cw_doc_print($doc_info, $mode);
    }
    $smarty->assign('slips',$slips);
    $smarty->assign('current_section', '');
    $smarty->assign('main', 'order_print');
    $smarty->assign('home_style', 'iframe');
    $smarty->assign('is_printing', true);
;
    cw_display($app_skins_dirs[$current_area].'/index.tpl', $smarty);
   exit();
} elseif ($mode == 'print_label') {
/*
    if ($doc_data['info']['shipping_id']) 
        $doc_shipping_time = cw_query_first_cell("SELECT shipping_time FROM $tables[shipping] WHERE shipping_id = '".$doc_data['info']['shipping_id']."'");
    elseif (!empty($doc_data['info']['shipping_label'])) 
        $doc_shipping_time = cw_query_first_cell("SELECT shipping_time FROM $tables[shipping] WHERE shipping = '".$doc_data['info']['shipping_label']."'");

    $smarty->assign('doc_shipping_time', $doc_shipping_time);
*/

    cw_display('admin/docs/doc_label_layout.tpl', $smarty);
    exit;
}


if (in_array($current_area, array('A', 'P')) && $action == 'make_relations' && is_array($relations) && ($relation_doc_id || $relation_doc_type)) {
    $relation_doc_id = $relation_doc_type?$relation_doc_type:$relation_doc_id;

    foreach($relations as $rel_item_id=>$v) {
        if (!$v['create']) continue;
        if (!is_numeric($relation_doc_id)) $relation_doc_id = cw_doc_make_relation_doc($relation_doc_id, $doc_id, $rel_item_id, $v['amount'], 1);
        else cw_doc_make_relation($relation_doc_id, $rel_item_id, $v['amount']);
        cw_doc_recalc($relation_doc_id);
    }
    cw_header_location("index.php?target=$target&doc_id=$doc_id&js_tab=relations");
}

if (in_array($current_area, array('A', 'P')) && $action == 'delete_relations' && is_array($del_relations)) {
    foreach($del_relations as $rel_doc_id=>$rel_items) {
        cw_doc_delete_relation($rel_doc_id, array_keys($rel_items));
    }
    cw_header_location("index.php?target=$target&doc_id=$doc_id&js_tab=relations");
}

if (in_array($current_area, array('A', 'P')) && $action == "update") {
	# Update orders info (status)
	if (is_array($order_status) && is_array($order_status_old)) {
		foreach ($order_status as $doc_id=>$status) {
			if (is_numeric($doc_id) && $status != $order_status_old[$doc_id])
				cw_change_order_status($doc_id, $status);
		}

		cw_header_location("index.php?target=$target".(empty($qrystring)?"":"&$qrystring"));
	}
}

if (in_array($current_area, array('A', 'P')) && $action == 'prolong_ttl' && $doc_id && $addons['egoods']) {
	#
	# Prolong TTL
	#
	$item_ids = cw_query("SELECT $tables[order_details].item_id FROM $tables[order_details], $tables[download_keys] WHERE $tables[order_details].doc_id = '$doc_id' AND $tables[order_details].item_id = $tables[download_keys].item_id");
	if ($item_ids) {
		foreach ($item_ids as $v)
			db_query("UPDATE $tables[download_keys] SET expires = '".(time()+$config['egoods']['download_key_ttl']*3600)."' WHERE item_id = '$v[item_id]'");
	}

	$pids = cw_query("SELECT $tables[order_details].item_id, $tables[order_details].product_id, $tables[products].distribution FROM $tables[order_details], $tables[products] WHERE $tables[order_details].doc_id = '$doc_id' AND $tables[order_details].product_id = $tables[products].product_id AND $tables[products].distribution != ''");
	if ($pids) {
		$keys = array();
		foreach ($pids as $v) {
			if (cw_query_first_cell("SELECT COUNT(*) FROM $tables[download_keys] WHERE item_id = '$v[item_id]'"))
				continue;

			$keys[$v['item_id']]['download_key'] = keygen($v['product_id'], $config['egoods']['download_key_ttl'], $v['item_id']);
			$keys[$v['item_id']]['distribution_filename'] = basename($v['distribution']);

		}

		if (!empty($keys)) {
			$order = cw_order_data($doc_id);
			if (!empty($order)) {
				foreach ($order['products'] as $k => $v) {
					if (isset($keys[$v['item_id']])) {
						$order['products'][$k] = cw_array_merge($v,$keys[$v['item_id']]);
					}
				}

				$smarty->assign('products', $order['products']);
				$smarty->assign('order', $order['order']);
				$smarty->assign('userinfo', $order['userinfo']);
				cw_call('cw_send_mail', array($config['Company']['orders_department'], $order['userinfo']['email'], "mail/egoods_download_keys_subj.tpl", "mail/egoods_download_keys.tpl"));
			}
		}
	}

	cw_header_location("index.php?target=$target&mode=details&doc_id=".$doc_id);
}

if (in_array($current_area, array('A', 'P')) && $action == 'send_ip' && $doc_id) {
	#
	# Send customer IP address to Anti Fraud server
	#
	list($a, $result) = cw_send_ip_to_af($doc_id, $reason);
	if ($result == "1") {
		$top_message['content'] = cw_get_langvar_by_name("msg_antifraud_ip_added");
		$top_message['type'] = "I";
	}
	else {
		$top_message['content'] = cw_get_langvar_by_name("txt_antifraud_service_generror");
		$top_message['type'] = "E";
	}

	cw_header_location("index.php?target=$target&mode=details&doc_id=".$doc_id);
}

$doc_ids = explode(",", $doc_id);
if (!is_array($doc_ids)) $doc_ids[] = $doc_id;

foreach ($doc_ids as $oid)
	if (!is_numeric($oid))
		cw_header_location("index.php?target=error_message&error=access_denied&id=8");

$smarty->assign('usertype_layout', 'A');

$smarty->assign('doc_id', $doc_id);

if ((!$doc_data['doc_id'] || $doc_data['type'] != $docs_type) && $mode != 'edit') 
    cw_header_location('index.php?target='.$target);

if (in_array($current_area, array('A', 'P', 'V')) && $action== "status_change") {
	# Update order
	$query_data = array (
		'tracking' => $request_prepared['tracking'],
        'ship_time' => $request_prepared['ship_time'],
		'customer_notes' => $request_prepared['customer_notes'],
		'notes' => $request_prepared['notes'],
	);
	if (isset($details))
		$query_data['details'] = cw_crypt_text($details);

    cw_doc_update_info($doc_data['doc_info_id'], $query_data);
    cw_doc_update_quotes($doc_id, $quote);

    cw_call('cw_doc_change_status', array($doc_id, $status));

	$top_message['content'] = cw_get_langvar_by_name('txt_order_has_been_changed');
	cw_header_location("index.php?target=$target&mode=details&doc_id=".$doc_id);
}

if (in_array($current_area, array('A', 'P')) && $action == "delete") {
	cw_call('cw_doc_delete', array($doc_id));
	cw_header_location("index.php?target=$target".$query_string);
}

$smarty->assign('main', 'document');

$predefined_lng_variables[] = 'lbl_doc_info_'.$doc_data['type'];

if ($mode == 'edit' && $current_area == 'A') { 
	cw_include('include/orders/order_edit.php');
    return;
}
else {
    $relations = cw_doc_get_relations($doc_id);
    $items_for_relations = cw_doc_get_relations_items($doc_id);
    $smarty->assign('relations', $relations);
    $smarty->assign('items_for_relations', $items_for_relations);

    $quotes = cw_doc_get_quotes($doc_id);
    $smarty->assign('quotes', $quotes);
}

if (in_array($current_area, array('A', 'P')) && $addons['stop_list'] && $action == "block_ip") {
	cw_add_ip_to_slist($order['extra']['ip']);
	$top_message['content'] = cw_get_langvar_by_name("msg_stoplist_ip_added");
	$top_message['type'] = "I";
	cw_header_location("index.php?target=$target&mode=details&doc_id=".$doc_id);
}

$smarty->assign('js_tab', $js_tab);

$location[] = array(cw_get_langvar_by_name('lbl_docs_info_'.$docs_type), 'index.php?target='.$target);

if ($mode == 'layout') {
    $location[] = array(cw_get_langvar_by_name('lbl_doc_info_'.$docs_type), 'index.php?target='.$target.'&doc_id='.$doc_id);
    $location[] = array(cw_get_langvar_by_name('lbl_layout'), '');

    $smarty->assign('home_style', 'popup');
    $smarty->assign('current_section', '');
    $smarty->assign('main', 'layout');

    $smarty->assign('layouts', cw_web_get_layouts('docs_'.$docs_type));
    $smarty->assign('copy_layouts', cw_web_get_layouts('docs_%', true));
}
elseif ($mode != 'edit') {
    $location[] = array(cw_get_langvar_by_name('lbl_doc_info_'.$docs_type), '');

    $smarty->assign('layout_url', 'index.php?target='.$target.'&mode=layout&doc_id='.$doc_id);
    $smarty->assign('show_layout', true);
    $smarty->assign('is_layout_modification_able', true);
}

if ($doc_data['info']['layout_id'])
    $smarty->assign('layout_data', cw_call('cw_web_get_layout_by_id', array($doc_data['info']['layout_id'])));
else
    $smarty->assign('layout_data', cw_call('cw_web_get_layout', array('docs_'.$docs_type)));

//cw_var_dump($doc_data);
