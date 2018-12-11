<?php
// CartWorks.com - Promotion Suite

if (!defined('XCART_START')) { header("Location: ../../"); die("Access denied"); }

function cw_special_offer_check ($bonusid) {

    global $tables;

    $pid = cw_query_first_cell("SELECT pid FROM $tables[bonuses] WHERE bonusid='$bonusid'");
    if ($pid>0) $logic = 'AND';
    elseif (defined('PS_COND_LOGIC')) $logic = PS_COND_LOGIC;
    else $logic = 'AND';

    $check_func = array('E','D','T','W','Z','P','M','C');
    $result = true;
    foreach ($check_func as $func) {
        $check[$func] = call_user_func('cw_check_condition_'.$func, $bonusid, $logic == 'AND');
        $result &= $check[$func];
        if (!$result) break;
    }

    if ($_GET["showmedebug"]=='Y' && ($pid == 0 || $_GET['all']=='Y')) {
        x_load("debug");
        global $affected_product_ids, $_affected_product_ids;
        echo __FILE__,': bonusID, Conditions check result, Already used products, could be used with this bonus if it pass';
        cw_print_r($bonusid,$check,$affected_product_ids, $_affected_product_ids);
    }

    return $result;
}

function cw_check_condition_D ($bonusid) {
    global $tables, $cart;

    $conditions = cw_query_column("SELECT additional FROM $tables[bonus_conditions] WHERE bonusid='$bonusid' AND type='D'");

    if (!empty($conditions) && is_array($conditions)) {
        if (in_array($cart['discount_coupon'],$conditions, true)) return true;
        return false; # there are D conditions, but they don't match applied coupon
    }

    return true; # there are no D conditions at all

}

function cw_check_condition_E ($bonusid) {
    global $tables, $user_account;

    $conditions = cw_query_column("SELECT objid FROM $tables[bonus_conditions] WHERE bonusid='$bonusid' AND type='E'");

    if (!empty($conditions) && is_array($conditions)) {
        if (in_array(strval(intval($user_account['membershipid'])),$conditions, true)) return true;
        return false; # there are E conditions, but they don't match customer's membership
    }

    return true; # there are no E conditions at all
}

function cw_check_condition_T ($bonusid) {
	global $cart, $tables, $affected_product_ids;

	$T_value = cw_query_first_cell("SELECT value FROM $tables[bonus_conditions] WHERE bonusid='$bonusid' AND type='T'");
	$result = $T_value < ($cart['discounted_subtotal']+$cart['tax_cost']+$cart['total_special_discount']+$cart['discount']);

	return $result;
}

function cw_check_condition_W ($bonusid) {
	global $cart, $tables, $affected_product_ids;

	$W_value = cw_query_first_cell("SELECT value FROM $tables[bonus_conditions] WHERE bonusid='$bonusid' AND type='W'");
	$cart_weight = 0;
	foreach ($cart['products'] as $kk=>$vv) $cart_weight += $vv['weight']*$vv['amount'];
	return (($cart_weight < $W_value) || (empty($W_value)));
}

function cw_check_condition_Z ($bonusid) {
	global $userinfo, $tables;

	$zones = cw_get_customer_zones_avail($userinfo,"");
	$conditions = cw_query_column("SELECT objid FROM $tables[bonus_conditions] WHERE bonusid='$bonusid' AND type='Z'");

	if (!empty($conditions) && is_array($conditions)) {
		foreach ($conditions as $k=>$v)
			if (isset($zones[$v])) return true; # one match found - conndition is met
		return false; # there are Z conditions, but they don't match customer's zones
	}

	return true; # there are no Z conditions at all
}

