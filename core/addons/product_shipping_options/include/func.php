<?php

function cw_product_shipping_get_options($product_id) {
    global $tables;

    $shipping_values = cw_query("select * from $tables[product_shipping_options_values] where product_id='$product_id' order by price asc");

    global $current_area;
    if ($current_area == 'C') {
        if (!empty($shipping_values)) {
            foreach ($shipping_values as $shv_k => $shv_v) {
                $shipping_values[$shv_k]['shipping'] = cw_call('cw_shipping_get', array($shv_v['shipping_id'])); 
            }
        }
    } else {
        $carriers = cw_shipping_get_carriers(true);
        if (!empty($carriers)) {
            foreach ($carriers as $k=>$v) {
//            $carriers[$k]['total_methods'] = cw_query_first_cell("select count(*) from $tables[shipping] where carrier_id='$v[carrier_id]'");
//            $carriers[$k]['total_enabled'] = cw_query_first_cell("select count(*) from $tables[shipping] where carrier_id='$v[carrier_id]' and active=1");
                $carriers[$k]['shipping'] = cw_func_call('cw_shipping_search', array('data' => array('carrier_id' => $v['carrier_id'])));
            
            }
        }
    }
    return array('shipping_options'=>$carriers, 'shipping_values'=>$shipping_values);
}

function cw_product_shipping_option_default(&$product) {

    if (empty($product['product_shipping_option'])) {
        $shipping_options_data = cw_call('cw_product_shipping_get_options', array($product['product_id']));
        if (!empty($shipping_options_data['shipping_values'])) {
            $product['product_shipping_option'] = $shipping_options_data['shipping_values'][0]['shipping_id'];
        }    
    }
    return $product;
}

function cw_product_shipping_option_update_cart(&$cart, $productindexes) {
    global $product_shipping_options;
    global $user_account, $tables;

    $shipping_cache_clean = false;
      
    if (!empty($product_shipping_options)) {
        foreach ($product_shipping_options as $cartindex => $shipping_id) {
            foreach ($cart['products'] as $cp_id => $cp_v) {
                if ($cp_v['cartid'] != $cartindex) continue;

                $shipping_option_data = 
                    cw_query_first("select * from $tables[product_shipping_options_values] where product_id='$cp_v[product_id]' and shipping_id='$shipping_id'");

                if (empty($shipping_option_data)) continue; 

                if ($cart['products'][$cp_id]['product_shipping_option'] != $shipping_id) 
                    $shipping_cache_clean = true;
  
                $cart['products'][$cp_id]['product_shipping_option'] = $shipping_id;
            }
        }
    }

    if ($shipping_cache_clean)
        cw_cache_clean('shipping_rates');

}


function cw_product_shipping_option_shipping_get_rates($params, $return) {

//    cw_log_add('shipping_get_rates', array($params, $return));
    global $config, $tables;
    if (empty($config['product_shipping_options']['dummy_shipping_method'])) return $return;

    $shipping = cw_func_call('cw_shipping_search', array('data' => array('active' => 1, 'where' => array("shipping_id = '".$config['product_shipping_options']['dummy_shipping_method']."'"))));

    $pso_shipping = reset($shipping);

    if (!empty($pso_shipping)) {
        $total_optional_rate = 0;
        $products = $params['cart']['products'];
        foreach ($products as $product) {
            $product_shipping_cost = cw_query_first_cell("select price from $tables[product_shipping_options_values] where shipping_id='$product[product_shipping_option]' and product_id='$product[product_id]'");
            if (!empty($product_shipping_cost))   
                $total_optional_rate += floatval($product_shipping_cost);
        }

        $shrate = array(
                  'rate_id' => 999999,
                  'shipping_id' => $pso_shipping['shipping_id'],//$config['product_shipping_options']['dummy_shipping_method'],
                  'zone_id' => 0,
                  'maxamount' => 0, 
                  'minweight' => 0,
                  'maxweight' => 999999,
                  'mintotal' => 0,
                  'maxtotal' => 999999,
                  'rate' => $total_optional_rate,
                  'original_rate' => $total_optional_rate,
                  'item_rate' => 0.00,
                  'weight_rate' => 0.00,
                  'rate_p' => 0.00,
                  'warehouse_customer_id' => 0,
                  'type' => 'D',
                  'overweight' => 0.00,
                  'overweight_rate' => 0.00,
                  'apply_to' => 'ST'
                  );

         $return = array($pso_shipping['shipping_id'] => array_merge($shrate, $pso_shipping)); 
    }

    return $return;
}

function cw_product_shipping_option_extra_data($product) {
    global $tables;

    $return = cw_get_return(); 

    $return['product_shipping'] = cw_query_first("select shipping_id, price from $tables[product_shipping_options_values] where shipping_id='$product[product_shipping_option]' and product_id='$product[product_id]'"); 

    return $return;
}

function cw_product_shipping_option_get_product_layout_elements() {

    $return = cw_get_return();

    if (!empty($return) && is_array($return)) {
/*
        $_return = array(); 
        foreach ($return as $key => $value) {
            $_return[$key] = $value;           

            if ($key == 'amount') 
                $_return['product_shipping_option'] = 'lbl_product_shipping_option_invoice';

        }
        $return = $_return;  
*/
        $return['product_shipping_option'] = 'lbl_product_shipping_option_invoice'; 

        return new EventReturn($return);
    }

    return $return;
}

function cw_product_shipping_option_doc_get($doc_id, $info_type) {
    global $current_location, $config;

    $return = cw_get_return();

    if (
        !empty($return)
        && $return['type'] == 'O'
        && (!empty($return['products']) || !empty($return['giftcerts']))
        && !empty($return['userinfo']['customer_id'])
    ) {
        if (!empty($return['giftcerts']) && is_array($return['giftcerts'])) {

            foreach ($return['giftcerts'] as $key => $giftcert) {
                $return['giftcerts'][$key]['product_shipping_option'] = '';
            }
        }

        if (!empty($return['products']) && is_array($return['products'])) {
            foreach ($return['products'] as $key => $product) {
                $product_id = $product['product_id'];
                $customer_id = $return['userinfo']['customer_id'];
                $shipping = cw_func_call('cw_shipping_search', array('data' => array('where' => array("shipping_id = '".$product['extra_data']['product_shipping']['shipping_id']."'"))));
                $shipping_method = reset($shipping); 
                $shipping_text = $shipping_method['shipping'].": ".$config['General']['currency_symbol'].$product['extra_data']['product_shipping']['price'];
                $return['products'][$key]['product_shipping_option'] = $shipping_text;
            }
        }

        return new EventReturn($return);
    }

    return $return;
}

function cw_product_shipping_option_get_layout($layout) {
    $return = cw_get_return();

    if (
        !empty($return)
        && $return['layout'] == 'docs_O'
        && !empty($return['data'])
        && !empty($return['data']['elements'])
    ) {
        $elems = array(); 
        foreach ($return['data']['elements'] as $key => $val) {
            $elems[] = $val; 
            if ($val == 'amount') 
                $elems[] = 'product_shipping_option';
        }
        $return['data']['elements'] = $elems;
        return new EventReturn($return);
    }

    return $return;
}

