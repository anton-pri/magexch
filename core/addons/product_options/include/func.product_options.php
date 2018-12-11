<?php
function cw_product_options_tabs_js_abstract($params, $return) {
    if ($return['name'] == 'product_data') {
        if (AREA_TYPE != 'A') return $return;

        $return['js_tabs']['product_options'] = array(
            'title' => cw_get_langvar_by_name('lbl_product_options'),
            'template' => 'addons/product_options/main/products/product/options.tpl',
        );
# kornev, need a condition here
        $return['js_tabs']['product_variants'] = array(
            'title' => cw_get_langvar_by_name('lbl_product_variants'),
            'template' => 'addons/product_options/main/products/product/variants.tpl',
        );
    }

    return $return;
}

function cw_product_options_product_build_flat($params, $return) {
    global $tables;

    $return['query_joins']['variants'] = array(
        'on' => "$tables[product_variants].product_id = $tables[products].product_id",
    );
    $return['query_joins']['products_warehouses_amount'] = array(
        'on' => "$tables[products_warehouses_amount].warehouse_customer_id=0 and $tables[products_warehouses_amount].variant_id = $tables[product_variants].variant_id and $tables[products_warehouses_amount].product_id=$tables[product_variants].product_id",
    );
    $return['query_joins']['product_options'] = array(
        'on' => "$tables[product_options].product_id = $tables[products].product_id",
    );

    $return['fields'][] = "IF($tables[product_variants].variant_id IS NULL, '', IF(MAX(pwa.avail) = 0, 'E', 'Y')) AS is_variants";
    $return['fields'][] = "IF($tables[product_options].product_id IS NULL, '', 'Y') AS is_product_options";

    return $return;
}

# kornev, here we will build the attribtutes for the integration with the attrribute filter
function cw_product_options_product_build_flat_post($params, $return) {
    global $config, $tables;

    extract($params);

    $where = "";
    if (!$product_id) $product_id = cw_query_column("select product_id from $tables[products]");
    if (!is_array($product_id)) $product_id = array($product_id);

    $index = 0;
    foreach($product_id as $pid) {
# kornev, only available options - we are preparing the customer data
# kronev, since it's duplicates - we ahould make it short
        $options = cw_query("select field, name, product_option_id from $tables[product_options] where product_id='$pid' and avail=1");
        if (!$options) continue;

        foreach($options as $option) {
            $field = cw_call('cw_attributes_cleanup_field', array('product_options_'.$option['field']));
            $att = cw_call('cw_attributes_filter', array(array('field'=>$field, 'addon'=>'product_options'), true));
            $att_id = $att['attribute_id'];
            if($attribute_field && !$att_id){
                $att_id = cw_call('cw_attributes_get_attribute_by_field', array($attribute_field));
                if($att_id)  cw_array2update('attributes', array('name' => $option['name'],'field' => $field, 'orderby' => $option['orderby']), "attribute_id='$att_id'");
            }
# kornev, we are making this if there are no attribute only
# kornev, then the customer is able to re-name it

            if (!$att_id)
                $att_id = cw_func_call('cw_attributes_create_attribute', array(
                    'data' => array(
                        'name' => $option['name'],
                        'field' => 'product_options_'.$option['field'],
                        'type' => 'multiple_selectbox',
                        'active' => 1,
                        'is_show' => 1,
                        'orderby' => $option['orderby'],
                        'addon' => 'product_options',
                        'item_type' => 'P',
# kornev, do not show on modification pages
                        'is_show_addon' => 0,
                        'protection' => ATTR_PROTECTION_FIELD | ATTR_PROTECTION_TYPE | ATTR_PROTECTION_DELETE,
                    ),
                    'language' => $config['default_admin_language']
                ));

            $lngs = cw_query("select name, code from $tables[product_options_lng] where product_option_id='$option[product_option_id]'");
            if ($lngs)
            foreach($lngs as $lng)
                if (!cw_query_first_cell("select count(*) from $tables[attributes_lng] where code='$lng[code]' and attribute_id='$att_id'")) cw_func_call('cw_attributes_update_lng', array('attribute_id' => $att_id, 'data' => $lng, 'language' => $lng['code']));

            $values = cw_query("select orderby, avail as active, name as value, option_id from $tables[product_options_values] where product_option_id='$option[product_option_id]' and avail=1");
# kornev, in the product_options case we should remove all of the values firstly
# kornev, we should cleanup the appropriate attribute only;
            cw_attributes_cleanup($pid, 'P', null, $att_id);
# kornev, we should add the default values - because in the multi-select the translation is stored here
# kornev, the attributes values store the attribute_value_id actually in the value field
            $att_values = array();
            if ($values)
            foreach($values as $att_value) {
# kornev, try to define the attribute_value_id by value
                $att_value['attribute_value_id'] = cw_query_first_cell("select attribute_value_id from $tables[attributes_default] where attribute_id='$att_id' and value='".addslashes($att_value['value'])."'");

                $att_values[] = $att_value['attribute_value_id'] = cw_call('cw_attributes_update_default_value', array('attribute_id' => $att_id, 'data' => $att_value, 'language' => $config['default_admin_language']));

                $lngs = cw_query("select name as value, code from $tables[product_options_values_lng] where option_id='$att_value[option_id]'");
                if ($lngs)
                foreach($lngs as $lng) {
                    $att_value = array_merge($att_value, $lng);
                    cw_call('cw_attributes_update_default_value', array('attribute_id' => $att_id, 'data' => $att_value, 'language' => $lng['code']));
                }
# kornev, now store the value
            }
            
            if ($att_values) {
                cw_func_call('cw_attributes_save_attribute', array('item_id' => $pid, 'item_type' => 'P', 'attributes' => array($field => $att_values)));
			}

            $index++;
            if ($tick > 0 && $updated % $tick == 0) cw_flush('.');
            if ($tick > 0  && ($updated/$tick) % 100 == 0) cw_flush('<br/>');
        }
    }
}

function cw_get_product_classes($product_id, $user_info = array(), $area = false, $language = '') {
	global $tables, $current_area;

	cw_load('taxes');

    $language = $language?$language:$current_language;
	$area = $area?$area:$current_area;

	$where = '';
	if ($area == 'C')
		$where = "AND $tables[product_options].avail = 1";

	$classes = cw_query($sql="SELECT $tables[product_options].*, IF($tables[product_options_lng].name != '', $tables[product_options_lng].name, $tables[product_options].name) as name FROM $tables[product_options] LEFT JOIN $tables[product_options_lng] ON $tables[product_options].product_option_id = $tables[product_options_lng].product_option_id AND $tables[product_options_lng].code = '$language' WHERE $tables[product_options].product_id = '$product_id' $where ORDER BY $tables[product_options].orderby");

	if (empty($classes))
		return false;

	if ($area == 'C') {
		$product = cw_query_first("SELECT product_id, free_shipping, shipping_freight, distribution, free_tax FROM $tables[products] WHERE product_id='$product_id'");
		$taxes = cw_get_product_tax_rates($product, $user_info);
	}

	$where = "";
	if ($area == 'C') {
		$where = "AND $tables[product_options_values].avail = 1";
	}

	# Get options
	foreach ($classes as $kc => $class) {
		if ($class['type'] == 'T')
			continue;

        $classes[$kc]['options'] = cw_query_hash("SELECT $tables[product_options_values].*, ifnull($tables[product_options_values_lng].name,  $tables[product_options_values].name) as name FROM $tables[product_options_values] LEFT JOIN $tables[product_options_values_lng] ON $tables[product_options_values].option_id = $tables[product_options_values_lng].option_id AND $tables[product_options_values_lng].code = '$language' WHERE $tables[product_options_values].product_option_id = '$class[product_option_id]' $where ORDER BY $tables[product_options_values].orderby, $tables[product_options_values].option_id ASC", "option_id", false);

        if (@count($classes[$kc]['options']) == 0) {
			if ($area == 'C')
				unset($classes[$kc]);
			continue;
		}

		# Calculate taxes for price modificators
		foreach ($classes[$kc]['options'] as $ko => $option) {
			$classes[$kc]['options'][$ko]['option_id'] = $ko;

			if ($class['type'] == 'Y' && $area == 'C' && $option['price_modifier'] != 0) {
				$_taxes = cw_tax_price($option['price_modifier'], 0, true, NULL, "", $taxes);
				if ($option['modifier_type'] == '$') {
					$classes[$kc]['options'][$ko]['price_modifier'] = $_taxes['taxed_price'];
				}

				$classes[$kc]['options'][$ko]['taxes'] = $_taxes['taxes'];
			}
		}
	}

	return $classes;
}

