<?php
cw_load( 'user', 'attributes');

$top_message = &cw_session_register('top_message', array());
$payment_modified_data = &cw_session_register('payment_modified_data', array());

if ($action == 'update' && is_array($posted_data)) {
    foreach ($posted_data as $k=>$v) {
        if ($v['del']) {
            cw_call('cw_payment_delete', array($k));
            continue;
        }
        $v['code'] = $edited_language;
        $v['payment_id'] = $k;

        if (strlen($v['payment_code']) < 4) $v['payment_code'] = cw_core_generate_string(4, true);
        cw_array2update('payment_methods', $v, "payment_id='$k'", array('payment_code', 'orderby', 'title', 'active'));
    }

    $top_message = array('content' => cw_get_langvar_by_name('msg_adm_payment_methods_upd'), 'type' => 'I');
    cw_header_location("index.php?target=$target&mode=$mode");
}

if ($action == 'update_method') {

    $rules = array(
        'payment_code' => '',
        'title' => '',
    );
    $posted_data['payment_id'] = $payment_id;
    $posted_data['attributes'] = $attributes;
	$fillerror = cw_error_check($posted_data, $rules, 'G');

    if (!$fillerror) {
        if (is_array($posted_data['payment_operations']))
            $posted_data['payment_operations'] = array_sum($posted_data['payment_operations']);

        if (!$payment_id) $payment_id = cw_array2insert('payment_methods', $posted_data, true, array('title', 'descr'));
        $fields = array('payment_code', 'payment_category_id', 'title', 'descr', 'orderby', 'min_limit', 'max_limit', 'surcharge', 'surcharge_type', 'protocol', 'is_cod', 'is_web', 'payment_operations', 'payment_category_id', 'is_quotes', 'active');
        if ($edited_language != $config['default_admin_language'])
            unset($fields['title'], $fields['descr']);

        if ($posted_data['is_quotes']) $posted_data['is_web'] = 0;

        cw_array2update('payment_methods', $posted_data, "payment_id='$payment_id'", $fields);

        $posted_data['code'] = $edited_language;
        $posted_data['payment_id'] = $payment_id;
        cw_array2insert('payment_methods_lng', $posted_data, true, array('code', 'payment_id', 'title', 'descr'));

        db_query("delete from $tables[payment_settings] where payment_id='$payment_id'");
        if ($posted_data['processor'])
            cw_array2insert('payment_settings', $posted_data, true, array('payment_id', 'param01', 'param02', 'param03', 'param04', 'param05', 'param06', 'param07', 'param08', 'param09', 'processor'));
        else
            cw_array2insert('payment_settings', array('processor' => '', 'payment_id' => $payment_id), true);

        if ($posted_data['is_quotes'] && is_array($quotes)) {
            foreach ($quotes as $quote_id => $quote) {
                if ($quote['del']) {
                    db_query("delete from $tables[payment_quotes] where quote_id='$quote_id'");
                    continue;
                }
                elseif(!$quote_id) {
                    if ($quote['quote']) $quote_id = cw_array2insert('payment_quotes', array('quote' => $quote['quote'], 'payment_id' => $payment_id));
                    else continue;
                }
                cw_array2update('payment_quotes', $quote, "quote_id='$quote_id'", array('quote', 'inc_payment_id', 'exp_days', 'start_exp_days', 'fixed_days', 'is_net', 'is_vat', 'is_fee', 'commission'));
            }

            $check_fields = array('is_net', 'is_vat', 'is_fee', 'commission');
            foreach($check_fields as $field)
                if (cw_query_first_cell("select sum($field) from $tables[payment_quotes] where payment_id='$payment_id'") != 100)
                    $top_message['content'] .= cw_get_langvar_by_name('err_payment_quotes_'.$field).'<br />';
        }

        cw_membership_update('payment_methods', $payment_id, $posted_data['membership_ids'], 'payment_id');

        db_query("delete from $tables[payments_shippings] where payment_id='$payment_id'");
        if (is_array($posted_data['shippings_ids']))
        foreach($posted_data['shippings_ids'] as $val)
            cw_array2insert('payments_shippings', array('payment_id' => $payment_id, 'shipping_id' => $val), true);

        cw_call('cw_attributes_save', array('item_id' => $payment_id, 'item_type' => 'G', 'attributes' => $attributes, 'language' => $edited_language));
    }
    else {
		$top_message = array('content' => $fillerror, 'type' => 'E');
		$payment_modified_data = $posted_data;
    }

    cw_header_location("index.php?target=$target&mode=$mode&payment_id=$payment_id&js_tab=$js_tab");
}

$location[] = array(cw_get_langvar_by_name('lbl_payment_methods'), '');

if (isset($payment_id)) {
    $cc_addon_files = cw_query($sql="select addon from $tables[addons] where parent='payment_system' and active=1");

    $smarty->assign('cc_addons', $cc_addon_files);

    $smarty->assign('memberships', cw_user_get_memberships(array('C', 'R')));
    $possible_shippings = cw_query("select shipping_id, shipping from $tables[shipping] where active=1");
    $smarty->assign('shippings', $possible_shippings);

    $payment = cw_payment_get($payment_id);
    if ($payment_modified_data) $payment = cw_array_merge($payment, $payment_modified_data);
    $smarty->assign('payment', $payment);
    $smarty->assign('payment_id', $payment_id);

    $smarty->assign('js_tab', $js_tab);

    $location[] = array(cw_get_langvar_by_name('lbl_modify_payment_method'), '');
    $smarty->assign('main', 'method');

    $attributes = cw_func_call('cw_attributes_get', array('item_id' => $payment_id, 'item_type' => 'G', 'prefilled' => $payment_modified_data['attributes'], 'language' => $edited_language));
    $smarty->assign('attributes', $attributes);
    $payment_modified_data = array();

    $payment_methods = cw_func_call('cw_payment_search', array('data' => array(0, false, 3, $edited_language)));
    $smarty->assign('payment_methods', $payment_methods);
}
else {
    $payment_methods = cw_func_call('cw_payment_search', array('language' => $edited_language));
    $smarty->assign('payment_methods', $payment_methods);
    $smarty->assign('main', 'methods');
}

$smarty->assign('mode', $mode);
$smarty->assign('current_main_dir', 'addons/payment_system');
$smarty->assign('current_section_dir', 'admin');
