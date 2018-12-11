<?php
# info_type
# 1 - cod types
function cw_shipping_search($params, $return = null) {
    extract($params);

    global $tables;

    $fields = $from_tbls = $query_joins = $where = $groupbys = $having = $orderbys = array();

    $from_tbls[] = 'shipping';
    $fields[] = "$tables[shipping].*";

# kornev, merge standart and additional variables
    if ($return)
    foreach ($return as $saname => $sadata)
        if (isset($$saname) && is_array($$saname) && empty($$saname)) $$saname = $sadata;

    if ($data['addon']) {
        $query_joins['shipping_carriers'] = array(
            'on' => "$tables[shipping_carriers].carrier_id = $tables[shipping].carrier_id",
            'parent' => 'shipping',
            'is_straight' => 1,
        );
        $where[] = "$tables[shipping_carriers].addon = '$data[addon]'";
    }

    if (isset($data['carrier_id']))
        $where[] = "carrier_id='$data[carrier_id]'";

    if (isset($data['active']))
        $where[] = "$tables[shipping].active='$data[active]'";

    if ($data['where'])
        $where = array_merge($where, $data['where']);

    $orderbys[] = "$tables[shipping].orderby";

    $search_query = cw_db_generate_query($fields, $from_tbls, $query_joins, $where, $groupbys, $having, $orderbys);
    $shippings = cw_query($search_query);

    if ($info_type & 1 && $shippings)
    foreach($shippings as $k=>$v)
       $shippings[$k]['cod_type_ids'] = cw_query_key("select cod_type_id from $tables[shipping_cods] where shipping_id='$v[shipping_id]'");

    return $shippings;
}


function cw_shipping_get($shipping_id) {
    global $tables;

    $shipping = cw_query_first("select s.*, sc.addon from $tables[shipping] as s, $tables[shipping_carriers] as sc where sc.carrier_id = s.carrier_id and shipping_id='$shipping_id'");
    $shipping['cod_type_ids'] = cw_query_key("select cod_type_id from $tables[shipping_cods] where shipping_id='$shipping_id'");
    return $shipping;
}


function cw_shipping_delete($shipping_id) {
    global $tables;

    $shipping_id = cw_query_first_cell($sql="select shipping_id from $tables[shipping] as s, $tables[shipping_carriers] as sc where sc.addon = '' and sc.carrier_id =  s.carrier_id and shipping_id='$shipping_id'");
    if ($shipping_id) {
        db_query("delete from $tables[shipping] where shipping_id='$shipping_id'");
        db_query("delete from $tables[shipping_rates] where shipping_id='$shipping_id'");
        db_query("delete from $tables[products_shipping] where shipping_id='$shipping_id'");
        db_query("delete from $tables[shipping_cods] where shipping_id='$shipping_id'");
		db_query("delete from $tables[payments_shippings] where shipping_id='$shipping_id'");
		
        cw_attributes_cleanup($shipping_id, 'D');
    }
}

function cw_shipping_get_carriers($special_order = false, $where = '', $limit = '') {
    global $tables;

    return cw_query("select * 
        from $tables[shipping_carriers] 
        left join $tables[addons] as m on m.addon=$tables[shipping_carriers].addon 
        where (m.active or m.addon is null) ".($where?"and $where ":"").
        'order by '.($special_order?'carrier':'carrier').
        ($limit?' limit '.$limit:''));
}

function cw_shipping_get_carrier($shipping_id) {
    global $tables;

    return cw_query_first("select sc.* from $tables[shipping_carriers] as sc, $tables[shipping] as s where s.shipping_id='$shipping_id' and s.carrier_id=sc.carrier_id");
}

function cw_shipping_insert_carrier($values) {
    return cw_array2insert('shipping_carriers', $values, false, array('carrier','addon'));
}

function cw_shipping_update_carrier($carrier_id, $values) {
    return cw_array2update('shipping_carriers', $values, "carrier_id='$carrier_id'");
}

