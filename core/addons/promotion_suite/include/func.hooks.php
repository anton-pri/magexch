<?php

// on_collect_discounts event handler. 
// Function adds offer discounts applied to whole cart. 
function cw_ps_on_collect_discounts(&$discounts,$avail_discount_total) {

	$special_offers_apply =& cw_session_register("special_offers_apply");

	if (!empty($special_offers_apply['supply'])) {

		foreach ($special_offers_apply['supply'] as $sup) {
			
			if (!isset($sup['D']['apply'])) continue; // No discount in this offer
			
			$bonus = $sup['D'];

			if ($bonus['apply']!=PS_APPLY_CART)	continue; // We do not take into account product specific discounts here
			
			$max_discount = ($bonus['disctype']==2)?$avail_discount_total*$bonus['discount']/100:$bonus['discount'];

			$discount_info = array (
				'discount_type' => $bonus['disctype']==1?'absolute':'percent',
				'discount'      => $bonus['discount'],
				'max_discount'  => $max_discount,
				'promotion_suite' => true
			);
			$discounts[] = $discount_info;

			if ($max_discount>$special_offers_apply['discount']['max_discount'])
				$special_offers_apply['discount'] = $discount_info;
		

		}

	}
	
	return true;	
	
}

// Function adds free products according to existing offers
function cw_ps_products_in_cart_pre($cart, $user_info) {

	$special_offers_apply =& cw_session_register("special_offers_apply");
	
	# Delete all free added products
	if (is_array($cart['products'])) {
    foreach ($cart['products'] as $kk=>$vv) {
		if ($vv["promotion_suite"]["free_product"] == 'Y') cw_call('cw_delete_from_cart', array(&$cart,$vv['cartid']));
	}
    }
	# / Delete all free added products

	if (!empty($special_offers_apply['free_products'])) {
		foreach($special_offers_apply['free_products'] as $pid=>$qty) {
			#
			# Add product to the cart
			#
			$product = cw_func_call('cw_product_get',array('id'=>$pid,'info_type'=>8192));
			$avail_amount = $product['avail'];
			$qty = min($qty,$avail_amount);
			$min_amount = $product['min_amount'];
			if ($qty>=$min_amount) {
				cw_load('warehouse');
				$possible_warehouses = cw_warehouse_get_avails_customer($pid);
				$warehouse = key($possible_warehouses);
				$add_product = array();
				$add_product["product_id"] = $pid;
				$add_product["amount"] = $qty;
				$add_product["product_options"] = "";
				$add_product["price"] = 0.00;
			  // warehouse is required, otherwise free product will be placed as separate order
		        $add_product['warehouse_customer_id'] = $warehouse;

			  // do to use cw_warehouse_add_to_cart_simple. It operates with global $cart while we use here local $cart copy
		      //  $result = cw_call('cw_warehouse_add_to_cart_simple', array($pid, $qty, '', 0.00));
		      
				$result = cw_call('cw_add_to_cart',array(&$cart, $add_product));

				# Adjust just added product
				foreach ($cart['products'] as $ck=>$cv)
					if ($cv["cartid"]==$result['cartid']) {
						$cart['products'][$ck]["promotion_suite"]["free_product"] = true; // mark just added product as offered
					}

			}
			else {
				unset($special_offers_apply['free_products'][$pid]);
			}
		} // foreach
	} // if

	return new EventReturn($cart['products'], array($cart, $user_info)); // replace cart in input params for main function
}

// Function makes offer's products really "free"
/* see handler cw_apply_special_offer_free for event cw_product_from_scratch
function cw_ps_products_in_cart_post($cart, $user_info, $leave_info = false) {
    global $config, $smarty, $tables, $addons;

    $return = cw_get_return(); // get result of previous function

    static $price_pattern = array(
		'price' => 0.00,
		'taxed_price' => 0.00,
		'total'	=> 0.00,
		'display_price' => 0.00,
		'display_net_price' => 0.00,
		);

    // - Go thru cart products
    // - If product is marked as free - make it really "free"
    
	foreach ($return as $kk=>$vv) {
		if (!empty($vv["promotion_suite"]["free_product"])) {
			$return[$kk] = array_merge($vv,$price_pattern);
		}
	}

	return $return;
    
}
*/

function cw_is_product_free($product) {
	if (is_array($product['product'])) $product = $product['product']; // function is called from smarty
	return $product['promotion_suite']['free_product'];
}

