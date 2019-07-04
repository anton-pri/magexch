<?php
cw_event_listen('on_build_cart_product_hash', 'cw_cart_on_build_cart_product_hash');

# Get the customer's zone
function cw_cart_get_zone_ship($params) {
	global $tables;

    extract($params);

	$zones = cw_call('cw_cart_get_zones', array('address' => $address, 'is_shipping' => 1));
	$zone = 0; # default zone
	if (is_array($zones)) {
		$tmp = cw_query_column("SELECT zone_id FROM $tables[shipping_rates] WHERE zone_id IN ('".implode("','", array_keys($zones))."') and type='$type' group by zone_id");
		if (is_array($tmp) && !empty($tmp)) {
			$unused = $zones;
			# remove not available zones
			foreach($tmp as $v) {
				if (isset($unused[intval($v)]))
					unset($unused[intval($v)]);
			}
			if (!empty($unused)) {
				foreach($unused as $k => $v)
					unset($zones[$k]);
			}

			reset($zones);
			$zone = key($zones);
		}
	}

	return $zone;
}

#
# Get the customer's zones
#
function cw_cart_get_zones($address, $is_shipping = 0) {

	global $tables, $config;
	static $z_flags = array (
		"C" => 0x01,
		"S" => 0x02,
		"G" => 0x04,
		"T" => 0x08,
		"Z" => 0x10,
		"A" => 0x20);
	static $zone_element_types = array (
		"S" => "state",
		"G" => "county",
		"T" => "city",
		"Z" => "zipcode",
		"A" => "address");
	static $results_cache = array();

	if ($config['General']['use_counties'] != "Y") {
		unset($z_flags['G']);
		unset($zone_element_types['G']);
	}

	$zones = array();

	if (!$address && $config['General']['apply_default_country'] == 'Y')
        $address = cw_user_get_default_address();

	if (!empty($address)) {

		$data_key = md5($address["country"] . $address["state"] . $address["county"] . $address["zipcode"] . $address["city"] . $is_shipping);

		if (isset($results_cache[$data_key]))
			return $results_cache[$data_key];

        // get the zones for the shipping or for the taxes
		$shipping_condition = "and is_shipping='$is_shipping'";

		# Possible zones for customer's country...
		$possible_zones = cw_query($sql="SELECT $tables[zone_element].zone_id FROM $tables[zone_element], $tables[zones] WHERE $tables[zone_element].zone_id=$tables[zones].zone_id AND $tables[zone_element].field='".$address["country"]."'  AND $tables[zone_element].field_type='C' $shipping_condition GROUP BY $tables[zone_element].zone_id");

		if (is_array($possible_zones)) {

			$zones_completion = array();
			$_possible_zones = array();
			foreach ($possible_zones as $pzone) {
				$_possible_zones[$pzone['zone_id']] = cw_query_column("SELECT field_type FROM $tables[zone_element] WHERE zone_id='$pzone[zone_id]' AND field<>'%' GROUP BY zone_id, field_type");
			}

			foreach ($_possible_zones as $_pzone_id=>$_elements) {
				if (is_array($_elements)) {
					foreach ($_elements as $k=>$v) {
						$zones_completion[$_pzone_id] += $z_flags[$v];
					}
				}
			}

			$cs_state = $address["state"];
			$cs_country = $address["country"];
			$cs_pair = $cs_country."_".$cs_state;

			$empty_condition = " AND $tables[zone_element].field<>'%'";

			foreach ($possible_zones as $pzone) {
				$zones[$pzone['zone_id']] = $z_flags['C'];

				# If only country is defined for this zone, skip further actions
				if ($zones_completion[$pzone['zone_id']] == $z_flags['C'])
					continue;

				foreach ($z_flags as $field_type=>$field_type_flag) {

					if ($field_type == "C")
						continue;

					if ($zones_completion[$pzone['zone_id']] & $field_type_flag) {
						# Checking the field for  equal...

						if ($field_type == "S") {
							# Checking the state...
							$found_zones = cw_query_first_cell("SELECT zone_id FROM $tables[zone_element], $tables[map_states] WHERE $tables[zone_element].field='".addslashes($cs_pair)."' AND $tables[zone_element].field_type='S' AND $tables[map_states].code='".addslashes($cs_state)."' AND $tables[map_states].country_code='".addslashes($cs_country)."' AND $tables[zone_element].zone_id='$pzone[zone_id]'");
						} elseif ($field_type == "G") {
							# Checking the county...
							$found_zones = cw_query_first_cell("SELECT zone_id FROM $tables[zone_element] WHERE field_type='G' AND field='".$address["county"]."' AND zone_id='$pzone[zone_id]'");
						}
						else {
							# Checking the rest fields (city, zipcode, address)
							$found_zones = cw_query_first_cell("SELECT $tables[zone_element].zone_id FROM $tables[zone_element], $tables[zones] WHERE $tables[zone_element].zone_id=$tables[zones].zone_id AND $tables[zone_element].field_type='$field_type' AND '".addslashes($address[$zone_element_types[$field_type]])."' LIKE $tables[zone_element].field  AND $tables[zone_element].zone_id='$pzone[zone_id]' $empty_condition $shipping_condition");
						}

						if (!empty($found_zones)) {
							# Field is found: increase the priority
							$zones[$pzone['zone_id']] += $field_type_flag;
						}
						else {
							# Remove zone from available zones list
							unset($zones[$pzone['zone_id']]);
							continue;
						}
					}
				} # /foreach ($z_flags)
			} # /foreach ($possible_zones)
		}
	}

	$zones[0] = 0;
	arsort($zones, SORT_NUMERIC);

    $results_cache[$data_key] = $zones;

	return $zones;
}