function cw_shipping_delete_carrier($carrier_id) {
    global $tables;

    $carrier_id = cw_query_first_cell("select carrier_id from $tables[shipping_carriers] where carrier_id='$carrier_id' and (addon='' or addon IS NULL)");

    if ($carrier_id) {
        db_query("delete from $tables[shipping_carriers] where carrier_id='$carrier_id'");
        db_query("update $tables[shipping] set carrier_id=0 where carrier_id='$carrier_id'");
        return true;
    }

    return false;
}

function cw_shipping_update_cods($shipping_id, $cods) {
    global $tables;

    db_query("delete from $tables[shipping_cods] where shipping_id='$shipping_id'");
    if (is_array($cods))
    foreach($cods as $v)
        cw_array2insert('shipping_cods', array('shipping_id' => $shipping_id, 'cod_type_id' => $v));
}

function cw_shipping_get_shipping_cod_types($shipping_id) {
    global $tables;

    return cw_query("select sct.* from $tables[shipping_cod_types] as sct, $tables[shipping_cods] as sc where sct.cod_type_id=sc.cod_type_id and sc.shipping_id='$shipping_id' order by orderby");
}

function cw_shipping_get_cod_types() {
    global $tables;

    return cw_query("select * from $tables[shipping_cod_types] order by orderby");
}

function cw_shipping_delete_cod_type($cod_type_id) {
    global $tables;

    db_query("delete from $tables[shipping_cod_types] where cod_type_id='$cod_type_id'");
    db_query("delete from $tables[shipping_cods] where cod_type_id='$cod_type_id'");
}


# kornev, here the offline rates are implemented, the online rates should be implemented in the addons which will hook this function.
# kornev, this function should be overloaded for the the online shipping
function cw_shipping_get_rates($params, $return) {
    global $tables;

    extract($params); // Damn extraction, do not use such approach. List vars explicitly instead.
    $total = $params['what_to_ship_params'];
    $weight = $params['weight'];

    $customer_zone = cw_func_call('cw_cart_get_zone_ship', array('address' => $to_address, 'type' => 'D'));

    $weight_condition = "weight_min<='".$total['valid']['weight']."' AND (weight_limit='0' OR weight_limit>='".$total['valid']['weight']."')";
    $shipping = cw_func_call('cw_shipping_search', array('data' => array('active' => 1, 'where' => array($weight_condition))));

    $rates = array();
    if ($shipping)
    foreach ($shipping as $k=>$v) {
        $is_rates = cw_func_call('cw_is_shipping_rates', array('shipping_id' => $v['shipping_id'], 'address' => $to_address, 'products' => $cart['products'], 'weight' => $total['valid']['weight'], 'subtotal' => $cart['info']['subtotal']));
        if (!$is_rates) continue;

        $total_condition = "IF(apply_to = 'ST', '" . $total["valid"]["ST"] . "', '" . $total["valid"]["DST"] . "')";
        $shrate = cw_query_first("select * from $tables[shipping_rates] WHERE shipping_id='$v[shipping_id]' AND zone_id='$customer_zone' AND minweight<='".$total['valid']['weight']."' AND (maxweight>='".$total['valid']['weight']."' or maxweight=0) and type='D' AND mintotal <= ".$total_condition." AND maxtotal >= ".$total_condition." ORDER BY maxtotal, maxweight limit 1");

        if (empty($shrate)) continue;

        $apply_to = ($shrate['apply_to'] == 'ST') ? 'ST' : 'DST';

        $v['original_rate'] = $shrate['rate'] +
                $total['apply']['weight'] * $shrate['weight_rate'] +
                $total['apply']['items'] * $shrate['item_rate'] +
                $total['apply'][$apply_to] * $shrate['rate_p'] / 100;

        if ($shrate['overweight'] > 0 and $shrate['overweight'] < $total['apply']['weight'] and $shrate['overweight_rate'] > 0) {
            $weight_diff = $total['apply']['weight'] - $shrate['overweight'];
            $v['original_rate'] += $weight_diff * $shrate['overweight_rate'];
	    }
//        cw_log_add('ship', "{$shrate['rate']} +{$total['apply']['weight']} * {$shrate['weight_rate']} + {$total['apply']['items']} * {$shrate['item_rate']} + {$total['apply'][$apply_to]} * {$shrate['rate_p']} / 100");
        $rates[$v['shipping_id']] = cw_array_merge($shrate, $v);
    }

    return $rates;
}

