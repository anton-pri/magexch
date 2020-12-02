<?php
# kornev
# doc types
# A - group order
# B - salesman order
# D - warehouse movement
#
# G - cash sellings
#
# I - invoice
# O - order
# S - ship doc
#
# P - supplier order
# Q - supplier invoice
# R - supplier ship doc

cw_load('mail', 'crypt');

function cw_doc_get_display_id($type, $info = array(), $min_number = 0) {
    global $tables, $config;

    $year = date('Y', cw_core_get_time());
    $year_condition = '';
    
    if ($config['order']['display_id_format'] == 'Y') {
        $year_condition = "and d.year='$year'";
    }

    $queries = array(
        'O' => '',
        'G' => '',
        'S' => "and di.warehouse_customer_id='$info[warehouse_customer_id]'",
        'I' => "and di.warehouse_customer_id='$info[warehouse_customer_id]'",
    );
    $current_max = intval(cw_query_first_cell("select max(display_doc_id) from $tables[docs] as d, $tables[docs_info] as di where d.type='$type' $year_condition and di.doc_info_id=d.doc_info_id ".$queries[$type]));

    if ($current_max < $min_number) $current_max = $min_number;
    return $current_max+1;
}

function cw_doc_create_empty($type, $info = array(), $prefix = '', $special_info = array(), $attributes = array()) {
    global $tables, $config;

    $docs_info = array(
        'total' => 0,
        'customer_notes' => '',
        'details' => '',
    );
    $doc_info_id = cw_array2insert('docs_info', $docs_info);
    cw_array2update('docs_info', $info, "doc_info_id='$doc_info_id'");

    $display_doc_id = cw_doc_get_display_id($type, $info);
    $year = date('Y', cw_core_get_time());
    $docs = array(
        'doc_info_id' => $doc_info_id,
        'type' => $type,
        'display_id' => ($prefix?$prefix.' ':'').($config['order']['display_id_format']=='Y'?$year.'/':'').$display_doc_id,  // free display format 
        'prefix' => $prefix,
        'display_doc_id' => $display_doc_id,                        // Sequential number within year or infinite sequence
        'year' => $year,
        'date' => cw_core_get_time(),
    );

    $doc_id = cw_array2insert('docs', $docs);

    if (is_array($special_info))
    foreach($special_info as $tbl=>$fields) {
        if (!is_array($fields) || !count($fields)) continue;
        $fields['doc_info_id'] = $doc_info_id;
        cw_array2insert($tbl, $fields, true);
    }

    $address = array(
        'main' => 1,
        'customer_id' => 0,
    );
    $main_address_id = cw_array2insert('customers_addresses', $address);

    $address = array(
        'current' => 1,
        'customer_id' => 0,
    );
    $current_address_id = cw_array2insert('customers_addresses', $address);

    $user_info = array(
        'doc_info_id' => $doc_info_id,
        'customer_id' => 0,
        'main_address_id' => $main_address_id,
        'current_address_id' => $current_address_id,
    );
    if ($special_info['docs_user_info']) {
        cw_array2update('docs_user_info', $user_info, "doc_info_id='$doc_info_id'", array('main_address_id', 'current_address_id'));
        $main_address = cw_addslashes(cw_query_first("select * from $tables[customers_addresses] where address_id='".$special_info['docs_user_info']['main_address_id']."'"));
        unset($main_address['address_id']);
        cw_array2update('customers_addresses', $main_address, "address_id='$main_address_id'");
        $current_address = cw_addslashes(cw_query_first("select * from $tables[customers_addresses] where address_id='".$special_info['docs_user_info']['current_address_id']."'"));
        unset($current_address['address_id']);
        cw_array2update('customers_addresses', $current_address, "address_id='$current_address_id'");
    }
    else
        cw_array2insert('docs_user_info', $user_info);

    if ($attributes)
        cw_call('cw_attributes_save', array('item_id' => $doc_id, 'item_type' => 'O', 'attributes' => $attributes));

    return $doc_id;
}

function cw_doc_prepare_user_information($user_info, $current_information = array()) {
    if (!is_array($user_info['additional_info'])) $user_info['additional_info'] = array();
    $current_information = array_merge($current_information, $user_info['additional_info']);

    $current_information['membership_id'] = $user_info['membership_id'];
    $current_information['email'] = $user_info['email'];

    if (!is_array($user_info['main_address'])) $user_info['main_address'] = array();
    if (!is_array($current_information['main_address'])) $current_information['main_address'] = array();
    if (!is_array($user_info['current_address'])) $user_info['current_address'] = array();
    if (!is_array($current_information['main_address'])) $current_information['main_address'] = array();
    unset($user_info['main_address']['address_id']);
    unset($user_info['current_address']['address_id']);
    $current_information['main_address'] = array_merge($current_information['main_address'], $user_info['main_address']);
    if ($user_info['current_address']['same_as_main']) {
        $current_information['current_address'] = $current_information['main_address'];
        $current_information['current_address']['same_as_main'] = 1;
    }
    else {
        $current_information['current_address'] = array_merge($current_information['current_address'], $user_info['current_address']);
        $current_information['current_address']['same_as_main'] = true;
        foreach($current_information['main_address'] as $k=>$v)
            if ($v != $current_information['current_address'][$k]) {
                $current_information['current_address']['same_as_main'] = false;
                break;
            }
    }
    $current_information['main_address']['customer_id']  = 0;
    $current_information['current_address']['customer_id']  = 0;
    $current_information['usertype'] = $user_info['usertype'];

    $current_information['main_address'] = cw_user_process_address($current_information['main_address']);
    $current_information['current_address'] = cw_user_process_address($current_information['current_address']);

    return cw_stripslashes($current_information);
}

function cw_doc_update_general($doc_id, $order_info) {
    $order_data_fields = array(
        'display_id', 'display_doc_id', 'prefix', 'date', 'status','type'
    );
    cw_array2update('docs', $order_info, "doc_id='$doc_id'", $order_data_fields);
}

function cw_doc_update_info($doc_info_id, $info) {
    global $tables, $current_language;

    if (isset($info['shipping_id']))
        $info['shipping_label'] =  cw_query_first_cell("select shipping from $tables[shipping] where shipping_id='".$info['shipping_id']."'");
    if (isset($info['cod_type_id'])) {
        $cod_info = cw_query_first("select title, leaving_type from $tables[shipping_cod_types] where cod_type_id='".$info['cod_type_id']."'");
        $info['cod_leaving_type'] = $cod_info['leaving_type'];
        $info['cod_type_label'] = $cod_info['title'];
    }
    if (!$info['payment_label'] && isset($info['payment_id'])) $info['payment_label'] = cw_func_call('cw_payment_get_label', $info);
    if (isset($info['applied_taxes']) || isset($info['taxes'])) {
        //$info['applied_taxes'] = isset($info['applied_taxes']) ? addslashes(serialize($info['applied_taxes'])) : addslashes(serialize($info['taxes']));
        $_applied_taxes = isset($info['applied_taxes']) ? $info['applied_taxes'] : $info['taxes'];
        if (!empty($_applied_taxes)) {
            $info['applied_taxes'] = addslashes(serialize($_applied_taxes));
            $info['tax'] = 0.00; 
            foreach ($_applied_taxes as $tax) {
                $info['tax'] += $tax['tax_cost']; 
            }
        }
    }

/*
    $order_info_fields = array(
        'warehouse_customer_id', 'company_id',  'salesman_customer_id', 'payment_id', 'payment_label', 'shipping_id', 'shipping_label', 'subtotal', 'display_subtotal', 'discounted_subtotal', 'display_discounted_subtotal', 'discount', 'giftcert_discount', 'coupon', 'coupon_discount', 'shipping_cost', 'display_shipping_cost', 'weight', 'shipping_insurance', 'tax', 'total', 'cod_type_id', 'cod_type_label', 'cod_leaving_type', 'extra', 'discount_value', 'applied_taxes', 'shipment_paid', 'pickup_date', 'box_number',
    );
*/
    cw_array2update('docs_info', $info, "doc_info_id='$doc_info_id'");//, $order_info_fields);
}

function cw_doc_update_pos($doc_info_id, $info) {
    global $tables;

    $order_info_fields = array('pos_customer_id', 'paid_by_cc', 'payment', 'gd_value', 'gd_type', 'vd_value');
    if (!cw_query_first_cell("select count(*) from $tables[docs_pos_info] where doc_info_id='$doc_info_id'"))
        cw_array2insert('docs_pos_info', array('doc_info_id' => $doc_info_id));
    cw_array2update('docs_pos_info', $info, "doc_info_id='$doc_info_id'", $order_info_fields);
}

