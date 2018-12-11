<?php
function cw_aom_get_doc_storage($type, $info = array(), $prefix = '', $special_info = array()) {

    $display_id = cw_doc_get_display_id($type);

    $usertype = 'C';
    if (in_array($type, array('P', 'Q', 'R'))) $usertype = 'S';
    elseif($type == 'D') $usertype = 'D';

    $ret = array (
        'doc_id' => 0,
        'display_id' => $prefix.$display_id,
        'prefix' => $prefix,
        'display_doc_id' => $display_id,
        'type' => $type,
        'date' => cw_core_get_time(),
        'status' => 'Q',
        'info' => array(
            'total' => 0
        ),
        'userinfo' => array(
            'customer_id' => 0,
            'usertype' => $usertype,
            'current_address' => array('same_as_main' => 1),
        ),
    );
    if ($info) $ret['info'] = array_merge($ret['info'], $info);

    foreach($special_info as $tbl=>$fields)
        $ret[$tbl] = $fields;

    return $ret;
}

function cw_aom_validate_price($price) {
    return cw_detect_price($price);
}

function cw_aom_update_prices($products, $customer_info) {
    global $config, $real_taxes;

    if (is_array($products))
    foreach ($products as $k=>$v) {
        $products[$k]['price_deducted_tax'] = "Y";
        if ($real_taxes == "Y")
            $_taxes = cw_get_products_taxes($products[$k], $customer_info, false);
        else
            $_taxes = cw_get_products_taxes($products[$k], $customer_info, false, $v['extra_data']['taxes']);

        $products[$k]['extra_data']['taxes'] = $products[$k]['taxes'] = $_taxes;
    }
    return $products;
}

function cw_aom_get_quantity_in_stock($warehouse_id, $product_id, $order_status, $options = array(), $order_product = array(), $type = null) {
    global $tables, $addons;

    $quantity_in_stock = (strpos("PCQI",$order_status) !== false) ? $order_product['amount'] : 0;
        
# kornev, TOFIX
    if ($addons['product_options'] && !empty($options)) {
        $is_equal = false;

        if (!empty($order_product['product_options']) && is_array($order_product['product_options'])) {
            $order_options = array();
            foreach ($order_product['product_options'] as $cid => $o)
                $order_options[$cid] = $o['option_id'];
            $order_variant_id = cw_get_variant_id($order_options);
            $variant_id = cw_get_variant_id($options);

            $is_equal = ($order_variant_id == $variant_id);
        }

        if (!$is_equal)
            $quantity_in_stock = 0;

        $quantity_in_stock += cw_warehouse_get_warehouse_avail($warehouse_id, $product_id, $type, $variant_id);
    } 
    else
        $quantity_in_stock += cw_warehouse_get_warehouse_avail($warehouse_id, $product_id, $type, 0);

    return $quantity_in_stock;
}

function cw_aom_recalculate_totals($cart) {
    global $addons, $config, $global_store;
    global $current_area;

    if ($real_taxes == 'Y') {
        global $current_area, $customer_id;
        $_saved_data = compact("current_area", "customer_id");
        $current_area = $current_area = $current_area == 'G'?'G':'C';
    }

    $saved_state = false;

    $global_store['discounts'] = array();
    if ($cart['info']['use_discount_alt'] == 'Y') {
        $global_store['discounts'][] = array(
           '__override' => true,
            'discount_id' => -1,
            'minprice' => 0,
            'discount' => $cart['info']['discount_alt'],
            'discount_type' => 'absolute'
        );
    }

    if ($cart['info']['use_coupon_discount_alt'] == 'Y') {
        $global_store['discount_coupons'] = array(array(
            "__override" => true,
            "coupon" => "Order#".$cart['doc_id'],
            "discount" => $cart['info']['coupon_discount_alt'],
            "coupon_type" => "absolute",
            "minimum" => 0,
            "times" => 999999999,
            "times_used" => 0,
            "expire" => time()+30879000,
            "status" => "A",
        ));
    }

# kornev, pos addon
    if ($cart['pos']['gd_value']) {
       $global_store['discounts'][] = array(
            '__override' => true,
            'discount_id' => -2,
            'minprice' => 0,
            'discount' => $cart['pos']['gd_value'],
            'discount_type' => $cart['pos']['gd_type']?'percent':'absolute',
        );
    }
    if ($cart['pos']['vd_value']) {
       $global_store['discounts_value'] = array(
            '__override' => true,
            'discount_id' => -3,
            'minprice' => 0,
            'discount' => $cart['pos']['vd_value'],
            'discount_type' => 'absolute',
        );
    }

    $cart['products'] = cw_aom_update_prices($cart['products'], $cart['userinfo']);
    $cart = cw_func_call('cw_cart_calc', array('cart' => $cart, 'products' => $cart['products'], 'userinfo' => $cart['userinfo']));

    $cart['info']['applied_taxes'] = $cart['info']['taxes'];

    if (!empty($_saved_data))
        extract($_saved_data);

    if ($addons['pos']) {
        cw_load('pos');
        if (!$cart['pos']['paid_by_cc']) {
            if ($cart['pos']['payment']) $cart['pos']['change'] = $cart['pos']['payment'] - $cart['info']['total'];
        }
        else 
            $cart['pos']['payment'] = $cart['pos']['change'] = 0;
        $cart['pos']['pos_user_info'] = cw_pos_user_info($cart['pos']['pos_customer_id']);
    }
    return $cart;
}