# kornev
function cw_shipping_get_original_rate($shipping_id, $order_hash) {
    global $saved_rates;
    return $saved_rates[$order_hash][$shipping_id]['original_rate'];
}

# kornev, function params
# $cart
# $products
# $userinfo
# $return_all_available
# $warehouse_customer_id - it's not used mostly see order_hash
# $order_hash - used to differ orders and cache shipping
function cw_shipping_get_list($params) {
    extract($params);

	global $tables, $config, $smarty, $addons;
	global $current_carrier, $cart;
    global $saved_rates;

	if (empty($products))
		return;

    $to_address = $userinfo['current_address'];
    $from_address = $config['Company'];

    $current_carrier = $cart['info']['current_carrier'];
    if (isset($saved_rates[$order_hash])) {
    	// if used quote
        if (
	    	$addons['quote_system']
	    	&& isset($cart['info']['quote_doc_id'])
	    	&& !empty($cart['info']['quote_doc_id'])
	    	&& isset($cart['info']['shipping_id'])
	    ) {
	    	if (isset($saved_rates[$order_hash][$cart['info']['shipping_id']])) {
		    	return array($cart['info']['shipping_id'] => $saved_rates[$order_hash][$cart['info']['shipping_id']]);
		    }
		    else {
		    	return array();
		    }
	    }
	    return $saved_rates[$order_hash];
    }

/*
    if ($warehouse_customer_id != 0) {
        $warehouseinfo = cw_user_get_info($warehouse_customer_id, 1);
        $from_address = $warehouseinfo['main_address'];
    }
*/

	if (empty($userinfo['customer_id']) && $config['General']['apply_default_country'] != "Y" && $config['Shipping']['enable_all_shippings'] == "Y") {
		$enable_all_shippings = true;
		$smarty->assign('force_delivery_dropdown_box', 'Y');
	}

    $what_to_ship_params = cw_call('cw_what_to_ship',array($products));

	cw_load('http');

    $key = md5(serialize($aa= array(
        'to_address'    => $to_address,
        'from_address'  => $from_address,
        'what_to_ship'  => $what_to_ship_params,
        'order_hash'    => $order_hash,
        'extra_hash'    => cw_event('on_collect_shipping_rates_hash', array($products), array()),
        )));

	if (!($rates = cw_cache_get($key, 'shipping_rates'))) {
		$rates = cw_func_call('cw_shipping_get_rates', array('weight' => $what_to_ship_params['apply']['weight'], 'to_address' => $to_address, 'from_address' => $from_address, 'cart' => $cart, 'products' => $products, 'userinfo' => $userinfo, 'what_to_ship_params'=>$what_to_ship_params));
        if (!empty($rates)) uasort($rates, 'cw_uasort_by_order');
        cw_cache_save($rates, $key, 'shipping_rates');
	}

    if ($what_to_ship_params['apply']['items'] == 0 && !empty($rates)) {
       foreach($rates as &$r) {
            $r['original_rate'] = 0;
       }
       unset($r);
    }

# kornev, it's required for original_rate saving
    $saved_rates[$order_hash] = $rates;

    if ($rates)
    foreach($rates as $k=>$rate) {
        $tmp_cart = $cart;
        $tmp_cart['info']['shipping_id'] = $rate['shipping_id'];
# kornev, warehouses fix
        $tmp_cart['info']['shipping_arr'][$warehouse_customer_id] = $rate['shipping_id'];
        $calc_result = cw_func_call('cw_cart_calc', array('cart' => $tmp_cart, 'products' => $products, 'userinfo' => $userinfo));
        $rates[$k]['rate'] = $calc_result['info']['display_shipping_cost'];
        $rates[$k]['tax_cost'] = price_format($calc_result['info']['tax_cost']); // TOFIX: Why rate info contains tax for whole cart

		// parse delivery time "3-4 days" to "3" and "4"
		if (preg_match_all('/\d+/',$rate['shipping_time'], $delivery_time)) {
			$rates[$k]['min_delivery_time'] = $delivery_time[0][0];
			$rates[$k]['max_delivery_time'] = $delivery_time[0][1]?$delivery_time[0][1]:$rates[$k]['min_delivery_time'];
		}
    }

# kornev, only available for each products shippings are enabled
    if (is_array($products) && is_array($rates)) {
        $res = null;
        $common_delivery = array('min_delivery_time'=>0, 'max_delivery_time'=>0);
        foreach($products as $product) {
			
			// Find longest supplier delivery time among all products
			if ($product['system']['supplier_customer_id']) {
				$product_delivery = cw_user_get_custom_fields($product['system']['supplier_customer_id'],0,'','field');
				if ($product_delivery['min_delivery_time']>$common_delivery['min_delivery_time']) $common_delivery['min_delivery_time'] = $product_delivery['min_delivery_time'];
				if ($product_delivery['max_delivery_time']>$common_delivery['max_delivery_time']) $common_delivery['max_delivery_time'] = $product_delivery['max_delivery_time'];
			}
			
            $ps = unserialize($product['shippings']);
            if (is_array($ps) && count($ps)){
                if(is_null($res))
                    $res = array_keys($ps);
                else
                    $res = array_intersect($res, array_keys($ps));
            }
        }
		foreach($rates as $k=>$v)
		{
			if (is_array($res) && !in_array($v['shipping_id'], $res)) {
				unset($rates[$k]);
				continue;
			}
			
			// Increase delivery time by supplier delivery..
			$rates[$k]['min_delivery_time'] += $common_delivery['min_delivery_time'];
			$rates[$k]['max_delivery_time'] += $common_delivery['max_delivery_time'];
			// ..and re-build shipping time into string
            $rates[$k]['shipping_time_label'] = $rates[$k]['shipping_time']; 
			if ($rates[$k]['min_delivery_time']==$rates[$k]['max_delivery_time']) 
				$rates[$k]['shipping_time'] = $rates[$k]['max_delivery_time'];
			else 
				$rates[$k]['shipping_time'] = $rates[$k]['min_delivery_time'].'-'.$rates[$k]['max_delivery_time'];
		}
    }
    else $rates = array();

# kornev, final save
    $saved_rates[$order_hash] = $rates;
	// if used quote
    if (
    	$addons['quote_system']
    	&& isset($cart['info']['quote_doc_id'])
    	&& !empty($cart['info']['quote_doc_id'])
    	&& isset($cart['info']['shipping_id'])
    ) {
    	if (isset($rates[$cart['info']['shipping_id']])) {
    		return array($cart['info']['shipping_id'] => $rates[$cart['info']['shipping_id']]);
    	}
    	else {
    		return array();
    	}
    }

	return $rates;
}