#
# Get product variants
#
function cw_get_product_variants($product_id, $membership_id = 0, $area = false) {
	global $tables, $current_area, $current_language, $keys, $cart, $user_account, $addons, $user_account;

	cw_load('files', 'taxes');

	$keys = cw_get_hash_options($product_id);
	if ($area === false)
		$area = $current_area;

	if ($area != 'C' || !$addons['wholesale_trading'])
		$products_prices_membership = "= 0";
    else
		$products_prices_membership = "IN (0, '$user_account[membership_id])')";

    $fields[] = "$tables[products_warehouses_amount].avail";
    $fields[] = "$tables[products_warehouses_amount].avail_ordered";
    $fields[] = "$tables[products_warehouses_amount].avail_sold";
    $fields[] = "$tables[products_warehouses_amount].avail_reserved";
    if ($current_area == 'C')
        $sql = "SELECT $tables[product_variants].*, $tables[products_prices].price, IF($tables[products_images_var].id IS NULL, '', 'Y') as is_image, $tables[products_images_var].image_path as image_path_W, ".implode(", ", $fields)." FROM $tables[product_variants] LEFT JOIN $tables[products_prices] ON $tables[product_variants].product_id = $tables[products_prices].product_id AND $tables[products_prices].variant_id = $tables[product_variants].variant_id AND $tables[products_prices].membership_id $products_prices_membership AND $tables[products_prices].quantity = 1 LEFT JOIN $tables[products_warehouses_amount] on $tables[products_warehouses_amount].product_id=$tables[product_variants].product_id and $tables[products_warehouses_amount].variant_id=$tables[product_variants].variant_id and $tables[products_warehouses_amount].warehouse_customer_id='".(AREA_TYPE=='P'?$user_account['warehouse_customer_id']:0)."' LEFT JOIN $tables[products_images_var] ON $tables[products_images_var].id = $tables[product_variants].variant_id WHERE $tables[product_variants].product_id = '$product_id' GROUP BY $tables[product_variants].variant_id";
    else
        $sql="SELECT $tables[product_variants].*, $tables[products_prices].price, IF($tables[products_images_var].id IS NULL, '', 'Y') as is_image, $tables[products_images_var].image_path as image_path_W, ".implode(", ", $fields)." FROM $tables[product_variants] LEFT JOIN $tables[products_prices] ON $tables[product_variants].product_id = $tables[products_prices].product_id AND $tables[products_prices].variant_id = $tables[product_variants].variant_id LEFT JOIN $tables[products_warehouses_amount] on $tables[products_warehouses_amount].product_id=$tables[product_variants].product_id and $tables[products_warehouses_amount].variant_id=$tables[product_variants].variant_id and $tables[products_warehouses_amount].warehouse_customer_id='".(AREA_TYPE=='P'?$user_account['warehouse_customer_id']:0)."' LEFT JOIN $tables[products_images_var] ON $tables[products_images_var].id = $tables[product_variants].variant_id WHERE $tables[product_variants].product_id = '$product_id' GROUP BY $tables[product_variants].variant_id";

    $variants = cw_query_hash($sql, 'variant_id', false);

	if (!$variants) return false;

	if ($area == 'C') {

		# Check variants' items
		$counts = cw_query_column("SELECT COUNT($tables[product_variant_items].option_id) FROM $tables[product_variant_items], $tables[product_variants], $tables[product_options_values], $tables[product_options] WHERE $tables[product_variant_items].variant_id = $tables[product_variants].variant_id AND $tables[product_variants].product_id = '$product_id' AND $tables[product_variant_items].option_id = $tables[product_options_values].option_id AND $tables[product_options].product_option_id= $tables[product_options_values].product_option_id AND $tables[product_options_values].avail = 1 AND $tables[product_options].avail = 1 GROUP BY $tables[product_variant_items].variant_id");
		if (empty($counts) || count($counts) < count($variants)) {
			return false;

		} else {
			$counts = array_unique($counts);
			if (count($counts) != 1)
				return false;

		}

		$chains = cw_query_hash("SELECT $tables[product_variant_items].* FROM $tables[product_variant_items], $tables[product_variants], $tables[product_options_values], $tables[product_options] WHERE $tables[product_variant_items].variant_id = $tables[product_variants].variant_id AND $tables[product_variants].product_id = '$product_id' AND $tables[product_variant_items].option_id = $tables[product_options_values].option_id AND $tables[product_options].product_option_id = $tables[product_options_values].product_option_id AND $tables[product_options_values].avail = 1 AND $tables[product_options].avail = 1", "variant_id", true, true);

	} else {
		$chains = cw_query_hash("SELECT $tables[product_variant_items].* FROM $tables[product_variant_items], $tables[product_variants], $tables[product_options_values] WHERE $tables[product_variant_items].variant_id = $tables[product_variants].variant_id AND $tables[product_variants].product_id = '$product_id' AND $tables[product_variant_items].option_id = $tables[product_options_values].option_id", "variant_id", true, true);
	}

	if (empty($chains))
		return false;

	# Get variants' wholesale prices
	$prices = array();
	if ($addons['wholesale_trading']) {
		$products_prices_membership = "";
		$min_amount = 1;
		if ($area == 'C') {
			$min_amount = intval(cw_query_first_cell("SELECT min_amount FROM $tables[products] WHERE product_id = '$product_id'"));
            $products_prices_membership = "AND membership_id IN (0, '$user_account[membership_id]')";
		}

		$prices = cw_query_hash("select *, price from $tables[products_prices] as pps where pps.product_id = '$product_id' AND pps.variant_id > 0 $products_prices_membership GROUP BY variant_id, quantity, membership_id ORDER BY quantity", "variant_id");

		if (!empty($prices)) {
		foreach ($prices as $vid => $ps) {
		    $last_key = false;
			foreach ($ps as $pid => $p) {
					cw_unset($ps[$pid], "product_id");
					if ($last_key !== false) {
						$ps[$last_key]['next_quantity'] = $p['quantity'];

						if ($area == 'C') {
							if ($min_amount > $ps[$last_key]['next_quantity']) {
								unset($ps[$last_key]);

							} elseif ($min_amount > $ps[$last_key]['quantity']) {
								$ps[$last_key]['quantity'] = $min_amount;
							}

						}
					}
					$last_key = $pid;
				}

				if (empty($ps)) {
					unset($prices[$vid]);
					continue;
				}

				$ps[$pid]['next_quantity'] = 0;

				$prices[$vid] = $ps;
			}
		}
	}

	$product = cw_query_first("SELECT product_id, free_shipping, shipping_freight, distribution, free_tax FROM $tables[products] WHERE product_id='$product_id'");
	$taxes = cw_get_product_tax_rates($product, $user_account);

	foreach ($variants as $kv => $variant) {
		# Get references to option array
		if (empty($chains[$kv])) {
			if ($area == "C")
				unset($variants[$kv]);
			continue;
		}

		# Get wholesale prices
		if (isset($prices[$kv])) {
			$variants[$kv]['wholesale'] = $prices[$kv];
			$variants[$kv]['wholesale'][0]['price'] = $variant['price'];
			unset($prices[$kv]);

			if ($area == 'C') {
				$last_price = $variant['price'];
				foreach($variants[$kv]['wholesale'] as $wpk => $wpv) {
					if ($wpv['price'] > $last_price) {
						unset($variants[$kv]['wholesale'][$wpk]);
						continue;
					}

					$last_price = $wpv['price'];
				}

				if (empty($variants[$kv]['wholesale'])) {
					unset($variants[$kv]['wholesale']);

				} else {
					$variants[$kv]['wholesale'] = array_values($variants[$kv]['wholesale']);
				}
			}
		}

		if ($area == "C") {
			if ($variant['is_image'] == 'Y')
                $variants[$kv]['image'] = cw_image_get('products_images_var', $kv);

			# Get variant's tax rates
			$_taxes = cw_tax_price($variant['price'], 0, true, NULL, "", $taxes);
			$variants[$kv]['taxed_price'] = $_taxes['taxed_price'];
			if (!empty($_taxes['taxes']))
				$variants[$kv]['taxes'] = $_taxes['taxes'];

			if (!empty($variants[$kv]['wholesale'])) {

				# Get variant's wholesale prices' tax rates
				foreach ($variants[$kv]['wholesale'] as $k => $v) {
					$_taxes = cw_tax_price($v['price'], 0, true, NULL, "", $taxes);
					$variants[$kv]['wholesale'][$k]['taxed_price'] = $_taxes['taxed_price'];
					if (!empty($_taxes['taxes']))
						$variants[$kv]['wholesale'][$k]['taxes'] = $_taxes['taxes'];

                    # Get variant's taxed list_price
                    if ($v['list_price']) {
                        $_taxes = cw_tax_price($v['list_price'], 0, true, NULL, "", $taxes);
                        $variants[$kv]['wholesale'][$k]['list_price'] = $_taxes['taxed_price'];
                    }

				}
			}

			if (!empty($cart['products']) && is_array($cart['products'])) {
				foreach ($cart['products'] as $v) {
					if ($v['product_id'] != $product_id)
						continue;

					if ($kv == cw_get_variant_id($v['options'], $product_id))
						$variants[$kv]['avail'] -= $v['amount'];
				}
			}
		}
        elseif ($variant['is_image'] == 'Y') $variants[$kv]['image'] = cw_image_get('products_images_var', $kv);



		$variants[$kv]['options'] = array();
		foreach ($chains[$kv] as $oid) {
			$variants[$kv]['options'][$oid] = $keys[$oid];
		}

		if (empty($variants[$kv]['options']) && $area == "C") {
			unset($variants[$kv]);
            continue;
        }
	}

	return $variants;
}

#
# Get product exceptions
#
function cw_get_product_exceptions($product_id, $area = false) {
	global $tables, $current_area, $current_language;

	$keys = cw_get_hash_options($product_id);
	if ($area === false)
		$area = $current_area;

	$avail_condition = '';
	if ($area == 'C')
		$avail_condition = " AND $tables[product_options].avail = 1 AND $tables[product_options_values].avail = 1";

	$exceptions = cw_query("SELECT $tables[products_options_ex].* FROM $tables[products_options_ex], $tables[product_options], $tables[product_options_values] WHERE $tables[product_options].product_option_id = $tables[product_options_values].product_option_id AND $tables[product_options_values].option_id = $tables[products_options_ex].option_id AND $tables[product_options].product_id = '$product_id'".$avail_condition." GROUP BY $tables[products_options_ex].exception_id, $tables[products_options_ex].option_id ORDER BY $tables[product_options].orderby");

	$return = array();
    if ($exceptions)
	foreach ($exceptions as $exception) {
		if (!isset($return[$exception['exception_id']]))
			$return[$exception['exception_id']] = array();

		$return[$exception['exception_id']][$exception['option_id']] = $keys[$exception['option_id']];
	}

	return $return;
}

#
# Get product JS code
#
function cw_get_product_js_code($product_id) {
	global $tables;

	return cw_query_first_cell("SELECT content FROM $tables[product_options_js] WHERE product_id = '$product_id'");
}