function cw_ps_delete_product($product_id, $update_categories=true, $delete_all=false) {
    global $tables, $config;

    $return = cw_get_return();

    if ($delete_all == true) {
        db_query("DELETE FROM $tables[ps_bonus_details] WHERE object_type = '" . PS_OBJ_TYPE_PRODS . "'");
        db_query("DELETE FROM $tables[ps_cond_details] WHERE object_type = '" . PS_OBJ_TYPE_PRODS . "'");
    } else {
        $product_id = (int) $product_id;
        if (!empty($product_id)) {
            $product_id_condition = "object_type = '" . PS_OBJ_TYPE_PRODS . "' AND object_id = '" . $product_id . "'";
            db_query('DELETE FROM ' . $tables['ps_bonus_details'] . ' WHERE ' . $product_id_condition);
            db_query('DELETE FROM ' . $tables['ps_cond_details'] . ' WHERE ' . $product_id_condition);
        }
    }

    return $return;
}



function cw_ps_category_delete($cat, $is_show_process = false) {
    global $tables, $config;

    $return = cw_get_return();
    
    $subcats = cw_category_get_subcategory_ids($cat);
    $subcats[] = $cat;
    
    if (!empty($subcats) && is_array($subcats)) {
        db_exec("DELETE FROM $tables[ps_bonus_details] WHERE object_type = '" . PS_OBJ_TYPE_CATS . "' AND object_id IN (?)", array($subcats));
        db_exec("DELETE FROM $tables[ps_cond_details] WHERE object_type = '" . PS_OBJ_TYPE_CATS . "' AND object_id IN (?)", array($subcats));
    }

    return $return;
}




function cw_ps_manufacturer_delete($manufacturer_id) {
    global $tables, $config;
    $return = cw_get_return(); 

    if (!empty($manufacturer_id)) {
        db_query("DELETE FROM $tables[ps_bonus_details] WHERE object_type = '" . PS_OBJ_TYPE_MANS . "' AND object_id = '$manufacturer_id'");
        db_query("DELETE FROM $tables[ps_cond_details] WHERE object_type = '" . PS_OBJ_TYPE_MANS . "' AND object_id = '$manufacturer_id'");
    }

    return $return;
}




function cw_ps_shipping_delete_zone($zone_id) {
    global $tables, $config;
    $return = cw_get_return();
 
    if (!empty($zone_id)) {
        db_query("DELETE FROM $tables[ps_bonus_details] WHERE object_type = '" . PS_OBJ_TYPE_ZONES . "' AND object_id = '$zone_id'");
        db_query("DELETE FROM $tables[ps_cond_details] WHERE object_type = '" . PS_OBJ_TYPE_ZONES . "' AND object_id = '$zone_id'");
    }

    return $return;
}



function cw_ps_warehouse_delete_division($division_id) {
    global $tables, $config;

    $return = cw_get_return();
 
    if (!empty($division_id)) {
        $zones = cw_query_column("SELECT zone_id FROM $tables[zones] WHERE warehouse_customer_id = '$division_id'");
        if (!empty($zones) && is_array($zones)) {
            db_exec("DELETE FROM $tables[ps_bonus_details] WHERE object_type = '" . PS_OBJ_TYPE_ZONES . "' AND object_id IN (?)", array($zones));
            db_exec("DELETE FROM $tables[ps_cond_details] WHERE object_type = '" . PS_OBJ_TYPE_ZONES . "' AND object_id IN (?)", array($zones));
        }
    }

    return $return;
}




function cw_ps_salesman_delete_discount($salesman_customer_id, $coupon) {
    global $tables, $config;

    $return = cw_get_return();
 
    if (!empty($coupon)) {
        db_query("UPDATE $tables[bonuses] SET coupon = '' WHERE coupon = '$coupon'");
    }

    return $return;
}