function cw_cart_get_products_hashes ($products) {
	if (empty($products) || !is_array($products))
		return array();

	$hashes = array();
	foreach ($products as $product) {
            if (defined('AOM') && constant('AOM')) {
                $product['order_hash'] = 'aom_cart';
            } 
            $hashes[$product['order_hash']] = 1;
        }  

	return array_keys($hashes);
}

#
# Will return array of products with preserved indexes
#
function cw_cart_get_products_by_order_hash ($products, $hash) {

	if (!is_array($products) || empty($products))
		return array();

	$result = array ();
	foreach ($products as $k=>$product) {

            if (defined('AOM') && constant('AOM')) {
                $product['order_hash'] = 'aom_cart';
            } 

		if ($product['order_hash'] == $hash)
			$result[$k] = $product;
	}

	return $result;
}

function cw_cart_summarize($params, $sum) {
    extract($params);

    $sum['info']['discount'] += $res['info']['discount'];
# kornev, TOFIX
//    $sum['info']['payment_surcharge'] += $ps_part;
    $sum['info']['weight'] += $res['info']['weight'];

    if ($res['coupon'])
        $sum['info']['coupon'] = $res['info']['coupon'];
    $sum['info']['coupon_discount'] += $res['info']['coupon_discount'];

    $sum['info']['subtotal'] += $res['info']['subtotal'];
    $sum['info']['discounted_subtotal'] += $res['info']['discounted_subtotal'];
    $sum['info']['display_discounted_subtotal'] += $res['info']['display_discounted_subtotal'];

//	$sum['info']['discounted_subtotal'] += $res['info']['discounted_subtotal'];
//	$sum['info']['display_discounted_subtotal'] += $res['info']['display_discounted_subtotal'];

    $res['warehouse_customer_id'] = $warehouse_id;
    $sum['orders'][] = $res;

    return $sum;
}

