<?php
cw_load('cart','product');

# This function perform actions to normalize cart content
function cw_cart_normalize(&$cart) {
	if (empty($cart['products']))
		return false;

	$hash = array();
	$cart_changed = false;

	foreach ($cart['products'] as $k => $p) {
		if ($p['hidden'])
			continue;

		$po = (!empty($p['options']) && is_array($p['options']) ? serialize($p['options']) : "");
		$key = $p['product_id'].$p['warehouse_customer_id'].$po.$p['price'];

		if (isset($p['offer_info']))
			$key .= '-fa'.$p['offer_info']['is_free_item'];
        

		if (isset($hash[$key])) {
			# Unite several product items
			$cart_changed = true;

			if (!$p['distribution'])
				$cart['products'][$hash[$key]]['amount'] += $p['amount'];
			else
				$cart['products'][$hash[$key]]['amount'] = 1;

			unset($cart['products'][$k]);
		}
		else {
			$hash[$key] = $k;
		}
	}

	return $cart_changed;
}

#
# This function is used to add product to the cart
#
function cw_add_to_cart(&$cart, $product_data) {
	global $user_account;
	global $addons, $config, $top_message, $app_main_dir, $HTTP_REFERER, $app_catalogs, $tables;
	global $from, $current_area;

	$return = array();

	# Extracts to: $product_id, $amount, $product_options, $price, $warehouse_customer_id
	extract($product_data);
    
    $warehouse = $product_data['warehouse_customer_id'];
    
    cw_load('warehouse');
	$added_product = cw_func_call('cw_product_get', array('id' => $product_id, 'user_account' => $user_account, 'info_type' => 3));
    
    if ($added_product['product_type'] == 10) $warehouse = $added_product['warehouse_customer_id'];

    if (!$warehouse)
        $possible_warehouse = cw_warehouse_get_max_amount_warehouse($product_id);


	if (!empty($addons['egoods']) && !empty($added_product['distribution']))
		$amount = 1;
	else
		$amount = abs(intval($amount));
    
    if ($amount == 0) $amount = 1;

# kornev, TOFIX
	if ($addons['product_options']) {
		#
		# Prepare the product options for added products
		#
		if (!empty($product_options)) {
			# Check the received options
			if (!cw_check_product_options($product_id, $product_options)) {
    			$return['redirect_to'] = "product.php?product_id=$product_id&err=options";
				return $return;
			}
		}
		else {
			# Get default options
			$product_options = cw_get_default_options($product_id, $amount, @$user_account['membership_id']);
			if ($product_options === false) {
				$return['redirect_to'] = 'index.php?target=error_message&error=access_denied&id=30';
				return $return;
			}
			elseif ($product_options === true) {
				$product_options = "";
				unset($product_options);
			}
		}

		# Get the variant_id of options
		$variant_id = cw_get_variant_id($product_options, $product_id);

		if (!empty($variant_id)) {

            $possible_warehouse = cw_warehouse_get_max_amount_warehouse($product_id, $variant_id);
            if (empty($warehouse))
                $warehouse = $possible_warehouse;

			# Get the variant amount
			$added_product['avail'] = cw_warehouse_get_warehouse_avail($warehouse, $product_id, null, $variant_id);//cw_get_options_amount($product_options, $product_id);

			if (!empty($cart['products']))  {
				foreach ($cart['products'] as $k => $v) {
					if ($v['product_id'] == $product_id && $variant_id == $v['variant_id'])
						$added_product['avail'] -= $v['amount'];
				}
			}
		}
        else {
            if (empty($warehouse))
                $warehouse = $possible_warehouse;
            $added_product['avail'] = cw_warehouse_get_warehouse_avail($warehouse, $product_id);
        }
	}

/*
kornev, the amount is checked by another function - during the calculation
	if ($config['General']['unlimited_products'] == "N" && $added_product['product_type'] != 10) {
		#
		# Add to cart amount of items that is not much than in stock
		#
		if ($amount > $added_product['avail'])
			$amount = $added_product['avail'];
	}
*/

	if ($from == 'salesman' && empty($amount)) {
		$return['redirect_to'] = ($app_catalogs['customer']."/product.php?product_id=".$product_id);
		return $return;
	}

	if ($product_id && $amount) {

		if ($amount < $added_product['min_amount']) {
			$return['redirect_to'] =  "index.php?target=error_message&error=access_denied&id=31";
			return $return;
		}

		$found = false;

        cw_log_add(__FUNCTION__, array($product_data, $added_product));        

        $_product = cw_array_merge(
            $product_data,
            $added_product,
            array('options'  =>$product_options,
                 'free_price'=>$price)
        );
        
        // Product hash defines how to differ/join products in cart
        // Listen for the event and return own part of hash. See also default handler.
        $product_hash = cw_event('on_build_cart_product_hash', array($_product),array());
        $product_data['product_hash'] = $_product['product_hash'] = join('-', $product_hash);
            
		if (!empty($cart) && @$cart['products']) {
			foreach ($cart['products'] as $k=>$v) {
                $product_hash = join('-', cw_event('on_build_cart_product_hash', array($v),array()));

				if ($product_hash == $_product['product_hash']) {
					if (doubleval($v['free_price']) != $price)
						continue;

					$found = true;
					if (($cart['products'][$k]['amount'] >=1) && (!empty($added_product['distribution']) || !empty($subscribed_product)))	{
						$cart['products'][$k]['amount']=1;
						$amount=0;
					}

					$cart['products'][$k]['amount'] += $amount;
                    $return['added_amount'] += $amount;
                    $return['productindex'] = $k;
                    $return['cartid'] = $v['cartid'];
                    $return['merged'] = true;
                    break;
				}
			}
		}
        
		if (!$found) {
			#
			# Add product to the cart
			#
			if (!empty($price)) {
				# price value is defined by customer if admin set it to '0.00'
				$free_price = abs(doubleval($price));
			}

			$cartid = cw_generate_cartid($cart['products']);

			if (empty($cart['products']))
				$add_to_cart_time = time();

            $_product = array(
				"cartid" => $cartid,
				"product_id" => $product_id,
				"amount" => $amount,
				"options" => $product_options,
				"free_price" => @price_format(@$free_price),
                "salesman_doc_id" => $salesman_doc_id,
				"distribution" => $added_product['distribution'],
				"variant_id" => $variant_id,
                "warehouse_customer_id" => $warehouse,
            );
            // Add all custom fields from added products
            foreach ($product_data as $k=>$v) {
                if (!isset($_product[$k])) $_product[$k]=$v;
            }

			$cart['products'][] = $_product;

            // count add to cart
            cw_call_delayed('cw_product_run_counter', array('product_id' => $product_id, 'count' => 1, 'type' => 3));

            $return['added_amount'] = $amount;
            $_ak = array_keys($cart['products']);
            $return['productindex'] = end($_ak);
            $return['cartid'] = $cartid;
            $return['merged'] = false;
            
		}

	}

cw_log_add(__FUNCTION__, $return);

	return $return;
}