#
# Get product options hash array
#
function cw_get_hash_options($product_id, $area = false, $language = false) {
	global $tables, $current_area, $current_language;

	$area = $area?$are:$current_area;
	$language = $language?$language:$current_language;

    if ($area == 'C')
        $where = " AND $tables[product_options_values].avail = 1 AND $tables[product_options].avail = 1";

    $keys = cw_query_hash("SELECT $tables[product_options].*, $tables[product_options_values].*, ifnull($tables[product_options_lng].name, $tables[product_options].name) as option_name, ifnull($tables[product_options_values_lng].name, $tables[product_options_values].name) as name FROM $tables[product_options_values] left join $tables[product_options_values_lng] ON $tables[product_options_values].option_id = $tables[product_options_values_lng].option_id and $tables[product_options_values_lng].code = '$language', $tables[product_options] LEFT JOIN $tables[product_options_lng] ON $tables[product_options].product_option_id = $tables[product_options_lng].product_option_id AND $tables[product_options_lng].code = '$language' WHERE $tables[product_options].product_id = '$product_id' AND $tables[product_options].product_option_id = $tables[product_options_values].product_option_id", "option_id", false);

    if (!$keys) return array();

    foreach ($keys as $kc => $class)
        $keys[$kc]['option_id'] = $kc;

	return $keys;
}

#
# Rebuild product variants
#
function cw_rebuild_variants($product_id, $force_rebuild = false, $tick = 1) {
    global $tables;

    if (!$force_rebuild) {
        # Check variant's matrix
        $options_count = cw_query_first_cell("SELECT COUNT(*) FROM $tables[product_options], $tables[product_options_values] WHERE $tables[product_options].product_option_id = $tables[product_options_values].product_option_id AND $tables[product_options].product_id = '$product_id' AND $tables[product_options].type = '' AND $tables[product_options].avail = 1 AND $tables[product_options_values].avail = 1");
        $variants_count = count(cw_query_column("SELECT COUNT(*) FROM $tables[product_variant_items], $tables[product_variants] WHERE $tables[product_variants].product_id = '$product_id' AND $tables[product_variants].variant_id = $tables[product_variant_items].variant_id GROUP BY $tables[product_variant_items].option_id"));
        if (($options_count == $variants_count && $options_count > 0))
            return true;
    }

    if ($tick > 0)
        cw_display_service_header("lbl_rebuild_variants");
    $ids = cw_query_column("SELECT variant_id FROM $tables[product_variants] WHERE product_id = '$product_id'");
    if (!empty($ids)) {
        # Save old data
        $vars = cw_query_hash("SELECT pv.*, pwa.avail  FROM $tables[product_variants] as pv LEFT JOIN $tables[products_warehouses_amount] as pwa ON pv.variant_id = pwa.variant_id WHERE pv.product_id = '$product_id'", "variant_id", false);
        $prices = db_query("select pp.* from $tables[products_prices] as pp where pp.product_id = '$product_id' AND variant_id != 0");
        if ($prices) {
            while ($v = db_fetch_array($prices)) {
                if (!isset($vars[$v['variant_id']]))
                    continue;
                $key = $v['quantity']."|".$v['membership_id'];
                if (!isset($vars[$v['variant_id']]['prices']))
                    $vars[$v['variant_id']]['prices'] = array();

                if (!isset($vars[$v['variant_id']]['prices'][$key]) || $vars[$v['variant_id']]['prices'][$key]['price'] > $v['price'])
                    $vars[$v['variant_id']]['prices'][$key] = $v;

            }

            db_free_result($prices);
        }

        unset($prices);


        $items = cw_query_hash("SELECT $tables[product_variant_items].*, $tables[product_options_values].product_option_id FROM $tables[product_variant_items], $tables[product_options_values], $tables[product_variants] WHERE $tables[product_variant_items].option_id = $tables[product_options_values].option_id AND $tables[product_variant_items].variant_id = $tables[product_variants].variant_id AND $tables[product_variants].product_id = '$product_id'", array('product_option_id', "option_id"), true, true);

        # Delete old variants
        /*
                $tmp = cw_query_first("SELECT MIN(avail) as avail, MIN(weight) as weight FROM $tables[product_variants] WHERE product_id = '$product_id'");
                db_query("UPDATE $tables[products] SET avail = '$tmp[avail]', weight = '$tmp[weight]' WHERE product_id = '$product_id'");
                unset($tmp);
        */
        db_query("DELETE FROM $tables[products_prices] WHERE product_id = '$product_id' AND variant_id != 0");
        db_query("DELETE FROM $tables[product_variant_items] WHERE variant_id IN ('".implode("','",$ids)."')");
        db_query("DELETE FROM $tables[products_warehouses_amount] WHERE product_id = '$product_id' AND variant_id != 0");

    }

    unset($ids);
    db_query("DELETE FROM $tables[product_variants] WHERE product_id = '$product_id'");

    # Get modifier-classes
    $classes = cw_query($sql="SELECT product_option_id FROM $tables[product_options] WHERE product_id = '$product_id' AND type = '' AND avail = 1 ORDER BY orderby");
    if (empty($classes))
        return false;

    foreach ($classes as $k => $v) {
        $classes[$k]['cnt'] = 0;
        $classes[$k]['options'] = cw_query_column("SELECT option_id FROM $tables[product_options_values] WHERE product_option_id = '$v[product_option_id]' AND avail = 1 ORDER BY orderby, option_id ASC ");
        if (!@count($classes[$k]['options']) || !is_array($classes[$k]['options']))
            unset($classes[$k]);
    }

    if (empty($classes))
        return false;

    $classes = array_values($classes);
    $classes[0]['cnt'] = -1;

    # Build variant's matrix
    $variants = array();

    # Write variants to DB
    $product = cw_query_first("SELECT $tables[products].eancode, $tables[products].productcode, $tables[products].weight,  $tables[products].cost, $tables[products_prices].price FROM $tables[products], $tables[products_prices]  WHERE  $tables[products_prices].variant_id = 0 AND $tables[products_prices].quantity = '1' AND $tables[products_prices].membership_id = 0 AND $tables[products].product_id = '$product_id' GROUP BY $tables[products].product_id");

    $cnt_row = $cnt = $cnd_ean = 0;
    do {
        $is_end = false;
        $options = array();
        $old_variants = array();
        foreach ($classes as $k => $c) {
            $option_id = 0;
            if (!$is_end) {
                if ($c['cnt'] >= count($c['options'])-1) {
                    $c['cnt'] = 0;

                } else {
                    $c['cnt']++;
                    $is_end = true;
                }

                $classes[$k] = $c;
            }
            $option_id = $c['options'][$c['cnt']];

            if (empty($option_id))
                continue;

            $options[] = $option_id;
            if (isset($items[$c['product_option_id']][$option_id])) {
                if (empty($old_variants)) {
                    $old_variants = $items[$c['product_option_id']][$option_id];
                }
                else {
                    $old_variants = array_intersect($old_variants, $items[$c['product_option_id']][$option_id]);
                }
            }
        }

        if (!$is_end || empty($options))
            break;

        $_product = $product;

        # Restore old data
        $old_variant_id = false;
        if (is_array($old_variants) && !empty($old_variants)) {
            $old_variant_id = array_shift($old_variants);
            if (isset($vars[$old_variant_id])) {
                $_product = cw_array_merge($_product, $vars[$old_variant_id]);
            }
        }
        unset($old_variants);

        # Get unique SKU
        $sku = $_product['productcode'];
        while (cw_query_first_cell("SELECT COUNT(*) FROM $tables[product_variants] WHERE productcode = '$sku'") > 0)
            $sku = $_product['productcode'].++$cnt;

        $eancode = $_product['eancode'];
        while (cw_query_first_cell("SELECT COUNT(*) FROM $tables[product_variants] WHERE eancode = '$eancode'") > 0)
            $eancode = $_product['eancode'].++$cnd_ean;

        $data = array(
            "product_id"		=> $product_id,
//			"avail"			=> $_product['avail'],
            "weight"		=> $_product['weight'],
            "cost"          => $_product['cost'],
            "productcode"	=> $sku,
            'eancode'       => $eancode,
        );
        # Check variant_id
        if (!empty($old_variant_id) && cw_query_first_cell("SELECT COUNT(*) FROM $tables[product_variants] WHERE variant_id = '$old_variant_id'") == 0) {
            $data['variant_id'] = $old_variant_id;
        }
        # Insert variant info
        $variant_id = cw_array2insert('product_variants', $data);
        if (empty($variant_id))
            continue;

        if($_product['avail']== NULL)
           $_product['avail'] = 0;
        if(cw_query_first_cell("SELECT COUNT(*) FROM $tables[products_warehouses_amount] WHERE variant_id = '$variant_id'") == 0)
            cw_array2insert('products_warehouses_amount', array('product_id'=>$product_id,'avail'=> $_product['avail'], 'avail_ordered' =>0, 'avail_sold'=>0, 'avail_reserved'=> '0', 'variant_id'=> $variant_id, 'warehouse_customer_id'=>'0'), false);

        # Write products_prices
        if (empty($_product['prices']))
            cw_product_replace_price($product_id, $_product['price'], $variant_id, true, true);
        else {
            foreach ($_product['prices'] as $p){
                cw_product_replace_price($product_id, $p['price'], $variant_id, true, $p['is_manual']);
            }
        }

        # Restore image
        if (!empty($old_variant_id) && ($variant_id != $old_variant_id)) {
            cw_image_delete($variant_id, "W");
            db_query("UPDATE $tables[products_images_var] SET id = '$variant_id' WHERE id = '$old_variant_id'");
        }

        # Write matrix
        foreach ($options as $i) {
            db_query("INSERT INTO $tables[product_variant_items] (variant_id, option_id) VALUES ('$variant_id','$i')");
        }

        if ($tick > 0 && $cnt_row++ % $tick == 0)
            cw_flush(". ");

    } while($is_end);

    # Clean old variants images
    $images = cw_query_column("SELECT $tables[products_images_var].id FROM $tables[product_variants] LEFT JOIN $tables[products_images_var] ON $tables[product_variants].variant_id = $tables[products_images_var].id WHERE $tables[products_images_var].id IS NULL");
    if (!empty($images)) {
        cw_image_delete($images, "W");
    }
    return true;
}

