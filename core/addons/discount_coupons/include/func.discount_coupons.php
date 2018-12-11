<?php
function cw_discount_coupons_is_valid($coupon, $products) {
	global $tables, $customer_id, $config;

	$my_coupon = cw_query_first("select * from $tables[discount_coupons] where coupon='$coupon' and status=1 AND expire>".cw_core_get_time());
# kornev, may be it's salesman discount
    $salesman_discount = false;
    if (!$my_coupon) {
        $my_coupon = cw_query_first("select * from $tables[discount_coupons] where coupon='$coupon' and status=1 and salesman_customer_id='$customer_id'");
        $salesman_discount = true;
    }

	if (!$my_coupon)
		return 1;

	if ($my_coupon['per_user']) {
		if (empty($cutomer_id))
			return 1;
		$_times_used = cw_query_first_cell("select times_used from $tables[discount_coupons_cutomer_id] where coupon='$coupon' and cutomer_id='$cutomer_id'");
		if ($_times_used >= $my_coupon['times'])
			return 5;
	}

	if ($my_coupon['coupon_type'] == "percent" && $my_coupon['discount'] > 100)
		return 1;

	if ($my_coupon['product_id'] > 0) {
		$found = false;

		foreach ($products as $value) {
			if ($value['product_id'] == $my_coupon['product_id'])
				$found = true;
		}

		return ($found ? 0 : 4);
	} elseif ($my_coupon['category_id'] > 0) {
		$found = false;

		$category_ids[] = $my_coupon['category_id'];

		if ($my_coupon['recursive'])
			$category_ids[] = cw_category_get_path($my_coupon['category_id']);

		if (!is_array($products))
			return 4;

		if ($config['Appearance']['categories_in_products'] == '1') {
			foreach ($products as $value) {
				$product_categories = cw_query("SELECT category_id FROM $tables[products_categories] WHERE product_id='$value[product_id]'");
				$is_valid_product = false;
				foreach ($product_categories as $k=>$v) {
					if (in_array($v['category_id'], $category_ids)) {
						$is_valid_product = true;
						break;
					}
				}
				if ($is_valid_product) {
					$found = true;
					break;
				}
			}
		}

		return ($found ? 0 : 4);
	} else {
		$total = 0;

		if (!empty($products) && is_array($products))
        foreach ($products as $value)
            $total += $value['price']*$value['amount'];

		if ($total < $my_coupon['minimum'])
			return 3;
		else
			return 0;
	}

	return 0;
}

function cw_discount_coupons_cart_calc_discounts($params, $return) {
    global $tables, $config, $addons, $global_store;
    extract($params);
	$coupon = $cart['info']['coupon'];

    foreach($products as $k=>$product) {
        if ($product['hidden']) continue;
        $products[$k]['coupon_discount'] = 0;
    }

    $return['coupon_discount'] = 0;
    $return['coupon'] = 0;

    if (!empty($coupon)) {

        $coupon_total = 0;
        $coupon_amount = 0;

        cw_load('salesman');
        $is_salesman_coupon = cw_is_salesman_coupon($discount_coupon);
        if ($is_salesman_coupon) {
            $discount_coupon_data = cw_query_first("select * from $tables[discount_coupons] where coupon='$discount_coupon'");
            if ($discount_coupon_data['from_account']) {
                $discount_coupon_data['discount'] = cw_get_salesman_discount($products, $discount_coupon, $membership_id, $warehouse);
                $discount_coupon_data['coupon_type'] = 'absolute';
            }
        }
        else {
            if (!empty($global_store['discount_coupons'])) {
                $discount_coupon_data = array();
                foreach ($global_store['discount_coupons'] as $v) {
                    if ($v['__override'] || ($v['coupon'] == $coupon && $v['warehouse_customer_id'] == $warehouse)) {
                        $discount_coupon_data = $v;
                        break;
                    }
                }
            }
            else
                $discount_coupon_data = cw_query_first("select * from $tables[discount_coupons] where coupon='$coupon'");
        }
        $return['discount_coupon_data'] = $discount_coupon_data;

        $return['coupon_type'] = $discount_coupon_data['coupon_type'];

        if (!empty($discount_coupon_data) && (($discount_coupon_data['coupon_type'] == 'absolute') || ($discount_coupon_data['coupon_type'] == 'percent'))) {
            $coupon_discount = 0;
            if ($discount_coupon_data['product_id'] > 0) {
                foreach($products as $k=>$product) {

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

						if ($config['Appearance']['categories_in_products'] == '1') {
							$product_categories = cw_query("SELECT category_id FROM $tables[products_categories] WHERE product_id='$product[product_id]'");
							$is_valid_product = false;
							foreach ($product_categories as $pc) {
								if (in_array($pc['category_id'], $category_ids)) {
									$is_valid_product = true;
									break;
								}
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

					if ($config['Appearance']['categories_in_products'] == '1') {
						$product_categories = cw_query("SELECT category_id FROM $tables[products_categories] WHERE product_id='$product[product_id]'");
						$is_valid_product = false;
						foreach ($product_categories as $pc) {
							if (in_array($pc['category_id'], $category_ids)) {
								$is_valid_product = true;
								break;
							}
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
        }

        if (isset($coupon_discount_orig))
            $return['coupon_discount_orig'] = $coupon_discount_orig;
        else
            $return['coupon_discount_orig'] = $return['coupon_discount'];

        $return['products'] = $products;
    }

    return $return;
}

?>