function cw_aom_update_order($cart, $old_products = array(), $is_invoice = false) {
    global $tables, $config, $addons, $app_main_dir, $dhl_ext_country;

    $cart = cw_call('cw_aom_recalculate_totals', array($cart));
    cw_doc_update($cart['doc_id'], $cart, array(), $is_invoice);
# kornev
# pos orders have to be completed in any case
    if ($cart['type'] == 'G') {
# kornev, because we need item_id
        $cart = cw_doc_get($cart['doc_id']);
        cw_doc_change_status_inner($cart, 'C', '');
    }
}

function cw_aom_add_new_products(&$doc, $products, $variants = array(), $amounts = array(), $discounts = array(), $prices = array()) {
    global $tables, $config, $customer_id, $current_area, $addons;

    $saved_data = compact('customer_id', 'current_area');
    $customer_id = $doc['userinfo']['customer_id'];

# kornev
# pos, warehouse movement, supplier order or usual sale
    if (in_array($doc['type'], array('G', 'D'))) $current_area = 'G';
    elseif(in_array($doc['type'], array('P', 'Q', 'R'))) $current_area = 'S';
    else $current_area = 'C';

    $customer_membership_id = $doc['userinfo']['membership_id'];
    $out_of_stock_products = array();

    if (is_array($products))
    foreach($products as $index=>$newproduct_id) {
        if (empty($newproduct_id)) continue;
        if ($prd = cw_func_call('cw_product_get', array('id' => $newproduct_id, 'user_account' => $doc['userinfo'], 'info_type' => 9))) {
            if ($prices[$index]) $prd['price'] = $prices[$index];
#kornev, salesman can create the doc with any warehouses, because it's only cart (customer will place the real doc later)
            if (AREA_TYPE != 'B') {
                $_cart_warehouse = $doc['info']['warehouse_customer_id'];
                if (!$_cart_warehouse) {
                    $_cart_warehouse = cw_warehouse_get_max_amount_warehouse($newproduct_id, $variants[$index]);
                    $doc['info']['warehouse_customer_id'] = $_cart_warehouse;
                }
                $_avail = cw_warehouse_get_warehouse_avail($_cart_warehouse, $newproduct_id, null, $variants[$index]);

                if (!$_avail && !$config['unlimited_products']) {
                    $out_of_stock_products[] = array(1, $prd['product']);
                    continue;
                }
                $prd['avail'] = $_avail;
                $prd['warehouse_customer_id'] = $_cart_warehouse;
            }

# kornev, TOFIX
            if ($addons['product_options']) {
                $prd['extra_data']['product_options'] = cw_get_default_options($newproduct_id, 1, $customer_membership_id, $variants[$index]);
                list($variant, $product_options_result) = cw_get_product_options_data($newproduct_id, $prd['extra_data']['product_options'], $customer_membership_id);
                $surcharge = 0;
                $prd['product_options'] = $product_options_result;
                if($product_options_result) {
                    foreach($product_options_result as $key=>$o)
                        $surcharge += ($o['modifier_type'] == '%'?($prd['price']*$o['price_modifier']/100):$o['price_modifier']);
                }
                $prd['price'] = price_format($prd['price'] + $surcharge);
            }
            if ($discounts[$index]) {
                list($discount, $is_persent) = cw_core_parse_discount($discounts[$index]);
                if ($discount) {
                    if ($is_persent == '%') $prd['price'] = $prd['price']*(100-$discount)/100;
                    else $prd['price'] = $prd['price'] - $discount;
                    if ($prd['price'] < 0) $prd['price'] = 0;
                }
            }
            $prd['amount'] = intval($amounts[$index])?intval($amounts[$index]):1;
            $prd['new'] = true;

            if (empty($doc['max_cartid']))
                $doc['max_cartid'] = 0;

            $doc['max_cartid']++;

            $prd['cartid'] = $doc['max_cartid'];

            if (in_array($doc['type'], array('P', 'Q', 'R'))) {
                $prd['is_auto_calc'] = $config['order']['is_auto_calc'] == 'Y';
# kornev, if we are modify the supplier order, we should get the supplier price instead of the usual one (get latest)
/*
                cw_load('supplier');
                $supplier_net_price = cw_supplier_get_price($doc['userinfo']['customer_id'], $prd['product_id']);
/*
                if (!$supplier_net_price)
                    $out_of_stock_products[] = array(3, $newproduct_id);
                else {
*/
                    $prd['net_price'] = $supplier_net_price;
//                    $prd['is_net_price'] = $supplier_net_price;
                    $prd['discount_formula'] = cw_query_first_cell("select discount from $tables[products_supplied_amount] where product_id='$prd[product_id]' and supplier_customer_id='".$doc['userinfo']['customer_id']."' order by date asc");
                    if (!$prd['discount_formula'])
                       $prd['discount_formula'] = cw_user_get_discount_formula($doc['userinfo']['customer_id']);
                    $prd['price'] = cw_user_apply_discount_by_formula($prd['discount_formula'], $supplier_net_price);
                    $spl = cw_supplier_get_product_info($doc['userinfo']['customer_id'], $prd['product_id']);
                    $prd['productcode'] = $spl['productcode'];
                    $doc['products'][] = $prd;
//                }
            }
            else 
                $doc['products'][] = $prd;
            unset($prd);
        }
        else
            $out_of_stock_products[] = array(2, $newproduct_id);
    }

    cw_load('cart_process');
    $doc['products'] = cw_call('cw_products_in_cart',array($doc, $doc['userinfo'], true));
    cw_cart_normalize($doc);
    extract($saved_data);
    return $out_of_stock_products;
}