function cw_ps_cart_actions($params, $cart) {
    global $config, $smarty, $tables, $addons;
    
    // here we will update the cart[products] array
    
    if (APP_AREA != 'customer') {
        return $cart;
    }
    
    extract($params);
    //action, products, userinfo
    
    $tracking_actions = array(
        'add' => 1,
        'update' => 1,
        'delete' => 1,
        'ajax_update' => 1
    );
    
    if (empty($action) || !isset($tracking_actions[$action])) {
        return $cart;
    }
    
    
    //return $cart;
    //die;
    
    $_products = $cart['products'];
    
    
    $offers = cw_ps_offers_exist($cart, $_products);
    
    //echo '<pre>action, products: '; print_r($cart['products']); echo '</pre>';
    //echo '<pre>products: '; print_r($products); echo '</pre>';
    //echo '<pre>offers: '; print_r($offers); echo '</pre>';
    //die;
    
    
    //echo '<pre>', "input data: ", __FUNCTION__, "- ", md5(cw_ps_prods_str($_products)), '</pre>';
    
    //$result = cw_ps_update_cart_products($cart, $_products, $userinfo);
    $result = cw_ps_update_cart_products($cart, $_products, $userinfo, $offers);
    
    //echo '<pre>_products '; print_r($_products); echo '</pre>';
    //echo '<pre>', "result: ", __FUNCTION__, "- $result", var_dump($result), '</pre>';
    //die;
    
    
    cw_ps_offers_set_hash($cart, $_products, $userinfo);
    
    if ($result === false) {
        return $cart;
    } else {
        
        //if ($result > 0 && $result != 2 && $action == 'add') {
        
        if ($result == 3) {
            //cw_ps_update_prods_prices($products);
            $cart['products'] = $products;
        }
        
        if ($result === true) {
            echo '<pre>', "\t\t\tbad! the duplicate processing was detected: ", '</pre>';
            cw_ps_update_prods_prices($_products);
            $cart['products'] = $_products;
        }
        
    }
    
    unset($_products);
    
    //$products = cw_products_from_scratch($products, $userinfo, false, false);
    
    /*$offers = cw_ps_get_customer_offers($cart, $products);
    
    if (empty($offers) || !is_array($offers)) {
        return $cart;
    }*/
    
    //$cart = cw_ps_apply_offers($cart, $products, $offers);
       
    
    //die(var_dump(__FUNCTION__, $action, $products));
    
    //echo '<pre>', __FUNCTION__, md5(serialize($cart['products'])), '</pre>';
    
    return $cart;
}




function cw_ps_cart_calc($params, $return) {
    global $config, $smarty;
    
    if (APP_AREA != 'customer') {
        return $return;
    }
    
    extract($params);
    //$cart, $products, $userinfo
    
    //die(var_dump(__FUNCTION__, $return));
    
    return $return;
}