# kornev, calculate the total cart
# default params $cart, $products, $userinfo
function cw_cart_calc($params) {
    extract($params);

	global $config, $tables;
	global $app_main_dir, $addons;

# kornev, here we should cut the userinfo, because we shouldn't store in the session a lot
    $cart['userinfo'] = $userinfo;
    foreach(array('addresses', 'additional_fields', 'system_info', 'additional_info', 'relations', 'custom_fields') as $k) unset($cart['userinfo'][$k]);

	$return = array();
    $return['pos'] = $cart['pos'];
	$return['orders'] = array();

    // Get all unique order hashes from cart products to split cart to orders by these hashes
    $products_hashes = cw_call('cw_cart_get_products_hashes', array($products));

	foreach ($products_hashes as $order_hash) {
        // Get products with specified hash
        $_products = cw_call('cw_cart_get_products_by_order_hash', array($products, $order_hash));
        
        // Get warehouse_customer_id from first product
        // Assumed that warehouse_customer_id is part of hash, thus all products with same hash have same warehouse_id anyway
        reset($_products);
        $_product = current($_products);
        $warehouse_id =  $_product['warehouse_customer_id'];

        // Calc single order
        $result = cw_func_call('cw_cart_calc_single', array('cart' => $cart, 'products' => $_products, 'userinfo' => $userinfo, 'warehouse_id' => $warehouse_id, 'order_hash'=>$order_hash));
        $return = cw_func_call('cw_cart_summarize', array('res' => $result, 'warehouse_id' => $warehouse_id, 'order_hash'=>$order_hash), $return);
    }
    unset($_products, $_product);

    if (!empty($cart['giftcerts'])) {
        $_products = array ();
        // Calc purchased gifcert as separate order
		$result = cw_func_call('cw_cart_calc_single', array('cart' => $cart, 'products' => $_products, 'userinfo' => $userinfo));
        $return = cw_func_call('cw_cart_summarize', array('res' => $result, 'warehouse_id' => 0, $products => $_products), $return);
    }

    $return['payment_surcharge'] = cw_payment_method_surcharge_ex($return['total'], $cart['info']['payment_id']);

    if (is_array($return['orders']))
    foreach($return['orders'] as $k=>$v) {

        $taxes = cw_cart_calc_taxes($v, $userinfo, $v['info']['shipping_cost'], $v['info']['payment_surcharge'], $v['warehouse_customer_id']);
        $v['info']['taxes'] = $taxes['taxes'];

        $_display_discounted_subtotal_tax = 0;
        $return['info']['taxes'] = array();
        if (is_array($taxes['taxes']))
        foreach ($taxes['taxes'] as $kt=>$vt) {
            if ($return['info']['taxes'][$kt]) {
                $return['info']['taxes'][$kt]['tax_cost'] += $vt['tax_cost'];
                $return['info']['taxes'][$kt]['tax_cost_no_shipping'] += $vt['tax_cost_no_shipping'];
            }
            else
                $return['info']['taxes'][$kt] = $vt;

            if (!$v['display_including_tax'])
                $_display_discounted_subtotal_tax += $v['tax_cost'];
        }
        $v['info']['shipping_cost'] += $v['info']['shipping_surcharge'];
        $_display_shipping_cost = $v['info']['shipping_cost'];
        if ($config['Taxes']['display_taxed_order_totals'] == 'Y') {
            $_display_shipping_cost = $v['info']['shipping_cost'] + $taxes['shipping'];
            $_display_subtotal = 0;
            $_display_discounted_subtotal = 0;
        }
        $return['info']['display_shipping_cost'] += $_display_shipping_cost;
        $v['info']['display_shipping_cost'] = $_display_shipping_cost;

        foreach($v['products'] as $kp=>$product) {
            if ($config['Taxes']['display_taxed_order_totals'] == 'Y') {
                if (is_array($v['taxes']))
                foreach ($v['taxes'] as $tn=>$tv)
                    if (!$vt['display_including_tax']) $_display_subtotal += $vt['tax_value'];

                if (!empty($product['discount']) || !empty($product['coupon_discount'])) {
                    $_taxes = cw_tax_price($product['price'], $userinfo, $product['product_id'], false, $product['discounted_price'], '', true);
                    if ($product['discounted_price'] > 0)
                        $product['display_discounted_price'] = $_taxes['taxed_price'];
                }
                else
                    $product['display_discounted_price'] = $product['display_price'] * $product['amount'];

                $product['display_subtotal'] = $product['display_discounted_price']+$product['surcharge']* $product['amount'];
                $_display_discounted_subtotal += $product['display_subtotal'];

                $_display_subtotal += $product['display_subtotal'];//$product['display_price'] * $product['amount'];
            }

            $v['products'][$kp] = $product;
        }

        if ($config['Taxes']['display_taxed_order_totals'] == 'Y')
            $v['info']['display_subtotal'] = price_format($_display_subtotal);

        $v['info']['tax_cost'] = $taxes['total'];
        $v['info']['total'] = price_format($v['info']['subtotal'] + $v['info']['shipping_cost'] + $v['info']['payment_surcharge'] + $v['info']['shipping_insurance'] + $v['info']['tax_cost'] - $v['info']['discount']-$v['info']['discount_value']-$v['info']['coupon_discount'], true, true);

        $return['orders'][$k] = $v;

        foreach(array('display_subtotal', 'taxed_subtotal', 'tax_cost', 'total') as $fld)
            $return['info'][$fld] += $v['info'][$fld];

        $return['products'] = cw_array_merge($return['products'], $v['products']);
//        $return['info']['display_discounted_subtotal'] = $_display_discounted_subtotal;
    }

    /*** GIFT CERTIFICATES ***/
	$giftcert_cost = 0;
	$applied_giftcerts = array();

	if (!empty($cart['info']['applied_giftcerts'])) {
		$gc_payed_sum = 0;
		$applied_giftcerts = array();

		foreach ($cart['info']['applied_giftcerts'] as $k=>$v) {

			if ($gc_payed_sum < $return['info']['total']) {
                $v['giftcert_cost'] = min(($return['info']['total'] - $gc_payed_sum), $v['giftcert_cost']);
                $gc_payed_sum += $v['giftcert_cost'];
                $applied_giftcerts[] = $v;
                continue;
            }

			db_query("UPDATE $tables[giftcerts] SET status='A' WHERE gc_id='$v[giftcert_id]'");
		}

		$giftcert_cost = $gc_payed_sum;
	}

	if ($return['info']['total'] >= $giftcert_cost) {
		$return['info']['giftcert_discount'] = $giftcert_cost;
	}
	elseif ($giftcert_cost) {
		$return['info']['giftcert_discount'] = $giftcert_cost - $return['info']['total'];
	}

	$return['info']['total'] = price_format($return['info']['total'] - $return['info']['giftcert_discount']);
	$return['info']['applied_giftcerts'] = $applied_giftcerts;

	if (is_array($return['orders'])) {

	    foreach ($return['orders'] as $k=>$order) {
	        $giftcert_discount = 0;

			foreach ($applied_giftcerts as $k1=>$applied_giftcert) {

			    if ($applied_giftcert['giftcert_cost'] == 0) continue;

	            if ($applied_giftcert['giftcert_cost'] > $order['info']['total']) {
	                $applied_giftcert['giftcert_cost'] = $order['info']['total'];
	            }

	            $giftcert_discount += $applied_giftcert['giftcert_cost'];
				$order['info']['total'] -= $applied_giftcert['giftcert_cost'];

				$applied_giftcert['giftcert_cost'] = price_format($applied_giftcert['giftcert_cost']);
				$applied_giftcerts[$k1]['giftcert_cost'] = $applied_giftcert['giftcert_cost'];

				$return['orders'][$k]['info']['applied_giftcerts'][] = $applied_giftcert;
				$return['orders'][$k]['info']['giftcert_discount'] = price_format($giftcert_discount);
	        }

	        $return['orders'][$k]['info']['total'] = price_format($return['orders'][$k]['info']['total'] - $return['orders'][$k]['info']['giftcert_discount']);
	    }
    }
    /*** GIFT CERTIFICATES ***/

    $return['userinfo'] = $cart['userinfo'];
    $return['info'] = cw_array_merge($cart['info'], $return['info']);
    $return = cw_array_merge($cart, $return);

    foreach (array('display_discounted_subtotal', 'total', 'display_subtotal', 'taxed_subtotal', 'tax_cost') as $k) {
        $return['info'][$k] = price_format($return['info'][$k], false, true);
    }

	return $return;
}