function cw_doc_update_user_information($doc_type, $doc_info_id, $userinfo, $is_create_user = false) {
    global $customer_id, $config;

    $address_fields = array(
        'company', 'title', 'firstname', 'lastname', 'address', 'address_2', 'city', 'county', 'state', 'country', 'zipcode', 'phone', 'fax', 'region',
    );
    
    foreach(array('main_address','current_address') as $address_type) {
        
        $address = $userinfo[$address_type];
        cw_array2update('customers_addresses', cw_addslashes($address), "address_id='$address[address_id]'", $address_fields);

        if (is_array($address['custom_fields']) && !empty($address['custom_fields'])) {
            cw_profile_fields_update_type(0, $address['address_id'], 'A', $address['custom_fields']);
        }

    }

    if (!$userinfo['customer_id'] && $is_create_user) {
        $userinfo['customer_id'] = cw_user_create_profile(array('usertype'=>$userinfo['usertype']));
        $userinfo['additional_info'] = $userinfo;
        cw_user_update(cw_addslashes($userinfo), $userinfo['customer_id'], $customer_id);
    }
    
    

    
    $userinfo_fields = array(
        'customer_id', 'membership_id', 'company', 'email', 'tax_number', 'tax_exempt', 'ssn', 'usertype',
    );

# kornev, 'company_id' ?
    cw_array2update('docs_user_info', cw_addslashes($userinfo), "doc_info_id='$doc_info_id'", $userinfo_fields);
}

function cw_doc_update_item($doc_id, &$product) {
    global $tables, $addons;

    $product['extra_data'] = cw_call('cw_doc_prepare_doc_item_extra_data', array($product));
 
    $product['product_options'] = '';
# kornev, TOFIX
    if ($addons['product_options'])
        $product['product_options'] = cw_serialize_options($options);

    $product['extra_data'] = serialize($product['extra_data']);
    $query_data = cw_addslashes($product);
    $query_data['doc_id'] = $doc_id;
    $query_data['history_cost'] = cw_call('cw_doc_prepare_doc_item_history_cost', array($product));
    $item = $product['item_id'] = cw_array2insert('docs_items', $query_data, true, array('item_id', 'doc_id', 'product_id', 'product_options', 'amount', 'price', 'history_cost', 'net_price', 'extra_data', 'productcode', 'warehouse_customer_id', 'product', 'discount_formula', 'variant_id', 'is_auto_calc', 'end_price'));

    return $item;
}

function cw_doc_prepare_doc_item_extra_data($product) {

    $options = array();
    if (is_array($product['product_options']))
    foreach ($product['product_options'] as $k=>$v)
        $options[intval($k)] = ($v['type'] == 'T') ? $v['name'] : $v['option_id'];

    $extra_data = array();   
    $extra_data['product_options'] = $options;
    $extra_data['taxes'] = $product['taxes'];
    $extra_data['display']['price'] = doubleval($product['display_price']);
    $extra_data['display']['net_price'] = doubleval($product['display_net_price']);
    $extra_data['display']['discounted_price'] = doubleval($product['display_discounted_price']);
    $extra_data['display']['subtotal'] = doubleval($product['display_subtotal']);
    $extra_data['surcharge'] = $product['surcharge'];

    return $extra_data;
}

function cw_doc_prepare_doc_item_history_cost($product) {

    $history_cost = !empty($product['cost'])?$product['cost']:12.45;

    return $history_cost;
}


function cw_doc_update_quote($doc_id, $doc) {
    global $tables;

    $payment_id = $doc['info']['payment_id'];
    $info = $doc['info'];

    $ready = $sum_paid = cw_query_first_cell("select sum(paid) from $tables[docs_quotes] where doc_id='$doc_id'");
    db_query("delete from $tables[docs_quotes] where doc_id='$doc_id'");

    $payment = cw_query_first("select * from $tables[payment_methods] where payment_id='$payment_id'");
    if (!$payment['is_quotes']) return 100;

    $payment_quotes = cw_query("select * from $tables[payment_quotes] where payment_id='$payment_id'");
    if ($payment_quotes) {
        $distr = array();
        $total = 0;
        foreach($payment_quotes as $index => $quote) {
            $insert = array(
            'doc_id' => $doc_id,
            'total' => price_format($info['subtotal']*$quote['is_net']/100 + $info['tax_cost']*$quote['is_net']/100 + ($info['total'] - $info['subtotal'] - $info['tax_cost'])*$quote['is_fee']/100),
            'paid' => 0,
            'commission' => $quote['commission'],
            'exp_date' => 0,
            'status' => 'Q',
            );

            $is_last = count($payment_quotes) == $index+1;
# kornev, correct the total if required
            $total += $insert['total'];
            if ($is_last && $total != $info['total']) $insert['total'] += $info['total'] - $total;

            if ($sum_paid) {
                $insert['paid'] = $sum_paid > $insert['total']?$insert['total']:$sum_paid;
                $sum_paid -= $insert['paid'];
            }
            if ($insert['total'] == $insert['paid']) $insert['status'] = 'C';
            if ($is_last && $sum_paid) $insert['paid'] += $sum_paid;

            $insert['exp_date'] = SECONDS_PER_DAY*$quote['exp_days'];
            if ($quote['start_exp_days'] == 1) $insert['exp_date'] += $doc['date'];
            elseif ($quote['start_exp_days'] == 2) $insert['exp_date'] += cw_core_get_month_end($doc['date']);
            elseif ($quote['start_exp_days'] == 4) $insert['exp_date'] += cw_core_get_time() + SECONDS_PER_DAY*$quote['fixed_days'];
            elseif ($quote['start_exp_days'] == 5) $insert['exp_date'] += cw_core_get_month_end($doc['date']) + SECONDS_PER_DAY*$quote['fixed_days'];

            if ($insert['exp_date'] && $quote['mail_before']) $insert['exp_mail_before'] = $insert['exp_date'] + $quote['mail_before']*SECONDS_PER_DAY;
            if ($insert['exp_date'] && $quote['mail_after']) $insert['exp_mail_after'] = $insert['exp_date'] + $quote['mail_after']*SECONDS_PER_DAY;

            cw_array2insert('docs_quotes', $insert);
        }
    }

    return $ready*100/$total;
}

function cw_doc_update_commissions($doc_id, $doc, $part = 100) {
    global $addons, $tables;

    if ($addons['Salesman'] && $doc['info']['salesman_customer_id']) {
        cw_load('salesman');

        db_query("delete from $tables[salesman_payment] where doc_id = '$doc_id'");
        db_query("delete from $tables[salesman_product_commissions] where doc_id = '$doc_id'");

        if (preg_match("/(free_ship|percent|absolute)(?:``)(.+)/S", $doc['info']['coupon'], $found))
            $real_coupon = $found[2];

        $salesman_commission_value = cw_salesman_get_commission($doc['products'], $doc['info']['salesman_customer_id'], $doc['userinfo']['membership_id'], $doc_id, $doc['info']['warehouse_customer_id'], $real_coupon, $doc['info']['coupon_discount'], $part);
    }
    return $salesman_commission_value;
}

function cw_doc_update_quotes($doc_id, $quotes) {
    global $tables;

    if ($quotes) {
        $sum_comission = 0;
        foreach($quotes as $quote_id => $quote) {
            cw_core_process_date_fields($quote, array('' => array('exp_mail_before' => 0, 'exp_mail_after' => 0, 'exp_date' => 0)));
            if (!$quote['exp_date']) unset($quote['exp_date']);

            $quote_info = cw_query_first("select * from $tables[docs_quotes] where doc_quote_id='$quote_id'");

            if ($quote['paid'] == $quote_info['total']) $quote['status'] = 'C';
            if (in_array($quote['status'], array('C', 'P'))) {
                $quote['paid'] = $quote_info['total'];
                $sum_comission += $quote_info['commission'];
            }
            cw_array2update('docs_quotes', $quote, "doc_quote_id='$quote_id'");
        }

        $doc = cw_call('cw_doc_get', array($doc_id));

        cw_doc_update_commissions($doc_id, $doc, $sum_comission);
    }
}