function cw_ps_cart_calc_discounts($params, $return) {
    global $tables, $config, $addons, $global_store;
    
    if (APP_AREA != 'customer') {
        return $return;
    }
    
    extract($params);
    //membership_id, products, cart, warehouse_id
    
    
    //let's get suitable offers for the current customer
    
    //$offers = cw_ps_get_customer_offers($cart, $products);
    return $return;
    
    //die(var_dump(__FUNCTION__, $offers));
    
    /*
    
    if ((($discount_coupon_data['coupon_type'] == 'absolute') || ($discount_coupon_data['coupon_type'] == 'percent'))) {
        
        $coupon_discount = 0;
        
        if ($discount_coupon_data['product_id'] > 0) {
            
            foreach($products as $k=>$product) {
                if ($addons['Special_Offers'] && !empty($product['free_amount']))
                    continue;

                if ($product['product_id'] != $discount_coupon_data['product_id'])
                    continue;

                $price = $product['discounted_price'];

                if ($discount_coupon_data['coupon_type'] == 'absolute' && $discount_coupon_data['discount'] > $price) {
                    $discount_coupon_data['discount'] = 100;
                    $discount_coupon_data['coupon_type'] = 'percent';
                }

                if ($discount_coupon_data['coupon_type'] == 'absolute' && $discount_coupon_data['apply_product_once'] == 0)
                    $multiplier = $product['amount'];
                else
                    $multiplier = 1;

                $_coupon_discount = $_taxed_coupon_discount = $discount_coupon_data['discount'] * $multiplier;

                if ($config['Taxes']['apply_discount_on_taxed_amount'] == "Y" && !empty($product['taxes']) && is_array($product['taxes'])) {
                    $_taxes = cw_tax_price($_coupon_discount, 0, false, NULL, '', $product['taxes'], ($discount_coupon_data['coupon_type'] == 'percent'));
                    $_taxed_coupon_discount = $_taxes['taxed_price'];
                    $_coupon_discount = $_taxes['net_price'];
                }

                if ($discount_coupon_data['coupon_type'] == 'absolute') {
                    $taxed_coupon_discount = $_taxed_coupon_discount;
                    $taxed_coupon_discount = $coupon_discount = $_coupon_discount;
                }
                else {
                    $taxed_coupon_discount = $price * $_taxed_coupon_discount / 100;
                    $coupon_discount = $price * $_coupon_discount / 100;
                }

                $products[$k]['coupon_discount'] = $taxed_coupon_discount;
                $products[$k]['discounted_price'] = max($price - $coupon_discount, 0.00);

                $return['coupon_discount'] += $taxed_coupon_discount;
            }
        }
        
        elseif ($discount_coupon_data['category_id'] > 0) {
            $category_ids[] = $discount_coupon_data['category_id'];

            if ($discount_coupon_data['recursive'])
                $category_ids = cw_category_get_subcategory_ids($discount_coupon_data['category_id']);

            if ($discount_coupon_data['coupon_type'] == 'absolute') {
                foreach ($products as $k=>$product) {

                    if ($addons['Special_Offers'] && !empty($product['free_amount']))
                        continue;

                    $product_categories = cw_query("SELECT category_id FROM $tables[products_categories] WHERE product_id='$product[product_id]'");
                    $is_valid_product = false;
                    foreach ($product_categories as $pc) {
                        if (in_array($pc['category_id'], $category_ids)) {
                            $is_valid_product = true;
                            break;
                        }
                    }

                    if ($is_valid_product) {
                        if ($discount_coupon_data['coupon_type']=="absolute" && ~$discount_coupon_data['apply_product_once'])
                            $multiplier = $product['amount'];
                        else
                            $multiplier = 1;

                        $sum_discount += $discount_coupon_data['discount'] * $multiplier;
                    }
            
                }

                if ($sum_discount > $return['total']) {
                    $discount_coupon_data['discount'] = 100;
                    $discount_coupon_data['coupon_type'] = 'percent';
                }
            }

            foreach ($products as $k=>$product) {

                if ($addons['Special_Offers'] && !empty($product['free_amount']))
                    continue;

                $product_categories = cw_query("SELECT category_id FROM $tables[products_categories] WHERE product_id='$product[product_id]'");
                $is_valid_product = false;
                foreach ($product_categories as $pc) {
                    if (in_array($pc['category_id'], $category_ids)) {
                        $is_valid_product = true;
                        break;
                    }
                }

                if ($is_valid_product) {

                    if ($discount_coupon_data['coupon_type']=="absolute" && $discount_coupon_data['apply_product_once'] == "N")
                        $multiplier = $product['amount'];
                    else
                        $multiplier = 1;

                    $_coupon_discount = $_taxed_coupon_discount = $discount_coupon_data['discount'] * $multiplier;

                    if ($config['Taxes']['apply_discount_on_taxed_amount'] == "Y" && !empty($product['taxes']) && is_array($product['taxes'])) {

                        $_taxes = cw_tax_price($_coupon_discount, 0, false, NULL, "", $product['taxes'], ($discount_coupon_data['coupon_type'] == "percent"));
                        $_taxed_coupon_discount = $_taxes['taxed_price'];
                        $_coupon_discount = $_taxes['net_price'];

                    }

                    $price = $product['discounted_price'];

                    if ($discount_coupon_data['coupon_type']=="absolute") {
                        $taxed_coupon_discount = $_taxed_coupon_discount;
                        $coupon_discount = $_coupon_discount;
                    }
                    else {
                        $taxed_coupon_discount = $price * $_taxed_coupon_discount / 100;
                        $coupon_discount = $price * $_coupon_discount / 100;
                    }

                    $taxed_coupon_discount = $taxed_coupon_discount;

                    $products[$k]['coupon_discount'] = $taxed_coupon_discount;
                    $products[$k]['discounted_price'] = max($price - $coupon_discount, 0.00);

                    $return['coupon_discount'] += $taxed_coupon_discount;

                    if ($discount_coupon_data['coupon_type'] == "absolute" && $discount_coupon_data['apply_category_once'] == "Y")
                        break;
                }
            }
        }
        
        else {
            if ($discount_coupon_data['coupon_type'] == 'absolute' && $discount_coupon_data['discount'] > $return['total']) {
                $discount_coupon_data['discount'] = 100;
                $discount_coupon_data['coupon_type'] = 'percent';
            }

            if ($discount_coupon_data['coupon_type'] == 'absolute')
                $return['coupon_discount'] = $discount_coupon_data['discount'];
            elseif ($discount_coupon_data['coupon_type'] == 'percent')
                $return['coupon_discount'] = $return['total'] * $discount_coupon_data['discount'] / 100;
            $updated = cw_distribute_discount("coupon_discount", $products, $discount_coupon_data['discount'], $discount_coupon_data['coupon_type'], $return['total'], $_taxes);

            extract($updated);
            unset($updated);

            $return['coupon_discount'] = $coupon_discount;

        }
    }*/
    
    
    return $return;
}