#
# This function checks for exception of product options for product
#
function cw_check_product_options ($product_id, $options, $trusted_options = false) {
	global $tables;

	if (empty($options) || !is_array($options))
		return false;

	$textids = cw_query_column("SELECT product_option_id FROM $tables[product_options] WHERE product_option_id IN ('".implode("','", cw_array_map("intval", array_keys($options)))."') AND type = 'T'");

	$where = array();
	$oids = array();
	foreach ($options as $_cid => $oid) {
		$cid = intval($_cid);
		if (empty($cid))
			return false;

		if (!is_numeric($oid) || empty($oid)) {
			$where[] = "$tables[product_options].product_option_id = '$cid' AND $tables[product_options_values].option_id IS NULL AND $tables[product_options].type = 'T'";

		} else {
			$where[] = "$tables[product_options].product_option_id = '$cid' AND ($tables[product_options_values].option_id = '$oid' OR ($tables[product_options_values].option_id IS NULL AND $tables[product_options].type = 'T'))";
			if (empty($textids) || !in_array($cid, $textids))
				$oids[] = $oid;
		}
	}

	if (!$trusted_options) {
		# Get classes data
		$classes = cw_query_hash("SELECT $tables[product_options].product_option_id, $tables[product_options].type FROM $tables[product_options] LEFT JOIN $tables[product_options_values] ON $tables[product_options].product_option_id = $tables[product_options_values].product_option_id AND $tables[product_options_values].avail = 1 WHERE $tables[product_options].avail = 1 AND $tables[product_options].product_id = '".intval($product_id)."' AND ((".implode(") OR (", $where).")) GROUP BY $tables[product_options].product_option_id", "product_option_id", false, true);
		if (count($classes) != count($options))
			return false;
	}
	unset($where);

	# Get number of all product classes
	$counter = @count(cw_query_column("SELECT $tables[product_options].product_option_id FROM $tables[product_options], $tables[product_options_values] WHERE $tables[product_options].product_id = '$product_id' AND $tables[product_options].avail = 1 AND $tables[product_options].product_option_id = $tables[product_options_values].product_option_id AND $tables[product_options_values].avail = 1 GROUP BY $tables[product_options].product_option_id"));

	$oids_counter = count($oids);
	$oids = implode("','", $oids);
	if ($counter == $oids_counter) {

		# Check full options data
		return !cw_query_first_cell("SELECT COUNT(*) as cnt_orig, SUM(IF(e2.option_id IS NULL, 0, 1)) as cnt_ex FROM $tables[products_options_ex] as e1 LEFT JOIN $tables[products_options_ex] as e2 ON e1.option_id = e2.option_id AND e2.option_id IN ('".$oids."') GROUP BY e1.exception_id HAVING cnt_orig = cnt_ex");

	} else {

		$exceptions = cw_query_hash("SELECT exception_id, COUNT(option_id) FROM $tables[products_options_ex] WHERE option_id IN ('".$oids."') GROUP BY exception_id", "exception_id", false, true);
		if (empty($exceptions))
			return true;

		$exception_counters = cw_query_hash("SELECT exception_id, COUNT(option_id) FROM $tables[products_options_ex] WHERE exception_id IN ('".implode("','", array_keys($exceptions))."') GROUP BY exception_id", "exception_id", false, true);
		foreach ($exceptions as $eid => $cnt) {
			if ($exception_counters[$eid] == $cnt)
				return false;
		}

		# Check partly options data
		$exceptions = cw_query_hash("SELECT $tables[product_options_values].product_option_id, COUNT($tables[products_options_ex].exception_id) FROM $tables[products_options_ex], $tables[product_options_values], $tables[product_options] WHERE $tables[products_options_ex].option_id = $tables[product_options_values].option_id AND $tables[products_options_ex].exception_id IN ('".implode("','", array_keys($exceptions))."') AND $tables[products_options_ex].option_id NOT IN ('".$oids."') AND $tables[product_options_values].avail = 1 AND $tables[product_options].avail = 1 AND $tables[product_options_values].product_option_id = $tables[product_options].product_option_id GROUP BY $tables[product_options_values].product_option_id", "product_option_id", false, true);
		if (empty($exceptions))
			return true;

		$class_counters = cw_query_hash("SELECT product_option_id, COUNT(*) FROM $tables[product_options_values] WHERE product_option_id IN ('".implode("','", array_keys($exceptions))."') AND avail = 1 GROUP BY product_option_id", "product_option_id", false, true);
		foreach ($exceptions as $cid => $cnt) {
			if (isset($class_counters[$cid]) && $class_counters[$cid] == $cnt)
				return false;
		}

		return true;
	}


}

#
# Get options modifications
#
function cw_get_product_options_data($product_id, $options, $membership_id = 0, $area = false, $language = false) {
	global $tables, $current_area, $current_language;
    global $user_account;

	if (empty($options) || !is_array($options))
		return array(false, false);

	if ($area === false)
		$area = $current_area;

	if ($language === false)
		$language = $current_language;

	$ids = cw_array_map("intval", array_keys($options));
	$classes = cw_query_hash("SELECT product_option_id, type FROM $tables[product_options] WHERE product_id = '".intval($product_id)."' AND product_option_id IN ('".implode("','", $ids)."')", "product_option_id", false, true);

	$ret = array();
	foreach ($options as $k => $v) {
		if (!isset($classes[$k]))
			continue;

		if ($classes[$k] != 'T')
			$v = intval($v);

		if ($area == "C") {
			if ($classes[$k] != 'T') {
				$option = cw_query_first("SELECT $tables[product_options].*, $tables[product_options_values].*, ifnull($tables[product_options_lng].name, $tables[product_options].name) as option_name FROM $tables[product_options_values], $tables[product_options] LEFT JOIN $tables[product_options_lng] ON $tables[product_options].product_option_id = $tables[product_options_lng].product_option_id AND $tables[product_options_lng].code = '$language' WHERE $tables[product_options_values].option_id = '$v' AND $tables[product_options].product_option_id = $tables[product_options_values].product_option_id AND $tables[product_options].product_option_id = '$k' AND $tables[product_options].avail = 1 AND $tables[product_options_values].avail = 1");
# kornev, TOFIX - possible to make in one query
				$name_lng = cw_query_first_cell("SELECT name FROM $tables[product_options_values_lng] WHERE $tables[product_options_values_lng].option_id = '$option[option_id]' AND code = '$language'");
				if (!empty($name_lng))
					$option['name'] = $name_lng;
			}
			else {
				$option = cw_query_first("SELECT $tables[product_options].*, ifnull($tables[product_options_lng].name, $tables[product_options].name) as option_name FROM $tables[product_options] LEFT JOIN $tables[product_options_lng] ON $tables[product_options].product_option_id = $tables[product_options_lng].product_option_id AND $tables[product_options_lng].code = '$language' WHERE $tables[product_options].product_option_id = '$k' AND $tables[product_options].avail = 1");
			}

		}
		else {
			if ($classes[$k] != 'T') {
				$option = cw_query_first("SELECT * FROM $tables[product_options], $tables[product_options_values] WHERE $tables[product_options_values].option_id = '$v' AND $tables[product_options].product_option_id = $tables[product_options_values].product_option_id AND $tables[product_options].product_option_id = '$k' AND $tables[product_options].avail = 1 AND $tables[product_options_values].avail = 1");
			}
			else {
				$option = cw_query_first("SELECT * FROM $tables[product_options] WHERE $tables[product_options].product_option_id = '$k' AND $tables[product_options].avail = 1");
			}
		}

		if (empty($option))
			continue;

		if ($classes[$k] == 'T') {
			$option['name'] = stripslashes($v);
		}
		elseif (empty($classes[$k])) {
			$variants[$k] = $v;
		}

		$ret[$k] = $option;
	}

	$variant = false;
	if ($variants) {
		$variant = cw_query_first("SELECT variant_id, COUNT(option_id) as count FROM $tables[product_variant_items] WHERE option_id IN ('".implode("','", $variants)."') GROUP BY variant_id ORDER BY count desc");
		if ($variant['count'] == count($variants)) {
			$variant = cw_query_first("SELECT $tables[product_variants].*, MIN($tables[products_prices].price) as price, $tables[products_images_var].image_path as pimage_path, $tables[products_images_var].image_x as pimage_x, $tables[products_images_var].image_y as pimage_y FROM $tables[products_prices], $tables[product_variants] LEFT JOIN $tables[products_images_var] ON $tables[product_variants].variant_id = $tables[products_images_var].id WHERE $tables[product_variants].variant_id = '$variant[variant_id]' AND $tables[product_variants].variant_id = $tables[products_prices].variant_id AND $tables[products_prices].product_id = '$product_id' AND $tables[products_prices].quantity = 1 AND $tables[products_prices].membership_id IN (0, '$user_account[membership_id]') GROUP BY $tables[product_variants].variant_id");
		}
	}


	if (empty($ret))
		$ret = false;

	return array($variant, $ret);
}

#
# Serialize product options
#
function cw_serialize_options($options, $ex = false) {
	global $tables;

	if (!is_array($options) || empty($options))
		return false;

	$return = array();
	$ids = cw_array_map("intval", array_keys($options));
	$classes = cw_query_hash("SELECT product_option_id, field, name, type FROM $tables[product_options] WHERE product_option_id IN ('".implode("','", $ids)."')", "product_option_id", false);
	foreach ($options as $c => $o) {
		if (!isset($classes[$c]))
			continue;

		$option_id = (is_array($o) ? $o['option_id'] : $o);

		if ($classes[$c]['type'] != 'T') {
			$option_id = intval($option_id);
			$option = cw_query_first_cell("SELECT name FROM $tables[product_options_values] WHERE option_id = '$option_id' AND product_option_id = '$c'");
			if (strlen($option) == 0)
				continue;
		}
		else {
			$option = stripslashes($option_id);
		}

		if ($ex) {
			$return[] = trim($classes[$c]['name'])." ($c): ".trim($option);
			if (!empty($option_id) && $option != $option_id)
				$return[count($return)-1] .= " ($option_id)";
		}
		else {
			$return[] = trim($classes[$c]['name']).": ".trim($option);
		}
	}

	return @implode("\n", $return);
}