function cw_doc_update($doc_id, $cart, $old_products = array(), $is_create_user = false) {
    global $tables, $addons, $config;
    global $app_main_dir;

    $old_doc = cw_call('cw_doc_get', array($doc_id));

    $products = $cart['products'];
    $userinfo = $cart['userinfo'];

    $_extra = $cart['info']['extra'];
    $_extra['tax_info']['taxed_subtotal'] = $cart['info']['display_subtotal'];
    $_extra['tax_info']['taxed_discounted_subtotal'] = $cart['info']['display_discounted_subtotal'];
    $_extra['tax_info']['taxed_shipping'] = $cart['info']['display_shipping_cost'];
    unset($_extra['tax_info']['product_tax_name']);
    $_extra['additional_fields'] = $userinfo['additional_fields'];

    if (!empty($dhl_ext_country)) {
        $is_dhl_shipping = cw_query_first_cell("SELECT COUNT(*) FROM $tables[shipping] WHERE shipping_id = '$cart[shipping_id]' AND code = 'ARB' AND destination = 'I'") > 0;
        if ($is_dhl_shipping) {
            if (!function_exists("cw_shipper_ARB"))
                require_once $app_main_dir.'/addons/shipping_dhl/mod_ARB.php';
            else
                global $dhl_ext_countries;

            if (empty($dhl_ext_countries))
                $dhl_ext_country = false;
        }
        else
            $dhl_ext_country = false;
    }

    if (!empty($dhl_ext_country))
        $_extra['dhl_ext_country'] = $dhl_ext_country;
    else
        cw_unset($_extra, 'dhl_ext_country');

    $applied_taxes = addslashes(serialize($cart['info']['taxes']));
    $cart['info']['extra'] = addslashes(serialize($_extra));

# kornev, update order information
    cw_doc_update_general($doc_id, $cart);

# kornev, update calculated information
    $doc_info_id = $cart['info']['doc_info_id'];
    cw_doc_update_info($doc_info_id, $cart['info']);

    if ($cart['type'] == 'G' && $addons['pos'])
        cw_doc_update_pos($doc_info_id, $cart['pos']);

# kornev, update address information
    cw_doc_update_user_information($cart['type'], $doc_info_id, $userinfo, $is_create_user);

    $margin_value = $cart['info']['total'];

    if (is_array($products)) {
        $items = array();

        foreach ($products as $pk => $product) {
            // if used quote
		    if (
		    	$addons['quote_system']
		    	&& isset($cart['info']['quote_doc_id']) 
		    	&& !empty($cart['info']['quote_doc_id'])
		    ) {
		    	$product['item_id'] = null;
		    }
            $items[] = cw_call('cw_doc_update_item',array($doc_id, &$product));
            // Calculate margin
            $margin_value = $margin_value - $product['cost'] * $product['amount'];
        }
        $deleted_items = cw_query_column("select item_id from $tables[docs_items] where doc_id='$doc_id' and item_id not in ('".implode("','", $items)."')");
        if ($deleted_items) {
            db_query("delete from $tables[docs_items] where item_id in ('".implode("', '", $deleted_items)."')");
        }
    }

    $_extras = $cart['info']['extras'];
	// Calculate and save margin
	$shipping_value = $config['General']['include_shipping_in_margin_calc'] == 'Y' ? $cart['info']['display_shipping_cost'] : 0;
	$margin_value = $margin_value - $shipping_value;

	if ($margin_value < 0) {
		$margin_value = 0;
	}

	$_extras['margin_value'] = price_format($margin_value);
	cw_call('cw_doc_place_extras_data', array($doc_id, $_extras));

    cw_load('accounting');

    if ($old_doc['info']['payment_id'] != $cart['info']['payment_id'] || 1) {
        $ready_part = cw_doc_update_quote($doc_id, $cart);
        cw_doc_update_commissions($doc_id, $cart, $ready_part);
    }
    else
        cw_doc_update_commissions($doc_id, $cart);
}

# info_type - used bit mask
# 00000000 00000000 (0) - standart
# 00000000 00000001 (1) - attributes info
# 00000100 00000000 (1024) - pos addon
# 00000100 00000000 (2048) - warehouse info
# 11111111 11111111 (65535) - full information
function cw_doc_get($doc_id, $info_type = 0) {
    global $tables;
    global $config, $addons;
    global $app_main_dir;
    global $smarty;

    $doc_id = intval($doc_id);
    if (!$doc_id) return array();
    
    cw_load('warehouse', 'profile_fields');
    $doc = cw_query_first("select * from $tables[docs] where doc_id='$doc_id'");
    if (empty($doc)) return array();
    $doc['info'] = cw_query_first("select * from $tables[docs_info] where doc_info_id='$doc[doc_info_id]'");
    $doc['info']['carrier'] = cw_call('cw_shipping_get_carrier', array($doc['info']['shipping_id']));
    $doc['info']['applied_taxes'] = unserialize($doc['info']['applied_taxes']);
    $doc['info']['extra'] = unserialize($doc['info']['extra']);
    $doc['info']['extras'] = cw_call('cw_doc_get_extras_data', array($doc_id));

    $doc['userinfo'] = cw_query_first("select * from $tables[docs_user_info] where doc_info_id='$doc[doc_info_id]'");
    $doc['userinfo']['main_address'] = cw_user_get_address(null, $doc['userinfo']['main_address_id']);
    $doc['userinfo']['current_address'] = cw_user_get_address(null, $doc['userinfo']['current_address_id']);
    $fields_area = cw_profile_fields_get_area($doc['userinfo']['customer_id'], $doc['userinfo']['membership_id']);
    list($profile_sections, $profile_fields, $additional_fields) = cw_profile_fields_get_sections('U', true, $fields_area);
    $doc['userinfo']['profile_sections'] = $profile_sections;
    $doc['userinfo']['profile_fields'] = $profile_fields;

    $doc['related_docs'] = cw_doc_get_related($doc_id);

    if ($addons['egoods']) {
        $join .= " left join $tables[download_keys] ON $tables[docs_items].item_id=$tables[download_keys].item_id AND $tables[download_keys].product_id=$tables[docs_items].product_id";
        $fields .= ", $tables[download_keys].download_key, $tables[download_keys].expires";
    }

    $join .= " left join $tables[products_system_info] on $tables[products_system_info].product_id=$tables[docs_items].product_id";
    $fields .= ", $tables[products_system_info].supplier_customer_id";

    $products = cw_query("select $tables[products].*, $tables[products].productcode as sku, $tables[docs_items].*, IF($tables[products].product_id IS NULL, 'Y', '') as is_deleted, IF($tables[docs_items].product = '', $tables[products].product, $tables[docs_items].product) as product $fields FROM $tables[docs_items] LEFT JOIN $tables[products] ON $tables[docs_items].product_id = $tables[products].product_id $join WHERE $tables[docs_items].doc_id='$doc_id'");
    $products = cw_doc_translate_products($products, $doc['info']['language']);

    $is_returns = false;
    cw_load('warehouse');
    
	$gift_doc_ids = cw_doc_get_related_docs($doc_id);
	$gift_doc_ids = array_unique(array_merge($gift_doc_ids, array($doc_id)));
    $giftcerts = cw_query("SELECT * $gc_add_date FROM $tables[giftcerts] WHERE doc_id in ('" . implode(',', $gift_doc_ids) . "')");
    if (!empty($giftcerts) && $config['General']['use_counties'] == "Y") {
        foreach ($giftcerts as $k => $v) {
            if (!empty($v['recipient_county']))
                $giftcerts[$k]['recipient_countyname'] = cw_get_county($v['recipient_county']);
        }
    }
    $doc['giftcerts'] = $giftcerts;
    
    if ($doc['info']['giftcert_ids']) {
        $doc['info']['applied_giftcerts'] = explode('*', $doc['info']['giftcert_ids']);

        if ($doc['info']['applied_giftcerts']) {
            $tmp = array();

            foreach ($doc['info']['applied_giftcerts'] as $k => $v) {

                if (empty($v))
                    continue;

                list(
                    $arr['giftcert_id'],
                    $arr['giftcert_cost']
                ) = explode(':', $v);

                $tmp[] = $arr;
            }
            $doc['info']['applied_giftcerts'] = $tmp;
        }
    }

    $doc['is_returns'] = $is_returns;

    if (cw_query_first_cell("select count(*) from $tables[docs_items], $tables[download_keys] WHERE $tables[docs_items].doc_id = '$doc_id' and $tables[download_keys].item_id = $tables[docs_items].item_id "))
        $doc['is_egood'] = 'Y';
    elseif (cw_query_first_cell("select count(*) from $tables[docs_items], $tables[products] WHERE $tables[docs_items].doc_id = '$doc_id' and $tables[docs_items].product_id=$tables[products].product_id AND $tables[products].distribution != ''"))
        $doc['is_egood'] = 'E';

    if (preg_match("/(free_ship|percent|absolute)(?:``)(.+)/S", $doc['coupon'], $found)) {
        $doc['coupon'] = $found[2];
        $doc['coupon_type'] = $found[1];
    }

    $order['info']['extra']['tax_info']['product_tax_name'] = '';
    $_products_taxes = array();

    if ($products)
    foreach ($products as $k=>$v) {

        if ($addons['sn'])
            $v['serial_numbers'] = cw_query("select * from $tables[docs_items_serials] where item_id='$v[item_id]'");

        $v['product_options_txt'] = $v['product_options'];
        if ($v['extra_data']) {
            $v['extra_data'] = unserialize($v['extra_data']);
            if (is_array(@$v['extra_data']['display'])) {
                foreach ($v['extra_data']['display'] as $i=>$j) {
                    $v["display_".$i] = $j;
                }
            }
            if (is_array($v['extra_data']['taxes'])) {
                foreach ($v['extra_data']['taxes'] as $i=>$j) {
                    if ($j['tax_value'] > 0)
                        $_products_taxes[$i] = $j['tax_display_name'];
                }
            }
        }

        $v['original_price'] = $v['ordered_price'] = $v['price'];
        $v['price_deducted_tax'] = "Y";
        if ($v['is_deleted'] != 'Y') {
            $v['original_price'] = cw_query_first_cell("SELECT $tables[products_prices].price FROM $tables[products_prices] WHERE $tables[products_prices].product_id = '$v[product_id]' AND $tables[products_prices].membership_id IN (0, '$userinfo[membership_id]') AND $tables[products_prices].quantity <= '$v[amount]' AND $tables[products_prices].variant_id = 0");
# kornev, TOFIX
            if ($addons['product_options'] && $v['extra_data']['product_options']) {
                list($variant, $product_options) = cw_get_product_options_data($v['product_id'], $v['extra_data']['product_options'],$userinfo['membership_id']);

                if ($product_options === false) {
                    unset($product_options);
                }
                else {
                    if (empty($variant['price']))
                        $variant['price'] = $v['original_price'];

                    $v['original_price'] = $variant['price'];
                    unset($variant['price']);
                    if ($product_options) {
                        foreach ($product_options as $o) {
                            if ($o['modifier_type'] == '%')
                                $v['original_price'] += $v['original_price']*$o['price_modifier']/100;
                            else
                                $v['original_price'] += $o['price_modifier'];
                        }
                    }

                    $v['product_options'] = $product_options;

                    # Check current and saved product options set
                    if (!empty($v['product_options_txt'])) {
                        $flag_txt = true;

                        # Check saved product options
                        $count = 0;
                        foreach ($v['product_options'] as $opt) {
                            if (preg_match("/".preg_quote($opt['class'],"/").": ".preg_quote($opt['option_name'], "/")."/Sm", $v['product_options_txt']))
                                $count++;
                        }
                        if ($count != count($v['product_options']))
                            $flag_txt = false;

                        # Check current product options set
                        if ($flag_txt) {
                            $count = 0;
                            $tmp = explode("\n", $v['product_options_txt']);
                            foreach ($tmp as $txt_row) {
                                if (!preg_match("/^([^:]+): (.*)$/S", trim($txt_row), $match))
                                    continue;

                                foreach ($v['product_options'] as $opt) {
                                    if ($match[1] == $opt['option_name'] && $match[2] == trim($opt['name'])) {
                                        $count++;
                                        break;
                                    }
                                }
                            }

                            if ($count != count($tmp))
                                $flag_txt = false;
                        }

                        # Force display saved product options set
                        # if saved and current product options sets wasn't equal
                        if(!$flag_txt)
                            $v['force_product_options_txt'] = true;
                    }

                    if (!empty($variant)) {
                        $v = cw_array_merge($v, $variant);
                    }
                }
            }
        }

        $products[$k] = $v;
    }
    $doc['products'] = $products;

    if (count($_products_taxes) == 1)
        $order['info']['extra']['tax_info']['product_tax_name'] = array_pop($_products_taxes);

    if ($order['coupon_type'] == "free_ship") {
        $order['shipping_cost'] = $order['coupon_discount'];
        $order['discounted_subtotal'] += $order['coupon_discount'];
    }

    $order['discounted_subtotal'] = price_format($order['discounted_subtotal']);

# kornev, 512 is free for now (something was removed)


    if ($info_type & 1024 && $addons['pos']) {
        cw_load('pos');
        $doc['pos'] = cw_pos_get_doc_info($doc['doc_info_id']);
    }

    if ($info_type & 2048) {
        $doc['warehouse'] = cw_warehouse_get_like_user($doc['info']['warehouse_customer_id'], $doc['info']['warehouse_customer_id']);
    }

    if ($info_type & 4096)
        $doc['quotes'] = cw_doc_get_quotes($doc_id);

    if ($info_type & 8192)
        $doc['info']['details'] = text_decrypt($doc['info']['details']);

    if ($info_type & 1)
        $doc['attributes'] = cw_func_call('cw_attributes_get', array('item_id' => $doc_id, 'item_type' => 'O'));

    return $doc;
}