function cw_ps_products_in_cart($cart, $user_info, $leave_info = false) {
    global $config, $smarty, $tables, $addons;
   
    $return = cw_get_return();

    if (APP_AREA != 'customer') {
        return $return;
    }
    
    if (empty($return) || !is_array($return) || count($return) < 1) {
        return $return;
    }
    
    //warehouse_customer_id - order
    //max_cartid - full cart
    
    //let's check if the current cart array is an array related to one of warehouses
    
    //echo '<pre>', "input data: ", __FUNCTION__, "- ", md5(cw_ps_prods_str($return)), '</pre>';
    
    if (!isset($cart['max_cartid'])) {
        //return $return;
        $result = -2; //warehouse is here...
    }
        
    
    /*echo '<pre>'; print_r($cart['products']); echo '</pre>';
    echo '<pre>'; print_r($return); echo '</pre>';
    die;*/
    
    //echo '<pre>cart: '; print_r($cart); echo '</pre>';
    //echo '<pre>init products: '; print_r($return); echo '</pre>';
    //die;
    
    if (isset($cart['max_cartid'])) { //we should update only the original cart
        $_products = $return;
        
        $offers = cw_ps_offers_exist($cart, $_products);
        
        //echo '<pre>cart products: '; print_r($cart['products']); echo '</pre>';
        //echo '<pre>products: '; print_r($_products); echo '</pre>';
        //echo '<pre>offers: '; print_r($offers); echo '</pre>';
        
        //$result = cw_ps_update_cart_products($cart, $_products, $user_info);
        $result = cw_ps_update_cart_products($cart, $_products, $user_info, $offers);
        
        //echo '<pre>returned products: '; print_r($_products); echo '</pre>';
    
        if ($result === true) {
            $return = $_products;
        }
    
        unset($_products);
    }
    
    if ($result !== false && !empty($return)) {
        cw_ps_update_prods_prices($return);
    }
    
    
    //echo '<pre>', "result: ", __FUNCTION__, "- $result", var_dump($result), '</pre>';
    
    //die;
    
    //unset($_products);

    /*if ($result === true) {
        return $_products;
    } else {
        unset($_products);
        return $return;
    }*/
    
    //echo '<pre>at the end: '; print_r($return); echo '</pre>';
    //die;
    
    //die(var_dump(__FUNCTION__, $return));
    
    
    //die;
    
    return $return;
}



function cw_ps_shipping_cart_calc($params, $return = null) {

    // $params
    // products, shipping_id, userinfo, order_hash, cart

    global $saved_rates;

    $shipping_id = $params['shipping_id'];
    $order_hash = $params['order_hash'];

    // save shipping rate without offer in cart 
    // it will be stored in order for profit reports
    $return['shipping_no_offer'] = $saved_rates[$order_hash][$shipping_id]['saved_rate'];

    return $return;
}

function cw_ps_shipping_cart_summarize ($params, $return = null) {
    $return['info']['shipping_no_offer'] += $params['res']['info']['shipping_no_offer'];
    return $return;
}


function cw_ps_cart_calc_single($params, $return = null) {
    
    extract($params);
    //cart, products, userinfo, warehouse_id
    
    /*
     * if the offer bonus is free shipping
     */
    
    
    //$offers = cw_ps_get_customer_offers($cart, $products);
    //echo '<pre>', print_r($offers), '</pre>';
    
    $empty_value = price_format(0);
    $shipping_info = array('shipping_cost' => $empty_value, 'shipping_insurance' => $empty_value);
    
    
    $ps_offers_info = &cw_session_register('ps_offers_info');
    //echo '<pre>', print_r($ps_offers_info['offers_ids']), '</pre>';
    
    $offers = cw_ps_offers_exist($cart, $products);
    //echo '<pre>', print_r($offers), '</pre>';
    
    $result = cw_ps_update_shipping($cart, $products, $userinfo, $return, $ps_offers_info['offers_ids']);
    
    //echo '<pre>', "result: $result", '</pre>';
    //die;
    
    
    
    //$ps_offers_info = &cw_session_register('ps_offers_info');
    
    //echo '<pre>', print_r($ps_offers_info), '</pre>';
    //die;
    
    
    $return['info'] = array_merge($return['info'], $shipping_info);
    
    return $return;
}


