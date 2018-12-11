<?php
cw_load('cart');

function cw_taxes_calc_simple(&$price, $taxes, $amount = 1) {

    $formula_data['ST'] = $price;
    $taxed_price = $price;

    foreach ($taxes as $k=>$tax_rate) {
        if (!empty($tax_rate['skip']))
            continue;

        $assessment = cw_cart_calc_assessment($tax_rate['formula'], $formula_data);

        if ($tax_rate['rate_type'] == "%") {
            $tax_rate['tax_value_precise'] = price_format($assessment * $tax_rate['rate_value'] / 100, true);
            $tax_rate['tax_value'] = $tax_rate['tax_value_precise'];
        }
        else 
            $tax_rate['tax_value'] = $tax_rate['tax_value_precise'] = price_format($tax_rate['rate_value'], true);

        $tax_rate['taxed_price'] = $price + $tax_rate['tax_value'];

        if ($tax_rate['display_including_tax']) 
            $taxed_price += $tax_rate['tax_value'];

        $taxes[$k] = $tax_rate;

        $formula_data[$k] = $tax_rate['tax_value'];
    }

    if (is_array($taxes))
    foreach ($taxes as $k=>$v)
        $taxes[$k]['tax_value'] = $v['tax_value_precise'] * $amount;

    $price = $taxed_price;

    return $taxes;
}

function cw_taxes_price_without_tax($price, $taxes) {
    $price_deducted_tax_flag = "";

    foreach ($taxes as $k=>$tax_rate) {
        if (!$tax_rate['price_includes_tax']) continue;
        
        if (!preg_match("!\b(DST|ST)\b!", $tax_rate['formula']))
            continue;

        if ($tax_rate['rate_type'] == "%") {
            $_tax_value = $price - $price*100/($tax_rate['rate_value'] + 100);
            $price = $price - $_tax_value;
        }
        else {
            $price = $price - $tax_rate['rate_value'];
        }
        $price_deducted_tax_flag = "Y";
    }
    return $price;
}

# This function gathers the product taxes information
function cw_get_products_taxes(&$product, $user_info, $calculate_discounted_price=false, $taxes='', $include_in_any_case = false) {
	global $config;

    $amount = ($product['amount'] > 0 ? $product['amount'] : 1);

    $clear_price = $product['price'];
	if ($calculate_discounted_price && isset($product['discounted_price']))
		$tps = $taxed_price = $product['discounted_price'] / $amount;
	else
		$tps = $taxed_price = $product['price'];

	if (empty($taxes)) $taxes = cw_get_product_tax_rates($product, $user_info, $include_in_any_case);

    if ($include_in_any_case && is_array($taxes))
    foreach($taxes as $k=>$v) $taxes[$k]['display_including_tax'] = 1;

    $clear_price = cw_taxes_price_without_tax($product['price'], $taxes);
    
    if ($product['free_tax'] == 'Y') {
    	$taxes_ = $taxes;
    }
    else {
    	$taxes_ = cw_taxes_calc_simple($taxed_price, $taxes, $amount);
    }

    if ($clear_price != $tps && $product['free_tax'] != 'Y') {
    	$taxes_ = cw_taxes_calc_simple($clear_price, $taxes, $amount);
    }
    else {
    	$clear_price = $taxed_price;
    }

	$product['taxed_price'] = price_format($taxed_price, true);
    $product['taxed_clear_price'] = price_format($clear_price, true);

    $product['display_price'] = $clear_price;

	return $taxes_;
}