#
# This function distributes the discount among the product prices and
# decreases the subtotal
#
function cw_distribute_discount($field_name, $products, $discount, $discount_type, $avail_discount_total=0, $taxes=array()) {
	global $config;

	$sum_discount = 0;
	$return = array();
	$_orig_discount = $taxed_discount = $discount;

	if (!empty($taxes) && $config['Taxes']['display_taxed_order_totals'] == "Y" && $config['Taxes']['apply_discount_on_taxed_amount'] == "Y") {
		if ($discount_type=="absolute") {
			$_taxes = cw_tax_price($discount, 0, false, NULL, "", $taxes, false);
			$taxed_discount = $_taxes ['net_price'];
		}
		else {
			$_taxes = cw_tax_price($discount, 0, false, NULL, "", $taxes, true);
			$taxed_discount = $_taxes ['taxed_price'];
		}
	}

	if ($discount_type=="absolute" && $avail_discount_total > 0) {
		# Distribute absolute discount among the products
		$index = 0;
		$_considered_sum_discount = 0;
		$_total_discounted_products = 0;
		foreach ($products as $k=>$product) {
			if (@$product['deleted']) continue;
			if ($product['hidden'])
				continue;
			$_total_discounted_products++;
		}
		foreach ($products as $k=>$product) {
			if (@$product['deleted']) continue;
			if ($product['hidden'])
				continue;
			$index++;
			if ($field_name == "coupon_discount" || $product['discount_avail']) {
				$koefficient = $product['price'] / $avail_discount_total;
				if ($index < $_total_discounted_products) {
					$products[$k][$field_name] = $taxed_discount * $koefficient * $product['amount'];
					$products[$k]["taxed_".$field_name] = $taxed_discount * $koefficient * $product['amount'];

					$_considered_sum_discount += $products[$k][$field_name];
					$_considered_sum_taxed_discount += $products[$k]["taxed_".$field_name];
				}
				else {
					$products[$k][$field_name] = $taxed_discount - $_considered_sum_discount;
					$products[$k]["taxed_".$field_name] = $taxed_discount - $_considered_sum_taxed_discount;
				}

				$products[$k]['discounted_price'] = max($products[$k]['discounted_price'] - $products[$k][$field_name], 0.00);
			}
		}
	}
	elseif ($discount_type=="percent") {
		# Distribute percent discount among the products
		foreach ($products as $k=>$product) {
			if (@$product['deleted']) continue;
			if ($product['hidden'])
				continue;

			if ($field_name == "coupon_discount" || $product['discount_avail']) {
				$products[$k][$field_name] = $product['price'] * $discount / 100 * $product['amount'];
				if ($taxed_discount != $discount) {
					if ($product['display_price'] > 0)
						$_price = $product['display_price'];
					else
						$_price = $product['taxed_price'];
					$products[$k]["taxed_".$field_name] = $_price * $_orig_discount / 100 * $product['amount'];
				}
				else
					$products[$k]["taxed_".$field_name] = $products[$k][$field_name];

				$products[$k]['discounted_price'] = max($product['discounted_price'] - $products[$k][$field_name], 0.00);
			}
		}
	}

	foreach($products as $product) {
		if ($product['hidden']) continue;
		$sum_discount += $product["taxed_".$field_name];
	}

	if ($discount_type == "absolute" && $sum_discount > $discount)
		$sum_discount = $discount;

	if ($discount_type=="percent")
		$return[$field_name."_orig"] = $sum_discount;
	else
		$return[$field_name."_orig"] = $_orig_discount;

	$return['products'] = $products;
	$return[$field_name] = $sum_discount;

	return $return;
}

# Sort discounts in cw_cart_calc_discounts in descent order
function cw_sort_max_discount($a, $b) {
	return $b['max_discount'] > $a['max_discount'];
}