function cw_ps_on_place_order_extra($params) {
	global $cart, $app_config_file;
    
	$special_offers_apply =& cw_session_register("special_offers_apply");
	
	$extra = cw_get_return();
	
	if (!empty($special_offers_apply)) $extra['promotion_suite'] = $special_offers_apply;
	if (!empty($cart['info']['shipping_no_offer'])) {
        $extra['shipping_no_offer'] = $cart['info']['shipping_no_offer'];
    }
    
    // Set/unset cookie
    foreach ($special_offers_apply['supply'] as $offer) {
        if ($offer['K']['postaction']) {
            if ($offer['K']['postaction'] == 'U') {
                cw_set_cookie($offer['K']['cookie'], '', constant('CURRENT_TIME')-constant('SECONDS_PER_DAY'), with_leading_slash_only($app_config_file['web']['web_dir'], true), $app_config_file['web']['http_host'], 0);
                cw_set_cookie($offer['K']['cookie'], '', constant('CURRENT_TIME')-constant('SECONDS_PER_DAY'), with_leading_slash_only($app_config_file['web']['web_dir'], true), $app_config_file['web']['https_host'], 0);
            }
            if ($offer['K']['postaction'] == 'S') {
                cw_set_cookie($offer['K']['cookie'], $offer['K']['postvalue'], 0, with_leading_slash_only($app_config_file['web']['web_dir'], true), $app_config_file['web']['http_host'], 0);
                cw_set_cookie($offer['K']['cookie'], $offer['K']['postvalue'], 0, with_leading_slash_only($app_config_file['web']['web_dir'], true), $app_config_file['web']['https_host'], 0);
            }
            $special_offers_apply['cookie'] = $offer['K'];
        }
    }
    
	return $extra;
}

function cw_ps_tabs_js_abstract($params, $return) {

    if ($return['name'] == 'product_data') {
        if (AREA_TYPE != 'A') return $return;

        if (!isset($return['js_tabs']['bundle']))
            $return['js_tabs']['bundle'] = array(
                'title' => cw_get_langvar_by_name('lbl_discount'),
                'template' => 'addons/promotion_suite/admin/product_modify.tpl',
            );
    }
	if ($return['name'] == 'product_data_customer') {
		global $product_bundle;
		if (!isset($return['js_tabs']['bundle']) && !empty($product_bundle['products'])) {
			$return['js_tabs']['bundle'] = array(
				'title' => "Buy together",
				'template' => 'addons/promotion_suite/customer/product_bundle.tpl',
			);
		}
	}
    return $return;
}

function cw_ps_on_cms_check_restrictions_PS($data) {
	
	static $active_offers_ids;
	
	if (!isset($active_offers_ids)) {
		$active_offers = cw_ps_get_offers();
		$active_offers_ids = array_column($active_offers, 'offer_id');
	}
	
	// Get offers related to cms
	$cms_offers = cw_ab_get_cms_restrictions($data['contentsection_id'],'PS');
	if (empty($cms_offers)) return true;

	$cms_offers_ids = array_column($cms_offers,'object_id');

	// Check if at least one offer is active now
	$is_valid = array_intersect($cms_offers_ids, $active_offers_ids);
	
	return $is_valid;
}

function cw_ps_on_cms_update($id, $content_section) {

	if (!empty($content_section['offers']) && is_array($content_section['offers']) && !empty($id)) {
	  foreach ($content_section['offers'] as $offer_id) {

		if (intval($offer_id)==0) continue;

		$data = array(
		  'contentsection_id'   => $id,
		  'object_type' => 'PS',
		  'object_id' => intval($offer_id)
		);
		cw_array2insert('cms_restrictions', $data, true);
	  }
	}
}


/* FIX: 
 * 
 * ./addons/Discount_Coupons/admin/coupons.php
 
 * functions are not used here to delete the data
 * 
 *import data, drop data - this functionality is not implemented using functions 
 * 
 * /

    
/* TODO:
 * 
 * check for used products during the categories deletion
 * 
 * */
