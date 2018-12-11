<?php
#namespace CW\promotion_suite

function cw_special_offer_check ($offer_id) {

    global $tables;

    /* Detect offer logic conjunction */
    $pid = cw_query_first_cell("SELECT pid FROM $tables[ps_offers] WHERE offer_id='$offer_id'");
    if ($pid>0) $logic = 'AND'; // Offer attached to product ("Buy together" feature) always applied with AND conjunction
    elseif (defined('PS_COND_LOGIC')) $logic = PS_COND_LOGIC; // as set in init.php
    else $logic = 'AND';  // AND by default

    $logic = ($logic=='AND'?'&&':'||');
 
    /* Check all conditions */
    
    // Logical formula to define if all conditions met or not
    $formula = $debug_formula = "T && Z && W && E && B && K && (P $logic M $logic C $logic A)";

	// Extract all conditions defined for this offer
	$conditions = cw_ps_offer_conditions($offer_id);
	$enabled_conditions = array_keys($conditions);
	if (!empty($conditions[PS_SPEC_PRODUCTS])) $enabled_conditions = array_merge($enabled_conditions, array('M','C','A'));
	
	// Extract from formula all condition letters
    $check_func = explode(',',str_replace(array('&&','||'),',',str_replace(array('(',')',' '), '', $formula)));  // all type of conditions
    $check_func = array_map('trim', $check_func);
 // $check_func = array('E','D','T','W','Z','P','M','C');  // all type of conditions
 
	$check =  array_fill_keys($check_func, 2); // By default all condition are met (value 2 anyway will consider as boolean)
    $result = true;

    foreach ($check_func as $func) {
		if (in_array($func, $enabled_conditions)) {
			$check[$func] = (int)cw_call('cw_check_condition_'.$func, array($offer_id, $logic == '&&'));
		}
        $formula = str_replace($func, $check[$func], $formula);
        $debug_formula = str_replace($func, $func.$check[$func], $debug_formula);
    }
    
	eval('$result = '.$formula.';'); // calculate result formula

    // Tricky backdoor to see debug information on cart page &showmedebug=Y&all=Y
    if ($_GET["showmedebug"]=='Y' && ($pid == 0 || $_GET['all']=='Y')) {
		cw_load('dev');
        global $affected_product_ids, $_affected_product_ids;
        echo __FILE__,': offer_id, formula, Already used products, could be used with this bonus if it pass';
        cw_var_dump($offer_id,(int)$result.' = '.$formula,$affected_product_ids, $_affected_product_ids);
    }

    return $result;
}