# This function calculates discounts on subtotal
function cw_cart_calc_discounts($params) {
    global $tables, $config, $addons, $global_store;
    extract($params);

//    $warehouse_condition = "AND warehouse_customer_id='$warehouse'";

    #
    # Search for subtotal to apply the global discounts
    #
    $avail_discount_total = 0;
    $total = 0;
    $_taxes = array();
    foreach($products as $k=>$product) {
        if ($product['hidden']) continue;

        $products[$k]['discount'] = 0;

         $products[$k]['discounted_price'] = $products[$k]['display_discounted_price'] = $product['price'] * $product['amount'];
         if ($product['discount_avail'])
                $avail_discount_total += $product['price'] * $product['amount'];

         $total += $product['price'] * $product['amount'];

         if ($config['Taxes']['apply_discount_on_taxed_amount'] == "Y" && is_array($product['taxes']))
                $_taxes = cw_array_merge($_taxes, $product['taxes']);
    }

	$return = array(
		'discount' => 0,
		'products' => $products,
        'total' => $total,
    );

	if ($avail_discount_total > 0) {

		#
		# Calculate global discount
		#
		if (!empty($global_store['discounts'])) {
			$discount_info = array();
			$__discounts = $global_store['discounts'];
			foreach ($__discounts as $k => $v) {
				if ($v['discount_type'] == 'absolute') {
					$__discounts[$k]['max_discount'] = $v['discount'];
				} else {
					$__discounts[$k]['max_discount'] = $avail_discount_total*$v['discount']/100;
				}
			}

			usort($__discounts, "cw_sort_max_discount");

			foreach ($__discounts as $v) {
				if (($v['__override']) || ($v['minprice'] <= $avail_discount_total && (empty($v['memberships']) || @in_array($membership_id, $v['memberships'])) && $v['warehouse_customer_id'] == $warehouse)) {
					$discount_info = $v;
					break;
				}
			}
			unset($__discounts);

		}
		else {
			$max_discount_str =
"IF ($tables[discounts].discount_type='absolute', $tables[discounts].discount, ('$avail_discount_total' * $tables[discounts].discount / 100)) as max_discount ";

			$discount_info = cw_query_first("SELECT $tables[discounts].*, $max_discount_str FROM $tables[discounts] LEFT JOIN $tables[discounts_memberships] ON $tables[discounts].discount_id = $tables[discounts_memberships].discount_id WHERE minprice<='$avail_discount_total' $warehouse_condition AND ($tables[discounts_memberships].membership_id IS NULL OR $tables[discounts_memberships].membership_id = '$membership_id') ORDER BY max_discount DESC");
		}
		
		/*
		 * Collect other customized discounts.
		 * 
		 * Add on_collect_discounts handler which will add own discounts info into array passed by reference
		 * 
		 */
		$__discounts = array($discount_info);
		cw_event('on_collect_discounts',array(&$__discounts, $avail_discount_total, $products)); // handlers fills &$__discounts by own discounts info
		usort($__discounts, "cw_sort_max_discount"); // Sort by max_discount
		$discount_info = array_shift($__discounts);  // Take the first - the best
		unset($__discounts);

		// Validate discount
		if (!empty($discount_info) && $discount_info['discount_type'] == 'percent' && $discount_info['discount'] > 100)
			unset($discount_info);

		if (!empty($discount_info)) {

			// Replace very big absolute discount to 100%
			if ($discount_info['discount_type'] == 'absolute' && $discount_info['discount'] > $total) {
				$discount_info['discount'] = 100;
				$discount_info['discount_type'] = 'percent';
			}

			$return = cw_array_merge($return, $discount_info);
			$return['discount'] += $discount_info['max_discount'];
			#
			# Distribute the discount among the products prices
			#
			$updated = cw_call('cw_distribute_discount', array("discount", $products, $discount_info['discount'], $discount_info['discount_type'], $avail_discount_total, $_taxes));
			#
			# $products and $discount are extracted from the array $updated
			#
			extract($updated);
			unset($updated);
			$return['products'] = $products;
			$return['discount'] = $discount;
			if (isset($discount_orig))
				$return['discount_orig'] = $discount_orig;
		}
	}

    if ($addons['pos'] && $global_store['discounts_value']) {
        $return['discount_value'] = $global_store['discounts_value']['discount'];
    }

	return $return;
}

#
# This function calculates taxes
#
# SUM = total sum of order
#
# TAX_US = country_tax_flat + SUM*country_tax_percent/100 + state_tax_flat + SUM*state_tax_percent/100;
#
# TAX_CAN = SUM*gst_tax/100 + SUM*pst_tax/100;
#

function cw_cart_calc_taxes_single($formula_data, $products_taxes, $amount, &$taxes) {
    foreach ($products_taxes as $tax_name=>$v) {
        if ($v['skip']) continue;

        if (!isset($taxes['taxes'][$tax_name])) {
            $taxes['taxes'][$tax_name] = $v;
            $taxes['taxes'][$tax_name]['tax_cost'] = 0;
        }

        if ($v['rate_type'] == '%') {
            $assessment = cw_cart_calc_assessment($v['formula'], $formula_data);
            $tax_value = $amount * price_format($assessment * $v['rate_value'] / 100, true);
        }
        else
            $tax_value = price_format($v['rate_value'], true) * $amount;

        $formula_data[$tax_name] = $tax_value;

        $tax_result += $tax_value;
        $taxes['taxes'][$tax_name]['tax_cost'] += $tax_value;
    }

    return $tax_result;
}

function cw_cart_calc_taxes(&$doc, $customer_info, $shipping_cost, $payment_cost, $warehouse = 0) {
	global $tables, $config, $addons, $current_language;
	global $app_main_dir, $current_area;

	$taxes = array();
	$taxes['total'] = 0;
	$taxes['shipping'] = 0;
	$_tmp_taxes = array();

    $doc['info']['taxed_subtotal'] = 0;
	foreach ($doc['products'] as $k=>$product) {
		$__taxes = array();
		if ($product['free_tax'] == 'Y') continue;

        $products_taxes = cw_get_products_taxes($product, $customer_info, true, '', ($current_area == 'G' && $customer_info['usertype'] != 'R'));
        $doc['info']['taxed_subtotal'] += $product['taxed_clear_price'] * $product['amount'];

		if ($config['Taxes']['display_taxed_order_totals'] == 'Y') {
		    $product['display_price'] = $product['taxed_price'];
            $product['display_net_price'] = $product['taxed_net_price'];
        }
        else {
            $product['display_price'] = $product['price'];
            $product['display_net_price'] = $product['net_price'];
        }
        $doc['products'][$k] = $product;

		if (is_array($products_taxes)) {
            $formula_data = array();
			$formula_data['ST'] = $product['price'];// * $product['amount'];
			$formula_data['DST'] = $product['discounted_price'] / $product['amount'];
			$formula_data['SH'] = 0;
            $formula_data['PM'] = 0;

            $tax_total = cw_cart_calc_taxes_single($formula_data, $products_taxes, $product['amount'], $taxes);
            $taxes['total'] += price_format($tax_total, true);
        }
    }

    $formula_data = array();
    $formula_data['ST'] = 0;
    $formula_data['DST'] = 0;
    $formula_data['SH'] = $shipping_cost;
    $formula_data['PM'] = $payment_cost;

    $product = array('product_id' => 0);
    $products_taxes = cw_get_products_taxes($product, $customer_info, true, '', ($current_area == 'G' && $customer_info['usertype'] != 'R'));
    $tax_total = cw_cart_calc_taxes_single($formula_data, $products_taxes, 1, $taxes);
    $taxes['shipping'] += price_format($tax_total, true);
    $taxes['total'] += price_format($tax_total, true);

	return $taxes;
}