#
# This function is used to delete product from the cart
#
function cw_delete_from_cart(&$cart, $productindex) {
	global $addons, $config, $app_main_dir, $tables;

	$mode = 'delete';

	$product_id = 0;

	foreach ($cart['products'] as $k=>$v) {
		if ($v['cartid'] == $productindex) {
			$product_id = $v['product_id'];
            cw_call_delayed('cw_product_run_counter', array('product_id' => $product_id, 'count' => 1, 'type' => 2));
			array_splice($cart['products'], $k, 1);
			break;
		}
	}

	return $product_id;
}

#
# This function updates the quantity of products in the cart
#
function cw_update_quantity_in_cart(&$cart, $productindexes, $warehouse_selection = array()) {
	global $addons, $config, $app_main_dir, $tables;
	
	if (empty($cart['products']))
		return;
	
	$action = "update";
	foreach ($productindexes as $_cartid=>$new_quantity) {
		foreach ($cart['products'] as $k=>$v) {
			if ($v['cartid'] == $_cartid) {
				$productindexes_tmp[$k] = $new_quantity;
				break;
			}
		}
	}

	$productindexes = $productindexes_tmp;
	unset($productindexes_tmp);

	$min_amount_warns = array();
	foreach ($cart['products'] as $k=>$v) {
		$tot = 0;
		$tot_amount = 0;
		$min_amount = cw_query_first_cell("SELECT min_amount FROM $tables[products] WHERE product_id = '$v[product_id]'");
		foreach ($productindexes as $productindex=>$new_quantity) {
			if (!is_numeric($new_quantity))
				continue;

			if ($cart['products'][$productindex]['product_id'] == $v['product_id'] && $cart['products'][$productindex]['variant_id'] == $v['variant_id'] && $cart['products'][$productindex]['warehouse'] == $v['warehouse']) {
				if ($new_quantity < $min_amount && $new_quantity > 0) {
					$productindexes[$productindex] = $new_quantity = $v['amount'];
					$min_amount_warns[$v['cartid']] = $min_amount;
				}
				$tot += floor($new_quantity);
			}
		}

		foreach ($cart['products'] as $k2=>$v2) {
			if ($v['product_id'] == $v2['product_id'] && $v2['variant_id'] == $v['variant_id'] && $v['warehouse'] == $v2['warehouse'])
				$tot_amount += $v2['amount'];
		}

		$updates_array[$k] = array("quantity"=>$v['amount'], "total_quantity"=>$tot, "total_amount" => $tot_amount);
	}

    cw_load('warehouse');
	$hash = array();
# kornev, TOFIX
	if (!empty($addons['product_options'])) {
		foreach ($productindexes as $productindex => $new_quantity) {
			if (!empty($cart['products'][$productindex]['options'])) {
				$variant_id = $cart['products'][$productindex]['variant_id'];
				if ($variant_id) {
					if (!isset($hash[$variant_id])) {
						$hash[$variant_id]['avail'] = cw_warehouse_get_warehouse_avail($cart['products'][$productindex]['warehouse'], $cart['products'][$productindex]['product_id'], null, $variant_id);//cw_get_options_amount($cart['products'][$productindex]['options'], $cart['products'][$productindex]['product_id']);
					}

					$hash[$variant_id]['old'] += $cart['products'][$productindex]['amount'];
					$hash[$variant_id]['new'] += $new_quantity;
					$hash[$variant_id]['ids'][] = $cart['products'][$productindex]['product_id'];
					$cart['products'][$productindex]['variant_id'] = $variant_id;
				}
			}
		}
	}

	foreach ($productindexes as $productindex => $new_quantity) {

		if (!is_numeric($new_quantity) || empty($cart['products'][$productindex]))
			continue;

		$new_quantity = floor($new_quantity);
		$product_id = $cart['products'][$productindex]['product_id'];
		$total_quantity = $updates_array[$productindex]['total_quantity'];
		$total_amount = $updates_array[$productindex]['total_amount'];
/*
kornev - the amount is checking during the calculation
		if ($config['General']['unlimited_products'] == "N") {
			if (!empty($cart['products'][$productindex]['variant_id'])) {
				$amount_max = $hash[$cart['products'][$productindex]['variant_id']]['avail'];
				$total_quantity = $hash[$cart['products'][$productindex]['variant_id']]['old'];
			}
			else {
                $amount_max = cw_warehouse_get_warehouse_avail($cart['products'][$productindex]['warehouse_customer_id'], $product_id, null);
			}
		}
		else {
*/
			$amount_max = $total_quantity + 1;
/*
		}
*/

		$amount_min = cw_query_first_cell("SELECT min_amount FROM $tables[products] WHERE product_id='$product_id'");

/*
the amoutn is checking during the calculation
		if ($config['General']['unlimited_products'] == "Y") {
			$cart['products'][$productindex]['amount'] = $new_quantity;
			continue;
		}
*/
        $cart['products'][$productindex]['amount'] = $new_quantity;
        continue;

		if (($new_quantity >= $amount_min ) && ($cart['products'][$productindex]['distribution'])) {
			$cart['products'][$productindex]['amount'] = 1;
		}
		elseif (($new_quantity >= $amount_min) && ($new_quantity <= ($amount_max - $total_amount + $cart['products'][$productindex]['amount']))) {
			$cart['products'][$productindex]['amount'] = $new_quantity;
			if(!empty($cart['products'][$productindex]['variant_id'])) {
				$hash[$cart['products'][$productindex]['variant_id']]['old'] += ($new_quantity - $cart['products'][$productindex]['amount']);
			}
			else {
				$updates_array[$productindex]['total_amount'] += ($new_quantity-$cart['products'][$productindex]['amount']);
			}
		}
		elseif ($new_quantity >= $amount_min) {
			$old_amount = $cart['products'][$productindex]['amount'];
			$cart['products'][$productindex]['amount'] = ($amount_max - $total_amount + $cart['products'][$productindex]['amount']);
			if (!empty($cart['products'][$productindex]['variant_id'])) {
				$hash[$cart['products'][$productindex]['variant_id']]['old'] += ($amount_max - $total_amount + $cart['products'][$productindex]['amount'] - $old_amount);
			}
			else {
				$updates_array[$productindex]['total_amount'] += ($amount_max - $total_amount + $cart['products'][$productindex]['amount'] - $old_amount);
			}
		}
		else {
			$cart['products'][$productindex]['amount'] = 0;
		}

		if ($cart['products'][$productindex]['amount'] < 0)
			$cart['products'][$productindex]['amount'] = 0;
	}

	$products = array();
	foreach ($cart['products'] as $product) {
		if ($product['amount'] > 0) {
            $product['destination_warehouse'] = $warehouse_selection[$product['cartid']];
			$products[] = $product;
	}
	}
	$cart['products'] = $products;

	return $min_amount_warns;
}

#
# This function counts the total quantity of products in the cart
#
function cw_cart_count_items(&$cart) {
	if (empty($cart) || empty($cart['products'])) return 0;

	$count = 0;
	foreach ($cart['products'] as $product) {
		$count += $product['amount'];
	}

	return $count;
}

function cw_save_customer_cart($customer_id, $cart) {
	global $tables;

	if (!empty($customer_id))
		db_query("update $tables[customers_customer_info] set cart='".addslashes(serialize($cart))."' where customer_id='$customer_id'");
}

?>