function cw_doc_get_related_docs($doc_id, $related = array(), $level = -1) {
    global $tables;

    $related = cw_query_column("select related_doc_id from $tables[docs_relations] where doc_id='$doc_id'");
/*
    $level ++;

    $items = cw_query_column("select item_id from $tables[docs_items_relation] where doc_id='$doc_id'");
    if (count($items)) {
        $related[] = $doc_id;
        $related = cw_query_column("select doc_id from $tables[docs_items] where item_id in ('".implode("', '", $items)."') and doc_id not in ('".implode("', '", $related)."')");
    }
*/
/*
    $related[] = $doc_id;
    if ($items)
        foreach($items as $val) {
            $rel_docs = cw_query_column("select doc_id from $tables[docs_items] where item_id='$val' and doc_id not in ('".implode("', '", $related)."')");
            if (is_array($rel_docs))
                foreach($rel_docs as $rd) {
                    $related = array_merge($related, cw_doc_get_related($val['doc_id'], $related, $level));
                }
        }
*/
    return array_unique($related);
}

function cw_doc_get_related($doc_id) {
    global $tables;

    $related = cw_doc_get_related_docs($doc_id);
    if (count($related));
        return cw_query_hash("select * from $tables[docs] where doc_id in ('".implode("', '", $related)."')", 'type');
    return array();
}

function cw_doc_get_relations($doc_id) {
    global $tables;

    $related = cw_doc_get_related_docs($doc_id);
    $items = cw_query_column("select item_id from $tables[docs_items] where doc_id='$doc_id'");
    $return = array();
    if (count($related) && count($items)){
        $docs = cw_query("select * from $tables[docs] where doc_id in ('".implode("', '", $related)."')");
        if (is_array($docs))
        foreach($docs as $val) {
//            if (!in_array($val['type'], array('S', 'I', 'C'))) continue;
// TODO: NO SQL
            $val['items'] = cw_query_hash("select di.* from $tables[docs_items] as di where di.doc_id='$val[doc_id]'", 'item_id', false);
            $return[$val['type']][] = $val;
        }
    }
    return $return;
}

function cw_doc_get_relations_items($doc_id) {
    global $tables;
    return cw_query_hash("select di.* from $tables[docs_items] as di where di.doc_id='$doc_id'", 'item_id', false);
}

function cw_doc_delete($doc_id) {
    global $tables;

    $doc_info_id = cw_query_first_cell("select doc_info_id from $tables[docs] where doc_id='$doc_id'");
    $doc_info = cw_query_first("select main_address_id, current_address_id from $tables[docs_user_info] where doc_info_id='$doc_info_id'");

    db_query("delete from $tables[docs] where doc_id='$doc_id'");
    db_query("delete from $tables[docs_info] where doc_info_id='$doc_info_id'");
    db_query("delete from $tables[docs_user_info] where doc_info_id='$doc_info_id'");
    db_query("delete from $tables[docs_items] where doc_id='$doc_id'");
    db_query("delete from $tables[customers_addresses] where address_id='$doc_info[main_address_id]' and customer_id=0");
    db_query("delete from $tables[customers_addresses] where address_id='$doc_info[current_address_id]' and customer_id=0");
    db_query("delete from $tables[docs_extras] where doc_id='$doc_id'");

    return true;
}

function cw_doc_delete_all() {
    global $tables;

    db_query("delete from $tables[docs]");
    db_query("delete from $tables[docs_extras]");
    db_query("delete from $tables[docs_items]");
    db_query("delete from $tables[docs_info]");
    db_query("delete from $tables[docs_user_info] where doc_info_id != 0");
    db_query("delete from $tables[customers_addresses] where customer_id=0");
}

function cw_doc_make_relation($doc_id, $item_id, $amount = 0, $full = false) {
    global $tables;

    $info = cw_query_first("select * from $tables[docs_items] where item_id='$item_id'");
    if ($amount) $info['amount'] = $amount;
    $info['doc_id'] = $doc_id;
    $info['item_id'] = null;
    cw_array2insert('docs_items', $info, true);

    $item_relation = cw_query_column("select doc_id from $tables[docs_items] where item_id='$item_id'");
    if(is_array($item_relation))
    foreach($item_relation as $val)
        cw_doc_make_related_doc($doc_id, $val);
}

function cw_doc_make_full_relation($type, $doc_id) {
    global $tables;

    $old_doc = cw_query_first("select prefix, doc_info_id from $tables[docs] where doc_id='$doc_id'");
    $old_items = cw_query_column("select item_id from $tables[docs_items] where doc_id='$doc_id'");

    $add_info = cw_query_first("select warehouse_customer_id from $tables[docs_info] where doc_info_id='$old_doc[doc_info_id]'");
    $docs_pos_info = cw_query_first("select * from $tables[docs_pos_info] where doc_info_id='$old_doc[doc_info_id]'");
    $docs_user_info = cw_query_first("select * from $tables[docs_user_info] where doc_info_id='$old_doc[doc_info_id]'");
    $doc_id = cw_doc_create_empty($type, array(), $old_doc['prefix'], array('docs_info' => $add_info, 'docs_pos_info' => $docs_pos_info, 'docs_user_info' => $docs_user_info));
    if ($old_items)
    foreach($old_items as $item_id)
        cw_doc_make_relation($doc_id, $item_id);

    cw_doc_recalc($doc_id);
}