# kornev, calculate single warehouse cart
# default params $cart, $products, $userinfo, $warehouse_id = '', $level = 0
function cw_cart_calc_single($params) {
    extract($params);

	global $addons, $config, $tables;
	global $app_main_dir;

	$giftcerts = $cart['giftcerts'];
    $weight = 0;
	$subtotal = 0;
	$discounted_subtotal = 0;
	$total_tax = 0;
	$giftcerts_cost = 0;

	foreach($products as $k=>$product) {

        $product['discounted_price'] = $product['price'] * $product['amount'];

        $product['surcharge'] = floatval(cw_call('on_collect_product_surcharge',array($product), 0.00));

        $product['subtotal'] = $product['discounted_price']+$product['surcharge']* $product['amount'];
    	$product['display_price'] = $product['price'];
	    $product['display_discounted_price'] = $product['discounted_price'];
		$product['display_subtotal'] = $product['subtotal'];

    	$products[$k] = $product;

        $product_subtotal = $product['price']*$product['amount'];
        $subtotal += $product_subtotal;
        $discounted_subtotal += $product['subtotal'];

        $weight += $product['weight']*$product['amount'];
	}

	$display_subtotal = $subtotal;
	$display_discounted_subtotal = $discounted_subtotal;

# kornev, moved with the DC changes
    if (!empty($products) && $level == 0) {
        $discounts_ret = cw_func_call('cw_cart_calc_discounts', array('membership_id' => $customer_info['membership_id'], 'products' => $products, 'cart' => $cart, 'warehouse_id' => $warehouse_id));
        extract($discounts_ret);
        unset($discounts_ret);
    }

	#
	# Enable shipping and taxes calculation if "apply_default_country" is ticked.
	#
	$calculate_enable_flag = true;

	if (empty($customer_info['current_address'])) {
		if ($config['General']['apply_default_country'] == "Y")
			$customer_info['current_address'] = cw_user_get_default_address();
		else
			$calculate_enable_flag = false;
	}

	#
	# Calculate Gift Certificates cost (purchased giftcerts)
	#
	if (!$warehouse_id && $giftcerts)
    foreach($giftcerts as $giftcert)
        $giftcerts_cost+=$giftcert['amount'];

	$subtotal += $giftcerts_cost;
	$display_subtotal += $giftcerts_cost;
	$discounted_subtotal += $giftcerts_cost;
	$display_discounted_subtotal += $giftcerts_cost;

	if ($discount > $display_subtotal)
		$discount = $display_subtotal - $display_discounted_subtotal;

	if ($coupon_discount > $display_subtotal)
		$coupon_discount = $display_subtotal - $display_discounted_subtotal;

	$return = array(
        'info' => array(
            'payment_surcharge' => price_format($payment_surcharge),
    		'discount' => price_format($discount),
            'discount_value' => price_format($discount_value),
	    	'coupon' => $coupon,
		    'coupon_discount' => price_format($coupon_discount),
    		'subtotal' => price_format($subtotal),
	    	'display_subtotal' => price_format($display_subtotal),
		    'discounted_subtotal' => price_format($discounted_subtotal),
	    	'display_discounted_subtotal' => price_format($display_discounted_subtotal),
            'weight' => $weight,
        ),
		'products' => $products,
    );

	return $return;
}

#
# This function calculates the payment method surcharge
#
function cw_payment_method_surcharge ($total, $payment_id) {
	global $tables;

	$surcharge = 0;

	if (!empty($total))
		$surcharge = cw_query_first_cell("SELECT IF (surcharge_type='$', surcharge, surcharge * $total / 100) as surcharge FROM $tables[payment_methods] WHERE payment_id='$payment_id'");

	return $surcharge;
}

/* TOFIX: WHAT FOR ? */
function cw_payment_method_surcharge_ex($total, $payment_id) {
    global $tables, $customer_id;

return array(0, 0);

    if (AREA_TYPE == 'G') return array(0, 0);

    $surcharge = 0;
    $payment_data = cw_query_first("select pm.apply_tax, cp.fee_basic, cp.fee_basic_limit, cp.fee_ex_flat, cp.fee_ex_percent from $tables[payment_methods] as pm where pm.payment_id='$payment_id'");
    if ($payment_data['fee_basic_limit'] > 0 && $payment_data['fee_basic_limit'] <= $total)
        $surcharge = $payment_data['fee_ex_flat'] + ($payment_data['fee_ex_percent']*$total)/100;
    else
        $surcharge = $payment_data['fee_basic'];

    return array($surcharge, $payment_data['apply_tax']);
}