#
# Unserialize product options
#
function cw_unserialize_options($data) {
	if (empty($data))
		return array(array(), array());

	$options = array();
	$options_hash = array();
	if (preg_match_all("/^(.+) \((\d+)\): (.+)$/Sm", $data, $preg)) {
		foreach ($preg[1] as $k => $c) {
			if (preg_match("/^(.+) \((\d+)\)$/S", $preg[3][$k], $preg2)) {
				$options[$c] = $preg2[1];
				$options_hash[$preg[2][$k]] = $preg2[2];
			}
			else {
				$options[$c] = $preg[3][$k];
			}
		}
	}
	elseif (preg_match_all("/^(.+): (.+)$/Sm", $data, $preg)) {
		foreach ($preg[1] as $k => $c) {
			$options[$c] = $preg[2][$k];
		}
	}

	return array($options, $options_hash);
}

#
# Convert product options array to variant_id
#
function cw_get_variant_id($options, $product_id = false) {
	global $tables;

	if (empty($options) || !is_array($options))
		return false;

	$ids = cw_array_map("intval", array_keys($options));
	$vids = cw_query_column("SELECT product_option_id FROM $tables[product_options] WHERE type != '' AND product_option_id IN ('".implode("','", $ids)."')");
	if (!empty($vids)) {
		foreach ($vids as $v) {
			unset($options[$v]);
		}
	}

	if (empty($options))
		return false;

	if ($product_id === false) {
		$ids = cw_array_map("intval", array_keys($options));
		$product_id = cw_query_first_cell("SELECT product_id FROM $tables[product_options] WHERE product_option_id IN ('".implode("','", $ids)."') LIMIT 1");
	}

	$cnt = 0;
	$res = db_query("SELECT $tables[product_options].product_option_id FROM $tables[product_options], $tables[product_options_values] WHERE $tables[product_options].type = '' AND $tables[product_options].avail = 1 AND $tables[product_options].product_id = '".intval($product_id)."' AND $tables[product_options].product_option_id = $tables[product_options_values].product_option_id AND $tables[product_options_values].avail = 1 GROUP BY $tables[product_options].product_option_id");
	if ($res) {
		$cnt = db_num_rows($res);
		db_free_result($res);
	}

	if ($cnt != count($options))
		return false;

	$options = cw_array_map("intval", $options);

	return cw_query_first_cell("SELECT variant_id, COUNT(variant_id) as cnt FROM $tables[product_variant_items] WHERE $tables[product_variant_items].option_id IN ('".implode("','", $options)."') GROUP BY variant_id HAVING cnt = ".$cnt." LIMIT 1");
}

#
# Get default product options
#
function cw_get_default_options($product_id, $amount, $membership_id = 0, $variant_id = 0) {
	global $tables, $config, $_orderby;

	$product_id = intval($product_id);
	$amount = intval($amount);

	# Get product options
	$classes = cw_query_hash("SELECT $tables[product_options].product_option_id, $tables[product_options].type FROM $tables[product_options] LEFT JOIN $tables[product_options_values] ON $tables[product_options].product_option_id = $tables[product_options_values].product_option_id AND $tables[product_options_values].avail = 1 WHERE $tables[product_options].avail = 1 AND $tables[product_options].product_id = '$product_id' AND ($tables[product_options_values].product_option_id IS NOT NULL OR $tables[product_options].type = 'T') GROUP BY $tables[product_options].product_option_id ORDER BY $tables[product_options].orderby", "product_option_id", false);
	if (empty($classes))
		return true;

	$_product_options = array();

	$_orderby = array_keys($classes);
	$_orderby = array_flip($_orderby);

	# Get default variant
	$variant_counter = @count(cw_query_column("SELECT $tables[product_options].product_option_id FROM $tables[product_options], $tables[product_options_values], $tables[product_variant_items] WHERE $tables[product_options].product_option_id = $tables[product_options_values].product_option_id AND $tables[product_options_values].avail = 1 AND $tables[product_options].avail = 1 AND $tables[product_options].product_id = '$product_id' AND $tables[product_options].type = '' AND $tables[product_variant_items].option_id = $tables[product_options_values].option_id GROUP BY $tables[product_options].product_option_id"));
	if ($variant_counter > 0) {

# kornev, may be we are trying to add some variant, but don't know the options
        if ($variant_id)
            $def_variant_id = $variant_id;
        elseif ($config['General']['disable_outofstock_products'] == 'Y') {
            $def_variant_id = cw_query_first_cell("SELECT v.variant_id FROM $tables[product_variants] as v, $tables[products_warehouses_amount] as pwa where pwa.product_id = '$product_id' and pwa.variant_id = v.variant_id and v.def = 'Y' and pwa.avail > 0");
            if (!$def_variant_id) $def_variant_id = cw_query_first_cell("SELECT v.variant_id FROM $tables[product_variants] as v, $tables[products_warehouses_amount] as pwa where pwa.product_id = '$product_id' and pwa.variant_id = v.variant_id and pwa.avail > 0");
        }
        else
    		$def_variant_id = cw_query_first_cell("SELECT variant_id FROM $tables[product_variants] WHERE product_id = '$product_id' and def = 'Y'");

		if (!empty($def_variant_id)) {
			$_product_options = cw_query_hash("SELECT $tables[product_options_values].product_option_id, $tables[product_options_values].option_id FROM $tables[product_options_values], $tables[product_variant_items] WHERE $tables[product_variant_items].variant_id = '$def_variant_id' AND $tables[product_variant_items].option_id = $tables[product_options_values].option_id", "product_option_id", false, true);
			if (count($_product_options) != $variant_counter)
				return false;

			# Check exceptions
			$exceptions = cw_query_hash("SELECT exception_id, COUNT(option_id) FROM $tables[products_options_ex] WHERE option_id IN ('".implode("','", $_product_options)."') GROUP BY exception_id", "exception_id", false, true);
			if (!empty($exceptions)) {

				# Get exceptions counters
				$exception_counters = cw_query_hash("SELECT exception_id, COUNT(option_id) FROM $tables[products_options_ex] WHERE exception_id IN ('".implode("','", array_keys($exceptions))."') GROUP BY exception_id", "exception_id", false, true);
				foreach ($exceptions as $eid => $cnt) {
					if ($exception_counters[$eid] == $cnt) {
						$_product_options = array();
						break;

					}
				}

				if (!empty($_product_options)) {

					# When the set of exceptions defined for a product covers not only the
					# combination of options that make the product's default variant, but
					# also a whole group of non-variant options which can be used in
					# combination with them, this check-up ensures that a different
					# (non-exceptional) combination of variant options is selected as the
					# products's default one.
					$exceptions = cw_query_hash("SELECT $tables[product_options_values].product_option_id, COUNT($tables[products_options_ex].exception_id) FROM $tables[products_options_ex], $tables[product_options_values] WHERE $tables[products_options_ex].option_id = $tables[product_options_values].option_id AND $tables[products_options_ex].exception_id IN ('".implode("','", array_keys($exceptions))."') AND $tables[products_options_ex].option_id NOT IN ('".implode("','", $_product_options)."') GROUP BY $tables[product_options_values].product_option_id", "product_option_id", false, true);
					if (!empty($exceptions)) {
						$class_counters = cw_query_hash("SELECT product_option_id, COUNT(*) FROM $tables[product_options_values] WHERE product_option_id IN ('".implode("','", array_keys($exceptions))."') AND avail = 1 GROUP BY product_option_id", "product_option_id", false, true);
						foreach ($exceptions as $cid => $cnt) {
							if (isset($classes[$cid]) && isset($class_counters[$cid]) && $class_counters[$cid] == $cnt) {
								$_product_options = array();
								break;
							}
						}
					}
				}
				unset($exceptions, $exception_counters);
			}

			# Unset variant-type classes
			if (!empty($_product_options)) {
				foreach ($_product_options as $cid => $oid) {
					if (isset($classes[$cid]))
						unset($classes[$cid]);
				}
			}
		}
	}

	# Get class options
	$options = cw_query_hash("SELECT product_option_id, option_id FROM $tables[product_options_values] WHERE product_option_id IN ('".implode("','", array_keys($classes))."') AND avail = 1 ORDER BY orderby", "product_option_id", true, true);
	$_flag = false;
	foreach ($classes as $k => $class) {
		if ($class['type'] == 'T') {
			$_product_options[$k] = '';
			unset($classes[$k]);
			continue;
		}
		$classes[$k]['cnt'] = $_flag ? 0 : -1;
		$_flag = true;

		if (isset($options[$k])) {
			$classes[$k]['options'] = array_values($options[$k]);

		} else {
			unset($classes[$k]);
		}
	}
	unset($options);

	if (empty($classes)) {
		if (empty($_product_options))
			return false;

		uksort($_product_options, "cw_get_default_options_callback");
		return $_product_options;
	}

	# Scan & check classes options array
	do {
		$product_options = $_product_options;
		$is_add = true;

		# Build full 'product_option_id->option_id' hash
		foreach ($classes as $k => $class) {
			if ($is_add) {
				if (count($class['options'])-1 <= $class['cnt']) {
					$class['cnt'] = 0;

				} else {
					$is_add = false;
					$class['cnt']++;
				}
			}

			$product_options[$k] = $class['options'][$class['cnt']];
			$classes[$k]['cnt'] = $class['cnt'];
		}

		# Check current product options array
		if (cw_check_product_options($product_id, $product_options)) {
			$variant_id = cw_get_variant_id($product_options, $product_id);

            # Check variant quantity in stock
            if (empty($variant_id) || $config['General']['disable_outofstock_products'] != 'Y' ||
                cw_query_first_cell("SELECT avail FROM $tables[products_warehouses_amount] WHERE variant_id = '$variant_id'") > 0) {
                break;
            }
		}
	} while(!$is_add);

	if (empty($product_options))
		return false;

	uksort($product_options, "cw_get_default_options_callback");
	return $product_options;
}