#
# This function generate the product tax rates array
#
function cw_get_product_tax_rates($product, $user_info, $include_in_any_case = false, $special_taxes = false) {
	global $tables, $config;
	static $saved_tax_rates = array();
    static $saved_tax = array();

	# Define input data
	$is_array = true;
    if (is_int($product)) {
        $is_array = false;
        $_product = array($product => array('product_id' => $product));
    } 
    elseif (isset($product['product_id'])) {
		$is_array = false;
		$_product = array($product['product_id'] => $product);

	} else {
		$_product = array();
		foreach ($product as $k => $p)
			$_product[$p['product_id']] = $p;
	}

	unset($product);

    $zone_account = $user_info;

    $company_id = $user_info['company_id'];

    if (isset($saved_tax[$company_id])) return $saved_tax[$company_id];

    if ($special_taxes)
        $_taxes = cw_func_call('cw_taxes_search', array('product_id' => $product['product_id'], 'data' => array('active' => 1, 'use_info' => 1)));
    else
        $_taxes = cw_func_call('cw_taxes_search', array('product_id' => $product['product_id'], 'data' => array('active' => 1)));

    $taxes = array();
    if (is_array($_taxes))
    foreach($_taxes as $k=>$v) {
        if ($v['address_type'] == 'O') {
            $v['address_type_real'] = 'O';
            $v['address_type'] = 'B';
            $taxes[] = $v;
            $v['address_type'] = 'S';
        }
        $taxes[] = $v;
    }

	if (empty($taxes) || !is_array($taxes))
		return array();

	# Define available customer zones
	$tax_rates = $address_zones = $_tax_names = array();

    $display_including_tax = null;
    if ($include_in_any_case)
        $display_including_tax = 1;

	foreach ($taxes as $k => $v) {
	    $_tax_names["tax_".$v['tax_id']] = true;
        if ($v['address_type_real'] == 'O')
            $taxes[$k]['tax_name'] = $v['tax_name'].'_'.$v['address_type'];
        if (isset($display_including_tax)) $taxes[$k]['display_including_tax'] = $display_including_tax;
    }

	# Get tax names
	$_tax_names = cw_get_languages_alt(array_keys($_tax_names));

	if ($config['Taxes']['enable_user_tax_exemption'] == "Y") {
		#
		# Get the 'tax_exempt' feature of customer
		#
		static $_customer_tax_exempt;

		if (empty($_customer_tax_exempt))
			$_customer_tax_exempt = cw_query_first_cell("select tax_exempt from $tables[customers_customer_info] where customer_id='$use_customer_id'");

		if ($_customer_tax_exempt == "Y")
			$tax_rate['skip'] = true;
	}
	else
		$_customer_tax_exempt = "";

	foreach ($_product as $product_id => $product) {
		if ($product['free_tax'] == 'Y') // || !is_array($_taxes[$product_id]) || empty($_taxes[$product_id]))
			continue;

# kornev, because all of the products are related with admin and we should get the appropriate settings
        $admin_warehouse = cw_get_default_account_for_zones();
		# Generate tax rates array

		foreach ($taxes as $k => $v) {
            $product['warehouse'] = $admin_warehouse;
			if (!isset($address_zones[$product['warehouse']][$v['address_type']])) {
                $address = ($v['address_type'] == 'B'?$zone_account['main_address']:$zone_account['current_address']);
				$address_zones[$product['warehouse']][$v['address_type']] = array_keys(cw_call('cw_cart_get_zones', array('address' =>$address)));
			}
			$zones = $address_zones[$product['warehouse']][$v['address_type']];

			$tax_rate = array();
# kornev. one rate can be repeated twice. the "both" address mode.
			if (!empty($zones) && is_array($zones)) {
				foreach ($zones as $zone_id) {

                $saved_key = $v['tax_id'].'_'.$v['address_type'].'_'.$zone_id.'_'.$membership_id;
                if (isset($saved_tax_rates[$saved_key])) {
                    $tax_rate = $saved_tax_rates[$saved_key];
                }
                else {
                    $tax_rate = cw_query_first($sql="SELECT $tables[tax_rates].tax_id, $tables[tax_rates].formula, $tables[tax_rates].rate_value, $tables[tax_rates].rate_type FROM $tables[tax_rates] LEFT JOIN $tables[tax_rate_memberships] ON $tables[tax_rate_memberships].rate_id = $tables[tax_rates].rate_id WHERE $tables[tax_rates].tax_id = '$v[tax_id]' $warehouse_condition AND $tables[tax_rates].zone_id = '$zone_id' AND ($tables[tax_rate_memberships].membership_id = '$membership_id' OR $tables[tax_rate_memberships].membership_id IS NULL) ORDER BY $tables[tax_rate_memberships].membership_id DESC LIMIT 1");

                        if ($tax_rate) {
                            $tax_rate['address_type'] = $v['address_type'];
                            $tax_rate['address_type_real'] = $v['address_type_real'];
                        }
                        $saved_tax_rates[$saved_key] = $tax_rate;
					}

					if (!empty($tax_rate))
						break;
				}
			}

			if (empty($tax_rate) || $_customer_tax_exempt == "Y") {
                if (!$v['price_includes_tax'] != "Y") continue;
				$tax_rate = cw_query_first("SELECT $tables[tax_rates].tax_id, $tables[tax_rates].formula, $tables[tax_rates].rate_value, $tables[tax_rates].rate_type FROM $tables[tax_rates] LEFT JOIN $tables[tax_rate_memberships] ON $tables[tax_rate_memberships].rate_id = $tables[tax_rates].rate_id WHERE $tables[tax_rates].tax_id='$v[tax_id]' $warehouse_condition AND ($tables[tax_rate_memberships].membership_id = '$membership_id' OR $tables[tax_rate_memberships].membership_id IS NULL) ORDER BY $tables[tax_rates].rate_value DESC LIMIT 1");
				$tax_rate['skip'] = true;
			}

			if (empty($tax_rate['formula']))
				$tax_rate['formula'] = $v['formula'];

			$tax_rate['rate_value'] *= 1;
			$tax_rate['tax_display_name'] = isset($_tax_names["tax_".$v['tax_id']]) ? $_tax_names["tax_".$v['tax_id']].($v['address_type_real'] == 'O'?($v['address_type'] == 'B'?' '.cw_get_langvar_by_name('lbl_tax_billing'):' '.cw_get_langvar_by_name('lbl_tax_shipping')):'') : $v['tax_name'];
			if ($is_array) {
				$tax_rates[$product_id][$v['tax_name']] = cw_array_merge($v, $tax_rate);
			} else {
				$tax_rates[$v['tax_name']] = cw_array_merge($v, $tax_rate);
			}
		}
	}

    $saved_tax[$company_id] = $tax_rates;
	return $tax_rates;
}