#
# Generate products array in $cart
#
function cw_products_in_cart($cart, $user_info, $leave_info = false) {
	if (empty($cart) || empty($cart['products']))
		return array();

   // cw_call('cw_cart_normalize', array(&$cart));

	return cw_products_from_scratch($cart['products'], $user_info, false, $leave_info);
}

# Generate products array from scratch
function cw_products_from_scratch($scratch_products, $user_info, $persistent_products, $leave_info = false) {
	global $addons, $tables, $config, $app_main_dir;
	global $current_area, $current_language, $customer_id;

    cw_load('image');

//print_r($scratch_products); die;

	$products = array();

	if (empty($scratch_products))
		return $products;

	$pids = array();
	foreach ($scratch_products as $product_data)
		$pids[] = $product_data['product_id'];

	$int_res = cw_query_hash("SELECT * FROM $tables[products_lng] WHERE code = '$current_language' AND product_id IN ('".implode("','", $pids)."')", "product_id", false);

	unset($pids);

    cw_event('on_before_products_from_scratch', array(&$scratch_products));

	$hash = array();
    cw_load('warehouse');
	foreach ($scratch_products as $product_data) {
		$product_id = $product_data['product_id'];
		$cartid = $product_data['cartid'];
		$amount = $product_data['amount'];
		$variant_id = $product_data['variant_id'];
        $warehouse = $product_data['warehouse_customer_id'];
        if (!cw_warehouse_is_customer($customer_id, $warehouse)) continue;
		if (!is_numeric($amount))
			$amount = 0;

		$options = $product_data['options'];
		$product_options = false;
		$variant = array();

# kornev, TOFIX
		if ($addons['product_options'] && !empty($options) && is_array($options)) {
			if (!cw_check_product_options($product_id, $options))
				continue;

			list($variant, $product_options) = cw_get_product_options_data($product_id, $options, $membership_id);

			if (empty($variant_id) && isset($variant['variant_id']))
				$variant_id = $variant['variant_id'];
		}

        $fields[] = "p.*";
# kornev, supplier has got it's own prices
        if ($current_area != 'S')
            $fields[] = "min(pq.price) as price";
        $fields[] = 'avail';

        $status = cw_core_get_required_status($current_area);
        $products_array = cw_func_call('cw_product_get', array('id' => $product_id, 'variant_id' => $variant_id, 'amount' => $amount, 'user_account' => $user_info,'info_type'=>8192));
//cw_query_first($sql="select ".implode(', ', $fields)." from $tables[products] as p, $tables[products_prices] as pq, $tables[products_enabled] as pe left join $tables[products_warehouses_amount] as pwa on pwa.product_id=pe.product_id and pwa.variant_id='$variant_id' and pwa.warehouse_customer_id='$warehouse' WHERE p.product_id= pe.product_id and pe.product_id=pq.product_id AND pe.status in (".implode(", ", $status).") AND pe.product_id='$product_id' AND pq.quantity<='$amount' AND pq.membership_id IN(0, '$user_info[membership_id]') AND pq.variant_id = '$variant_id' ORDER BY pq.quantity DESC");

        $unlimited_products = true;
        if ($products_array['avail'] < $amount && in_array($current_area, array('G', 'C'))) {
            $unlimited_products = cw_query_first_cell("select backorder & ".($current_area=='G'?2:1)." from $tables[warehouse_divisions] where division_id = '$warehouse'");
            if (!$unlimited_products) $amount = $products_array['avail'];
        }

		if ($products_array) {
			$products_array = cw_array_merge($product_data, $products_array);
            if ($leave_info) $products_array['price'] = abs($product_data['price']);

            $products_array['warehouse_customer_id'] = $warehouse;
			$hash_key = $product_id."|".$warehouse;

			cw_event('on_product_from_scratch', array(&$products_array));

			#
			# If priduct's price is 0 then use customer-defined price
			#
			$free_price = false;
			if ($products_array['price'] == 0) {
				$free_price = true;
				$products_array['taxed_price'] = $products_array['price'] = price_format($product_data['free_price'] ? $product_data['free_price'] : 0);
			}

# kornev, TOFIX
			if ($addons['product_options'] && $options) {
				if (!empty($variant)) {
# kornev, it's not allow to set the variant price.
//					unset($variant['price']);
					if (is_null($variant['pimage_path'])) {
						cw_unset($variant, "pimage_path", "pimage_x", "pimage_y");
					} else {
						$variant['is_pimage'] = 'W';
					}
					$products_array = cw_array_merge($products_array, $variant);
				}

				$hash_key .= "|".$products_array['variant_id'];

				if ($product_options === false) {
					unset($product_options);

				} else {
					$variant['price'] = $products_array['price'];
					$variant['cost'] = $products_array['cost'];
					$products_array['options_surcharge'] = 0;
					$products_array['cost_surcharge'] = 0;
					if ($product_options) {
						foreach($product_options as $o) {
							$products_array['options_surcharge'] += ($o['modifier_type'] ? ($products_array['price']*$o['price_modifier']/100) : $o['price_modifier']);
							$products_array['cost_surcharge'] += ($o['cost_modifier_type'] ? ($products_array['cost']*$o['cost_modifier']/100) : $o['cost_modifier']);
						}
					}
				}

			}

			if (!$unlimited_products && !$persistent_products && ($products_array['avail']-$hash[$hash_key]) < $amount)
				continue;

			# Get thumbnail's URL (uses only if images stored in FS)
            $products_array['image_thumb'] = cw_image_get('products_images_thumb', $product_id);

			$products_array['price'] += $products_array['options_surcharge'];
			$products_array['cost'] += $products_array['cost_surcharge'];

			if ($products_array['price'] < 0)
				$products_array['price'] = 0;

			if ($products_array['cost'] < 0)
				$products_array['cost'] = 0;


			if (in_array($current_area, array('C', 'G'))) {
                $products_array['taxes'] = cw_get_products_taxes($products_array, $user_info, false, '', ($current_area == 'G' && $customer_info['usertype'] != 'R'));
                if ($config['Taxes']['display_taxed_order_totals'] == 'Y') {
                    $products_array['display_price'] = $products_array['taxed_price'];
                    $products_array['display_net_price'] = $products_array['taxed_net_price'];
                }
                else {
                    $products_array['display_price'] = $products_array['price'];
                    $products_array['display_net_price'] = $products_array['net_price'];
                }
            }

			$products_array['total'] = $amount*$products_array['price'];
			$products_array['product_options'] = $product_options;
			$products_array['options'] = $options;
			$products_array['amount'] = $amount;
			$products_array['cartid'] = $cartid;

			$products_array['product_orig'] = $products_array['product'];

			if (isset($int_res[$product_id])) {
				$products_array['product'] = stripslashes($int_res[$product_id]['product']);
				$products_array['descr'] = stripslashes($int_res[$product_id]['descr']);
				$products_array['fulldescr'] = stripslashes($int_res[$product_id]['fulldescr']);

				cw_unset($int_res, $product_id);
			}

			if ($products_array['descr'] == strip_tags($products_array['descr']))
				$products_array['descr'] = str_replace("\n", "<br />", $products_array['descr']);

			if ($products_array['fulldescr'] == strip_tags($products_array['fulldescr']))
				$products_array['fulldescr'] = str_replace("\n", "<br />", $products_array['fulldescr']);
            
            // Order hash defines how all products in cart will be split by orders
            // Listen for the event and return own part of hash
            $order_hash = cw_event('on_build_order_hash', array($products_array),array());
            $order_hash[] = 'W'.$products_array['warehouse_customer_id'];
            $products_array['order_hash'] = join('-', $order_hash);

			$products[] = $products_array;

			$hash[$hash_key] += $amount;
		}
	}
//cw_var_dump($products);
	return $products;
}