function cw_get_default_options_callback($a, $b) {
	global $_orderby;

	$a = $_orderby[$a];
	$b = $_orderby[$b];
	if ($a == $b)
		return 0;
	return $a > $b ? 1 : -1;
}

#
# Get default options markup
#
function cw_get_default_options_markup($product_id, $price) {
	global $tables;

	# Get product options
	$classes = cw_query_hash("SELECT $tables[product_options].product_option_id FROM $tables[product_options], $tables[product_options_values] WHERE $tables[product_options].product_option_id = $tables[product_options_values].product_option_id AND $tables[product_options_values].avail = 1 AND $tables[product_options].avail = 1 AND $tables[product_options].product_id = '$product_id' AND $tables[product_options].type = 'Y' GROUP BY $tables[product_options].product_option_id ORDER BY $tables[product_options].orderby", "product_option_id", false);
	if (empty($classes))
		return 0;

	# Get class options
	$options = cw_query_hash("SELECT product_option_id, option_id, modifier_type, price_modifier FROM $tables[product_options_values] WHERE product_option_id IN ('".implode("','", array_keys($classes))."') AND avail = 1 ORDER BY orderby", "product_option_id", true);
	$_flag = false;
	foreach ($classes as $k => $class) {
		$classes[$k]['cnt'] = $_flag ? 0 : -1;
		$_flag = true;

		if (isset($options[$k])) {
			$classes[$k]['options'] = array_values($options[$k]);

		} else {
			unset($classes[$k]);
		}
	}
	unset($options);

	if (empty($classes))
		return 0;

	# Scan & check classes options array
	$markup = 0;
	do {
		$product_options = array();
		$is_add = true;
		$counters = array();

		# Build full 'product_option_id->option_id' hash
		foreach ($classes as $k => $class) {
			if ($is_add) {
				if (count($class['options'])-1 <= $class['cnt']) {
					$class['cnt'] = 0;

				} else {
					$is_add = false;
					$class['cnt']++;
				}
			}

			$counters[$k] = $class['cnt'];
			$product_options[$k] = $class['options'][$class['cnt']]['option_id'];
			$classes[$k]['cnt'] = $class['cnt'];
		}

		# Check current product options array
		if (cw_check_product_options($product_id, $product_options)) {
			foreach ($counters as $cid => $idx) {
				if ($classes[$cid]['options'][$idx]['modifier_type'] == '$') {
					$markup += $classes[$cid]['options'][$idx]['price_modifier'];

				} elseif ($price != 0) {
					$markup += $price / 100 * $classes[$cid]['options'][$idx]['price_modifier'];
				}
			}
			break;

		}

	} while(!$is_add);

	return $markup;
}

#
# Get default options markup for products list
#
function cw_get_default_options_markup_list($products) {
	global $tables;

	if (empty($products) || !is_array($products))
		return array();

	$in_products = "IN ('".implode("','", array_keys($products))."')";
	# Get product options
	$tmp = cw_query_hash("SELECT $tables[product_options].product_id, $tables[product_options].product_option_id FROM $tables[product_options], $tables[product_options_values] WHERE $tables[product_options].product_option_id = $tables[product_options_values].product_option_id AND $tables[product_options_values].avail = 1 AND $tables[product_options].avail = 1 AND $tables[product_options].product_id $in_products AND $tables[product_options].type = 'Y' GROUP BY $tables[product_options].product_option_id ORDER BY $tables[product_options].orderby", "product_id", true, true);
	if (empty($tmp))
		return array();

	$classes = array();
	$cids = array();
	foreach ($tmp as $pid => $subclasses) {
		foreach ($subclasses as $cid) {
			$classes[$pid][$cid] = array();
			$cids[] = $cid;
		}
	}
	unset($tmp);

	# Get class options
		$options = cw_query_hash("SELECT product_option_id, option_id, modifier_type, price_modifier FROM $tables[product_options_values] WHERE product_option_id IN ('".implode("','", $cids)."') AND avail = 1 ORDER BY orderby, option_id", "product_option_id", true);


	foreach ($classes as $pid => $subclasses) {
		$_flag = false;
		foreach($subclasses as $cid => $class) {
			$classes[$pid][$cid]['cnt'] = $_flag ? 0 : -1;
			$_flag = true;

			if (isset($options[$cid])) {
				$classes[$pid][$cid]['options'] = array_values($options[$cid]);

			} else {
				unset($classes[$pid][$cid]);
			}
		}

		if (empty($classes[$pid]))
			unset($classes[$pid]);
	}
	unset($options);

	if (empty($classes))
		return array();

	# Scan & check classes options array
	$markup = array();
	foreach ($classes as $pid => $subclasses) {
		$markup[$pid] = 0;
		do {
			$product_options = array();
			$is_add = true;
			$counters = array();

			# Build full 'product_option_id->option_id' hash
			foreach ($subclasses as $cid => $class) {
				if ($is_add) {
					if (count($class['options'])-1 <= $class['cnt']) {
						$class['cnt'] = 0;

					} else {
						$is_add = false;
						$class['cnt']++;
					}
				}

				$counters[$cid] = $class['cnt'];
				$product_options[$cid] = $class['options'][$class['cnt']]['option_id'];
				$subclasses[$cid]['cnt'] = $class['cnt'];
			}

			# Check current product options array
			if (cw_check_product_options($pid, $product_options, true)) {
				foreach ($counters as $cid => $idx) {
					if ($subclasses[$cid]['options'][$idx]['modifier_type'] == '%') {
						$markup[$pid] += $products[$pid] / 100 * $subclasses[$cid]['options'][$idx]['price_modifier'];

					} else {
						$markup[$pid] += $subclasses[$cid]['options'][$idx]['price_modifier'];
					}
				}
				break;

			}

		} while(!$is_add);
	}

	return $markup;
}

#
# Get default variant
#
function cw_get_default_variant_id($product_id) {
	global $tables, $config;

	# Get classes (variant type)
	$classes = cw_query_hash("SELECT $tables[product_options].product_option_id FROM $tables[product_options], $tables[product_options_values], $tables[product_variant_items] WHERE $tables[product_options].product_option_id = $tables[product_options_values].product_option_id AND $tables[product_options_values].avail = 1 AND $tables[product_options].avail = 1 AND $tables[product_options].product_id = '$product_id' AND $tables[product_options].type = '' AND $tables[product_variant_items].option_id = $tables[product_options_values].option_id GROUP BY $tables[product_options].product_option_id", "product_option_id");
	if (empty($classes))
		return false;

	$avail_where = "";
	if ($config['General']['disable_outofstock_products'] == 'Y')
		$avail_where = "AND pa.avail > 0";

	# Detect default variant
	$def_variant_id = cw_query_first_cell("SELECT pv.variant_id FROM $tables[product_variants] as pv, $tables[products_warehouses_amount] as pa WHERE pa.variant_id=pv.variant_id and pv.product_id = '$product_id' AND def = 'Y' ".$avail_where);
	if (!empty($def_variant_id)) {
		$_product_options = cw_query_hash("SELECT $tables[product_options_values].product_option_id, $tables[product_options_values].option_id FROM $tables[product_options_values], $tables[product_variant_items] WHERE $tables[product_variant_items].variant_id = '$def_variant_id' AND $tables[product_variant_items].option_id = $tables[product_options_values].option_id", "product_option_id", false, true);
		if (count($_product_options) != count($classes))
			return false;

		# Check exceptions
		$exceptions = cw_query_hash("SELECT exception_id, COUNT(option_id) FROM $tables[products_options_ex] WHERE option_id IN ('".implode("','", $_product_options)."') GROUP BY exception_id", "exception_id", false, true);
		if (!empty($exceptions)) {

			# Get exceptions counters
			$exception_counters = cw_query_hash("SELECT exception_id, COUNT(option_id) FROM $tables[products_options_ex] WHERE exception_id IN ('".implode("','", array_keys($exceptions))."') GROUP BY exception_id", "exception_id", false, true);
			foreach ($exceptions as $eid => $cnt) {
				if ($exception_counters[$eid] == $cnt) {
					$_product_options = array();
					break;

				}
			}

			if (!empty($_product_options)) {

				# When the set of exceptions defined for a product covers not only the
				# combination of options that make the product's default variant, but
				# also a whole group of non-variant options which can be used in
				# combination with them, this check-up ensures that a different
				# (non-exceptional) combination of variant options is selected as the
				# products's default one.
				$exceptions = cw_query_hash("SELECT $tables[product_options_values].product_option_id, COUNT($tables[products_options_ex].exception_id) FROM $tables[products_options_ex], $tables[product_options_values] WHERE $tables[products_options_ex].option_id = $tables[product_options_values].option_id AND $tables[products_options_ex].exception_id IN ('".implode("','", array_keys($exceptions))."') AND $tables[products_options_ex].option_id NOT IN ('".implode("','", $_product_options)."') GROUP BY $tables[product_options_values].product_option_id", "product_option_id", false, true);
				if (!empty($exceptions)) {
					$class_counters = cw_query_hash("SELECT $tables[product_options_values].product_option_id, COUNT($tables[product_options_values].option_id) FROM $tables[product_options], $tables[product_options_values] WHERE $tables[product_options_values].product_option_id IN ('".implode("','", array_keys($exceptions))."') AND $tables[product_options_values].avail = 1 AND $tables[product_options].product_option_id = $tables[product_options_values].product_option_id AND $tables[product_options].avail = 1 GROUP BY $tables[product_options_values].product_option_id", "product_option_id", false, true);
					foreach ($exceptions as $cid => $cnt) {
						if (isset($class_counters[$cid]) && $class_counters[$cid] == $cnt) {
							$_product_options = array();
							break;
						}
					}
				}
			}
			unset($exceptions, $exception_counters);
		}

		if (!empty($_product_options))
			return $def_variant_id;

	}

	# Get class options
	$options = cw_query_hash("SELECT product_option_id, option_id FROM $tables[product_options_values] WHERE product_option_id IN ('".implode("','", array_keys($classes))."') AND avail = 1 ORDER BY orderby", "product_option_id", true, true);
	$_flag = false;
	foreach ($classes as $k => $class) {
		$classes[$k]['cnt'] = $_flag ? 0 : -1;
		$_flag = true;

		if (isset($options[$k])) {
			$classes[$k]['options'] = array_values($options[$k]);

		} else {
			unset($classes[$k]);
		}
	}
	unset($options);

	if (empty($classes))
		return false;

	# Scan & check classes options array
	$variant_id = false;
	$first_variant_id = false;
	do {
		$product_options = array();
		$is_add = true;

		# Build full 'product_option_id->option_id' hash
		foreach ($classes as $k => $class) {
			if ($is_add) {
				if (count($class['options'])-1 <= $class['cnt']) {
					$class['cnt'] = 0;

				} else {
					$is_add = false;
					$class['cnt']++;
				}
			}

			$product_options[$k] = $class['options'][$class['cnt']];
			$classes[$k]['cnt'] = $class['cnt'];
		}

		# Check current product options array
		if (cw_check_product_options($product_id, $product_options)) {
			$variant_id = cw_get_variant_id($product_options, $product_id);

			# Save first valid variant id
			if (!$first_variant_id)
				$first_variant_id = $variant_id;

			# Check variant quantity in stock
			if ($config['General']['disable_outofstock_products'] != 'Y' ||
				cw_query_first_cell("SELECT avail FROM $tables[products_warehouses_amount] WHERE variant_id = '$variant_id'") > 0
			) {
				break;
			}

			$variant_id = false;

		}

	} while(!$is_add);

	# Get first valid variant if all valid variants is out-of-stock
	if ($variant_id === false && !empty($first_variant_id))
		$variant_id = $first_variant_id;

	return $variant_id;
}