# kornev, check if there are any rate for the offline shipping
function cw_is_shipping_rates($params) {//$shipping_id, $address, $products, $weight=0, $subtotal=0) {
    extract($params);

    global $tables, $config;

    if (empty($address)) {
        if ($config['Shipping']['enable_all_shippings'] == "Y") return true;
        if ($config['General']['apply_default_country'] != "Y") return false;
    }

    $customer_zone = cw_func_call('cw_cart_get_zone_ship', array('address' => $address, 'type' => 'D'));
 
    $is_rates = cw_query_first_cell("select count(*) from $tables[shipping_rates] where shipping_id='$shipping_id' AND minweight<='$weight' AND (maxweight>='$weight' or maxweight=0) and zone_id='$customer_zone' and type='D'");

    return $is_rates;
}

#
# Add new realtime shipping method
#
function cw_add_new_smethod($method, $code, $added = array()) {
	global $tables;

	if (cw_query_first_cell("SELECT COUNT(*) FROM $tables[shipping] WHERE code = '".addslashes($code)."'") == 0)
		return false;

	if (cw_query_first_cell("SELECT COUNT(*) FROM $tables[shipping] WHERE shipping = '".addslashes($method)."' AND code = '".addslashes($code)."'") > 0)
		return false;

	if (isset($added['service_code'])) {
		if (cw_query_first_cell("SELECT COUNT(*) FROM $tables[shipping] WHERE code = '".addslashes($code)."' AND service_code = '".addslashes($added['service_code'])."'") > 0)
			return false;
	}

	$max_subcode = cw_query_first_cell("SELECT MAX(subcode+0) FROM $tables[shipping]")+1;
	$data = array(
		"shipping"	=> addslashes($method),
		"subcode"	=> $max_subcode,
		"active"	=> 0,
		"is_new"	=> "Y",
		"code"		=> $code);

	if (!empty($added) && is_array($added))
		$data = cw_array_merge($data, $added);

	$id = cw_array2insert("shipping", $data);
	if (empty($id))
		return false;

	return $id;
}