function cw_aom_normalize_after_update($doc, $original = array()) {
    global $addons, $app_main_dir;

    $doc = cw_call('cw_aom_recalculate_totals', array($doc));
    cw_cart_normalize($doc);

    if (is_array($doc['products']))  {
        foreach($doc['products'] as $pk=>$product) {
            $product_id = $product['product_id'];

            $options = array();
            if (is_array($product['product_options']))
                foreach ($product['product_options'] as $k => $v)
                    $options[$k] = $v['option_id'];

            $doc['products'][$pk]['items_in_stock'] = cw_aom_get_quantity_in_stock($product['warehouse_customer_id'], $product_id, $original['order']['status'], $options, $original['products'][$pk]);
            $doc['products'][$pk]['items_in_backorder'] = cw_aom_get_quantity_in_stock($product['warehouse_customer_id'], $product_id, $original['order']['status'], $options, $original['products'][$pk], 'avail_sold');

# kornev, TOFIX
            if ($addons['product_options']) {
                $options = $product['extra_data']['product_options'];
                $product_options = cw_call('cw_get_product_classes', array('product_id' => $product['product_id']));
                $product_options = cw_call('cw_product_options_set_selected', array($product_options, $options));

                $doc['products'][$pk]['display_options'] = $product_options;
            }

        }

    }

    return $doc;
}

function cw_aom_update_warehouse(&$doc, $warehouse_customer_id) {
    $doc['info']['warehouse_customer_id'] = $warehouse_customer_id;
    $products = $doc['products'];
    if (is_array($products))
    foreach($products as $k=>$v)
        $products[$k]['warehouse_customer_id'] = $warehouse_customer_id;
    $doc['products'] = $products;
}

function cw_aom_update_customer(&$doc, $customer_id) {
    $doc['userinfo']['customer_id'] = $customer_id;
# kornev, warehouses are different from users
    if ($doc['type'] == 'D')
        $user_info = cw_warehouse_get_like_user($customer_id, $doc['info']['warehouse_customer_id']);
    else 
        $user_info = cw_user_get_info($customer_id, 65);

    $doc['userinfo'] = cw_doc_prepare_user_information($user_info, $doc['userinfo']);
    if (!$doc['info']['company_id'])
        $doc['info']['company_id'] = $doc['userinfo']['company_id'];
    if (!$doc['info']['shipment_paid'])
        $doc['info']['shipment_paid'] = $user_info['additional_info']['shipment_paid'];
}
?>