#
# Get Product options amount
#
function cw_get_options_amount($product_options, $product_id) {
	global $tables, $config;

	$product_id = intval($product_id);
	if (empty($product_id))
		return false;

	if (!empty($product_options) && is_array($product_options)) {

		$classes = cw_query_column("SELECT product_option_id FROM $tables[product_options] WHERE product_id = '$product_id' AND type = ''");
		if (count($classes) > 0) {
			$ids = array();
			foreach ($product_options as $k => $v) {
				$k = intval($k);
				if (in_array($k, $classes)) {
					$ids[] = "$tables[product_options].product_option_id = '$k' AND $tables[product_options_values].option_id = '".intval($v)."'";
				}
			}

			if (!empty($ids)) {
				$ids = cw_query_column("SELECT $tables[product_options_values].option_id FROM $tables[product_options_values], $tables[product_options], $tables[product_variant_items] WHERE $tables[product_options].product_option_id = $tables[product_options_values].product_option_id AND $tables[product_options].product_id = '$product_id' AND $tables[product_options].type = '' AND $tables[product_options_values].option_id = $tables[product_variant_items].option_id AND (".implode(") OR (", $ids).") GROUP BY $tables[product_options_values].option_id");

				$variant = cw_query_first("SELECT variant_id, COUNT(option_id) as count FROM $tables[product_variant_items] WHERE option_id IN ('".implode("','", $ids)."') GROUP BY variant_id ORDER BY count desc");

				if (count($classes) == $variant['count'] && cw_query_first_cell("SELECT COUNT(*) FROM $tables[product_variants] WHERE product_id = '$product_id' AND variant_id = '$variant[variant_id]'") > 0) {
					return cw_query_first_cell("SELECT ov.avail FROM $tables[product_options_values] ov LEFT JOIN $tables[product_variant_items] vi ON ov.option_id = vi.option_id WHERE vi.variant_id = '$variant[variant_id]'");
				}
			}
		}
	}

	return cw_query_first_cell("SELECT avail FROM $tables[products] WHERE product_id = '$product_id'");
}

#
# Delete product option class
#
function cw_delete_po_class($product_option_id) {
	global $tables;

	if (is_numeric($product_option_id)) {
		$where = "= '$product_option_id'";

	} elseif (is_array($product_option_id) && !empty($product_option_id)) {
		$where = "IN ('".implode("','", $product_option_id)."')";

	} else {
		return false;
	}

	$ids = cw_query_column("SELECT option_id FROM $tables[product_options_values] WHERE product_option_id $where");
	if (!empty($ids)) {
		db_query("DELETE FROM $tables[product_options_values] WHERE product_option_id $where");
		db_query("DELETE FROM $tables[product_options_values_lng] WHERE option_id IN ('".implode("','", $ids)."')");
		db_query("DELETE FROM $tables[products_options_ex] WHERE option_id IN ('".implode("','", $ids)."')");
	}

	db_query("DELETE FROM $tables[product_options] WHERE product_option_id $where");
	db_query("DELETE FROM $tables[product_options_lng] WHERE product_option_id $where");

	return true;
}

function cw_variants_get_same($variant_id, $product_id) {
    global $tables;

    $vid = false;
    $name_where = cw_query_hash("SELECT $tables[product_options].class, $tables[product_options_values].name FROM $tables[product_variant_items], $tables[product_options], $tables[product_options_values] WHERE $tables[product_options].product_option_id = $tables[product_options_values].product_option_id AND $tables[product_options_values].option_id = $tables[product_variant_items].option_id AND $tables[product_variant_items].variant_id = '$variant_id'", "class", true, true);
    foreach ($name_where as $cn => $opts) {
        $name_where[$cn] = "($tables[product_options].class = '".addslashes($cn)."' AND $tables[product_options_values].name IN ('".implode("','", cw_addslashes($opts))."'))";
    }
    $name_where = " AND (".implode(" OR ", $name_where).")";

    $cnt = cw_query_first_cell("SELECT COUNT($tables[product_variant_items].option_id) as cnt FROM $tables[product_variants], $tables[product_variant_items] WHERE $tables[product_variants].variant_id = $tables[product_variant_items].variant_id AND $tables[product_variants].product_id = '$product_id' GROUP BY $tables[product_variants].variant_id ORDER BY cnt DESC");
    if (!empty($cnt))
        $vid = cw_query_first_cell("SELECT $tables[product_variant_items].variant_id, COUNT($tables[product_variant_items].option_id) as cnt FROM $tables[product_options], $tables[product_options_values], $tables[product_variant_items] WHERE $tables[product_options].product_id = '$product_id' AND $tables[product_options].product_option_id = $tables[product_options_values].product_option_id AND $tables[product_variant_items].option_id = $tables[product_options_values].option_id".$name_where." GROUP BY $tables[product_variant_items].variant_id HAVING cnt = '$cnt'");

    return $vid;
}

function cw_product_options_set_selected($product_options, $options) {
    if (is_array($product_options))
    foreach ($product_options as $k => $v) {
        if (preg_match("/^\d+$/S", $options[$v['product_option_id']])) {
            if ($v['type'] == 'T')
                $product_options[$k]['default'] = $options[$v['product_option_id']];
            else
                $product_options[$k]['options'][$options[$v['product_option_id']]]['selected'] = 'Y';
        }
        else
            $product_options[$k]['default'] = $options[$v['product_option_id']];
    }
    return $product_options;
}

function cw_delete_product_options_values($product_option_id) {
    global $tables;

    $opts = cw_query_column("select option_id from $tables[product_options_values] where product_option_id = '$product_option_id'");
    if (!empty($opts)) {
        db_query("delete from $tables[product_options_values] where product_option_id = '$product_option_id'");
        db_query("delete from $tables[product_options_values_lng] where option_id IN ('".implode("','", $opts)."')");
    }
}

function cw_product_options_delete_product($product_id = 0, $update_categories = true, $delete_all = false) {

    global $tables;

    if ($delete_all === true) {
        db_query("DELETE FROM $tables[product_options]");
        db_query("DELETE FROM $tables[product_options_values]");
        db_query("DELETE FROM $tables[product_options_values_lng]");
        db_query("DELETE FROM $tables[products_options_ex]");
        db_query("DELETE FROM $tables[product_options_js]");
        db_query("DELETE FROM $tables[product_variant_items]");
        db_query("DELETE FROM $tables[product_variants]");
        return true;
    }

    $options = cw_query_column("SELECT product_option_id FROM $tables[product_options] WHERE product_id='$product_id'");
    db_query("DELETE FROM $tables[product_options] where product_id='$product_id'");
    if (!empty($options)) {
            $values = cw_query_column("SELECT option_id FROM $tables[product_options_values] where product_option_id IN ('".implode("','", $options)."')");
            db_query("DELETE FROM $tables[product_options_lng] where product_option_id IN ('".implode("','", $options)."')");
            if (!empty($values)) {
                db_query("DELETE FROM $tables[product_options_values] where product_option_id IN ('".implode("','", $options)."')");
                db_query("DELETE FROM $tables[product_options_values_lng] WHERE option_id IN ('".implode("','", $values)."')");
                db_query("DELETE FROM $tables[products_options_ex] WHERE option_id IN ('".implode("','", $values)."')");
                db_query("DELETE FROM $tables[product_variant_items] WHERE option_id IN ('".implode("','", $values)."')");
            }
    }

    db_query("DELETE FROM $tables[product_options_js] WHERE product_id='$product_id'");
    $vids = db_query("SELECT variant_id FROM $tables[product_variants] WHERE product_id='$product_id'");
    if ($vids) {
            while ($row = db_fetch_array($vids))
                cw_image_delete($row['variant_id'], 'products_images_var');
            db_free_result($vids);
    }
    db_query("delete from $tables[product_variants] WHERE product_id='$product_id'");
}

function cw_product_options_product_check_avail($params) {
    global $tables;
    $is_variants_avail = cw_query_first_cell("select sum(avail) from $tables[products_warehouses_amount] where product_id='$params[product][productid]'");
    return $is_variants_avail > 0;
}