function cw_doc_make_relation_doc($type, $doc_id, $item_id = 0, $amount = 0, $is_full = false) {
    global $tables, $config;

    $old_doc = cw_query_first("select date, doc_info_id, prefix, display_id from $tables[docs] where doc_id='$doc_id'");
    $add_info = cw_query_first("select warehouse_customer_id from $tables[docs_info] where doc_info_id='$old_doc[doc_info_id]'");
    if ($is_full) {
        $add_info = cw_query_first("select warehouse_customer_id from $tables[docs_info] where doc_info_id='$old_doc[doc_info_id]'");
        $docs_user_info = cw_query_first("select * from $tables[docs_user_info] where doc_info_id='$old_doc[doc_info_id]'");
    }

    $doc_id = cw_doc_create_empty($type, array(), $old_doc['prefix'], array('docs_info' => $add_info, 'docs_user_info' => $docs_user_info));
    if ($item_id)
        cw_doc_make_relation($doc_id, $item_id, $amount);

    cw_doc_recalc($relation_doc_id);
    return $doc_id;
}

function cw_doc_delete_relation($doc_id, $item_id) {
    global $tables;
    if (!is_array($item_id)) $item_id = array($item_id);
    db_query("delete from $tables[docs_items] where doc_id='$doc_id' and item_id in ('".implode("', '", $item_id)."')");
}

function cw_doc_make_related_doc($doc_id1, $doc_id2) {
    cw_array2insert('docs_relations', array('doc_id' => $doc_id1, 'related_doc_id' => $doc_id2), true);
    cw_array2insert('docs_relations', array('doc_id' => $doc_id2, 'related_doc_id' => $doc_id1), true);
}

# kornev, full re-calc
# BTW - all of the required info have to be stored separatly in this case (discount + additions conditions)
function cw_doc_recalc($doc_id) {
    global $config, $tables;

    cw_load('aom', 'cart_process');

    $orig = $config['Taxes']['display_taxed_order_totals'];
    $doc_data = cw_call('cw_doc_get', array($doc_id, 65535));
    if ($doc_data['type'] == 'G')
        $config['Taxes']['display_taxed_order_totals'] = 'Y';
    else
        $config['Taxes']['display_taxed_order_totals'] = 'N';

	$company_id = 0;
	if (is_numeric($doc_data['userinfo']['customer_id'])) { 
		$company_id = cw_query_first_cell("SELECT company_id FROM $tables[customers_customer_info] WHERE customer_id = " . $doc_data['userinfo']['customer_id']);
	}
    $doc_data['userinfo']['company_id'] = $company_id;
    $doc_data['use_discount_alt'] = '';
    $doc_data['use_shipping_cost_alt'] = '';
    $doc_data['use_shipping_insurance_alt'] = '';
    $doc_data = cw_aom_normalize_after_update($doc_data, array());
    cw_aom_update_order($doc_data, array());

    $config['Taxes']['display_taxed_order_totals'] = $orig;
}

// TODO: explore this functionality in admin (used in core/include/users/docs.php)
function cw_doc_generate_group($doc_ids) {
    global $tables;

    if (is_array($doc_ids)) {
        $docs_info = array();
        $sum_fields = array('subtotal', 'discount', 'giftcert_discount', 'coupon_discount', 'shipping_cost', 'weight', 'shipping_insurance', 'tax','total');
        $doc_type = 'O';
        foreach($doc_ids as $doc_id) {
            $fields = cw_query_first("select di.*, d.type from $tables[docs_info] as di, $tables[docs] as d where d.doc_id='$doc_id' and d.doc_info_id=di.doc_info_id");
            if ($fields)
            foreach($sum_fields as $k=>$v)
                $docs_info[$v] += $fields[$v];
            $doc_type = $fields['type'];
        }
        $doc_info_id = cw_array2insert('docs_info', $docs_info);

        $display_id = cw_doc_get_display_id($doc_type);
        $doc = array(
            'doc_info_id' => $doc_info_id,
            'type' => $doc_type,
            'display_id' => $display_id,
            'prefix' => '',
            'display_doc_id' => $display_id,
            'year' => date('Y', cw_core_get_time()),
            'date' => cw_core_get_time(),
            'status_change' => cw_core_get_time(),
        );
        $doc_id_new = cw_array2insert('docs', $doc);

        $first_order_user_info = cw_query_first($sql="select dui.* from $tables[docs_user_info] as dui, $tables[docs] as d where dui.doc_info_id=d.doc_info_id and d.doc_id in ('".implode("', '", $doc_ids)."') limit 1");
        $first_order_user_info['doc_info_id'] = $doc_info_id;
        cw_array2insert('docs_user_info', $first_order_user_info);

        foreach($doc_ids as $doc_id) {
            $relations = cw_query("select * from $tables[docs_items_relation] where doc_id='$doc_id'");
            if ($relations)
            foreach($relations as $k=>$v) {
                $v['doc_id'] = $doc_id_new;
                cw_array2insert('docs_items_relation', $v, true);
            }
        }
    }
}

function cw_doc_translate_products($products, $code) {
    global $tables;

    if (!is_array($products) || empty($products) || empty($code))
        return $products;

    $hash = array();
    foreach($products as $k => $p) {
        $hash[$p['product_id']][] = $k;
    }

    if (empty($hash))
        return $products;

    foreach ($hash as $pid => $keys) {
        $local = cw_query_first("SELECT product, descr, fulldescr, features_text, specifications FROM $tables[products_lng] WHERE product_id = '$pid' AND code = '$code'");
        if (empty($local) || !is_array($local))
            continue;

        foreach($keys as $k) {
            $products[$k] = cw_array_merge($products[$k], preg_grep("/\S/S", $local));
        }
    }

    return $products;
}

function cw_doc_details_fields_as_labels($force=false) {
    $rval = array();
    foreach (cw_doc_details_fields(true) as $field) {
        if (preg_match('!^\{(.*)\}$!S', $field, $sublabel))
            $rval[$field] = cw_get_langvar_by_name('lbl_payment_'.$sublabel[1], NULL, false, $force);
    }

    return $rval;
}

function cw_doc_details_fields($all=false) {
    global $store_cc, $store_ch, $store_cvv2;
    static $all_fields = array (
        "CC" => array (
            "card_name" => "{CardOwner}",
            "card_type" => "{CardType}",
            "card_number" => "{CardNumber}",
            "card_valid_from" => "{ValidFrom}",
            "card_expire" => "{ExpDate}",
            "card_issue_no" => "{IssueNumber}"
        ),
        "CC_EXT" => array (
            "card_cvv2" => "CVV2"
        ),
        "CH" => array (
            # ACH
            "check_name" => "{AccountOwner}",
            "check_ban" => "{BankAccount}",
            "check_brn" => "{BankNumber}",
            "check_number" => "{FractionNumber}",
            # Direct Debit
            "debit_name" => "{AccountOwner}",
            "debit_bank_account" => "{BankAccount}",
            "debit_bank_number" => "{BankNumber}",
            "debit_bank_name" => "{BankName}"
        )
    );

    $keys = array();
    if ($store_cc || $all) {
        $keys[] = "CC";
        if ($store_cvv2 || $all) $keys[] = "CC_EXT";
    }

    if ($store_ch || $all) $keys[] = "CH";

    $rval = array();
    foreach ($keys as $key) {
        $rval = cw_array_merge($rval, $all_fields[$key]);
    }

    return $rval;
}

function cw_doc_details_translate_smarty($params) {
    return cw_doc_details_translate($params['details'], false);
}

function cw_doc_details_translate($order_details, $force=false) {
    static $labels = array();
    global $current_language;

    if (empty($labels[$current_language]))
        $labels[$current_language] = cw_doc_details_fields_as_labels($force);

    $order_details = str_replace(
        array_keys($labels[$current_language]),
        array_values($labels[$current_language]),
        $order_details);

    return $order_details;
}