#
# This function generates the unique cartid number
#
function cw_generate_cartid($cart_products) {
	global $cart;

	if (empty($cart['max_cartid']))
		$cart['max_cartid'] = 0;

	$cart['max_cartid']++;

	return $cart['max_cartid'];
}

#
# Detectd ESD product(s) in cart
#
function cw_esd_in_cart($cart) {
	if (!empty($cart['products'])) {
		foreach($cart['products'] as $p) {
			if (!empty($p['distribution'])) {
				return true;
			}
		}
	}

	return false;
}

#
# Calculate total amount of all products in cart. Used for cart validation
#
function cw_get_cart_products_amount($products) {
	$amount = 0;
	if (!empty($products) && is_array($products)) {
		foreach ($products as $product) {
			$amount += $product['amount'];
		}
	}

	return $amount;
}

#
# Validate cart contents
#
function cw_cart_is_valid($cart, $userinfo) {
	# test: all total amount should not change
	$current_amount = cw_get_cart_products_amount($cart['products']);
    $validated_products = cw_call('cw_products_in_cart',array($cart, $userinfo));
	$validated_amount = cw_get_cart_products_amount($validated_products);

	$is_valid = ($current_amount == $validated_amount);

	return $is_valid;
}

function cw_get_delivery($shipping_id) {
    global $tables;
    return cw_query_first_cell("select shipping from $tables[shipping] where shipping_id='$shipping_id'");
}

function cw_cart_get_warehouses_cart($params) { //$cart, $userinfo
    global $smarty;
    extract($params);

    $pc = $cart['orders'];
    $enought_count = (count($cart['orders']) > 1);
    if (is_array($pc))
    foreach($pc as $k=>$_temp)
        $pc[$k]['products'] = cw_call('cw_products_in_cart',array($pc[$k], $userinfo));

    $smarty->assign('enought_count', $enought_count);
    return $pc;
}

function cw_check_product_warehouses(&$cart){
	global $tables, $customer_id;

	if (is_array($cart["products"]))
	foreach($cart["products"] as $k => $v)
		if ($v["warehouse_customer_id"] == 0){
			$warehouse_id = cw_query_first_cell("SELECT pwa.warehouse_customer_id FROM $tables[products_warehouses_amount] as pwa, $tables[customers_customer_info] as ci WHERE ci.customer_id = $customer_id AND pwa.product_id = $v[product_id]");
			$cart["products"][$k]["warehouse_customer_id"] = $warehouse_id;
		}
	return true;
}

function cw_cart_actions($params, $return) {
    global $smarty;
    $smarty->assign('cart', $return);
    return $return;
}

function cw_cart_on_build_cart_product_hash($product) {
    $h = array(
        'PID'.$product['product_id'],
        'OPT'.crc32(serialize($product['otptions'])),
        //'W'.$product['warehouse_customer_id'],
        );
    if (!empty($product['hidden'])) $h[] = 'HID'.crc32(uniqid(rand())); // Hidden must be always separate in cart
    if ($product['free_price']>0) $h[] = 'FREEPRICE'.$product['free_price'];
    return join('-',$h);
}