// Returns main cart parameters to select and calculate shipping rates
// "valid" means physically in cart and must be taken into account to filter rates by subtotal/weight range
// "apply" means what shall be considered as base to calculate percentage, per item and per weight rates
function cw_what_to_ship($products) {
   	global $config;

    $result = array(
        'valid' => array(),
        'apply' => array(),
    );

	foreach ($products as $product) {
        if (@$product['deleted']) continue;

        $result['valid']['weight'] += $product['weight'] * $product['amount'];
		// ! Taxes may not consider although Subtotal line in cart includes taxes
		// if you need to take taxes into account - try "display_discounted_price" for DST and "display_subtotal" for ST
        $result['valid']['DST'] += $product['display_discounted_price']; //$product['subtotal'];
        $result['valid']['ST']  += $product['display_subtotal']; //price_format($product['price'] * $product['amount']);
        $result['valid']['items'] += $product['amount'];

        if ($config['Shipping']['replace_shipping_with_freight'] == "Y" && $product['shipping_freight'] > 0) continue;

		if ($product['free_shipping'] == "Y") continue;

        $result['apply']['weight'] += $product['weight'] * $product['amount'];
        $result['apply']['DST'] += $product['display_discounted_price']; //$product['subtotal'];
        $result['apply']['ST']  += $product['display_subtotal']; //price_format($product['price'] * $product['amount']);
        $result['apply']['items'] += $product['amount'];

    }

    return $result;

}

function cw_shipping_delete_zone($zone_id) {
    global $tables;

    db_query("delete from $tables[zones] WHERE zone_id='$zone_id'");
    db_query("delete from $tables[zone_element] WHERE zone_id='$zone_id'");
    db_query("delete from $tables[shipping_rates] WHERE zone_id='$zone_id'");
    db_query("delete from $tables[tax_rates] WHERE zone_id='$zone_id'");
}

# kornev, hooks
function cw_shipping_is_need($params) {
    global $addons;

    extract($params);
    if ($addons['shipping_system'] && is_array($products)) return true;
    return false;
}

function cw_shipping_checkout_login_prepare() {
    global $smarty, $userinfo;

    $cart = &cw_session_register('cart', array());
    $products = cw_call('cw_products_in_cart',array($cart, $userinfo));

    $need_shipping = cw_func_call('cw_shipping_is_need', array('products' => $products));
    $shipping = cw_func_call('cw_shipping_get_list', array('cart' => $cart, 'products' => $products, 'userinfo' => $userinfo));
    $smarty->assign('shipping', $shipping);
    $smarty->assign('need_shipping', $need_shipping);
}

function cw_shipping_get_default_id($params) { // $cart, $userinfo, $warehouse = ''
    global $userinfo;
    extract($params);
    $products = cw_call('cw_products_in_cart',array($cart, $userinfo));
    $shipping = cw_func_call('cw_shipping_get_list', array('cart' => $cart, 'products' => $products, 'userinfo' => $userinfo, 'warehouse_customer_id' => $warehouse_customer_id));
    $shipping_matched = false;
    if(!empty($shipping))
    foreach($shipping as $shipping_method)
        if($cart['info']['shipping_id'] == $shipping_method['shipping_id']) return $shipping_method['shipping_id'];
    if(!$shipping_matched && !empty($shipping)) {
        $sh = array_shift($shipping);
        return $sh['shipping_id'];
    }
    return 0;
}