function cw_doc_change_status_inner(&$doc_data, $status, $advinfo) {
    global $tables, $config, $smarty, $current_area;

    global $current_language;
cw_log_add(__FUNCTION__, array($doc_data, $status, $advinfo));
    $doc_id = $doc_data['doc_id'];

    if ($advinfo) {
        $info = addslashes(cw_crypt_text($doc_data['info']['details']."\n--- Advanced info ---\n".$advinfo));
        db_query("update $tables[docs_info] set details='".$info."' where doc_info_id='".$doc_data['info']['doc_info_id']."'");
    }
    db_query("update $tables[docs] set status='$status', status_change='" . cw_core_get_time() . "' where doc_id='$doc_id'");

    if ($status != $doc_data['status']) {

        cw_event('on_doc_change_status', array($doc_data, $status));

    	cw_load('web', 'email', 'accounting');
        cw_accounting_generate_movement($doc_data, 0, $doc_data['status'], $status);

        if ($current_area == 'C') $session_failed_transaction++;
        
        if ($doc_data['info']['layout_id'])
	    $layout = cw_web_get_layout_by_id($doc_data['info']['layout_id']);
	else
            $layout = cw_call('cw_web_get_layout', array('docs_'.$doc_data['type']), true);
	
        $smarty->assign('layout_data', $layout);
	$smarty->assign('info', $doc_data['info']);
	$smarty->assign('products', $doc_data['products']);

        $smarty->assign('new_status', $status);
        $smarty->assign('old_status', $doc_data['status']); 

        $doc_data['status'] = $status;
        $smarty->assign('order', $doc_data);
        $smarty->assign('doc', $doc_data);
 
        if ($notify_emails = cw_call('cw_doc_order_status_emails', array($doc_data, $status, 'admin'))) {
            $smarty->assign('usertype_layout', 'A');
            $smarty->assign('is_email_invoice', 'Y');
            foreach ($notify_emails as $notify_email) {
                $to_customer  = cw_query_first_cell("SELECT language FROM $tables[customers] WHERE email='$notify_email' ORDER BY customer_id DESC");
                if (empty($to_customer))
                    $to_customer = $config['default_admin_language'];
                $current_language = $to_customer;            
                cw_call('cw_send_mail', array($config['Company']['orders_department'], $notify_email, 
                                            /*'mail/docs/admin_subj.tpl', 'mail/docs/admin.tpl'*/ 
                         'mail/docs/status_changed_admin_subj.tpl', 'mail/docs/status_changed_admin.tpl', $config['default_admin_language'], true));
            }
            $smarty->assign('is_email_invoice', 'N');
            $smarty->assign('usertype_layout', '');
        }

        if ($notify_emails = cw_call('cw_doc_order_status_emails', array($doc_data, $status, 'customer'))) {
            $smarty->assign('is_email_invoice', 'Y');
            foreach ($notify_emails as $notify_email) {
                $to_customer  = cw_query_first_cell("SELECT language FROM $tables[customers] WHERE email='$notify_email' ORDER BY customer_id DESC");
                if (empty($to_customer))
                    $to_customer = $config['default_customer_language'];
                    
                $doc_data['products'] = cw_doc_translate_products($doc_data['products'], $to_customer);
                $smarty->assign('order', $doc_data);
                $current_language = $to_customer;
                cw_call('cw_send_mail', array($config['Company']['orders_department'], $notify_email, 
                                              /*'mail/docs/customer_subj.tpl', 'mail/docs/customer.tpl'*/
                                              'mail/docs/status_changed_customer_subj.tpl', 'mail/docs/status_changed_customer.tpl', null, false, true));
            }
            $smarty->assign('is_email_invoice', 'N');
        }
 
        cw_event('on_doc_change_status_emails_send', array($doc_data, $status));

        $func = 'cw_doc_change_status_' . $status;
        if (function_exists($func))
            cw_func_call($func, $doc_data);

        // invoice approved
		if ($status == 'P' && $doc_data['type'] == 'I') {
                        $smarty->assign('is_email_invoice', 'Y');
			cw_call('cw_send_mail', array(
				$config['Company']['orders_department'], 
				$doc_data['userinfo']['email'], 
				'mail/docs/customer_subj.tpl', 
				'mail/docs/customer_invoice_approved.tpl', 
				null, 
				false, 
				true
			));
                        $smarty->assign('is_email_invoice', 'N');
		}

		// invoice expired
		if ($status == 'F' && $doc_data['type'] == 'I') {
                        $smarty->assign('is_email_invoice', 'Y');
			cw_call('cw_send_mail', array(
				$config['Company']['orders_department'], 
				$doc_data['userinfo']['email'], 
				'mail/docs/customer_subj.tpl', 
				'mail/docs/customer.tpl', 
				null, 
				false, 
				true
			));
                        $smarty->assign('is_email_invoice', 'N');
		}
    }
}

function cw_doc_change_status($doc_ids, $status, $advinfo='') {
    global $config, $smarty, $addons, $current_area;
    global $tables;
    global $session_failed_transaction;

    $allowed_order_status = cw_doc_get_allowed_statuses();

    if (!in_array($status, $allowed_order_status)) return;

    if (!is_array($doc_ids)) $doc_ids = array($doc_ids);

    foreach ($doc_ids as $doc_id) {
        $doc_data = cw_call('cw_doc_get', array($doc_id, 8192));
        if (empty($doc_data)) continue;

        cw_call('cw_doc_change_status_inner', array(&$doc_data, $status, $advinfo));
    }
}

function cw_doc_change_status_P($doc_data) {
    global $addons, $tables, $smarty, $app_main_dir;

    if ($addons['egoods'])
        include $app_main_dir.'/addons/egoods/send_keys.php';
}

function cw_doc_change_status_C($doc_data) {
    global $addons, $app_main_dir;
}

function cw_doc_change_status_L($doc_data) {
    cw_doc_change_status_D($doc_data);
}

function cw_doc_change_status_F($doc_data) {
    cw_doc_change_status_D($doc_data);
}

function cw_doc_change_status_D($doc_data) {
    global $addons, $tables;

    $discount_coupon = $doc_data['info']['coupon'];
    if ($discount_coupon) {
        $_per_user = cw_query_first_cell("SELECT per_user FROM $tables[discount_coupons] WHERE coupon='$discount_coupon' LIMIT 1");
        if ($_per_user == "Y")
            db_query("UPDATE $tables[discount_coupons_login] SET times_used=IF(times_used>0, times_used-1, 0) WHERE coupon='$discount_coupon' AND customer_id='".$userinfo['customer_id']."'");
        else {
            db_query("UPDATE $tables[discount_coupons] SET status='A' WHERE coupon='$discount_coupon' and times_used=times");
            db_query("UPDATE $tables[discount_coupons] SET times_used=times_used-1 WHERE coupon='$discount_coupon'");
        }
        $discount_coupon="";
    }

    $order = $doc_data['order'];
    if ($order['info']['applied_giftcerts'])
    foreach ($order['info']['applied_giftcerts'] as $k=>$v) {
        if ($doc_data['status']=="P" || $doc_data['status']=="C")
            db_query("UPDATE $tables[giftcerts] SET debit=debit+'$v[giftcert_cost]' WHERE gc_id='$v[giftcert_id]'");
        db_query("UPDATE $tables[giftcerts] SET status='A' WHERE debit>0 AND gc_id='$v[giftcert_id]'");
    }

    if ($giftcerts)
    foreach($giftcerts as $giftcert)
        db_query("UPDATE $tables[giftcerts] SET status='D' WHERE gc_id='$giftcert[gc_id]'");
}

function cw_doc_get_basic_info($doc_id) {
    global $tables;

    return cw_query_first("select dui.* from $tables[docs] as d, $tables[docs_user_info] as dui where dui.doc_info_id=d.doc_info_id and d.doc_id='$doc_id'");
}

function cw_doc_place_extras_data($doc_id, $extras) {

	if (
		is_array($extras) 
		&& !empty($extras)
		&& !empty($doc_id)
	) {

		foreach ($extras as $k => $v) {
			cw_array2insert(
				'docs_extras', 
				array(
					'doc_id' => $doc_id, 
					'khash' => addslashes($k), 
					'value' => addslashes($v)
				), 
				true
			);
		}
	}
}

function cw_doc_get_extras_data($doc_id) {
	global $tables;

	$extras = array();

	$result = cw_query("SELECT * FROM $tables[docs_extras] WHERE doc_id='" . $doc_id . "'");
	
	if (!empty($result)) {
		
		foreach ($result as $value) {
			$extras[$value['khash']] = $value['value'];
		}
	}
	
	return $extras;
}