// Used discount coupon
function cw_check_condition_B ($offer_id) {
    global $tables, $cart;

    $conditions = cw_query_column("
        SELECT coupon
        FROM $tables[ps_conditions]
        WHERE offer_id = '$offer_id' AND type = '" . PS_USE_COUPON . "'
    ");

    if (!empty($conditions) && is_array($conditions)) {
        if (in_array($cart['info']['coupon'], $conditions, TRUE)) return TRUE;
        return FALSE; # there are B conditions, but they don't match applied coupon
    }

    return TRUE; # there are no B conditions at all
}

// Cookie
function cw_check_condition_K($offer_id) {
    global $tables;

    $conditions = cw_query_first_cell("
        SELECT param1
        FROM $tables[ps_cond_details]
        WHERE offer_id = '$offer_id' AND object_type = '" . PS_OBJ_TYPE_COOKIE . "'
    ");
    
    $conditions = unserialize($conditions);

    if (!empty($conditions) && is_array($conditions)) {
        if ($conditions['operation'] == 'S' && $_COOKIE[$conditions['cookie']] == $conditions['value']) return TRUE;
        elseif ($conditions['operation'] == 'N' && !isset($_COOKIE[$conditions['cookie']])) return TRUE;
        elseif (($conditions['operation'] == 'E' || $conditions['operation'] == '') && isset($_COOKIE[$conditions['cookie']])) return TRUE;
        return FALSE; # there are K conditions, but they don't match applied coupon
    }

    return TRUE; # there are no K conditions at all
    
}

// Membership
function cw_check_condition_E ($offer_id) {
    global $tables, $user_account;

    $conditions = cw_query_first("
        SELECT object_id
        FROM $tables[ps_cond_details]
        WHERE offer_id = '$offer_id' AND object_type = '" . PS_OBJ_TYPE_MEMBERSHIP . "'
    ");

    if (!empty($conditions) && is_array($conditions)) {

        if (
            (empty($user_account['membership_id']) && empty($conditions['object_id']))
            || intval($user_account['membership_id']) == intval($conditions['object_id'])
        ) {
            return TRUE;
        }
        return FALSE; # there are E conditions, but they don't match customer's membership
    }

    return TRUE; # there are no E conditions at all
}

// Total
# qa.1 - basic - PASSED
# qa.2 - empty condition - PASSED
# TODO: qa.3 - cart with tax,shipping,discount components -NA
function cw_check_condition_T ($offer_id) {
	global $cart, $tables;

    $value = cw_query("
	    SELECT c.total, cd.object_type
	    FROM $tables[ps_conditions] c
	    LEFT JOIN $tables[ps_cond_details] cd ON c.cond_id = cd.cond_id
	    WHERE c.offer_id = '$offer_id' AND c.type = '" . PS_TOTAL . "'
	");

    $info = $cart['info'];
    $cart_total = $info['discounted_subtotal'] + $info['tax_cost'];// + $info['total_special_discount'] + $info['discount'];

    $from = 0;
    $till = 0;
    if (!empty($value) && is_array($value)) {
        foreach ($value as $vv) {

            if ($vv['object_type'] == PS_OBJ_TYPE_FROM) {
                $from = $vv['total'];
            }

            if ($vv['object_type'] == PS_OBJ_TYPE_TILL) {
                $till = $vv['total'];
            }
        }

        if (
            $from <= $cart_total
            && ($till == 0 || $cart_total <= $till)
        ) {
            return TRUE;
        }
        else {
            return FALSE;
        }
    }

    return TRUE;
}

// Weight
function cw_check_condition_W ($offer_id) {
	global $cart, $tables;

	$value = cw_query("
	    SELECT c.total, cd.object_type
	    FROM $tables[ps_conditions] c
	    LEFT JOIN $tables[ps_cond_details] cd ON c.cond_id = cd.cond_id
	    WHERE c.offer_id = '$offer_id' AND c.type = '" . PS_WEIGHT . "'
	");

	$cart_weight = 0;
    if (!empty($cart['products']) && is_array($cart['products'])) {
        foreach ($cart['products'] as $kk => $vv) {
            $cart_weight += $vv['weight'] * $vv['amount'];
        }
    }

    $from = 0;
    $till = 0;
    if (!empty($value) && is_array($value)) {
        foreach ($value as $vv) {

            if ($vv['object_type'] == PS_OBJ_TYPE_FROM) {
                $from = $vv['total'];
            }

            if ($vv['object_type'] == PS_OBJ_TYPE_TILL) {
                $till = $vv['total'];
            }
        }

        if (
            $from <= $cart_weight
            && ($till == 0 || $cart_weight <= $till)
        ) {
            return TRUE;
        }
        else {
            return FALSE;
        }
    }

	return TRUE;
}

// Zone
# qa.1 - basic - PASSED
# qa.2 - empty condition - PASSED
# TODO: qa.3 - logged in user with different addresses - NA
function cw_check_condition_Z ($offer_id) {
	global $user_address, $tables;

  $zones = cw_call('cw_cart_get_zones', array('address' => cw_user_get_address_by_type('current'), 'is_shipping' => 1));


  $conditions = cw_query_column("SELECT object_id FROM $tables[ps_cond_details] WHERE offer_id='$offer_id' AND object_type='".PS_OBJ_TYPE_ZONES."'");
	if (!empty($conditions) && is_array($conditions)) {
		foreach ($conditions as $k=>$v)
			if (isset($zones[$v])) return true; # one match found - conndition is met
		return false; # there are PS_SHIP_ADDRESS conditions, but they don't match customer's zones
	}

	return true; // there are no PS_SHIP_ADDRESS conditions at all \
}

// Products
# qa.1 - basic - PASSED
# qa.2 - empty condition - PASSED
# TODO: qa.3 - same product in two offers - NA
function cw_check_condition_P ($offer_id, $and_logic = true) {
	global $cart, $tables;
	global $affected_product_ids, $_affected_product_ids;

	$conditions = cw_query("SELECT object_id, quantity FROM $tables[ps_cond_details] WHERE offer_id='$offer_id' AND object_type='".PS_OBJ_TYPE_PRODS."'");

	if (!empty($conditions) && is_array($conditions) && is_array($cart['products']) && !empty($cart['products'])) {
		foreach ($conditions as $k=>$v) {

			foreach ($cart['products'] as $kk=>$vv) {
				if ($vv['product_id']==$v['object_id']) {
					$v['quantity'] -= $vv['amount'];
				}
			}

			# check if condition of the current product is met, taking into account
			# products used in previous applied bonuses
			if (($v['quantity']+$affected_product_ids['ids'][$v['object_id']])>0 && $and_logic)
				return false; # one of the products is not in cart or has insufficient amount (AND logic)
			else {
				$_affected_product_ids['ids'][$v['object_id']] += $conditions[$k]['quantity'];
                if (($v['quantity']+$affected_product_ids['ids'][$v['object_id']])<=0 && !$and_logic) return true; // at least one product found in cart with enough amount (OR logic)
            }
		}

        if (!$and_logic) return false; // Nothing found with enough amount (OR logic)

	}

	if (empty($conditions) && !$and_logic) return false; // this result should not affect whole formula if OR is used

	return true; # there are no P conditions at all OR all conditions are met

}

// Manufacturers
# qa.1 - basic - PASS
# qa.2 - empty cond - PASS
# qa.3 - req qty is 2 and cart has 2 different products from this man - PASS
# TODO: qa.4 - one of req products is locked by another offer - NA
function cw_check_condition_M ($offer_id, $and_logic = true) {
	global $cart, $tables;
	global $affected_product_ids, $_affected_product_ids;

	# Check all M conditions
	$conditions = cw_query("SELECT object_id, quantity FROM $tables[ps_cond_details] WHERE offer_id='$offer_id' AND object_type='".PS_OBJ_TYPE_MANS."'");

	if (!empty($conditions) && is_array($conditions)) {

        $semicart = array();
        // Join products in cart by productid and calculate total available amount taking into account affected ids
        foreach ($cart['products'] as $prod) {
            if (!isset($semicart[$prod['product_id']])) {
				$attributes = cw_func_call('cw_attributes_get',array('item_id'=>$prod['product_id'],'item_type'=>'P','attribute_fields'=>array('manufacturer_id')));
                $semicart[$prod['product_id']] = array(
                    'product_id' => $prod['product_id'],
                    'amount'    => -1*$_affected_product_ids['ids'][$prod['product_id']],
                    'manufacturer_id' => $attributes['manufacturer_id']['value'],
                    );
            }
            $semicart[$prod['product_id']]['amount'] += $prod['amount'];
        }
        // Clear unavailable products
        foreach ($semicart as $pid=>$prod) {
            if ($prod['amount']<=0) unset($semicart[$pid]);
        }

        // Now semicart contains only available products and we consider it as cart

		# Check for all products in cart
		foreach ($conditions as $c) {
			foreach ($semicart as $kk=>$prod) {
				if ($prod['manufacturer_id']==$c['object_id']) {
					$_affected_product_ids['ids'][$prod['product_id']] += min($prod['amount'],$c['quantity']);
					$c['quantity'] -= $prod['amount'];
                    if ($c['quantity']<=0) break;  // Ok, we reached condition quantity, no need to check other products
				}

			}

            if ($c['quantity']>0 && $and_logic) {
                return false; // one of the products is not in cart or has insufficient amount (AND logic)
            } elseif ($c['quantity']<=0 && !$and_logic) {
                return true;  // at least one manufacturer found in cart with enough amount (OR logic)
            }

		}

        if (!$and_logic) return false; // Nothing found with (OR logic)
	}

	if (empty($conditions) && !$and_logic) return false; // this result should not affect whole formula if OR is used
	
	return true; # there are no M conditions at all OR all conditions are met
}

// Attributes
function cw_check_condition_A ($offer_id, $and_logic = true) {

	global $cart, $tables;
	global $affected_product_ids, $_affected_product_ids;
	
	# Check all A conditions
	$conditions = cw_query("SELECT object_id, quantity, param1 as value, param2 as operation FROM $tables[ps_cond_details] WHERE offer_id='$offer_id' AND object_type='".PS_OBJ_TYPE_ATTR."'");
	
	
	if (!empty($conditions) && is_array($conditions)) {

		$condition_attributes = array();
		foreach ($conditions as $k=>$c) {
			$a = cw_func_call('cw_attributes_get_attribute', array('attribute_id'=>$c['object_id']));
			$condition_attributes[$a['attribute_id']] = $a;
		}
		unset($a);

        $semicart = array();
        // Join products in cart by productid and calculate total available amount taking into account affected ids
        if (is_array($cart['products'])) {
        foreach ($cart['products'] as $prod) {
            if (!isset($semicart[$prod['product_id']])) {
				$attributes = cw_func_call('cw_attributes_get',array('item_id'=>$prod['product_id'],'item_type'=>'P','attribute_fields'=>array_column($condition_attributes,'field')));
                $semicart[$prod['product_id']] = array(
                    'product_id' => $prod['product_id'],
                    'amount'    => -1*$_affected_product_ids['ids'][$prod['product_id']],
                    'attributes' => $attributes,
                    );
            }
            $semicart[$prod['product_id']]['amount'] += $prod['amount'];
        }
        }
        // Clear unavailable products
        foreach ($semicart as $pid=>$prod) {
            if ($prod['amount']<=0) unset($semicart[$pid]);
        }

        // Now semicart contains only available products and we consider it as cart
		# Check for all products in cart
		foreach ($conditions as $c) {
			if (in_array($condition_attributes[$c['object_id']]['type'], array('selectbox','multi-selectbox'))) {
				$condition_attribute_value = cw_attributes_get_value_by_attribute_value_id($c['value']);
			} else {
				$condition_attribute_value = $c['value'];
			}			
			
			foreach ($semicart as $kk=>$prod) {
				if (in_array($condition_attributes[$c['object_id']]['type'], array('selectbox','multi-selectbox'))) {
					$product_attribute_value = $prod['attributes'][$condition_attributes[$c['object_id']]['field']]['values_str'][0];
				} else {
					$product_attribute_value = $prod['attributes'][$condition_attributes[$c['object_id']]['field']]['value'];
				}
				if (cw_call('cw_ps_compare_attributes', array($product_attribute_value,$condition_attribute_value,$c['operation']))) {
					$_affected_product_ids['ids'][$prod['product_id']] += min($prod['amount'],$c['quantity']);
					$c['quantity'] -= $prod['amount'];
                    if ($c['quantity']<=0) break;  // Ok, we reached condition quantity, no need to check other products
				}

			}

            if ($c['quantity']>0 && $and_logic) {
                return false; // one of the products is not in cart or has insufficient amount (AND logic)
            } elseif ($c['quantity']<=0 && !$and_logic) {
                return true;  // at least one manufacturer found in cart with enough amount (OR logic)
            }

		}

        if (!$and_logic) return false; // Nothing found with (OR logic)
	}
	
	if (empty($conditions) && !$and_logic) return false; // this result should not affect whole formula if OR is used

	return true; # there are no A conditions at all OR all conditions are met	
}

function cw_ps_compare_attributes($a, $b, $operation) { 
	if ($operation=='bt' || $operation=='in') {
		$b = explode(',',$b);
		$b = array_map('trim', $b);
	}
	$a = trim($a);
	switch ($operation) {
		case 'eq': return $a == $b;
		case 'le': return $a <= $b;
		case 'lt': return $a<$b;
		case 'ge': return $a>=$b;
		case 'gt': return $a>$b;
		case 'bt': return $a>$b[0] && $a<$b[1];
		case 'in': return in_array($a,$b);
	}
}
// Categories
# qa.1 - basic - PASS
# qa.2 - empty cond - PASS
# qa.3 - req qty is 2 and cart has 2 different products from this cat - PASS
# TODO: qa.4 - one of req products is locked by another offer - NA
function cw_check_condition_C ($offer_id, $and_logic = true) {
	global $cart, $tables;
	global $affected_product_ids, $_affected_product_ids;

	# Check all C conditions
	$conditions = cw_query("SELECT object_id, quantity FROM $tables[ps_cond_details] WHERE offer_id='$offer_id' AND object_type='".PS_OBJ_TYPE_CATS."'");

	if (!empty($conditions) && is_array($conditions)) {

        $semicart = array();
        // Join products in cart by productid and calculate total available amount taking into account affected ids
        foreach ($cart['products'] as $prod) {
            if (!isset($semicart[$prod['product_id']])) {
                $semicart[$prod['product_id']] = array(
                    'product_id' => $prod['product_id'],
                    'amount'    => -1*$_affected_product_ids['ids'][$prod['product_id']] // fill semicart by used amount 
                    );
            }
            $semicart[$prod['product_id']]['amount'] += $prod['amount'];
        }
        // Clear unavailable products
        foreach ($semicart as $pid=>$prod) {
            if ($prod['amount']<=0) unset($semicart[$pid]);
        }

        // Now semicart contains only available products and we consider it as cart

        foreach ($conditions as $c) {
            // Check for all products in cart
			foreach ($semicart as $kk=>$prod) {
				
				// Detect if product is from required category or its subcategories
				$pcats = Product\Category\get($prod['product_id']);
				$parents = cw_category_get_path(array_column($pcats, 'category_id'));
				$is_parent = in_array($c['object_id'], $parents, true);

                if (!empty($is_parent)) {
                    $_affected_product_ids['ids'][$prod['product_id']] += min($prod['amount'],$c['quantity']);
                    $c['quantity'] -= $prod['amount'];
                    if ($c['quantity']<=0) break;  // Ok, we reached condition quantity, no need to check other products
				}
			}

            if ($c['quantity']>0 && $and_logic) {
                return false; // one of the products is not in cart or has insufficient amount (AND logic)
            } elseif ($c['quantity']<=0 && !$and_logic) {
                return true;  // at least one catregory found in cart with enough amount
            }

        }

        if (!$and_logic) return false; // Nothing found with (OR logic)

	}

	if (empty($conditions) && !$and_logic) return false; // this result should not affect whole formula if OR is used

	return true; # there are no C conditions at all OR all conditions are met

}

function cw_apply_special_offer_discount(&$product) {
	global $tables;

    cw_load('category');
	if ($product['taxed_price'] == 0) return $product; // Trick: let first time calculate products without offer to gather correct taxed_price

	$special_offers_apply =& cw_session_register("special_offers_apply");

	if (!empty($special_offers_apply['supply']) && $product['promotion_suite']['free_product'] !=  true) {
		
		$current_discount = array();
		$discounted_in_bundle = false;
		
		$discount_was_applied = false;

		$saved_price = $product['price'];
	
		// for product walk thru all bonuses
		foreach ($special_offers_apply['supply'] as $offer_id=>$bonus) {

			if ($bonus['D']['apply'] != PS_APPLY_PRODS) continue;

			$offer_id = intval($offer_id); // Eliminate subindex for repeated offers, e.g. 10.2->10

			$is_bundle = cw_query_first_cell("SELECT pid FROM $tables[ps_offers] WHERE offer_id='$offer_id'") != 0;

			$is_valid_product = false;

			if (is_array($bonus['D']['products']) && in_array($product['product_id'], array_keys($bonus['D']['products']))) {
				$is_valid_product = true; 
			}
			elseif (is_array($bonus['D']['categories'])) {
				foreach ($bonus['D']['categories'] as $cid=>$qty) {

					// Detect if product is from required category or its subcategories
					$pcats = Product\Category\get($product['product_id']);
					$parents = cw_category_get_path(array_column($pcats, 'category_id'));
					$is_parent = in_array($cid, $parents);

					if (!empty($is_parent)) {
						$is_valid_product = true;
						break;
					}
				}
			}

			if (!$is_valid_product)	continue;

			$_current_discount = price_format(($bonus['D']['disctype']== PS_DISCOUNT_TYPE_PERCENT)?$product['price']*$bonus['D']['discount']/100:$bonus['D']['discount']);
			if ($is_bundle) {
				if ($discounted_in_bundle) continue; # Bundle discount can be applied only once
				# Apply bundle discount immediately to product price
				$product['price'] = max ($product['price'] - $_current_discount, 0.00);
				$discounted_in_bundle = $_current_discount;
				$special_offers_apply['discount']['products'][$product['product_id']][] = array (
					'discount_type' => $bonus['D']['disctype'],
					'discount'      => $bonus['D']['discount'],
					'max_discount'  => $_current_discount
				);
			}
			elseif ($_current_discount > $current_discount['max_discount']) { # hey, we've found better regular discount
				$current_discount = array (
					'discount_type'	=> $bonus['D']['disctype'],
					'discount'		=> $bonus['D']['discount'],
					'max_discount'	=> $_current_discount
				);
				$special_offers_apply['discount']['products'][$product['product_id']][] = $current_discount;
			}

			$discount_was_applied = true;

		}
		
		if ($discount_was_applied) {
			$product['promotion_suite']['saved_price'] = $saved_price;
			$product['promotion_suite']['saved_taxed_price'] = $product['taxed_price'];			
			$product['price'] = max ($product['price'] - $current_discount['max_discount'], 0.00);
		}
	
	}

	return $product;
}

function cw_apply_special_offer_free (&$product) {
	if ($product['promotion_suite']['free_product']) {
		$product['price'] = 0;
	}
	return $product;	
}

/* Old fashioned hook for cw_shipping_get_rates 
 * must be called after all real time shipping rates hooks
 */
function cw_apply_special_offer_shipping($params, $return) {
	global $tables;

	$special_offers_apply =& cw_session_register('special_offers_apply');
	$products = $params['products'];
	if (empty($special_offers_apply['free_shipping'])) return $return;
	if (empty($return)) return $return;
	
	//cw_var_dump($return);
	$new_rates = array();
	
	// Re-calculate applicable total weight / items / subtotal	taking into account bonuses
	$total = $params['what_to_ship_params'];
	if (is_array($special_offers_apply['free_shipping']) && 
		is_array($special_offers_apply['free_shipping']['products']) &&
		!empty($special_offers_apply['free_shipping']['products'])) {

		$hash = crc32(serialize($total));

		foreach ($special_offers_apply['free_shipping']['products'] as $pid=>$qty) {
			foreach ($products as $kk=>$product) {
				if ($product['product_id'] == $pid) {
					
					$tmp_qty = min($qty,$product['amount']);
					
					if ($tmp_qty <= 0) continue;
					
					# Calculate total_cost and total_+weight for shipping calculation
					if ($product["free_shipping"] == "Y")
						continue;

					if (
						$product['shipping_freight'] <= 0
						|| $config['Shipping']['replace_shipping_with_freight'] != 'Y'
					) {

						$total['apply']['weight'] -= $product["weight"] * $tmp_qty;
						$total['apply']['items'] -= $tmp_qty;
						$total['apply']['DST'] -= $product['display_discounted_price']*$tmp_qty/$product['amount'];
						$total['apply']['ST']  -= $product['display_subtotal']*$tmp_qty/$product['amount'];

                        // Correct products array
                        $product['amount'] -= $tmp_qty;
                        $product['display_subtotal'] = $product['display_subtotal']*$product['amount']/($tmp_qty+$product['amount']);
                        $product['display_discounted_price'] = $product['display_discounted_price']*$product['amount']/($tmp_qty+$product['amount']);
                        $products[$kk] = $product;
                        if($products[$kk]['amount']<=0) unset($products[$kk]);

                        $qty -= $tmp_qty;
					}


				}
			}
		}
	
		if ($hash != crc32(serialize($total)) && $total['apply']['items']>0) {
			// Unset this function hook and retrieve rates again with corrected params
			cw_addons_unset_hooks(
				array('post', 'cw_shipping_get_rates', 'cw_apply_special_offer_shipping')
			);
            $_params = $params;
            $_params['what_to_ship_params'] = $total;
            $_params['weight'] = $total['apply']['weight'];
            $_params['products'] = $products;
			$new_rates = cw_func_call('cw_shipping_get_rates', $_params);

			// Restore function hook
			cw_addons_set_hooks(
				array('post', 'cw_shipping_get_rates', 'cw_apply_special_offer_shipping')
			);
		}	
	}

// Re-calc rates				
	foreach ($return as $k=>$rate) {
		
		// If bonus is applicable for certain methods only, then check this method
		if (!empty($special_offers_apply['free_shipping']['methods']) && 
			is_array($special_offers_apply['free_shipping']['methods']) && 
			!in_array($rate['shipping_id'],$special_offers_apply['free_shipping']['methods'])
			) {
			continue;
		}

		// If bonus is applicable for whole cart, then set the new rate regardless cart content
		if ($special_offers_apply['free_shipping']['apply'] == constant('PS_APPLY_CART')) {
			$return[$k]['saved_rate'] = $rate['original_rate'];	// save initial rate to calc discount
			$return[$k]['original_rate'] = $special_offers_apply['free_shipping']['rate'];
			continue;
		}
		
		// If bonus is applicable for selected products then 
		if (in_array($special_offers_apply['free_shipping']['apply'],array(PS_APPLY_COND, PS_APPLY_PRODS)) && 
			!empty($special_offers_apply['free_shipping']['products'])) {
			
			if (isset($new_rates[$k])) $return[$k] = $new_rates[$k];
			if ($total['apply']['items']<=0) $return[$k]['original_rate'] = 0;
			$return[$k]['original_rate'] += $special_offers_apply['free_shipping']['rate'];
			$return[$k]['saved_rate'] = $rate['original_rate'];	// save initial rate to calc discount
            if (defined('AOM') && constant('AOM')) {
                $return[$k]['shipping'] .= '*';
                //$return[$k]['shipping'] .= ' [offer: '.$rate['original_rate'].' => '.floatval($special_offers_apply['free_shipping']['rate']).'+'.floatval($new_rates[$k]['original_rate']).']';
            }
		}
	}

	// strange, but $rate['original_rate'] is exactly final rate value, not initial as you may think
	return $return;
}

function cw_ps_offer($offer_id) {
	global $tables;
	
	$offer_id = intval($offer_id);

	$offer = cw_query_first("SELECT * FROM $tables[ps_offers] WHERE offer_id='$offer_id'");
	
	$offer['bonuses'] = cw_call('cw_ps_offer_bonuses', array($offer_id));
	$offer['conditions'] = cw_call('cw_ps_offer_conditions', array($offer_id));

	// TODO: fetch an image

	return $offer;

}
function cw_ps_offer_conditions($offer_id) {
	global $tables;
	
	$offer_id = intval($offer_id);
	$cond = array();

	$cond_hash = cw_query("SELECT c.type, c.total, c.coupon, cd.cd_id, cd.object_id, cd.quantity, cd.object_type, cd.param1, cd.param2 
		FROM $tables[ps_conditions] c 
		LEFT JOIN $tables[ps_cond_details] cd ON c.cond_id=cd.cond_id 
		WHERE c.offer_id='$offer_id'");
	
	foreach ($cond_hash as $v) {
		switch ($v['type']) {
		case PS_TOTAL:
		case PS_WEIGHT:
			if ($v['object_type'] == PS_OBJ_TYPE_FROM) $cond[$v['type']]['from'] = $v['total'];
			else $cond[$v['type']]['to'] = $v['total'];
			break;
		case PS_USE_COUPON:
			$cond[$v['type']] = $v['coupon'];
			break;
		case PS_MEMBERSHIP:
			$cond[$v['type']] = $v['object_id'];
			break;
        case PS_COOKIE:
            $cond[$v['type']] = unserialize($v['param1']);
		case PS_SHIP_ADDRESS:
			$cond[$v['type']][$v['object_id']] = $v['object_id'];
			break;
		case PS_SPEC_PRODUCTS:
			if ($v['object_type']==PS_OBJ_TYPE_PRODS) $cond[$v['type']]['products'][$v['object_id']] = $v['quantity'];
			if ($v['object_type']==PS_OBJ_TYPE_CATS)  $cond[$v['type']]['categories'][$v['object_id']] = $v['quantity'];
			if ($v['object_type']==PS_OBJ_TYPE_MANS)  $cond[$v['type']]['manufacturers'][$v['object_id']] = $v['quantity'];

			if ($v['object_type']==PS_OBJ_TYPE_ATTR)
				$cond[$v['type']]['attributes'][$v['object_id']] = array(
					'quantity'	=> $v['quantity'],
					'value'		=> $v['param1'],
					'operation'	=> $v['param2'],
					'cd_id'		=> $v['cd_id'],
					);
			
		}
	}

	return $cond;	
		
}
function cw_ps_offer_bonuses($offer_id) {
	global $tables;
	
	$offer_id = intval($offer_id);
	$bonuses = array();

	$bonuses_hash = cw_query("SELECT b.type, b.apply, b.coupon, b.discount, b.disctype, bd.object_id, bd.quantity, bd.object_type 
		FROM $tables[ps_bonuses] b 
		LEFT JOIN $tables[ps_bonus_details] bd ON b.bonus_id=bd.bonus_id 
		WHERE b.offer_id='$offer_id'");
	
	foreach ($bonuses_hash as $v) {
		switch ($v['type']) {
		case PS_COUPON:
			$bonuses[$v['type']] = $v['coupon'];
			break;
		case PS_FREE_PRODS:
			$bonuses[$v['type']][$v['object_id']] = $v['quantity'];
			break;
		case PS_DISCOUNT:
			$bonuses[$v['type']]['discount'] = $v['discount'];
			$bonuses[$v['type']]['disctype'] = $v['disctype'];
			// go to next case to fill products and categories
		case PS_FREE_SHIP:
			$bonuses[$v['type']]['discount'] = $v['discount'];
			$bonuses[$v['type']]['apply'] = $v['apply'];
			if ($v['object_type']==PS_OBJ_TYPE_PRODS) $bonuses[$v['type']]['products'][$v['object_id']] = $v['quantity'];
			if ($v['object_type']==PS_OBJ_TYPE_CATS) $bonuses[$v['type']]['categories'][$v['object_id']] = $v['quantity'];
            if ($v['object_type']==PS_OBJ_TYPE_SHIPPING) $bonuses[$v['type']]['methods'][$v['object_id']] = $v['object_id'];
		}
	}

    $conditions = cw_ps_offer_conditions($offer_id);
    if ($conditions['K']) {
        $bonuses['K'] = array(
        'cookie'=>$conditions['K']['cookie'],
        'postaction'=>$conditions['K']['postaction'],
        'postvalue'=>$conditions['K']['postvalue']);
    }

	return $bonuses;
}

function cw_ps_offer_delete($offer_id) {
	global $tables;
	$offer_id = intval($offer_id);
	$ps_tables = array('ps_bonus_details', 'ps_bonuses', 'ps_cond_details', 'ps_cond_details', 'ps_offers');
	foreach ($ps_tables as $t)
		if (!empty($tables[$t]))
			db_query('DELETE FROM '.$tables[$t].' WHERE offer_id="'.$offer_id.'"');
			
	cw_load('image');
	cw_image_delete($offer_id, 'ps_offer_images');
}

function cw_ps_offer_bundle_update($product_id, $update_data) {
    global $tables, $config;

    $product_id = (int) $product_id;

    $offer_id = cw_query_first_cell("SELECT offer_id FROM $tables[ps_offers] WHERE pid='$product_id'");
	
	if (empty($offer_id)) {
			// There is no offer for this product yet. Create it
			$data = array(
			'title' => "Product #$product_id bundle",
			'description' => 'Buy products together and get discount '.$update_data['discount'].($update_data['disctype']==PS_DISCOUNT_TYPE_PERCENT?'%':$config['General']['currency_symbol']),
			'startdate' => time(),
			'enddate' => 9999999999,
			'exlusive' => 0,
			'position' => -1,
			'active' => 1,
			'priority' => -1,
			'pid' => $product_id,
			'auto' => $update_data['auto'],
			'repeat' => 0
			);
			$offer_id = cw_array2insert('ps_offers', $data);
            
            // Add offer to all domains
            $attribute_id = cw_call('cw_attributes_filter', array(array('field'=>'domains','item_type'=>'PS'),true,'attribute_id'));
            $data = array(
                'item_id' => $offer_id,
                'attribute_id' => $attribute_id,
                'value' => 0,
                'item_type' => 'PS'
            );
            cw_array2insert('attributes_values', $data);
	}
	
	$cond_id = cw_query_first_cell("SELECT cond_id FROM $tables[ps_conditions] WHERE offer_id='$offer_id' AND type='".PS_SPEC_PRODUCTS."'");
	if (empty($cond_id)) {
			// Create condition
			$data = array(
			'type' => PS_SPEC_PRODUCTS,
			'offer_id' => $offer_id
			);
			$cond_id = cw_array2insert('ps_conditions', $data);
			$data = array(
			'cond_id' => $cond_id,
			'offer_id' => $offer_id,
			'object_id' => $product_id,
			'quantity' => 1,
			'object_type' => PS_OBJ_TYPE_PRODS,
			);
			cw_array2insert('ps_cond_details', $data);
			
			// Create bonus
			$data = array(
				'offer_id' => $offer_id,
				'type' => PS_DISCOUNT,
				'apply' => PS_APPLY_PRODS,
				'discount' => floatval($_POST['discount']),
				'disctype' => intval($_POST['disctype'])
			);
			$bonus_id = cw_array2insert('ps_bonuses', $data);
			$data = array(
				'bonus_id' => $bonus_id,
				'offer_id' => $offer_id,
				'object_id' => $product_id,
				'quantity' => 1,
				'object_type' => PS_OBJ_TYPE_PRODS			
			);
			cw_array2insert('ps_bonus_details', $data);
	}

	// Add new selected products
	foreach ($update_data['bundle'] as $k => $v) {
		if ($v['id'] == $product_id || empty($v['id'])) continue;
		$data = array(
			'cond_id' => $cond_id,
			'offer_id' => $offer_id,
			'object_id' => $v['id'],
			'quantity' => 1,
			'object_type' => PS_OBJ_TYPE_PRODS,			
		);
		cw_array2insert('ps_cond_details', $data);
		$data = array(
			'bonus_id' => $bonus_id,
			'offer_id' => $offer_id,
			'object_id' => $v['id'],
			'quantity' => 1,
			'object_type' => PS_OBJ_TYPE_PRODS,			
		);
		cw_array2insert('ps_bonus_details', $data);
	}


	$data = array(
		'discount' => floatval($update_data['discount']),
		'disctype' => intval($update_data['disctype'])
	);
	cw_array2update('ps_bonuses', $data, "offer_id='$offer_id' AND type='".PS_DISCOUNT."'");	

	return $offer_id;


}

function cw_ps_on_collect_shipping_rates_hash() {
    return cw_session_register('special_offers_apply');
}
function cw_ps_aom_recalculate_totals($order) {
    if (defined('AOM') && constant('AOM')) {
        global $cart;
        $cart = $order;
        $cart['info']['use_shipping_cost_alt'] = 'N';
        cw_include('addons/promotion_suite/customer/cart_init.php');
    }
}
function cw_ps_aom_recalculate_totals_extra($order) {
    $cart = cw_get_return();
    $extra = cw_call('cw_ps_on_place_order_extra', array(array()));
    $cart['info']['extra'] = array_merge($cart['info']['extra'], $extra);
    return $cart;
}

// change search query params for order search
function cw_ps_prepare_search_orders($data, $docs_type, &$fields, &$query_joins, &$where, &$groupbys, &$having, &$orderbys) {
    global $tables;

    if (
        $data['search_sections']['tab_search_orders_advanced']
        && $docs_type == 'O'
        && $data['promotion_suite']['offer']
    ) {
        $query_joins['promo_offer'] = array(
            'tblname' => 'docs_extras',
            'on' => "promo_offer.doc_id = $tables[docs].doc_id AND promo_offer.khash LIKE 'offer_{$data['promotion_suite']['offer']}%'",
            'is_inner' => true,
        );
    }
}