# kornev, the return of this function is cart - so work with it
function cw_shipping_cart_actions($params, $cart) {
    global $config, $smarty;
    extract($params);

    $need_shipping = cw_func_call('cw_shipping_is_need', array('products' => $products));
    $smarty->assign('need_shipping', $need_shipping);

    if ($need_shipping) {
        if ($config['Appearance']['show_cart_summary'] == 'Y') {
            if (empty($cart['orders'])) {
                $products = cw_call('cw_products_in_cart',array($cart, $userinfo));
                $cart = cw_func_call('cw_cart_calc', array('cart' => $cart, 'products' => $products, 'userinfo' => $userinfo));
            }
            if (is_array($cart['orders']))
            foreach($cart['orders'] as $or => $order)  {

            	if (!$cart['info']['shipping_arr'][$order['warehouse_customer_id']]) {
					$shid = cw_func_call('cw_shipping_get_default_id', array('cart' => $order, 'userinfo' => $userinfo, 'warehouse_customer_id' => $order['warehouse_customer_id']));
	                if ($shid) $cart['info']['shipping_arr'][$order['warehouse_customer_id']] = $shid;
	                else $sh_error = true;
            	}
                $cart['info']['shipping_id'] = $cart['info']['shipping_arr'][$order['warehouse_customer_id']];
                $cart['orders'][$or]['delivery'] = cw_get_delivery($shid);
            }
        }
        else {
            $shid = cw_func_call('cw_shipping_get_default_id', array('cart' => $cart, 'userinfo' => $userinfo));
# kornev, we have to recalculate the cart in this case btw
			// remove condition (#34584 Shipping rate does not calculates correctly for new created address shipping)
            //if ($cart['info']['shipping_id'] != $shid) {
                $cart['info']['shipping_id'] = $shid;
                $products = cw_call('cw_products_in_cart',array($cart, $userinfo));
                $cart = cw_func_call('cw_cart_calc', array('cart' => $cart, 'products' => $products, 'userinfo' => $userinfo));
            //}
            //else $sh_error = true;
            $cart['delivery'] = cw_get_delivery($cart['info']['shipping_id']);
            $shipping = cw_func_call('cw_shipping_get_list', array('cart' => $cart, 'products' => $products, 'userinfo' => $userinfo));
            $smarty->assign('shipping', $shipping);
        }
    }
    else {
        $cart['delivery'] = '';
    	$cart['info']['shipping_id'] = 0;
    }

    return $cart;
}

function cw_shipping_cart_get_warehouses_cart($params, $pc) {

    extract($params);
    $need_shipping = cw_func_call('cw_shipping_is_need', array('products' => $products));

    if (is_array($pc))
    foreach($pc as $k=>$_temp) {
        if ($need_shipping)
            $pc[$k]['shipping'] = cw_func_call('cw_shipping_get_list', array('cart' => $_temp, 'products' => $_temp['products'], 'userinfo' => $userinfo, 'warehouse_customer_id' => $_temp['warehouse_customer_id']));
        $pc[$k]['info']['shipping_id'] = $cart['info']['shipping_arr'][$_temp['warehouse_customer_id']];
        $pc[$k]['info']['current_carrier'] = $cart['info']['carrier_arr'][$_temp['warehouse_customer_id']];
    }

    return $pc;
}