function cw_product_options_clone($product_id) {
	global $tables, $addons, $config;
	
	$new_product_id = cw_get_return();
	cw_core_copy_tables('product_options_js', 'product_id', $product_id, $new_product_id);
	
	$hash = array();
	$classes = cw_query("SELECT * FROM $tables[product_options] WHERE product_id = '$product_id'");
	
	if (!empty($classes)) {
		foreach ($classes as $v) {
			$options = cw_query("SELECT * FROM $tables[product_options_values] WHERE product_option_id = '$v[product_option_id]'");
			$old_classid = $v['product_option_id'];
			unset($v['product_option_id']);
			$v['product_id'] = $new_product_id;
			$v = cw_addslashes($v);
			$classid = cw_array2insert('product_options', $v);
			
			if ($options) {
				foreach ($options as $o) {
					$old_optionid = $o['option_id'];
					unset($o['option_id']);
					$o['product_option_id'] = $classid;
					$o = cw_addslashes($o);
					$optionid = cw_array2insert('product_options_values', $o);
					$hash[$old_optionid] = $optionid;
					cw_core_copy_tables('product_options_values_lng', 'option_id', $old_optionid, $optionid);
				}
			}

			cw_core_copy_tables('product_options_lng', 'product_option_id', $old_classid, $classid);
		}
	}

	// Clone product option exceptions
	if (!empty($hash)) {
		$hash_ex = array();
		$exceptions = cw_query("SELECT * FROM $tables[products_options_ex] WHERE option_id IN ('".implode("','", array_keys($hash))."')");
		
		if (!empty($exceptions)) {
			foreach ($exceptions as $v) {
				if (empty($hash[$v['option_id']]))
					continue;

				$v['option_id'] = $hash[$v['option_id']];
				
				if (empty($hash_ex[$v['exception_id']]))
					$hash_ex[$v['exception_id']] = cw_query_first_cell("SELECT MAX(exception_id) FROM $tables[product_options_ex]")+1;

				$v['exception_id'] = $hash_ex[$v['exception_id']];
				cw_array2insert('products_options_ex', $v);
			}
		}

		unset($hash_ex);
	}

	// Clone product option variants
	$variants = db_query("SELECT * FROM $tables[product_variants] WHERE product_id = '$product_id' ORDER BY variant_id");
	if ($variants) {
		while ($v = db_fetch_array($variants)) {
			$old_variantid = $v['variant_id'];
			$v['product_id'] = $new_product_id;
			unset($v['variant_id']);
			$v['productcode'] = cw_product_generate_sku();
			
			if ($addons['barcode'] && $config['barcode']['gen_product_code']) {
				$v['eancode'] = cw_product_generate_sku($config['barcode']['gen_product_code'], 'eancode');
			}
			else {
				$v['eancode'] = cw_product_generate_sku(0, 'eancode');
			}
			//cw_ean_clear($v['eancode']);

			$v = cw_addslashes($v);
			$variantid = cw_array2insert('product_variants', $v);

			// Add Variant items
			$items = cw_query("SELECT option_id FROM $tables[product_variant_items] WHERE variant_id = '$old_variantid'");
			if (!empty($items)) {
				foreach($items as $i) {
					if (isset($hash[$i['option_id']])) {
						db_query("INSERT INTO $tables[product_variant_items] (variant_id, option_id) VALUES ('$variantid', '".$hash[$i['option_id']]."')");
					}
				}
			}
			
		    // warehouse
		    if ($addons['warehouse']) {
		    	$items = cw_query("SELECT * FROM $tables[products_warehouses_amount] WHERE variant_id = '$old_variantid' AND product_id = '$product_id'");
				if (!empty($items)) {
					foreach($items as $i) {
						db_query("INSERT INTO $tables[products_warehouses_amount] (product_id, warehouse_customer_id, avail, avail_ordered, avail_sold, avail_reserved, variant_id) VALUES ('$new_product_id', '".$i['warehouse_customer_id']."', '".$i['avail']."', '".$i['avail_ordered']."', '".$i['avail_sold']."', '".$i['avail_reserved']."', '$variantid')");
					}
				}
		    }

			// Add Variant prices
			$prices = cw_query("SELECT * FROM $tables[products_prices] WHERE variant_id = '$old_variantid' AND product_id = '$product_id'");	
			if ($prices) {
				foreach($prices as $p) {
					unset($p['price_id']);
					$p['variant_id'] = $variantid;
					$p['product_id'] = $new_product_id;
					cw_array2insert('products_prices', $p);
				}
			}
			
			// Add Variant thumbnails & variant images
			cw_core_copy_tables('products_images_var', 'id', $old_variantid, $variantid);
		}
		db_free_result($variants);
	}
	
	return $new_product_id;
}

function cw_product_has_variants($product_id) {
    global $tables;
    return cw_query_first_cell("SELECT count(*) FROM $tables[product_options] WHERE product_id = '$product_id' AND type=''") > 0;
};

function cw_product_has_options($product_id) {
    global $tables;
    return cw_query_first_cell("SELECT count(*) FROM $tables[product_options] WHERE product_id = '$product_id'") > 0;
};

function cw_product_options_prepare_products_found(&$products, $data, $user_account, $info_type) {
    global $tables;

    if (!empty($data['attributes']) && !empty($products)) {

        foreach ($products as $_key => $_product) {
            $attr_def_values = array();

            foreach ($data['attributes'] as $k => $v) {

                if (in_array($k, array('price', 'substring'))) continue;
                if (!is_array($v)) $v = array($v);
                if (!is_numeric($v[0])) continue;

                $attr_def_values = array_merge($attr_def_values, $v);
            }

            $result = cw_query(
                "SELECT pp.price, pp.variant_id, pv.productcode, pw.avail, pvi.option_id
                FROM $tables[attributes_default] ad
                JOIN $tables[product_options_values] pov ON pov.name = ad.value
                JOIN $tables[product_options] po ON po.product_option_id = pov.product_option_id
                JOIN $tables[product_variant_items] pvi ON pvi.option_id = pov.option_id
                JOIN $tables[product_variants] pv ON pv.variant_id = pvi.variant_id
                JOIN $tables[products_prices] pp ON pp.variant_id = pvi.variant_id
                JOIN $tables[products_warehouses_amount] pw ON pw.product_id = pp.product_id
                    AND pw.variant_id = pp.variant_id
                    AND pw.warehouse_customer_id = '" . $user_account['warehouse_customer_id'] . "'
                    WHERE ad.attribute_value_id in ('" . implode("','", $attr_def_values). "')
                        AND po.product_id = '" . $_product['product_id'] . "' AND po.avail=1 AND pov.avail=1"
            );

            if (!empty($result)) {
                $products[$_key]['price']       = $result[0]['price'];
                $products[$_key]['avail']       = $result[0]['avail'];
                $products[$_key]['productcode'] = $result[0]['productcode'];
                $products[$_key]['variant_id']  = $result[0]['variant_id'];

                $first_option_ids = array();
                $option_ids = array();
                foreach ($result as $_v) {
                    $option_ids[] = $_v['option_id'];
                }
                $option_ids = array_unique($option_ids);

                // Product options
                $product_options = cw_call('cw_get_product_classes', array($_product['product_id']));

                if (!empty($product_options)) {

                    foreach ($product_options as $_k => $_v) {
                        foreach ($option_ids as $option_id) {
                            // Hide selected option
                            if (isset($product_options[$_k]['options'][$option_id])) {

                                if (count($product_options) == 1) {
                                    unset($product_options[$_k]);
                                }
                                else {
                                    $product_options[$_k]['options'] = array($option_id => $product_options[$_k]['options'][$option_id]);
                                    $product_options[$_k]['hidden'] = 1;
                                }
                                break 1;
                            }
                        }
                        if (is_array($product_options[$_k]) && is_array($product_options[$_k]['options'])) {
                            reset($product_options[$_k]['options']);
                            $first_option_ids[] = key($product_options[$_k]['options']);
                        }
                    }
                }

                if (!empty($product_options)) {
                    $products[$_key]['options'] = array_values($product_options);

                    // Product options ex
                    $products_options_ex = cw_get_product_exceptions($_product['product_id']);
                    if (!empty($products_options_ex)) {
                        $products[$_key]['options_ex'] = $products_options_ex;
                    }

                    // Product variants
                    $variants = cw_get_product_variants($_product['product_id'], $user_account['membership_id']);

                    if (!empty($variants)) {
                        $products[$_key]['variants'] = $variants;

                        // Find the selected variant
                        $selected_variant_id = $result[0]['variant_id'];

                        if (!empty($first_option_ids)) {

                            foreach ($variants as $vk => $variant) {
                                $match = TRUE;
                                foreach ($variant['options'] as $_k => $_v) {

                                    if (!in_array($_k, $first_option_ids)) {
                                        $match = FALSE;
                                        break 1;
                                    }
                                }

                                if ($match) {
                                    $selected_variant_id = $vk;
                                    break 1;
                                }
                            }
                        }

                        // If find variant, then change start values
                        if ($selected_variant_id != $result[0]['variant_id']) {
                            $products[$_key]['price']       = $variants[$selected_variant_id]['price'];
                            $products[$_key]['avail']       = $variants[$selected_variant_id]['avail'];
                            $products[$_key]['productcode'] = $variants[$selected_variant_id]['productcode'];
                            $products[$_key]['variant_id']  = $selected_variant_id;
                        }

                        if (
                            $info_type & 128
                            && !empty($variants[$selected_variant_id]['image'])
                        ) {
                            $products[$_key]['variants']['image_thumb'] = $variants[$selected_variant_id]['image'];
                        }

                        $products[$_key]['image_det'] = cw_image_get('products_images_det', $_product['product_id']);

                        $min_avail = cw_query_first_cell("SELECT min_amount FROM $tables[products] WHERE product_id = '$_product[product_id]'");

                        if (!$min_avail) {
                            $min_avail = 1;
                        }
                        $products[$_key]['min_avail'] = $min_avail;
                    }
                }
            }
        }
    }
}