function cw_check_condition_P ($bonusid, $and_logic = true) {
	global $cart, $tables;
	global $affected_product_ids, $_affected_product_ids;

	$conditions = cw_query("SELECT objid, quantity FROM $tables[bonus_conditions] WHERE bonusid='$bonusid' AND type='P'");

	if (!empty($conditions) && is_array($conditions)) {
		foreach ($conditions as $k=>$v) {

			foreach ($cart['products'] as $kk=>$vv) {
				if ($vv['productid']==$v['objid']) {
					$v['quantity'] -= $vv['amount'];
				}
			}

			# check if condition of the current product is met, taking into account
			# products used in previous applied bonuses
			if (($v['quantity']+$affected_product_ids['ids'][$v['objid']])>0 && $and_logic)
				return false; # one of the products is not in cart or has insufficient amount
			else {
				$_affected_product_ids['ids'][$v['objid']] += $conditions[$k]['quantity'];
                if (($v['quantity']+$affected_product_ids['ids'][$v['objid']])<=0 && !$and_logic) return true; // at least one product found in cart with enough amount
            }
		}

        if (!$and_logic) return false; // Nothing found with (OR logic)

	}

	return true; # there are no P conditions at all OR all conditions are met

}

function cw_check_condition_M ($bonusid, $and_logic = true) {
	global $cart, $tables;
	global $affected_product_ids, $_affected_product_ids;

	# Check all M conditions
	$conditions = cw_query("SELECT objid, quantity FROM $tables[bonus_conditions] WHERE bonusid='$bonusid' AND type='M'");

	if (!empty($conditions) && is_array($conditions)) {

        $semicart = array();
        // Join products in cart by productid and calculate total available amount taking into account affected ids
        foreach ($cart['products'] as $prod) {
            if (!isset($semicart[$prod['productid']])) {
                $semicart[$prod['productid']] = array(
                    'productid' => $prod['productid'],
                    'amount'    => -1*$_affected_product_ids['ids'][$prod['productid']],
                    'manufacturerid' => cw_query_first_cell("SELECT manufacturerid FROM $tables[products] WHERE productid=$prod[productid]"),
                    );
            }
            $semicart[$prod['productid']]['amount'] += $prod['amount'];
        }
        // Clear unavailable products
        foreach ($semicart as $pid=>$prod) {
            if ($prod['amount']<=0) unset($semicart[$pid]);
        }

        // Now semicart contains only available products and we consider it as cart

		# Check for all products in cart
		foreach ($conditions as $c) {
			foreach ($semicart as $kk=>$prod) {
				if ($prod['manufacturerid']==$c['objid']) {
					$_affected_product_ids['ids'][$prod['productid']] += min($prod['amount'],$c['quantity']);
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

	return true; # there are no M conditions at all OR all conditions are met
}

function cw_check_condition_C ($bonusid, $and_logic = true) {
	global $cart, $tables;
	global $affected_product_ids, $_affected_product_ids;

	# Check all C conditions
	$conditions = cw_query("SELECT objid, quantity FROM $tables[bonus_conditions] WHERE bonusid='$bonusid' AND type='C'");

	if (!empty($conditions) && is_array($conditions)) {

        $semicart = array();
        // Join products in cart by productid and calculate total available amount taking into account affected ids
        foreach ($cart['products'] as $prod) {
            if (!isset($semicart[$prod['productid']])) {
                $semicart[$prod['productid']] = array(
                    'productid' => $prod['productid'],
                    'amount'    => -1*$_affected_product_ids['ids'][$prod['productid']]
                    );
            }
            $semicart[$prod['productid']]['amount'] += $prod['amount'];
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
				$cat_lpos = cw_query_column("SELECT cat.lpos FROM $tables[products_categories] pc, $tables[categories] cat WHERE pc.productid='$prod[productid]' AND pc.categoryid=cat.categoryid");
				foreach($cat_lpos as $lpos) {
					$is_parent = cw_query_first_cell("SELECT cat.categoryid FROM $tables[categories] cat WHERE cat.categoryid='$c[objid]' AND $lpos BETWEEN cat.lpos AND cat.rpos");
					if (!empty($is_parent)) break;
				}

                if (!empty($is_parent)) {
                    $_affected_product_ids['ids'][$prod['productid']] += min($prod['amount'],$c['quantity']);
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

	return true; # there are no C conditions at all OR all conditions are met

}

function cw_apply_special_offer_discount ($products) {
global $special_offers_apply, $tables;

if (!empty($special_offers_apply['supply'])) {
foreach($products as $k=>$product) {
	$current_discount = array();
	$discounted_in_bundle = false;
	foreach ($special_offers_apply['supply'] as $bonusid=>$sup) {

        $bonusid=intval($bonusid); // Eliminate subindex

		$is_valid_product = false;
		$is_bundle = false;

		if ($sup['D']['type']!='S') continue;

		$is_bundle = cw_query_first_cell("SELECT pid FROM $tables[bonuses] WHERE bonusid='$bonusid'");

		if (is_array($sup['D']['product']) && in_array($product['productid'], array_keys($sup['D']['product']))) {
			$is_valid_product = true;
		}
		elseif (is_array($sup['D']['category'])) {
			foreach ($sup['D']['category'] as $cid=>$category) {

				$cat_lpos = cw_query_column("SELECT cat.lpos FROM $tables[products_categories] pc, $tables[categories] cat
				WHERE pc.productid='$product[productid]' AND pc.categoryid=cat.categoryid");
				foreach($cat_lpos as $lpos) {
					$is_parent = cw_query_first_cell("SELECT cat.categoryid FROM $tables[categories] cat WHERE cat.categoryid='$cid' AND $lpos BETWEEN cat.lpos AND cat.rpos");
					if (!empty($is_parent)) break;
				}

				if (!empty($is_parent)) {
					$is_valid_product = true;
					break;
				}
			}
		}

		if ($product['special_offer']['free_product'] == "Y") $is_valid_product = false; # Don't try to apply discount to free added products

		if (!$is_valid_product)	continue;

		$_current_discount = price_format(($sup['D']['discount_type']=="percent")?$product['price']*$sup['D']['discount']/100:$sup['D']['discount']);
		if ($is_bundle) {
			if ($discounted_in_bundle) continue; # Bundle discount can be applied only once
			# Apply bundle discount immediately
			$product['price'] = max ($product['price'] - $_current_discount, 0.00);
			$discounted_in_bundle = $_current_discount;
			$special_offers_apply['discount']['products'][$product['productid']][] = array (
                'discount_type' => $sup['D']['discount_type'],
                'discount'      => $sup['D']['discount'],
                'max_discount'  => $_current_discount
            );
		}
		elseif ($_current_discount > $current_discount['max_discount']) { # hey, we've found better regular discount
			$current_discount = array (
				'discount_type'	=> $sup['D']['discount_type'],
				'discount'		=> $sup['D']['discount'],
				'max_discount'	=> $_current_discount
			);
			$special_offers_apply['discount']['products'][$product['productid']][] = $current_discount;
		}
	}
	$products[$k]['price'] = max ($product['price'] - $current_discount['max_discount'], 0.00);
	$products[$k]['special_offer']['discount'] = $current_discount['max_discount']+$discounted_in_bundle;
}
}
#cw_print_r($products);

return $products;
}

function cw_delete_special_offer ($bonusid) {
	global $tables;

	# Find orphaned bonuses of deleted products
	$bids = cw_query_column("SELECT b.bonusid FROM $tables[bonuses] b LEFT JOIN $tables[products] p ON p.productid=b.pid WHERE b.pid!=0 AND p.productid IS NULL");

	# Append requested bonus
	if ($bonusid!=0) $bids[] = $bonusid;

	$q = implode("','",$bids);

	# Delete bonus related information
	db_query("DELETE FROM $tables[bonuses] WHERE bonusid IN ('$q')");
	db_query("DELETE FROM $tables[bonus_conditions] WHERE bonusid IN ('$q')");
	db_query("DELETE FROM $tables[bonuses_lng] WHERE bonusid IN ('$q')");
	db_query("DELETE FROM $tables[bonus_supply] WHERE bonusid IN ('$q')");
	# Multidomain addon support
	if (!empty($tables['domain_bonuses'])) db_query("DELETE FROM $tables[domain_bonuses] WHERE bonusid IN ('$q')");

	# Delete images
	if (!empty($bids))
		foreach ($bids as $bid)
			cw_delete_image($bid, "PS");
}

// CartWorks.com - Promotion Suite
?>