#
# This function get the taxed price
#
function cw_tax_price($price, $user_info = '', $product_id=0, $disable_abs=false, $discounted_price=NULL, $taxes="", $price_deducted_tax=false) {
	global $tables, $config, $addons, $current_language;

	$return_taxes = array();

	$no_discounted_price = false;
	if (empty($discounted_price)) {
		$discounted_price = $price;
		$no_discounted_price = true;
	}

	if ($product_id > 0) {
		$product = cw_query_first("SELECT product_id, free_shipping, shipping_freight, distribution, '$price' as price FROM $tables[products] WHERE product_id='$product_id'");
		$taxes = cw_get_product_tax_rates($product, $user_info);
	}
	
	$total_tax_cost = 0;

	if (is_array($taxes)) {
		#
		# Calculate price and tax_value
		#
		foreach ($taxes as $k=>$tax_rate) {
			if (!$tax_rate['price_includes_tax'] || $price_deducted_tax)
    			if (!$price_includes_tax || $price_deducted_tax) continue;

			if (!preg_match("!\b(DST|ST)\b!S", $tax_rate['formula']))
				continue;

			if ($tax_rate['rate_type'] == "%") {
				$_tax_value = $price - $price*100/($tax_rate['rate_value'] + 100);
				$price -= $_tax_value;
				if ($discounted_price > 0)
					$_tax_value = $discounted_price - $discounted_price*100/($tax_rate['rate_value'] + 100);

				$discounted_price -= $_tax_value;

			}
			else {
				$price -= $tax_rate['rate_value'];
				$discounted_price -= $tax_rate['rate_value'];
			}
		}

		$taxed_price = $discounted_price;

		$formula_data['ST'] = $price;
		if (!$no_discounted_price)
			$formula_data['DST'] = $discounted_price;

		foreach ($taxes as $k=>$v) {
			if (!empty($v['skip']))
				continue;

			if (!$v['display_including_tax']) continue;

			if ($v['rate_type'] == "%") {
				$assessment = cw_cart_calc_assessment($v['formula'], $formula_data);
				$tax_value = $assessment * $v['rate_value'] / 100;
			}
			elseif (!$disable_abs) {
				$tax_value = $v['rate_value'];
			}

			$formula_data[$v['tax_name']] = $tax_value;

			$total_tax_cost += $tax_value;

			$taxed_price += $tax_value;

			$return_taxes['taxes'][$v['tax_id']] = $tax_value;
		}
	}

	$return_taxes['taxed_price'] = $taxed_price;

	return $return_taxes;
}

#
# This function cacluate the assessment according to the formula string
#
function cw_cart_calc_assessment($formula, $formula_data) {
	$return = 0;
	if (is_array($formula_data)) {
		# Correct the default values...
		if (is_null($formula_data['DST']))
			$formula_data['DST'] = $formula_data['ST'];

		if (empty($formula_data['SH']))
			$formula_data['SH'] = 0;
        if (empty($formula_data['PM']))
            $formula_data['PM'] = 0;

		# Preparing math expression...
		$_formula = $formula;
		foreach ($formula_data as $unit=>$value) {
			if (!is_numeric($value))
				$value = 0;

			$_formula = preg_replace("/\b".preg_quote($unit,"/")."\b/S", $value, $_formula);
		}

        if (!$_formula) return '';

		$to_eval = "\$return = $_formula;";
		# Perform math expression...
		eval($to_eval);
	}

	return $return;
}


function cw_taxes_search($params, $return = null) {

    extract($params);

    global $tables, $current_language;

    $fields = $from_tbls = $query_joins = $where = $groupbys = $having = $orderbys = array();

    $from_tbls[] = 'taxes';
    $fields[] = "$tables[taxes].*";

    if (!empty($product_id)) {
        $fields[] = "$product_id as product_id";
    }

# kornev, merge standart and additional variables
    if ($return)
    foreach ($return as $saname => $sadata)
        if (isset($$saname) && is_array($$saname) && empty($$saname)) $$saname = $sadata;

    if ($data['rates_count']) {
        $fields[] = "COUNT($tables[tax_rates].tax_id) as rates_count";
        $query_joins[$tables['tax_rates']] = array(
            'tblname' => "tax_rates",
            'on' => "$tables[tax_rates].tax_id=$tables[taxes].tax_id"
        );
        $groupbys[] = "$tables[taxes].tax_id";
    }

    if (isset($data['active']))
        $where[] = "$tables[taxes].active='$data[active]'";

    if (isset($data['use_info']))
        $where[] = "$tables[taxes].use_info='$data[use_info]'";


    if (!empty($data['sort_field'])) {
        $direction = $data['sort_direction'] ? 'DESC' : 'ASC';
        $orderbys[] = $data['sort_field'].' '.$direction;
    }
    else
        $orderbys[] = 'priority';

    $search_query = cw_db_generate_query($fields, $from_tbls, $query_joins, $where, $groupbys, $having, $orderbys);
    return cw_query($search_query);
}