# This function calculates delivery cost
# kornev, function params
# $products - from the cart mostly
# $shipping_id
# $userinfo
# $warehouse_id = 0
# $order_hash - hash to differ orders
function cw_shipping_cart_calc($params) {
    extract($params);

    global $tables;

    $shipping_cost = 0;
    $is_free_shipping = TRUE;
    
    if (!empty($products)) {
	    foreach($products as $k=>$product) {
	    	// If all product is free_shipping then $shipping_cost must be 0
	    	if ($product['free_shipping'] != 'Y' && $is_free_shipping) {
	    		$is_free_shipping = FALSE;
	    	}
	        $shipping_cost += $product['shipping_freight'] * $product['amount'];
	    }
    }
    else {
    	$is_free_shipping = FALSE;
    }

# kornev, it's possible that here we haven't calculate any rates till now - so let's make it for one shipping (in any case it's cached)
    $shipping = cw_func_call('cw_shipping_get_list', array('cart' => $cart, 'products' => $products, 'userinfo' => $userinfo, 'warehouse_customer_id' => $warehouse_id,'order_hash'=>$order_hash));

    // If edit invoice
    if (isset($cart['type']) && $cart['type'] == "I") {
	    if (!$is_free_shipping && !isset($cart['info']['shipping_cost'])) {
	    	$shipping_cost += cw_call('cw_shipping_get_original_rate', array('shipping_id' => $shipping_id, 'order_hash' => $order_hash));
	    }
	    else {
	    	$shipping_cost = $cart['info']['shipping_cost'];
	    }
    } else {
        if ($cart['info']['use_shipping_cost_alt'] == 'Y') {
            $shipping_cost = $cart['info']['shipping_cost'];
        } elseif (!$is_free_shipping) {
	    $shipping_cost += cw_call('cw_shipping_get_original_rate', array('shipping_id' => $shipping_id, 'order_hash' => $order_hash));
	}
    }
/*
# kornev, disabled for now, need a way to get the zone and weight here
    $markups = cw_query_first("SELECT * FROM $tables[shipping_rates] WHERE shipping_id='$shipping_id' AND zone_id='$customer_zone' AND minweight<='$total_weight_shipping' AND maxweight>='$total_weight_shipping' AND type='R' ORDER BY maxtotal, maxweight");

    if ($markups && $shipping_cost > 0)
        $shipping_cost += $shipping_rt[0]['rate']+$total_weight_shipping*$shipping_rt[0]['weight_rate']+$total_ship_items*$shipping_rt[0]['item_rate']+$total_shipping*$shipping_rt[0]['rate_p']/100;

    $return['shipping_cost'] = $shipping_cost + $shipping_freight;
*/

    $shipping_fees = cw_query_first("select s.* from $tables[shipping] as s, $tables[shipping_carriers] as c where s.shipping_id='$shipping_id' and s.carrier_id=c.carrier_id");

    if ($shipping_fees['fee_basic_limit'] && $shipping_fees['fee_basic_limit'] <= $cart['info']['subtotal'])
        $surcharge = $shipping_fees['fee_ex_flat'] + ($shipping_fees['fee_ex_percent']*$cart['info']['subtotal'])/100;
    else
        $surcharge = $shipping_fees['fee_basic'];

    // shipping_surcharge can be added by addons and is not taxable
    return array('shipping_cost' => $shipping_cost, 'shipping_insurance' => $surcharge, 'shipping_surcharge'=>0);
}

function cw_shipping_cart_calc_single($params, $return) {
    extract($params);
    global $config;
# kornev, for 0 value on default
    $warehouse_id = intval($warehouse_id);
    if (empty($order_hash)) $order_hash = $warehouse_id;
    if ($config['Appearance']['show_cart_summary'] == 'Y' && $cart['info']['shipping_arr'][$order_hash])
        $shipping_id = $cart['info']['shipping_arr'][$order_hash];
    else
        $shipping_id = $cart['info']['shipping_id'];

    $ret = cw_func_call('cw_shipping_cart_calc', array('products' => $products, 'shipping_id' => $shipping_id, 'userinfo' => $userinfo, 'warehouse_id' => $warehouse_id, 'cart' => $cart,'order_hash'=>$order_hash));
    $return['info'] = array_merge($return['info'], $ret);
    return $return;
}

function cw_shipping_cart_summarize($params, $return) {
# kornev, TOFIX display_shippign_cost is calculated in the taxes
    foreach(array('shipping_cost', 'shipping_insurance') as $field)
        $return['info'][$field] += $params['res']['info'][$field];
//    $return['info']['total'] += $return['info']['shipping_cost'];
    return $return;
}

function cw_shipping_product_get($params , $return){
    global $config;

    if($config['Shipping']['enable_all_shippings_for_all'] == 'Y')
    $return['shippings'] = '';

    return $return;
}

function cw_shipping_get_packages($params) {
    return array(array('weight' => $params['weight']));
}

function cw_shipping_doc_trackable($doc) {
    global $app_skin_dir, $app_dir;
    return $doc['info']['carrier']['addon'] && $doc['info']['tracking'] && file_exists($app_dir.$app_skin_dir.'/addons/'.$doc['info']['carrier']['addon'].'/tracking.tpl');
}