function cw_doc_place_order($params, $return = null) {
    extract($params);
    global $cart, $discount_coupon, $smarty, $config, $addons, $salesman, $adv_campaign_id, $salesman_click_id;
    global $tables, $to_customer;
    global $wlid;
    global $app_main_dir, $REMOTE_ADDR, $PROXY_IP, $CLIENT_IP, $add_to_cart_time;

    $mintime = 10;
    cw_load('web');
    cw_lock('cw_doc_place_order');

    $doc_ids = array();

    foreach ($cart['orders'] as $cart_order_idx => $current_order) {

        # $extra - one serialized field in doc details
        # 	use $extra as storage of details which will not be used for orders search or aggregate 
        # $extras - key=>value pairs in doc extras table
        # 	use $extras for scalar values which can be used in SQL queries
        
        $extra = cw_event('on_place_order_extra', array($current_order));
        $extra['additional_fields'] = $userinfo['additional_fields'];

        if (!empty($current_order['info']['shipping_no_offer'])) {
            $extra['shipping_no_offer'] = $current_order['info']['shipping_no_offer'];
        }


        $extras = cw_event('on_place_order_extras', array($current_order));
        
        //$extras['ip'] = $CLIENT_IP;
        //$extras['proxy_ip'] = $PROXY_IP;
        
# kornev, each doc has got the same attributes as the other elements, like products
# kornev, the attributes should be defined in the params by the pre function
        $doc_id = cw_doc_create_empty(
        	$order_type, 
        	array('warehouse_customer_id' => $current_order['warehouse_customer_id']), 
        	$params['prefix'], 
        	array(), 
        	$return['attributes']
        );

        $cart['orders'][$cart_order_idx]['created_doc_id'] = $doc_id;

        $extra['tax_info'] = array (
            'display_taxed_order_totals' => $config['Taxes']['display_taxed_order_totals'],
            'display_cart_products_tax_rates' => $config['Taxes']['display_cart_products_tax_rates'] == "Y",
            'taxed_subtotal' => $current_order['display_subtotal'],
            'taxed_discounted_subtotal' => $current_order['display_discounted_subtotal'],
            'taxed_shipping' => $current_order['display_shipping_cost']
        );

        $giftcert_discount = $current_order['info']['giftcert_discount'];
        $applied_taxes = addslashes(serialize($current_order['info']['taxes']));

        $discount_coupon = $current_order['coupon'];
        if (!empty($current_order['coupon'])) {
            $current_order['coupon'] = cw_query_first_cell("SELECT coupon_type FROM $tables[discount_coupons] WHERE coupon='".addslashes($current_order['coupon'])."'")."``".$current_order['coupon'];
        }

        $current_order['userinfo'] = $userinfo;
        
        $current_order['new'] = true; // Flag can be used in cw_doc_update to differ just placed empty doc from update of existing doc
        
        $current_order['info']['shipping_id'] = $cart['info']['shipping_id'];
        $current_order['info']['payment_id'] = $cart['info']['payment_id'];
        $current_order['info']['payment_label'] = $cart['info']['payment_label'];
        $current_order['info']['quote_doc_id'] = isset($cart['info']['quote_doc_id']) ? $cart['info']['quote_doc_id'] : null;
        $current_order['info']['details'] = addslashes(cw_crypt_text($order_details));
        $current_order['info']['customer_notes'] = addslashes($customer_notes);

        if ($config['Appearance']['show_cart_summary'] == 'Y')
            $current_order['info']['shipping_id'] = $cart['info']['shipping_arr'][$current_order['warehouse_customer_id']];
        $current_order['info']['extra'] = $extra;
        $current_order['info']['extras'] = $_extras;
        $current_order['status'] = $order_status;

        $doc_info = cw_doc_get_basic_info($doc_id);
        $current_order['info']['doc_info_id'] = $doc_info['doc_info_id'];
        $current_order['info']['warehouse_customer_id'] = $current_order['warehouse_customer_id'];
        $current_order['userinfo']['main_address']['address_id'] = $doc_info['main_address_id'];
        $current_order['userinfo']['current_address']['address_id'] = $doc_info['current_address_id'];

        $logged_current_order = $current_order;
        unset($logged_current_order['userinfo']['card_name']);
        unset($logged_current_order['userinfo']['card_number']);
        unset($logged_current_order['userinfo']['card_expire_Month']);
        unset($logged_current_order['userinfo']['card_expire_Year']);
        unset($logged_current_order['userinfo']['card_cvv2']);
        cw_log_add('doc_placed', array('doc_id'=>$doc_id, 'current_order'=>$logged_current_order, 'cart'=>$cart));

        cw_call('cw_doc_update', array($doc_id, $current_order));
        $doc_ids[] = $doc_id;
        $order = cw_call('cw_doc_get', array($doc_id));

        if ($discount_coupon) {
// artem, TODO: no SQL
            $_per_user = cw_query_first_cell("SELECT per_user FROM $tables[discount_coupons] WHERE coupon='$discount_coupon' LIMIT 1");
            if ($_per_user == "Y") {
                $_need_to_update = cw_query_first_cell("SELECT COUNT(*) FROM $tables[discount_coupons_login] WHERE coupon='$discount_coupon' AND customer_id='".intval($userinfo['customer_id'])."' LIMIT 1");
                if ($_need_to_update > 0)
                    db_query("UPDATE $tables[discount_coupons_login] SET times_used=times_used+1 WHERE coupon='$discount_coupon' AND customer_id='".intval($userinfo['customer_id'])."'");
                else
                    db_query("INSERT INTO $tables[discount_coupons_login] (coupon, customer_id, times_used) VALUES ('$discount_coupon', '".intval($userinfo['customer_id'])."', '1')");
            }
            else {
                db_query("UPDATE $tables[discount_coupons] SET times_used=times_used+1 WHERE coupon='$discount_coupon'");
                db_query("UPDATE $tables[discount_coupons] SET status='U' WHERE coupon='$discount_coupon' AND times_used=times");
            }
            $discount_coupon="";
        }


        $doc_data = $doc_data_customer = cw_call('cw_doc_get', array($doc_id));
        cw_load('web', 'email', 'accounting');
        cw_accounting_generate_movement($doc_data, 0, null, $order_status);

        if ($notify_emails = cw_call('cw_doc_order_status_emails', array($doc_data, $order_status, 'customer'))) {
            $to_customer = ($userinfo['language']?$userinfo['language']:$config['default_customer_language']);
            $doc_data_customer['products'] = cw_doc_translate_products($doc_data['products'], $to_customer);
            $smarty->assign('doc_data', $doc_data_customer);

            if ($doc_data['info']['layout_id'])
                $layout = cw_web_get_layout_by_id($doc_data['info']['layout_id']);
            else
                $layout = cw_call('cw_web_get_layout', array('docs_'.$doc_data['type']), true);

            $smarty->assign('layout_data', $layout);
            $smarty->assign('info', $doc_data['info']);
            $smarty->assign('products', $doc_data_customer['products']);
            $smarty->assign('order', $doc_data);
            $smarty->assign('doc', $doc_data);
            $smarty->assign('is_email_invoice', 'Y');  
            foreach ($notify_emails as $notify_email) {
                cw_call('cw_send_mail', array($config['Company']['orders_department'], $notify_email, 'mail/docs/customer_subj.tpl', 'mail/docs/customer.tpl', null, false, true));
            }
            $smarty->assign('is_email_invoice', 'N');
        }

        if ($notify_emails = cw_call('cw_doc_order_status_emails', array($doc_data, $order_status, 'admin'))) {

            # Notify orders department by email
            $smarty->assign('doc_data', $doc_data);
            $smarty->assign('usertype_layout', 'A');
            $smarty->assign('is_email_invoice', 'Y');
            foreach ($notify_emails as $notify_email) {
                cw_call('cw_send_mail', array($userinfo['email'], $notify_email, 'mail/docs/admin_subj.tpl', 'mail/docs/admin.tpl', $config['default_admin_language'], true));
            }
            $smarty->assign('is_email_invoice', 'N');
            $smarty->assign('usertype_layout', '');
        }

       cw_event('on_doc_change_status_emails_send', array($doc_data, $order_status));
 
       //cw_call_delayed('cw_doc_save_history_totals_by_customer', array(array(intval($userinfo['customer_id']))));
       cw_call_delayed('cw_doc_save_history_categories', array(array($doc_id)));
       cw_call_delayed('cw_doc_save_history_attributes', array(array($doc_id)));
       cw_call_delayed('cw_doc_save_history_manufacturers', array());
    }

    # Send notifications to orders department and warehouses when product amount in stock is low
    foreach($cart['products'] as $product) {

        if (!empty($product['distribution']) && $addons['egoods']) continue;
# kornev, TOFIX
            if ($addons['product_options'] && $product['extra_data']['product_options'])
                $avail_now = cw_get_options_amount($product['extra_data']['product_options'], $product['product_id']);
            else
                $avail_now = cw_query_first_cell("SELECT avail FROM $tables[products_warehouses_amount] WHERE product_id='".$product['product_id']."' AND warehouse_customer_id ='".$product['warehouse_customer_id']."'");

            if ($product['low_avail_limit'] >= $avail_now && $config['Email']['eml_lowlimit_warning'] == 'Y') {
                # Mail template processing
                $product['avail'] = $avail_now;
                $smarty->assign('product', $product);

                cw_call('cw_send_mail', array($config['Company']['orders_department'], $config['Company']['orders_department'], 'mail/lowlimit/subj.tpl', 'mail/lowlimit/body.tpl'));

                $pr_result = cw_query_first ("SELECT email, language FROM $tables[customers] WHERE customer_id='".$product['warehouse_customer_id']."'");
            }
    }

    cw_unlock('cw_doc_place_order');

    return $doc_ids;
}

function cw_doc_print($doc_data, $mode) {
    global $smarty, $current_area, $app_skins_dirs;

    cw_load('web');
    if ($doc_data['info']['layout_id'])
        $layout = cw_web_get_layout_by_id($doc_data['info']['layout_id']);
    else
        $layout = cw_web_get_layout('docs_'.$doc_data['type']);

    $smarty->assign('layout_data', $layout);

    $smarty->assign('doc', $doc_data);

    if ($mode == 'print') {
        return cw_display('admin/docs/doc_layout_print.tpl', $smarty,false);
    }
    elseif ($mode == 'print_pdf' || $mode == 'print_aom_pdf') {
        cw_load('pdf');
        cw_pdf_generate(cw_get_langvar_by_name('lbl_doc_info_'.$doc_data['type'], false, false, true), $app_skins_dirs[$current_area].'/index.tpl');
    }
}

function cw_doc_get_quotes($doc_id) {
    global $tables;

    $quotes = cw_query("select * from $tables[docs_quotes] where doc_id='$doc_id' order by doc_quote_id");
    return $quotes;
}

function cw_doc_get_defaulttype($type) {
    if ($type == 'B') return 'B';
    if ($type == 'D') return 'P';
    if (in_array($type, array('P', 'R', 'Q'))) return 'S';
    return 'C';
}

/**
 * Return array of emails to send order status notification
 * 
 * @param array $doc_data - order info
 * @param string $status_code - order status code
 * @param string $area_name - [admin|customer]
 * 
 * @return array
 */
function cw_doc_order_status_emails($doc_data, $status_code, $area_name) {
    global $tables, $config;
    $emails = array();
    if ($area_name == 'admin') {
        if (cw_query_first_cell("select email_$area_name from $tables[order_statuses] where code='$status_code'")) {
            $emails[] = $config['Company']['orders_department'];
        }
        $emails = array_merge($emails, cw_call('cw_doc_order_status_extra_admin_email', array($status_code, $doc_data['doc_id'])));
    }
    if ($area_name == 'customer') {
        if (cw_query_first_cell("select email_$area_name from $tables[order_statuses] where code='$status_code'")) {
            $emails[] = $doc_data['userinfo']['email'];
        }
    }
    
    return $emails;
}

function cw_doc_get_allowed_statuses () {
    global $tables;
    return cw_query_column("select distinct code from $tables[order_statuses] where deleted=0");
}

function cw_doc_get_inventory_decreasing_statuses () {
    global $tables;
    return cw_query_column("select distinct code from $tables[order_statuses] where inventory_decreasing=1 and deleted=0"); 
}

function cw_doc_get_order_status_email($params, $return) {
//params: $status_code, $area_name, $email_part
    extract($params);
    global $tables;
    if ($email_part == 'message') {
        $mode_letter = ($mode == 'R')?'R':'I';  
        $mode_condition = "and email_message_".$area_name."_mode='$mode_letter'";
    }
    return cw_query_first_cell("select email_".$email_part."_$area_name from $tables[order_statuses] where code='$status_code' $mode_condition"); 
}

function cw_doc_get_order_status_color($params, $return) {
    //params: $status_code
    extract($params);
    global $tables;
    return cw_query_first_cell("select color from $tables[order_statuses] where code='$status_code'");
}

function cw_doc_order_status_extra_admin_email($status_code, $doc_id='') {
    global $tables;
    
    $status_condition = '';
    if (!empty($status_code)) {
        $status_condition = "where code='$status_code'";
    }
    $emails_str = cw_query_first_cell("select group_concat(extra_admin_email) from $tables[order_statuses] $status_condition");
    $emails = explode(',',$emails_str);
    $emails = array_map('trim',$emails);
    $emails = array_filter($emails);
    
    return $emails;
}

/**
 * Get security key to show orders w/o login
 * 
 * @param $doc_ids array of doc_ids
 * @param $seed string|null - null if key active always for orders or for additional seed e.g. for session only
 */
function cw_doc_security_key($doc_ids, $seed=null) {
    global $config;
    $doc_ids = (array)$doc_ids;
    $hash = array();
    foreach ($doc_ids as $doc_id) {
        $doc = cw_call('cw_doc_get', array($doc_id));
        $hash[] = intval($doc_id);
        $hash[] = $doc['userinfo']['customer_id'];
        $hash[] = $doc['userinfo']['email'];
    }
    if (empty($hash)) return null;
    if (!is_null($seed)) {
        $hash[] = $seed;
    }
    $hash[] = $config['Security']['cron_code'];
    $skey = md5(serialize($hash));
    if (!is_null($seed)) $skey = 's-'.$skey;
    return $skey;
}

function cw_doc_save_history_categories($doc_ids = array()) {
    global $tables;

    if (!empty($doc_ids)) {
        $docs_conditions = " where $tables[docs_items].doc_id in ('".implode("','",$doc_ids)."')";
    }

    db_query("replace into $tables[doc_history_categories] (doc_id, category_id) select $tables[docs_items].doc_id, $tables[products_categories].category_id from $tables[docs_items] left join $tables[products_categories] on $tables[products_categories].product_id = $tables[docs_items].product_id $docs_conditions");

    return true;

}


function cw_doc_save_history_attributes($doc_ids = array()) {

    global $tables;

    if (!empty($doc_ids)) {
        $docs_condition = " and $tables[docs_items].doc_id in ('".implode("','", $doc_ids)."')";
    }

    $ordered_products = cw_query("select $tables[docs_items].doc_id, $tables[docs_items].product_id from $tables[docs_items] left join $tables[doc_history_attributes] on $tables[doc_history_attributes].doc_id=$tables[docs_items].doc_id where $tables[doc_history_attributes].doc_id is null $docs_condition order by $tables[docs_items].item_id");

    foreach ($ordered_products as $o_prod) {
        $po_attributes = cw_func_call('cw_attributes_get', array('item_id' => $o_prod['product_id'], 'item_type' => 'P', 'prefilled' => array(), 'is_default' => $is_default_attributes, 'language' => $edited_language));
        foreach ($po_attributes as $attr_name => $attr_data) {
            if ($attr_data['addon'] != '') continue;
            if (!empty($attr_data['values'])) {
                foreach ($attr_data['values'] as $val_attr_id) {
                    $val_str2insert = '';  
                    foreach ($attr_data['default_value'] as $def_value) {
                        if ($def_value['attribute_value_id'] == $val_attr_id) {
                            $val_str2insert = $def_value['value'];
                            break;  
                        } 
                    }
                    if (!empty($val_str2insert)) {
                        cw_array2insert('doc_history_attributes', array('doc_id'=>$o_prod['doc_id'], 'attribute_id'=>$attr_data['attribute_id'], 'attribute_name'=>$attr_data['name'], 'value'=>addslashes($val_str2insert)), true); 
                    }
                }  
            }        
        }
    }
    return $ordered_products;
}


function cw_doc_save_history_totals_by_customer ($update_customer_id = array()) { 

    global $tables;

//    if (empty($update_customer_id))
//        $update_customer_id = cw_query_column("select distinct($tables[docs_user_info].customer_id) as update_customer_id from $tables[docs_info], $tables[docs_user_info], $tables[docs] left join $tables[customers_docs_stats_processed_docs] on $tables[customers_docs_stats_processed_docs].doc_id=$tables[docs].doc_id where $tables[customers_docs_stats_processed_docs].doc_id is null and $tables[docs].doc_info_id = $tables[docs_user_info].doc_info_id and $tables[docs_info].doc_info_id = $tables[docs].doc_info_id", 'update_customer_id');

    $valid_statuses = cw_query_column("select code from cw_order_statuses where inventory_decreasing=1");

    if (!empty($update_customer_id)) {
        db_query("replace into $tables[customers_docs_stats] (customer_id, avg_subtotal, total_spent, orders_count) select $tables[docs_user_info].customer_id, avg($tables[docs_info].subtotal), sum($tables[docs_info].total), count(*) from $tables[docs_info], $tables[docs_user_info], $tables[docs] where $tables[docs].doc_info_id = $tables[docs_user_info].doc_info_id and $tables[docs_info].doc_info_id = $tables[docs].doc_info_id and $tables[docs_user_info].customer_id in ('".implode("','", $update_customer_id)."') and $tables[docs].status in ('".implode("','",$valid_statuses)."') group by $tables[docs_user_info].customer_id");
        db_query("replace into $tables[customers_docs_stats_processed_docs] (doc_id) select doc_id from cw_docs");
    }

    return $update_customer_id;
}

function cw_doc_save_history_manufacturers() {
    global $tables;

    $max_processed_doc_id = cw_query_first_cell("select max(doc_id) from $tables[doc_history_manufacturers]");

    if ($max_processed_doc_id)
    db_query("replace into $tables[doc_history_manufacturers] (doc_id, manufacturer_id) select $tables[docs_items].doc_id, $tables[attributes_values].value from $tables[docs_items] inner join $tables[attributes_values] on $tables[attributes_values].item_id = $tables[docs_items].product_id and $tables[attributes_values].item_type='P' and $tables[attributes_values].attribute_id='49' where $tables[docs_items].doc_id > '$max_processed_doc_id'");

    return true;
}

function cw_doc_order_status_email_enabled ($status_code, $area_name) {
    global $tables;
    return cw_query_first_cell("select email_$area_name from $tables[order_statuses] where code='$status_code'");
}

function cw_doc_allowed_to_customer($doc_id, $customer_id) {
    global $tables;

    $result = cw_query_first_cell($s =
        "SELECT COUNT(*) 
        FROM $tables[customers] c1 
        INNER JOIN $tables[customers] c2 ON c1.email = c2.email AND c1.usertype = c2.usertype AND c1.usertype = 'C'
        INNER JOIN $tables[docs] d ON d.doc_id = '$doc_id'
        INNER JOIN $tables[docs_user_info] dui ON dui.doc_info_id = d.doc_info_id AND dui.customer_id = c2.customer_id
        WHERE c1.customer_id = '$customer_id'"
        );

    
    return $result;    
}

function cw_doc_get_linked_customers_list($customer_id) {
    global $tables, $current_area;

    if ($current_area == 'C')
        $result = cw_query_column(
            "SELECT c2.customer_id 
            FROM $tables[customers] c1 
            INNER JOIN $tables[customers] c2 
                ON 
                    c1.email = c2.email 
                    AND c1.usertype = c2.usertype 
                    AND c1.usertype = 'C' 
            WHERE c1.customer_id = '$customer_id'"
            );
    else 
        $result = [$customer_id];    

    return $result;    
}
