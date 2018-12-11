<?php




function cw_ps_get_offers($active=true, $bundles=false) {
    global $tables, $domain_attributes, $addons, $smarty;

    $fields = $from_tbls = $query_joins = $where = $groupbys = $having = $orderbys = array();

    $fields = array("$tables[ps_offers].offer_id as hash_id", "$tables[ps_offers].offer_id", 'title', 'description', 'enddate');
    $from_tbls[] = 'ps_offers';


    if (isset($addons['multi_domains'])) {

        $conditions = cw_md_get_available_domains();

        if ($conditions !== false) {
            $query_joins['attributes_values'] = array(
                'on' => "$tables[ps_offers].offer_id = $tables[attributes_values].item_id and $tables[attributes_values].attribute_id='" . $domain_attributes[PS_ATTR_ITEM_TYPE] . "' and $tables[attributes_values].value in " . $conditions,
           		'is_inner' => 1,
            );
        }
    }

    $query_joins['ps_bonuses'] = array(
        'on' => "$tables[ps_offers].offer_id = $tables[ps_bonuses].offer_id",
   		'is_inner' => 1,
    );

    $query_joins['ps_conditions'] = array(
        'on' => "$tables[ps_offers].offer_id = $tables[ps_conditions].offer_id",
   		'is_inner' => 1,
    );

	if ($active) {
		$where[] = "$tables[ps_offers].startdate<= '" . CURRENT_TIME . "'";
		$where[] = "$tables[ps_offers].enddate >='" .(CURRENT_TIME - SECONDS_PER_DAY)."'"; // better to use constant in right part than enddate+SECONDS_PER_DAY>=CURRENT_TIME
		$where[] = "$tables[ps_offers].active='1'";
	}
    
    if (!$bundles) $where[] = "$tables[ps_offers].pid=0";

    $having[] = "COUNT($tables[ps_bonuses].bonus_id) > 0";
    $having[] = "COUNT($tables[ps_conditions].cond_id) > 0";

    $groupbys[] = "$tables[ps_offers].offer_id";

    $search_query = cw_db_generate_query($fields, $from_tbls, $query_joins, $where, $groupbys, $having, $orderbys);

    $offers = cw_stripslashes(cw_query_hash($search_query, 'hash_id', false));

    if (!empty($offers) && is_array($offers)) {

        $fields = $from_tbls = $query_joins = $where = $groupbys = $having = $orderbys = array();
        $fields[] = '*';
        $from_tbls[] = PS_IMG_TYPE;
        $where[] = "id IN ('" . implode("', '", array_keys($offers)) . "')";
        $where[] = "avail = 1";

        $search_query = cw_db_generate_query($fields, $from_tbls, $query_joins, $where, $groupbys, $having, $orderbys);
        $offer_images = cw_query_hash($search_query, 'id', false);

        $image_width = array();

        if (!empty($offer_images) && is_array($offer_images)) {
            cw_load('image');
            foreach ($offer_images as $i => $image_data) {
                $offers[$i]['img'] = cw_image_info(PS_IMG_TYPE, $image_data);
                $image_width[] = $offers[$i]['img']['image_x'];
            }

            $smarty->assign('ps_image_width', max($image_width));
        }

    }

    return $offers;

}





function cw_ps_get_featured_offer() {
    global $tables, $domain_attributes, $addons, $smarty;


    $fields = $from_tbls = $query_joins = $where = $groupbys = $having = $orderbys = array();

    $fields = array("$tables[ps_offers].offer_id", "$tables[ps_offers].offer_id as id", 'title', 'description', 'enddate', 'priority', 'position');

    $offer = array();


    $from_tbls[] = 'ps_offers';

    if (isset($addons['multi_domains'])) {

        $conditions = cw_md_get_available_domains();

        if ($conditions !== false) {
            $query_joins['attributes_values'] = array(
                'on' => "$tables[ps_offers].offer_id = $tables[attributes_values].item_id and $tables[attributes_values].item_type = '" . PS_ATTR_ITEM_TYPE . "' and $tables[attributes_values].attribute_id='" . $domain_attributes[PS_ATTR_ITEM_TYPE] . "' and $tables[attributes_values].value in " . $conditions,
           		'is_inner' => 1,
            );
        }
    }

    $query_joins['ps_bonuses'] = array(
        'on' => "$tables[ps_offers].offer_id = $tables[ps_bonuses].offer_id",
   		'is_inner' => 1,
    );

    $query_joins['ps_conditions'] = array(
        'on' => "$tables[ps_offers].offer_id = $tables[ps_conditions].offer_id",
   		'is_inner' => 1,
    );

    $where[] = "$tables[ps_offers].enddate >= '" . cw_core_get_time() . "'";

    $having[] = "COUNT($tables[ps_bonuses].bonus_id) > 0";
    $having[] = "COUNT($tables[ps_conditions].cond_id) > 0";

    $groupbys[] = "$tables[ps_offers].offer_id";

    $orderbys[] = 'priority DESC';
    $orderbys[] = 'position';


    $_query_joins = $query_joins;
    $_where = $where;
    $_having = $having;
    $_groupbys = $groupbys;
    $_fields = $fields;


    global $customer_id, $user_info;

    $customer_zone = null;

    if (isset($customer_id) && !empty($customer_id)) {
        if (!isset($user_info) || empty($user_info)) {
            $user_info = cw_user_get_info($customer_id, 1);
        }

        //cw_user_get_info($customer_id, 256)
        //cw_user_get_addresses
        //cw_user_get_addresses_smarty
        //cw_user_get_default_address
        //if ($info_type & 256)


        //$zones = cw_call('cw_cart_get_zones', array('address' => $address, 'is_shipping' => 1));
        $customer_zone = cw_func_call('cw_cart_get_zone_ship', array('address' => $user_info['current_address'], 'type' => 'D'));

    }


    //let's try to find an offer with the required category defined in the offer's condition

    global $cat;

    if (isset($cat) && !empty($cat)) {

        $query_joins['ps_cond_details'] = array(
            'on' => "$tables[ps_offers].offer_id = $tables[ps_cond_details].offer_id",
       		'is_inner' => 1,
        );

        $where[] = "$tables[ps_cond_details].object_type = '" . PS_OBJ_TYPE_CATS . "'";
        $where[] = "$tables[ps_cond_details].object_id = '" . $cat . "'";

        $search_query = cw_db_generate_query($fields, $from_tbls, $query_joins, $where, $groupbys, $having, $orderbys);

        if (empty($customer_zone)) {
            $offer = cw_stripslashes(cw_query_first($search_query . ' LIMIT 1'));
        } else {
            $cat_offers = cw_stripslashes(cw_query_hash($search_query, 'offer_id', false));

            if (!empty($cat_offers)) {

                $fields = $from_tbls = $query_joins = $where = $groupbys = $having = $orderbys = array();
                $from_tbls[] = 'ps_cond_details';
                $fields[] = 'offer_id';
                $where[] = "($tables[ps_cond_details].object_type = '" . PS_OBJ_TYPE_ZONES . "' AND $tables[ps_cond_details].object_id != '$customer_zone')";
                $where[] = "$tables[ps_cond_details].offer_id IN ('" . implode("', '", array_keys($cat_offers)) .  "')";

                $search_query = cw_db_generate_query($fields, $from_tbls, $query_joins, $where, $groupbys, $having, $orderbys);
                $zone_offers = cw_stripslashes(cw_query_hash($search_query, 'offer_id', false));

                $suitable_offers = array_diff_key($cat_offers, $zone_offers);

                if (!empty($suitable_offers)) {
                    $offer = array_shift($suitable_offers);
                    unset($suitable_offers);
                }

            }

        }

    }


    //if there is no suitable offer in the store, let's take the first one

    if (empty($offer)) {

        $query_joins = $_query_joins;
        $where = $_where;
        $having = $_having;
        $groupbys = $_groupbys;
        $fields = $_fields;

        if (!empty($customer_zone)) {

            $query_joins['ps_cond_details'] = array(
                'on' => "$tables[ps_offers].offer_id = $tables[ps_cond_details].offer_id"
            );

            $where[] = "($tables[ps_cond_details].object_type = '" . PS_OBJ_TYPE_ZONES . "' AND $tables[ps_cond_details].object_id = '$customer_zone')";

            $search_query = cw_db_generate_query($fields, $from_tbls, $query_joins, $where, $groupbys, $having, $orderbys);
            $offer = cw_stripslashes(cw_query_first($search_query . ' LIMIT 1'));

            if (empty($offer)) {
                $where = $_where;
                $where[] = "($tables[ps_cond_details].object_type != '" . PS_OBJ_TYPE_ZONES . "' OR $tables[ps_cond_details].object_type IS NULL)";

                $search_query = cw_db_generate_query($fields, $from_tbls, $query_joins, $where, $groupbys, $having, $orderbys);
                $offer = cw_stripslashes(cw_query_first($search_query . ' LIMIT 1'));
            }

        } else {
            $search_query = cw_db_generate_query($fields, $from_tbls, $query_joins, $where, $groupbys, $having, $orderbys);
            $offer = cw_stripslashes(cw_query_first($search_query . ' LIMIT 1'));
        }

        unset($_query_joins, $_where, $_having, $_groupbys);

    }


    if (!empty($offer)) {
        $fields = $from_tbls = $query_joins = $where = $groupbys = $having = $orderbys = array();
        $fields[] = '*';
        $from_tbls[] = PS_IMG_TYPE;
        $where[] = "id = '$offer[id]'";
        $where[] = "avail = 1";

        $search_query = cw_db_generate_query($fields, $from_tbls, $query_joins, $where, $groupbys, $having, $orderbys);
        $offer_image_info = cw_query_first($search_query);

        if (!empty($offer_image_info) && is_array($offer_image_info)) {
            $offer['img'] = cw_image_info(PS_IMG_TYPE, $offer_image_info);
            $smarty->assign('ps_image_width', $offer['img']['image_x']);
        }

    }

    return $offer;

}




function cw_ps_get_customer_offers(&$cart, &$products, $_user_info = array()) {
    global $tables, $domain_attributes, $addons, $smarty;

    static $offers_hash;

    if (empty($cart) || empty($products)) {
        return array();
    }


    global $customer_id, $user_info;

    $customer_zone = null;

    if (isset($customer_id) && !empty($customer_id)) {
        if (!isset($user_info) || empty($user_info)) {
            $user_info = cw_user_get_info($customer_id, 1);
        }
    } else {
        $user_info = $_user_info;
        if (empty($_user_info)) {
            $user_info = $cart['userinfo'];
        }
    }

    $customer_zone = cw_func_call('cw_cart_get_zone_ship', array('address' => $user_info['current_address'], 'type' => 'D'));


    list($_products, $_categories, $_manufacturers) = cw_ps_normalize_products($products);

    if (empty($_products) || empty($_categories)) {
        return array();
    }

    //$hash_key = md5(serialize($_products) . serialize($user_info));

    $shipping_address = array();
    $shipping_address_fields = array('country', 'state', 'zipcode', 'city', 'address');

    if (!isset($user_info) || empty($user_info)) {
        foreach ($shipping_address_fields as $field) {
            if (isset($user_info['current_address'][$field])) {
                $shipping_address[] = $user_info['current_address'][$field];
            }
        }
    }

    $hash_key = md5(serialize($_products) . serialize($shipping_address));

    if (!isset($offers_hash[$hash_key])) {
        $offers_hash[$hash_key] = array();
    } else {
        return $offers_hash[$hash_key];
    }


    $fields = $from_tbls = $query_joins = $where = $groupbys = $having = $orderbys = array();

    $fields = array("$tables[ps_offers].offer_id");

    $offers = array();


    $from_tbls[] = 'ps_offers';

    if (isset($addons['multi_domains'])) {

        $conditions = cw_md_get_available_domains();

        if ($conditions !== false) {
            $query_joins['attributes_values'] = array(
                'on' => "$tables[ps_offers].offer_id = $tables[attributes_values].item_id and $tables[attributes_values].item_type = '" . PS_ATTR_ITEM_TYPE . "' and $tables[attributes_values].attribute_id='" . $domain_attributes[PS_ATTR_ITEM_TYPE] . "' and $tables[attributes_values].value in " . $conditions,
           		'is_inner' => 1,
            );
        }
    }

    $query_joins['ps_bonuses'] = array(
        'on' => "$tables[ps_offers].offer_id = $tables[ps_bonuses].offer_id",
   		'is_inner' => 1,
    );

    $query_joins['ps_conditions'] = array(
        'on' => "$tables[ps_offers].offer_id = $tables[ps_conditions].offer_id",
   		'is_inner' => 1,
    );

    $where[] = "$tables[ps_offers].enddate >= '" . cw_core_get_time() . "'";
    $where[] = "$tables[ps_offers].active = '1'";

    $having[] = "COUNT($tables[ps_bonuses].bonus_id) > 0";
    $having[] = "COUNT($tables[ps_conditions].cond_id) > 0";

    $groupbys[] = "$tables[ps_offers].offer_id";

    $orderbys[] = 'priority DESC';
    $orderbys[] = 'position';


    $_query_joins = $query_joins;
    $_where = $where;
    $_having = $having;
    $_groupbys = $groupbys;
    $_fields = $fields;


    // let's take offers suitable by date and domain
    $search_query = cw_db_generate_query($fields, $from_tbls, $query_joins, $where, $groupbys, $having, $orderbys);
    $offers = cw_query_hash($search_query, 'offer_id', false);


    if (empty($offers) || !is_array($offers)) {
        return array();
    }


    // let's take offers with the subtotal condition defined

    $offers_condition = "$tables[ps_cond_details].offer_id IN ('" . implode("', '", array_keys($offers)) .  "')";

    $fields = $from_tbls = $query_joins = $where = $groupbys = $having = $orderbys = array();

    $fields = array('offer_id');

    $from_tbls[] = 'ps_conditions';



    //TODO: update the code below


    //echo '<pre>', print_r($tmp_cart), '</pre>';

    $discounted_subtotal = 0;

    //if (!isset($cart['info']['discounted_subtotal'])) {
        //$cart['discounted_subtotal'] = 10;

        $tmp_cart = array();

        $products_warehouses = cw_cart_get_products_warehouses($products);

        if (!empty($products_warehouses) && is_array($products_warehouses)) {

        	foreach ($products_warehouses as $warehouse_id) {
                $tmp_products = cw_get_products_by_warehouse ($products, $warehouse_id);

                $result = cw_func_call('cw_cart_calc_single', array('cart' => $cart, 'products' => $tmp_products, 'userinfo' => $user_info, 'warehouse_id' => $warehouse_id));
                //echo '<pre>result: ', print_r($result), '</pre>';
                $tmp_cart = cw_func_call('cw_cart_summarize', array('res' => $result, 'warehouse_id' => $warehouse_id), $tmp_cart);
            }
            unset($tmp_products);
        } else {
            $warehouse_id = 0;
            $result = cw_func_call('cw_cart_calc_single', array('cart' => $cart, 'products' => $products, 'userinfo' => $user_info, 'warehouse_id' => $warehouse_id));
            $tmp_cart = cw_func_call('cw_cart_summarize', array('res' => $result, 'warehouse_id' => $warehouse_id), $tmp_cart);
        }

        if (!empty($tmp_cart) && is_array($tmp_cart)) {
            if (isset($tmp_cart['info']['discounted_subtotal'])) {
                $discounted_subtotal = $tmp_cart['info']['discounted_subtotal'];
                if ($tmp_cart['info']['discounted_subtotal'] > $tmp_cart['info']['subtotal']) {
                    $discounted_subtotal = $tmp_cart['info']['subtotal'];
                }
            }
        }
        unset($tmp_cart);

    //}

    //echo '<pre>', "discounted_subtotal: $discounted_subtotal", '</pre>';
    //echo '<pre>tmp_cart: ', print_r($tmp_cart), '</pre>';
    //die;


    $where[] = "type = '" . PS_TOTAL . "'";
    $where[] = "total > $discounted_subtotal";
    $where[] = "offer_id IN ('" . implode("', '", array_keys($offers)) .  "')";

    $search_query = cw_db_generate_query($fields, $from_tbls, $query_joins, $where, $groupbys, $having, $orderbys);
    $not_allowed_offers = cw_query_hash($search_query, 'offer_id', false);

    //echo '<pre>1 ', print_r($offers), '</pre>';
    //echo '<pre>2 ', print_r($not_allowed_offers), '</pre>';

    if (!empty($not_allowed_offers) && is_array($not_allowed_offers)) {
        $offers = array_diff_key($offers, $not_allowed_offers);
    }
    //echo '<pre>3 ', print_r($offers), '</pre>';
    //die;


    if (empty($offers) || !is_array($offers)) {
        return array();
    }


    // let's take offers with the destination zone defined

    $fields = $from_tbls = $query_joins = $where = $groupbys = $having = $orderbys = array();

    $fields = array('offer_id');
    $from_tbls[] = 'ps_cond_details';

    if (!empty($customer_zone)) {

        $where[] = "object_type = '" . PS_OBJ_TYPE_ZONES . "'";
        $where[] = "object_id != '" . $customer_zone . "'";
        $where[] = "offer_id IN ('" . implode("', '", array_keys($offers)) .  "')";

        $search_query = cw_db_generate_query($fields, $from_tbls, $query_joins, $where, $groupbys, $having, $orderbys);
        $not_allowed_offers = cw_query_hash($search_query, 'offer_id', false);

        //die(var_dump($offers, $customer_zone, $not_allowed_offers));
        if (!empty($not_allowed_offers) && is_array($not_allowed_offers)) {
            $offers = array_diff_key($offers, $not_allowed_offers);
        }

    } else {

        $where[] = "object_type = '" . PS_OBJ_TYPE_ZONES . "'";
        $where[] = "offer_id IN ('" . implode("', '", array_keys($offers)) .  "')";

        $search_query = cw_db_generate_query($fields, $from_tbls, $query_joins, $where, $groupbys, $having, $orderbys);
        $not_allowed_offers = cw_query_hash($search_query, 'offer_id', false);

        if (!empty($not_allowed_offers) && is_array($not_allowed_offers)) {
            $offers = array_diff_key($offers, $not_allowed_offers);
        }

    }


    if (empty($offers) || !is_array($offers)) {
        return array();
    }


    //let's take offers with suitable products

    //list($_products, $_categories, $_manufacturers) = cw_ps_normalize_products($products);


    foreach ($offers as $offer_id => $trash) {

        $checking_result = cw_ps_check_offer($offer_id, $_products);
        if ($checking_result == false) {
            unset($offers[$offer_id]);
        }

    }


    //let's delete offers which cannot be applied after the application of the first ones by priority
    //die(var_dump($offers, $customer_zone));

    $first_offer = null;

    foreach ($offers as $offer_id => $trash) {

        if (empty($first_offer)) {
            $first_offer = $offer_id;
            cw_ps_update_conditions($offer_id, $_products, $_categories, $_manufacturers);
            continue;
        }

        $checking_result = false;
        if (!empty($_products)) {
            $checking_result = cw_ps_check_offer($offer_id, $_products);
        }

        if ($checking_result == false) {
            unset($offers[$offer_id]);
        } else {
            cw_ps_update_conditions($offer_id, $_products, $_categories, $_manufacturers);
        }

    }
    //var_dump($offers, $_categories, $_manufacturers, $_products);
    //die('end');


    $offers_hash[$hash_key] = $offers;
    unset($offers);

    return $offers_hash[$hash_key];

}




function cw_ps_check_offer($offer_id, &$products) {

    if (empty($offer_id) || empty($products) || !is_array($products)) {
        return false;
    }


    list($cond_categories, $cond_manufacturers, $cond_products) = cw_get_offer_conditions($offer_id);


    //categories

    $prod_categories = cw_ps_get_prods_data($products, PS_OBJ_TYPE_CATS);

    if (empty($prod_categories) || !is_array($prod_categories)) {
        return false;
    }


    $cheking_result = true;

    if (!empty($cond_categories)) {

        foreach ($cond_categories as $category => $cond_cat_data) {
            if (isset($prod_categories[$category])) {
                if ($prod_categories[$category] < $cond_cat_data['quantity']) {
                    $cheking_result = false;
                    break;
                }
            } else {
                if (!empty($cond_cat_data['subcats'])) {
                    foreach ($cond_cat_data['subcats'] as $subcategory => $quantity) {
                        if (isset($prod_categories[$subcategory])) {
                            if ($prod_categories[$subcategory] < $cond_cat_data['quantity']) {
                                $cheking_result = false;
                                break 2;
                            }
                        } else {
                            $cheking_result = false;
                            break 2;
                        }
                    }
                } else {
                    $cheking_result = false;
                    break;
                }
            }
        }

        //var_dump('categories: ' . (int)$cheking_result);

        if ($cheking_result == false) {
            return false;
        }

    }



    //manufacturers

    $prod_manufacturers = cw_ps_get_prods_data($products, PS_OBJ_TYPE_MANS);


    if (!empty($cond_manufacturers)) {
        if (!empty($prod_manufacturers) && is_array($prod_manufacturers)) {

            foreach ($cond_manufacturers as $manufacturer => $quantity) {
                if (isset($prod_manufacturers[$manufacturer])) {
                    if ($prod_manufacturers[$manufacturer] < $quantity) {
                        $cheking_result = false;
                        break;
                    }
                } else {
                    $cheking_result = false;
                    break;
                }
            }

            //var_dump('manufacturers: ' . (int)$cheking_result);

            if ($cheking_result == false) {
                return false;
            }

        } else {
            return false;
        }
    }



    // products

    $prod_products = cw_ps_get_prods_data($products, PS_OBJ_TYPE_PRODS);

    if (empty($prod_products) || !is_array($prod_products)) {
        return false;
    }

    //var_dump($cond_products, $prod_products);


    if (!empty($cond_products)) {

        foreach ($cond_products as $product => $quantity) {
            if (isset($prod_products[$product])) {
                if ($prod_products[$product] < $quantity) {
                    $cheking_result = false;
                    break;
                }
            } else {
                $cheking_result = false;
                break;
            }
        }

        //var_dump('products: ' . (int)$cheking_result);

        if ($cheking_result == false) {
            return false;
        }

    }

    return $cheking_result;

    //die(var_dump($cond_categories, $cond_manufacturers, $cond_products, $prod_categories));

}




function cw_ps_get_prods_data(&$products, $type) {
    static $product_categories;

    $type = (int)$type;
    $types = array(
        PS_OBJ_TYPE_CATS => 1,
        PS_OBJ_TYPE_MANS => 1,
        PS_OBJ_TYPE_PRODS => 1
    );

    if (empty($products) || !is_array($products) || empty($type) || !isset($types[$type])) {
        return array();
    }

    if (!isset($product_categories)) {
        $product_categories = array();
    }


    //this solution can be updated by including product IDs only fromt the array as the array can be huge...

    $hash_key = md5(serialize($products));

    if (!isset($product_categories[$hash_key])) {

        $categories = array();
        $manufacturers = array();
        $_products = array();

        foreach ($products as $product) {
            if (!isset($categories[$product['category_id']])) {
                $categories[$product['category_id']] = $product['amount'];
            } else {
                $categories[$product['category_id']] += $product['amount'];
            }

            if (!isset($manufacturers[$product['manufacturer_id']])) {
                $manufacturers[$product['manufacturer_id']] = $product['amount'];
            } else {
                $manufacturers[$product['manufacturer_id']] += $product['amount'];
            }

            if (!isset($_products[$product['product_id']])) {
                $_products[$product['product_id']] = $product['amount'];
            } else {
                $_products[$product['product_id']] += $product['amount'];
            }
        }

        $product_categories[$hash_key] = array(
            PS_OBJ_TYPE_CATS => $categories,
            PS_OBJ_TYPE_MANS => $manufacturers,
            PS_OBJ_TYPE_PRODS => $_products
        );

        unset($categories, $manufacturers, $_products);

    }

    return $product_categories[$hash_key][$type];
}




function cw_ps_array2str(&$data, $prefix=null, $sep='', $key='') {

    if (empty($data)) {
        return null;
    }

    $ret = array();

    foreach((array)$data as $k => $v) {
        if(is_int($k) && $prefix != null) {
            $k = $prefix . $k;
        }
        if(!empty($key)) {
            $k = $key . '[' . $k . ']';
        }

        if(is_array($v) || is_object($v)) {
            array_push($ret, cw_ps_array2str($v, null, $sep, $k));
        }
        else {
            array_push($ret, $k . '=' . $v);
        }
    }

    if(empty($sep)) {
        $sep = ini_get('arg_separator.output');
    }

    $result = implode($sep, $ret);
    unset($ret);

    $result = $result ? preg_replace("/([\s]+)/", ' ', $result) : null;
    $result = $result ? preg_replace("/&$/", '', $result) : null;

    return $result;
}



function cw_ps_apply_offers(&$cart, &$products, $offers) {

    if (empty($cart) || empty($products) || empty($offers)) {
        return;
    }


    foreach ($offers as $offer) {

        list($discounts, $free_products, $free_shipping, $coupon) = cw_ps_get_offer_details($offer);

    }

}



function cw_ps_get_offer_details($offer) {
    static $offer_details;

    $discounts = $free_products = $free_shipping = array();
    $coupon = null;

    $result = array($discounts, $free_products, $free_shipping, $coupon);

    if (empty($offer)) {
        return $result;
    }

    if (!isset($offer_details[$offer])) {
        $offer_details[$offer] = $result;
    }

    // discounts



}




function cw_ps_update_conditions($offer_id, &$products, &$categories, &$manufacturers) {

    if (empty($offer_id) || empty($products) || !is_array($products) || empty($categories)) {
        return false;
    }

    list($cond_categories, $cond_manufacturers, $cond_products) = cw_get_offer_conditions($offer_id);


    if (!empty($cond_products)) {

        foreach ($cond_products as $product_id => $quantity) {
            if (isset($products[$product_id])) {

                $products[$product_id]['amount'] -= $quantity;

                if ($products[$product_id]['amount'] <= 0) {
                    unset($products[$product_id]);
                }
            }
        }

    }


    if (!empty($cond_categories)) {
        foreach ($cond_categories as $category_id => $cat_data) {
            if (isset($categories[$category_id]) && !empty($categories[$category_id]['products'])) {

                foreach ($categories[$category_id]['products'] as $product_id => $orig_amount) {

                    $tmp_quantity = $cat_data['quantity'];

                    $cat_data['quantity'] -= $products[$product_id]['amount'];

                    if ($cat_data['quantity'] >= 0) {

                        unset($categories[$category_id]['products'][$product_id]);

                        if (isset($products[$product_id])) {

                            $products[$product_id]['amount'] -= $products[$product_id]['amount'];

                            if ($products[$product_id]['amount'] <= 0) {
                                unset($products[$product_id]);
                            }
                        }

                    } else {
                        $products[$product_id]['amount'] -= $tmp_quantity;
                        $categories[$category_id]['products'][$product_id] = $products[$product_id]['amount'];
                        break;
                    }
                }

            }
        }
    }


    if (!empty($cond_manufacturers)) {
        foreach ($cond_manufacturers as $manufacturer_id => $quantity) {
            if (isset($manufacturers[$manufacturer_id]) && !empty($manufacturers[$manufacturer_id]['products'])) {

                foreach ($manufacturers[$manufacturer_id]['products'] as $product_id => $orig_amount) {

                    $tmp_quantity = $quantity;

                    $quantity -= $products[$product_id]['amount'];

                    if ($quantity >= 0) {

                        unset($manufacturers[$manufacturer_id]['products'][$product_id]);

                        if (isset($products[$product_id])) {

                            $products[$product_id]['amount'] -= $products[$product_id]['amount'];

                            if ($products[$product_id]['amount'] <= 0) {
                                unset($products[$product_id]);
                            }
                        }

                    } else {
                        $products[$product_id]['amount'] -= $tmp_quantity;
                        $manufacturers[$manufacturer_id]['products'][$product_id] = $products[$product_id]['amount'];
                        break;
                    }
                }

            }
        }
    }


}




function cw_get_offer_conditions($offer_id) {
    static $offer_conditions;

    $cond_categories = $cond_manufacturers = $cond_products = array();

    $result = array($cond_categories, $cond_manufacturers, $cond_products);

    if (empty($offer_id)) {
        return $result;
    }

    if (!isset($offer_conditions)) {
        $offer_conditions = array();
    }

    if (isset($offer_conditions[$offer_id])) {
        return $offer_conditions[$offer_id];
    }


    //product categories
    $fields = $from_tbls = $query_joins = $where = $groupbys = $having = $orderbys = array();

    $fields = array('object_id', 'quantity');

    $from_tbls[] = 'ps_cond_details';

    $where[] = "object_type = '" . PS_OBJ_TYPE_CATS . "'";
    $where[] = "offer_id = '$offer_id'";

    $search_query = cw_db_generate_query($fields, $from_tbls, $query_joins, $where, $groupbys, $having, $orderbys);
    $cond_categories = cw_query_hash($search_query, 'object_id', false, true);


    $result_cond_categories = array();

    if (!empty($cond_categories) && is_array($cond_categories)) {

        foreach ($cond_categories as $category => $quantity) {

            $cond_categories[$category] = array('subcats' => array(), 'quantity' => $quantity);

            $sub_cond_categories = cw_category_get_subcategory_ids($category);

            if (!empty($sub_cond_categories) && is_array($sub_cond_categories)) {

                $sub_cond_categories = array_fill_keys($sub_cond_categories, $quantity);
                $cond_categories[$category] = array('subcats' => $sub_cond_categories, 'quantity' => $quantity);

                $result_cond_categories += $sub_cond_categories;
            }
        }

        unset($sub_cond_categories);

    }



    // manufacturers
    $where = array();

    $where[] = "object_type = '" . PS_OBJ_TYPE_MANS . "'";
    $where[] = "offer_id = '$offer_id'";

    $search_query = cw_db_generate_query($fields, $from_tbls, $query_joins, $where, $groupbys, $having, $orderbys);
    $cond_manufacturers = cw_query_hash($search_query, 'object_id', false, true);

    if (!is_array($cond_manufacturers)) {
        $cond_manufacturers = array();
    }


    // attributes
    $where = array();

    $where[] = "object_type = '" . PS_OBJ_TYPE_ATTR . "'";
    $where[] = "offer_id = '$offer_id'";

    $search_query = cw_db_generate_query(array_merge($fields, array('cd_id','param1','param2')), $from_tbls, $query_joins, $where, $groupbys, $having, $orderbys);
    $cond_attributes = cw_query_hash($search_query, 'object_id', false, true);

    if (!is_array($cond_attributes)) {
        $cond_attributes = array();
    }
    
    // products

    $where = array();

    $where[] = "object_type = '" . PS_OBJ_TYPE_PRODS . "'";
    $where[] = "offer_id = '$offer_id'";

    $search_query = cw_db_generate_query($fields, $from_tbls, $query_joins, $where, $groupbys, $having, $orderbys);
    $cond_products = cw_query_hash($search_query, 'object_id', false, true);

    $offer_conditions[$offer_id] = array($cond_categories, $cond_manufacturers, $cond_products, $cond_attributes);

    unset($cond_categories, $cond_manufacturers, $cond_products);

    return $offer_conditions[$offer_id];

}



function cw_ps_normalize_products(&$products) {

    $categories = $manufacturers = $_products = array();

    if (empty($products)) {
        return array($_products, $categories, $manufacturers);
    }


    foreach ($products as $product) {

        if (!isset($_products[$product['product_id']])) {
            $_products[$product['product_id']] = array(
            	'category_id' => $product['category_id'],
            	'manufacturer_id' => $product['manufacturer_id'],
                'amount' => $product['amount'],
                'product_id' => $product['product_id']
            );
        } else {
            $_products[$product['product_id']]['amount'] += $product['amount'];
        }


        if (!isset($categories[$product['category_id']])) {
            $categories[$product['category_id']] = array('amount' => 0, 'products' => array());
        }

        if (!isset($categories[$product['category_id']]['products'][$product['product_id']])) {
            $categories[$product['category_id']]['products'][$product['product_id']] = $product['amount'];
        } else {
            $categories[$product['category_id']]['products'][$product['product_id']] += $product['amount'];
        }
        $categories[$product['category_id']]['amount'] += $product['amount'];


        if (!isset($manufacturers[$product['manufacturer_id']])) {
            $manufacturers[$product['category_id']] = array('amount' => 0, 'products' => array());
        }

        if (!isset($manufacturers[$product['manufacturer_id']]['products'][$product['product_id']])) {
            $manufacturers[$product['manufacturer_id']]['products'][$product['product_id']] = $product['amount'];
        } else {
            $manufacturers[$product['manufacturer_id']]['products'][$product['product_id']] += $product['amount'];
        }
        $manufacturers[$product['manufacturer_id']]['amount'] += $product['amount'];

    }

    return array($_products, $categories, $manufacturers);

}




function genTime() {
  static $startPoint;

  if (!isset($startPoint)) {
    $startPoint = getMicroTime();
  } else {
    return (getMicroTime() - $startPoint);
  }

}




function getMicroTime() {
  static $phpBench;

  if (!isset($phpBench)) {
    $phpBench = substr(PHP_VERSION, 0, 1);
    if (PHP_VERSION != '5.0.0' && $phpBench < 5) {
      $phpBench = false;
    } else {
      $phpBench = true;
    }
  }

  return ($phpBench) ? microtime(true) : array_sum(explode(' ', microtime()));
}


function cw_ps_update_cart_products_(&$cart, &$products, $user_info, $offers = array()) {
    global $config, $smarty, $tables, $addons;
    static $execution;

    if (APP_AREA != 'customer') {
        return false;
    }

    /*if (empty($offers) || !isset($offers['new']) || !isset($offers['to_delete']) || !isset($offers['suitable'])) {
        return false;
    }*/


    if (empty($products) || !is_array($products)) {
        cw_session_unregister('ps_offers_info');
        return false;
    }

    // we should track actions here as well...
    global $action;

    $action = (string)$action;

    $tracking_actions = array(
        'add' => 1,
        'update' => 1,
        'delete' => 1,
        'ajax_update' => 1,
        'clear_cart' => 1
    );

    if (empty($action) || !isset($tracking_actions[$action])) {
        return -1; // view cart
    }

    if ($action == 'clear_cart') {
        cw_session_unregister('ps_offers_info');
        return false;
    }


    $ps_offers_info = &cw_session_register('ps_offers_info');



    //echo '<pre>', debug_print_backtrace(), '</pre>';

    if ($execution == 3) {
        //echo "<pre>2:\n"; print_r($ps_offers_info); echo '</pre>';
        //echo '<pre>'; print_r($_SESSION); echo '</pre>';
        //die;
    }


    if (!empty($ps_offers_info) && is_array($ps_offers_info)) {

        if (isset($ps_offers_info['processed_prods_hash']) || isset($ps_offers_info['product_hash'])) {
            $current_product_hash = md5(cw_ps_prods_str($products));
                    //echo '<pre>', "current_product_hash: $current_product_hash\n", '</pre>';
        }

        if (isset($ps_offers_info['processed_prods_hash'])) {

            if ($ps_offers_info['processed_prods_hash'] == $current_product_hash) {
                return 3; //already processed, we should replace the cart[products] with the existing products array
            }
        }

        if (isset($ps_offers_info['product_hash'])) {

            if ($ps_offers_info['product_hash'] == $current_product_hash) {
                return 2; //already processed
            }
        }

        if ($current_product_hash != $ps_offers_info['processed_prods_hash'] && $current_product_hash != $ps_offers_info['product_hash']) {
            echo '<pre>', "ps_offers_info['processed_prods_hash']: $ps_offers_info[processed_prods_hash]", '</pre>';
            echo '<pre>', "ps_offers_info['product_hash']: $ps_offers_info[product_hash]", '</pre>';
        }

    } else {
        $current_product_hash = md5(cw_ps_prods_str($products));
        echo '<pre>', "current_product_hash: $current_product_hash\n", '</pre>';
    }

    echo '<pre>', "ps_offers_info['processed_prods_hash']: $ps_offers_info[processed_prods_hash]", '</pre>';
    echo '<pre>', "ps_offers_info['product_hash']: $ps_offers_info[product_hash]", '</pre>';
    //die('-');

    $offers = array();


    if (!empty($ps_offers_info) && is_array($ps_offers_info)) {

        if (isset($ps_offers_info['product_hash'])) {
            // let's check if there are offers for the original cart

            if (isset($ps_offers_info['added_free_prods']) && is_array($ps_offers_info['added_free_prods']) && isset($ps_offers_info['applied_offers_free']) && is_array($ps_offers_info['applied_offers_free'])) {

                //echo '<pre>', print_r($products), '</pre>';
                //die;

                if (isset($tracking_actions[$action])) {

                    $_products = $products;

                    foreach ($products as $key => $cart_record) {
                        if (isset($ps_offers_info['added_free_prods'][$cart_record['cartid']])) {
                            unset($_products[$key]);
                        }
                    }
                    //echo '<pre>', print_r($_products), '</pre>';

                    if (empty($_products)) {
                        $ps_offers_info = array();
                        cw_session_unregister('ps_offers_info');
                        // there are only products for free in the cart, so let's delete them
                        $products = array();
                        return true; //true
                    }

                    //echo '<pre>updating... ', print_r($_products), '</pre>';

                    $offers = cw_ps_get_customer_offers($cart, $_products);

                    //echo '<pre>offersssss ', print_r($offers), '</pre>';
                    //die;

                    if (empty($offers) || !is_array($offers)) {
                        $ps_offers_info = array();
                        cw_session_unregister('ps_offers_info');
                        // no products for free are available after the cart updation, so let's delete the products for free added previously
                        $products = $_products;
                        $ps_offers_info['product_hash'] = md5(cw_ps_prods_str($products));
                        $ps_offers_info['processed_prods_hash'] = md5(cw_ps_prods_str($cart['products']));
                        unset($_products, $ps_offers_info['applied_offers_free'], $ps_offers_info['added_free_prods'], $ps_offers_info['already_applied']);
                        return true; //true
                    }

                    unset($_products);

                    //echo '<pre>', print_r($offers), '</pre>';

                    // we will not restore products for free which were deleted by a customer
                    $new_offers = array_diff_key($offers, $ps_offers_info['applied_offers_free']);
                    $offers_to_delete = array_diff_key($ps_offers_info['applied_offers_free'], $offers);
                    $suitable_offers = array_diff_key($ps_offers_info['applied_offers_free'], $offers_to_delete);

                    echo '<pre>offers: ', print_r($offers), '</pre>';
                    echo '<pre>new_offers: ', print_r($new_offers), '</pre>';
                    echo '<pre>offers_to_delete: ', print_r($offers_to_delete), '</pre>';
                    echo '<pre>$suitable_offers: ', print_r($suitable_offers), '</pre>';
                    //die;


                    if (!empty($new_offers) && is_array($new_offers)) {
                        $offers = $new_offers;
                    }

                    $deleted_prods_exist = false;

                    if (!empty($offers_to_delete)) {

                        $cart_records_to_delete = array();

                        foreach ($offers_to_delete as $key => $trash) {
                            //$cart_records_to_delete = array_merge($cart_records_to_delete, $ps_offers_info['applied_offers_free'][$key]);
                            $cart_records_to_delete = $cart_records_to_delete + $ps_offers_info['applied_offers_free'][$key];
                            unset($ps_offers_info['applied_offers_free'][$key]);
                        }

                        if (!empty($cart_records_to_delete)) {

                            //echo '<pre>cart_records_to_delete: ', print_r($cart_records_to_delete), '</pre>';

                            foreach ($products as $key => $cart_record) {
                                if (isset($cart_records_to_delete[$cart_record['cartid']])) {
                                    unset($products[$key]);
                                    $deleted_prods_exist = true;

                                    if (isset($ps_offers_info['added_free_prods'][$cart_record['cartid']])) {
                                        unset($ps_offers_info['added_free_prods'][$cart_record['cartid']]);
                                    }
                                }
                            }
                        }
                    } // offers to delelete



                    // let's update the products for free in the cart; their quantities and prices should be untouched

                    $free_products_exist = false;

                    if (!empty($suitable_offers) && is_array($suitable_offers)) {

                        $cart_records_to_update = array();

                        foreach ($suitable_offers as $key => $trash) {
                            //$cart_records_to_update = array_merge($cart_records_to_update, $ps_offers_info['applied_offers_free'][$key]);
                            $cart_records_to_update = $cart_records_to_update + $ps_offers_info['applied_offers_free'][$key];
                        }

                        //echo '<pre>$cart_records_to_update: ', print_r($cart_records_to_update), '</pre>';
                        //echo '<pre>$ps_offers_info: ', print_r($ps_offers_info), '</pre>';
                        //echo '<pre>before update: ', print_r($products), '</pre>';

                        $free_products_exist = false;

                        if (!empty($cart_records_to_update)) {
                            //echo '<pre>cart_records_to_update: ', print_r($cart_records_to_update), '</pre>';
                            foreach ($products as $key => $cart_record) {
                                if (isset($cart_records_to_update[$cart_record['cartid']])) {
                                    $free_products_exist = true;
                                    $products[$key] = $ps_offers_info['added_free_prods'][$cart_record['cartid']];
                                }
                            }
                        }
                        //echo '<pre>after update: ', print_r($products), '</pre>';
                        //die;

                    }

                    //echo '<pre>$free_products_exist: ', $free_products_exist, '</pre>';
                    //die;

                    if (!empty($new_offers) && is_array($new_offers)) {
                    } else {

                        if ($free_products_exist == true) {
                            $products = cw_products_from_scratch($products, $user_info, false, false);
                        }
                        //echo '<pre>after update: ', print_r($products), '</pre>';
                        //echo '<pre>after update: ', print_r($products), '</pre>';
                        //die;

                        if ($deleted_prods_exist == true || $free_products_exist == true) {
                            $ps_offers_info['product_hash'] = md5(cw_ps_prods_str($products));
                            $ps_offers_info['processed_prods_hash'] = md5(cw_ps_prods_str($cart['products']));
                            //die('yes');
                            return true;
                        } else {
                            return 4; // no new offers and cart prods were not updated
                        }

                    }


                } // action == update

            }

        }

    } // empty($ps_offers_info)


    if (empty($offers)) {
        $offers = cw_ps_get_customer_offers($cart, $products);
    }


    if (empty($offers) || !is_array($offers)) {
        return false;
    }

    // check if the suitable products exist...

    $fields = $from_tbls = $query_joins = $where = $groupbys = $having = $orderbys = array();

    $fields = array("$tables[ps_bonuses].offer_id", 'object_id', 'quantity', "$tables[ps_bonuses].offer_id as offerid", "$tables[ps_bonuses].bonus_id");

    $from_tbls[] = 'ps_bonuses';

    $query_joins['ps_bonus_details'] = array(
        'on' => "$tables[ps_bonuses].bonus_id = $tables[ps_bonus_details].bonus_id",
   		'is_inner' => 1,
    );

    $where[] = "$tables[ps_bonuses].offer_id IN ('" . implode("', '", array_keys($offers)) .  "')";
    $where[] = "$tables[ps_bonuses].type = '" . PS_FREE_PRODS .  "'";
    $where[] = "$tables[ps_bonus_details].object_type = '" . PS_OBJ_TYPE_PRODS .  "'";


    $search_query = cw_db_generate_query($fields, $from_tbls, $query_joins, $where, $groupbys, $having, $orderbys);
    $free_products = cw_query_hash($search_query, 'object_id', false);

    if (empty($free_products) || !is_array($free_products)) {
        return false;
    }


    //global $ps_offers_info;
    //$ps_offers_info = array();


    $ps_offers_info['already_applied'] = true;
    //$ps_offers_info = &cw_session_register('ps_offers_info');


    //var_dump('suitable products:', $free_products);
    //return $products;


    //die(var_dump('suitable products:', $free_products));



    global $user_account;

    cw_load('warehouse');

    foreach ($free_products as $product_id => $product_data) {

        /*
         * we will add the suitable products to the cart directly to avoid the groupping of the default products
         * and special ones
         */

        $product_status = cw_query_first_cell("SELECT status FROM $tables[products_enabled] as pe WHERE pe.product_id = '$product_id' AND pe.status = '1'");

        if ($product_status != 1) {
            continue;
        }

        $product_data['quantity'] = abs(intval($product_data['quantity']));

        if (empty($product_data['quantity'])) {
            continue;
        }

        $new_product = cw_func_call('cw_product_get', array('id' => $product_id, 'user_account' => $user_account, 'info_type' => 3));

        if ($new_product['product_type'] != constant('PRODUCT_TYPE_GENERAL')) {
            if (!empty($new_product['product_type'])) {
                continue;
            }
        }


        $amount = $product_data['quantity'];

        if (!empty($addons['egoods']) && !empty($new_product['distribution'])) {
    		$amount = 1;
        }

        if ($amount < $new_product['min_amount']) {
    		continue;
    	}


    	$possible_warehouses = cw_warehouse_get_avails_customer($product_id);

    	if (empty($possible_warehouses) || !is_array($possible_warehouses)) {
    	    $possible_warehouse = cw_warehouse_get_max_amount_warehouse($product_id);
    	    $possible_warehouses = array($possible_warehouse => 1);
    	}

    	$warehouse = array_shift(array_keys($possible_warehouses));

        //foreach($possible_warehouses as $warehouse => $tmp) {

        if (!$warehouse) {
            $possible_warehouse = cw_warehouse_get_max_amount_warehouse($product_id);
        }

        if ($addons['product_options']) {

    		# Get default options
    		$product_options = cw_get_default_options($product_id, $amount, @$user_account['membership_id']);

    		if ($product_options === false) {
    			continue; //continue 2;
    		} elseif ($product_options === true) {
    			$product_options = null;
    		}

    		# Get the variant_id of options
    		$variant_id = cw_get_variant_id($product_options, $product_id);

    		if (!empty($variant_id)) {

                $possible_warehouse = cw_warehouse_get_max_amount_warehouse($product_id, $variant_id);

                if (empty($warehouse)) {
                    $warehouse = $possible_warehouse;
                }

    		} else {
    		    if (empty($warehouse)) {
                    $warehouse = $possible_warehouse;
                }
    		}
    	}

        if (empty($warehouse)) {
            $warehouse = $possible_warehouse;
        }


    	// let's add a product the cart

    	$free_price = 0.00;

    	$cartid = cw_generate_cartid($products);

    	$products[] = array(
    		"cartid" => $cartid,
    		"product_id" => $product_id,
    		"amount" => $amount,
    		"options" => $product_options,
    		"free_price" => @price_format(@$free_price),
            "salesman_doc_id" => 0,
    		"distribution" => $new_product['distribution'],
    		"variant_id" => $variant_id,
            "warehouse_customer_id" => $warehouse,
        );


        //$amount = $amount - $result['added_amount'];
        //if ($amount <= 0) break;

        //}



        if (!isset($ps_offers_info['added_free_prods'])) {
            $ps_offers_info['added_free_prods'] = array();
        }

        $ps_offers_info['added_free_prods'][$cartid] = $products[count($products)-1];
        $ps_offers_info['added_free_prods'][$cartid]['offer_id'] = $product_data['offerid'];
        $ps_offers_info['added_free_prods'][$cartid]['bonus_id'] = $product_data['bonus_id'];


        if (!isset($ps_offers_info['applied_offers_free'])) {
            $ps_offers_info['applied_offers_free'] = array();
        }

        if (!isset($ps_offers_info['applied_offers_free'][$product_data['offerid']])) {
            $ps_offers_info['applied_offers_free'][$product_data['offerid']] = array();
        }
        $ps_offers_info['applied_offers_free'][$product_data['offerid']][$cartid] = 1;

    }

    if (!isset($execution)) {
        $execution = 1;
    } else {
        $execution++;
    }
    echo '<pre>', "execution: $execution", '</pre>';

    $products = cw_products_from_scratch($products, $user_info, false, false);


    //$ps_offers_info['product_hash'] = md5(serialize($products));
    $ps_offers_info['product_hash'] = md5(cw_ps_prods_str($products));
    $ps_offers_info['processed_prods_hash'] = md5(cw_ps_prods_str($cart['products']));
    //$ps_offers_info['processed_prods_hash'] = md5(serialize($cart['products']));

    //echo '<pre>'; print_r($ps_offers_info); echo '</pre>';

    echo '<pre>', "ps_offers_info['processed_prods_hash']: $ps_offers_info[processed_prods_hash]", '</pre>';
    echo '<pre>', "ps_offers_info['product_hash']: $ps_offers_info[product_hash]", '</pre>';


    if ($execution == 1) {
        //echo '<pre>', print_r($products), '</pre>';
    }


    return true;

}




function cw_ps_update_cart_products(&$cart, &$products, $user_info, $offers_ids = array()) {
    global $config, $smarty, $tables, $addons;
    static $iter;

    if (APP_AREA != 'customer') {
        return false;
    }

    if (empty($products) || !is_array($products)) {
        cw_session_unregister('ps_offers_info');
        return false;
    }

    // we should track actions here as well...
    global $action;

    $action = (string)$action;

    $tracking_actions = array(
        'add' => 1,
        'update' => 1,
        'delete' => 1,
        'ajax_update' => 1,
        'clear_cart' => 1
    );

    if (empty($action) || !isset($tracking_actions[$action])) {
        return -1; // view cart
    }

    if ($action == 'clear_cart') {
        cw_session_unregister('ps_offers_info');
        return false;
    }

    if (!isset($iter)) {
        $iter = 0;
    }


    $ps_offers_info = &cw_session_register('ps_offers_info');

    $current_product_hash = md5(cw_ps_prods_str($products));
    echo '<pre>$current_product_hash: ', $current_product_hash, '</pre>';
    //die;

    if (!empty($ps_offers_info) && is_array($ps_offers_info)) {

        if (isset($ps_offers_info['hash_offer_free']) && !empty($ps_offers_info['hash_offer_free'])) {
            if (isset($ps_offers_info['hash_offer_free'][$current_product_hash])) {
                return $ps_offers_info['hash_offer_free'][$current_product_hash];
                //3 - already processed, we should replace the cart[products] with the existing products array
                //2 - already processed
            } else {
                $iter++;
                $ps_offers_info['hash_offer_free'][$current_product_hash] = array();

                if ($iter > 1) {
                    echo '<pre>', "\t\t!!!", "$iter iteration: ", "ps_offers_info['processed_prods_hash']: $ps_offers_info[processed_prods_hash]", '</pre>';
                    echo '<pre>', "\t\tps_offers_info['product_hash']: $ps_offers_info[product_hash]", '</pre>';
                    //echo '<pre>', print_r($products), '</pre>';
                }
            }
        }


        /*if (isset($ps_offers_info['processed_prods_hash']) || isset($ps_offers_info['product_hash'])) {
            $current_product_hash = md5(cw_ps_prods_str($products));
        }

        if (isset($ps_offers_info['processed_prods_hash'])) {

            if ($ps_offers_info['processed_prods_hash'] == $current_product_hash) {
                return 3; //already processed, we should replace the cart[products] with the existing products array
            }
        }

        if (isset($ps_offers_info['product_hash'])) {

            if ($ps_offers_info['product_hash'] == $current_product_hash) {
                return 2; //already processed
            }
        }


        if (isset($ps_offers_info['product_hash']) || isset($ps_offers_info['processed_prods_hash'])) {
            if ($ps_offers_info['product_hash'] != $current_product_hash && $ps_offers_info['processed_prods_hash'] != $current_product_hash && $iter > 1) {
                echo '<pre>', "\t\t!!!", "$iter iteration: ", "ps_offers_info['processed_prods_hash']: $ps_offers_info[processed_prods_hash]", '</pre>';
                echo '<pre>', "ps_offers_info['product_hash']: $ps_offers_info[product_hash]", '</pre>';
                echo '<pre>', print_r($products), '</pre>';
            }
        }*/


        /*if (isset($ps_offers_info['hash_offer_free']) && !empty($ps_offers_info['hash_offer_free'])) {
            if (!isset($ps_offers_info['hash_offer_free'][$current_product_hash]) && $iter > 1) {
                echo '<pre>', "\t\t!!!", "$iter iteration: ", "ps_offers_info['processed_prods_hash']: $ps_offers_info[processed_prods_hash]", '</pre>';
                echo '<pre>', "\t\tps_offers_info['product_hash']: $ps_offers_info[product_hash]", '</pre>';
                echo '<pre>', print_r($products), '</pre>';
            }
        }*/

    }


    if (empty($offers_ids) || !isset($offers_ids['new']) || !isset($offers_ids['to_delete']) || !isset($offers_ids['suitable'])) {
        return false;
    }

    //echo '<pre>', "ps_offers_info['processed_prods_hash']: $ps_offers_info[processed_prods_hash]", '</pre>';
    //echo '<pre>', "ps_offers_info['product_hash']: $ps_offers_info[product_hash]", '</pre>';
    //die('-');

    $offers = array();


    //if (!empty($ps_offers_info) && is_array($ps_offers_info) && isset($ps_offers_info['product_hash'])) {
    if (!empty($ps_offers_info) && isset($ps_offers_info['hash_offer_free'])) {

        if (isset($ps_offers_info['added_free_prods']) && is_array($ps_offers_info['added_free_prods']) && isset($ps_offers_info['applied_offers_free']) && is_array($ps_offers_info['applied_offers_free'])) {

            // we will not restore products for free which were deleted by a customer
            $new_offers = $offers_ids['new'];
            $offers_to_delete = $offers_ids['to_delete'];
            $suitable_offers = $offers_ids['suitable'];


            if (!empty($new_offers) && is_array($new_offers)) {
                $offers = $new_offers;
            }

            $deleted_prods_exist = false;

            if (!empty($offers_to_delete)) {

                $cart_records_to_delete = array();

                foreach ($offers_to_delete as $key => $trash) {
                    $cart_records_to_delete += $ps_offers_info['applied_offers_free'][$key];
                    //unset($ps_offers_info['applied_offers_free'][$key]); //?
                }

                if (!empty($cart_records_to_delete)) {

                    foreach ($products as $key => $cart_record) {
                        if (isset($cart_records_to_delete[$cart_record['cartid']])) {
                            unset($products[$key]);
                            $deleted_prods_exist = true;

                            if (isset($ps_offers_info['added_free_prods'][$cart_record['cartid']])) {
                                //unset($ps_offers_info['added_free_prods'][$cart_record['cartid']]); //?
                            }
                        }
                    }
                }
            } // offers to delelete



            // let's update the products for free in the cart; their quantities and prices should be untouched

            $free_products_exist = false;

            if (!empty($suitable_offers) && is_array($suitable_offers)) {

                $cart_records_to_update = array();

                foreach ($suitable_offers as $key => $trash) {
                    $cart_records_to_update += $ps_offers_info['applied_offers_free'][$key];
                }

                //echo '<pre>$cart_records_to_update: ', print_r($cart_records_to_update), '</pre>';
                //echo '<pre>$ps_offers_info: ', print_r($ps_offers_info), '</pre>';
                //echo '<pre>before update: ', print_r($products), '</pre>';

                $free_products_exist = false;

                if (!empty($cart_records_to_update)) {
                    foreach ($products as $key => $cart_record) {
                        if (isset($cart_records_to_update[$cart_record['cartid']])) {
                            $free_products_exist = true;
                            $products[$key] = $ps_offers_info['added_free_prods'][$cart_record['cartid']];
                        }
                    }
                }

            }

            if (!empty($new_offers) && is_array($new_offers)) {
            } else {

                if ($free_products_exist == true) {
                    $products = cw_products_from_scratch($products, $user_info, false, false);
                }

                if ($deleted_prods_exist == true || $free_products_exist == true) {
                    $ps_offers_info['product_hash'] = md5(cw_ps_prods_str($products));
                    $ps_offers_info['processed_prods_hash'] = md5(cw_ps_prods_str($cart['products']));

                    $ps_offers_info['hash_offer_free'][md5(cw_ps_prods_str($products))] = 2;
                    $ps_offers_info['hash_offer_free'][md5(cw_ps_prods_str($cart['products']))] = 3;

                    //$ps_offers_info['data_hash'] = md5(cw_ps_prods_str($products) . cw_ps_address_str($user_info));
                    $ps_offers_info['hash'][md5(cw_ps_prods_str($products) . cw_ps_address_str($user_info))] = 2;
                    //die('yes');
                    return true;
                } else {
                    return 4; // no new offers and cart prods were not updated
                }

            }

        }

    } // empty($ps_offers_info)


    if (empty($offers) && !empty($offers_ids['new'])) {
        $offers = $offers_ids['new'];
    }


    if (empty($offers) || !is_array($offers)) {
        return false;
    }

    // check if the suitable products exist...

    $fields = $from_tbls = $query_joins = $where = $groupbys = $having = $orderbys = array();

    $fields = array("$tables[ps_bonuses].offer_id", 'object_id', 'quantity', "$tables[ps_bonuses].offer_id as offerid", "$tables[ps_bonuses].bonus_id");

    $from_tbls[] = 'ps_bonuses';

    $query_joins['ps_bonus_details'] = array(
        'on' => "$tables[ps_bonuses].bonus_id = $tables[ps_bonus_details].bonus_id",
   		'is_inner' => 1,
    );

    $where[] = "$tables[ps_bonuses].offer_id IN ('" . implode("', '", array_keys($offers)) .  "')";
    $where[] = "$tables[ps_bonuses].type = '" . PS_FREE_PRODS .  "'";
    $where[] = "$tables[ps_bonus_details].object_type = '" . PS_OBJ_TYPE_PRODS .  "'";


    $search_query = cw_db_generate_query($fields, $from_tbls, $query_joins, $where, $groupbys, $having, $orderbys);
    $free_products = cw_query_hash($search_query, 'object_id', false);

    if (empty($free_products) || !is_array($free_products)) {
        return false;
    }


    //global $ps_offers_info;
    //$ps_offers_info = array();


    $ps_offers_info['already_applied'] = true;
    //$ps_offers_info = &cw_session_register('ps_offers_info');


    //var_dump('suitable products:', $free_products);
    //return $products;


    //die(var_dump('suitable products:', $free_products));



    global $user_account;

    cw_load('warehouse');

    foreach ($free_products as $product_id => $product_data) {

        /*
         * we will add the suitable products to the cart directly to avoid the groupping of the default products
         * and special ones
         */

        $product_status = cw_query_first_cell("SELECT status FROM $tables[products_enabled] as pe WHERE pe.product_id = '$product_id' AND pe.status = '1'");

        if ($product_status != 1) {
            continue;
        }

        $product_data['quantity'] = abs(intval($product_data['quantity']));

        if (empty($product_data['quantity'])) {
            continue;
        }

        $new_product = cw_func_call('cw_product_get', array('id' => $product_id, 'user_account' => $user_account, 'info_type' => 3));

        if ($new_product['product_type'] != constant('PRODUCT_TYPE_GENERAL')) {
            if (!empty($new_product['product_type'])) {
                continue;
            }
        }


        $amount = $product_data['quantity'];

        if (!empty($addons['egoods']) && !empty($new_product['distribution'])) {
    		$amount = 1;
        }

        if ($amount < $new_product['min_amount']) {
    		continue;
    	}


    	$possible_warehouses = cw_warehouse_get_avails_customer($product_id);

    	if (empty($possible_warehouses) || !is_array($possible_warehouses)) {
    	    $possible_warehouse = cw_warehouse_get_max_amount_warehouse($product_id);
    	    $possible_warehouses = array($possible_warehouse => 1);
    	}

    	$warehouse = array_shift(array_keys($possible_warehouses));

        //foreach($possible_warehouses as $warehouse => $tmp) {

        if (!$warehouse) {
            $possible_warehouse = cw_warehouse_get_max_amount_warehouse($product_id);
        }

        if ($addons['product_options']) {

    		# Get default options
    		$product_options = cw_get_default_options($product_id, $amount, @$user_account['membership_id']);

    		if ($product_options === false) {
    			continue; //continue 2;
    		} elseif ($product_options === true) {
    			$product_options = null;
    		}

    		# Get the variant_id of options
    		$variant_id = cw_get_variant_id($product_options, $product_id);

    		if (!empty($variant_id)) {

                $possible_warehouse = cw_warehouse_get_max_amount_warehouse($product_id, $variant_id);

                if (empty($warehouse)) {
                    $warehouse = $possible_warehouse;
                }

    		} else {
    		    if (empty($warehouse)) {
                    $warehouse = $possible_warehouse;
                }
    		}
    	}

        if (empty($warehouse)) {
            $warehouse = $possible_warehouse;
        }


    	// let's add a product the cart

    	$free_price = 0.00;

    	$cartid = cw_generate_cartid($products);

    	$products[] = array(
    		"cartid" => $cartid,
    		"product_id" => $product_id,
    		"amount" => $amount,
    		"options" => $product_options,
    		"free_price" => @price_format(@$free_price),
            "salesman_doc_id" => 0,
    		"distribution" => $new_product['distribution'],
    		"variant_id" => $variant_id,
            "warehouse_customer_id" => $warehouse,
        );


        //$amount = $amount - $result['added_amount'];
        //if ($amount <= 0) break;

        //}



        if (!isset($ps_offers_info['added_free_prods'])) {
            $ps_offers_info['added_free_prods'] = array();
        }

        $ps_offers_info['added_free_prods'][$cartid] = $products[count($products)-1];
        $ps_offers_info['added_free_prods'][$cartid]['offer_id'] = $product_data['offerid'];
        $ps_offers_info['added_free_prods'][$cartid]['bonus_id'] = $product_data['bonus_id'];


        if (!isset($ps_offers_info['applied_offers_free'])) {
            $ps_offers_info['applied_offers_free'] = array();
        }

        if (!isset($ps_offers_info['applied_offers_free'][$product_data['offerid']])) {
            $ps_offers_info['applied_offers_free'][$product_data['offerid']] = array();
        }
        $ps_offers_info['applied_offers_free'][$product_data['offerid']][$cartid] = 1;

    }


    $products = cw_products_from_scratch($products, $user_info, false, false);


    //$ps_offers_info['product_hash'] = md5(serialize($products));
    $ps_offers_info['product_hash'] = md5(cw_ps_prods_str($products));
    $ps_offers_info['processed_prods_hash'] = md5(cw_ps_prods_str($cart['products']));

    $ps_offers_info['hash_offer_free'][md5(cw_ps_prods_str($products))] = 2;
    $ps_offers_info['hash_offer_free'][md5(cw_ps_prods_str($cart['products']))] = 3;

    //$ps_offers_info['data_hash'] = md5(cw_ps_prods_str($products) . cw_ps_address_str($user_info));
    $ps_offers_info['hash'][md5(cw_ps_prods_str($products) . cw_ps_address_str($user_info))] = 2;
    cw_ps_save('products', $products);
    //$ps_offers_info['processed_prods_hash'] = md5(serialize($cart['products']));

    //echo '<pre>'; print_r($ps_offers_info); echo '</pre>';

    echo '<pre>', "ps_offers_info['processed_prods_hash']: $ps_offers_info[processed_prods_hash]", '</pre>';
    echo '<pre>', "ps_offers_info['product_hash']: $ps_offers_info[product_hash]", '</pre>';


    return true;

}



function cw_ps_prods_str(&$data) {

    $result = null;

    if (empty($data) || !is_array($data)) {
        return $result;
    }

    $fields = array('cartid', 'product_id', 'productcode', 'options', 'variant_id', 'amount');

    foreach ($data as $record) {
        $index = count($result);

        foreach ($fields as $field) {
            $result[$index][$field] = $record[$field];
        }
    }

    return cw_ps_array2str($result);
}




function cw_ps_update_prods_prices(&$products) {

    $ps_offers_info = &cw_session_register('ps_offers_info');

    if (!empty($ps_offers_info) && is_array($ps_offers_info)) {

        if (isset($ps_offers_info['product_hash'])) {
            // let's check if there are offers for the original cart

            if (isset($ps_offers_info['added_free_prods']) && is_array($ps_offers_info['added_free_prods']) && isset($ps_offers_info['applied_offers_free']) && is_array($ps_offers_info['applied_offers_free'])) {


                foreach ($products as $key => $cart_record) {

                    if (isset($ps_offers_info['added_free_prods'][$cart_record['cartid']])) {

                        $empty_value = price_format(0.00);

                        $products[$key]['price'] = $empty_value;
                        $products[$key]['display_price'] = $empty_value;
                        $products[$key]['total'] = $empty_value;

                        if (isset($products[$key]['taxed_price'])) {
                            $products[$key]['taxed_price'] = $empty_value;
                        }

                        if (isset($products[$key]['taxed_clear_price'])) {
                            $products[$key]['taxed_clear_price'] = $empty_value;
                        }

                        if (isset($products[$key]['taxed_net_price'])) {
                            $products[$key]['taxed_net_price'] = $empty_value;
                        }

                        if (isset($products[$key]['options_surcharge'])) {
                            $products[$key]['options_surcharge'] = $empty_value;
                        }

                        if (isset($products[$key]['net_price'])) {
                            $products[$key]['net_price'] = $empty_value;
                        }

                        if (isset($products[$key]['display_net_price'])) {
                            $products[$key]['display_net_price'] = $empty_value;
                        }

                        if (isset($cart_record['product_options']) && !empty($cart_record['product_options']) && is_array($cart_record['product_options'])) {
                            foreach (array_keys($cart_record['product_options']) as $opt_id) {
                                $products[$key]['product_options'][$opt_id]['price_modifier'] = $empty_value;
                            }
                        }

                    }
                }

            }
        }
    }

}




function cw_ps_offers_exist(&$cart, &$products, $_user_info = array()) {
    static $iter;

    $offers_ids = array('new' => array(), 'suitable' => array(), 'to_delete' => array());
    $offers = array();

    //we should track actions here as well...
    global $action;
    $action = (string)$action;

    $tracking_actions = array(
        'add' => 1,
        'update' => 1,
        'delete' => 1,
        'ajax_update' => 1,
        'clear_cart' => 1
    );

    if (empty($action) || !isset($tracking_actions[$action])) {
        return $offers_ids;
    }

    if (!isset($iter)) {
        $iter = 0;
    }
    $iter++;


    global $customer_id, $user_info;

    if (isset($customer_id) && !empty($customer_id)) {
        if (!isset($user_info) || empty($user_info)) {
            $user_info = cw_user_get_info($customer_id, 1);
        }
    } else {
        $user_info = $_user_info;
        if (empty($_user_info)) {
            $user_info = $cart['userinfo'];
        }
    }


    $ps_offers_info = &cw_session_register('ps_offers_info');

    $current_hash = md5(cw_ps_prods_str($products) . cw_ps_address_str($user_info));

    if (!empty($ps_offers_info) && is_array($ps_offers_info)) {

        if (isset($ps_offers_info['hash']) && !empty($ps_offers_info['hash'])) {
            if (isset($ps_offers_info['hash'][$current_hash])) {
                if (isset($ps_offers_info['offers_ids'])) {
                    return $ps_offers_info['offers_ids'];
                }
            } else {
                $ps_offers_info['hash'] = array();
                if ($iter > 1) {
                    echo '<pre>is differ! ', "$iter iteration: ", print_r($products), print_r(cw_ps_save('products', true)), '</pre>';
                    echo '<pre>', print_r($ps_offers_info['hash']), "\n",  md5(cw_ps_prods_str($products)), '</pre>';
                }
            }
        }

        /*if (isset($ps_offers_info['processed_data_hash'])) {

            //we have already processed the input data
            if ($ps_offers_info['processed_data_hash'] == $current_hash) {
                if (isset($ps_offers_info['offers_ids'])) {
                    return $ps_offers_info['offers_ids'];
                }
            }
        }

        if (isset($ps_offers_info['data_hash'])) {

            //we have already processed the input data
            if ($ps_offers_info['data_hash'] == $current_hash) {
                if (isset($ps_offers_info['offers_ids'])) {
                    return $ps_offers_info['offers_ids'];
                }
            }
        }


        if (isset($ps_offers_info['data_hash']) || isset($ps_offers_info['processed_data_hash'])) {
            if ($ps_offers_info['data_hash'] != $current_hash && $ps_offers_info['processed_data_hash'] != $current_hash && $iter > 1) {
                echo '<pre>is differ! ', "$iter iteration: ", print_r($products), print_r(cw_ps_save('products', true)), '</pre>';
            }
        }*/

        /*
        if (isset($ps_offers_info['hash']) && !empty($ps_offers_info['hash'])) {
            //if ($ps_offers_info['data_hash'] != $current_hash && $ps_offers_info['processed_data_hash'] != $current_hash && $iter > 1) {
            if (!isset($ps_offers_info['hash'][$current_hash]) && $iter > 1) {
                echo '<pre>is differ! ', "$iter iteration: ", print_r($products), print_r(cw_ps_save('products', true)), '</pre>';
                echo '<pre>', print_r($ps_offers_info['hash']), "\n",  md5(cw_ps_prods_str($products)), '</pre>';
            }
        }*/

    }

    cw_ps_save('products', $products);


    //if (!empty($ps_offers_info) && is_array($ps_offers_info) && isset($ps_offers_info['data_hash'])) {
    if (isset($ps_offers_info) && isset($ps_offers_info['applied_offers']) && !empty($ps_offers_info['applied_offers']) && isset($ps_offers_info['hash'])) {

		// let's check if there are offers for the original cart

		$_products = $products;


        if (isset($ps_offers_info['added_free_prods']) && is_array($ps_offers_info['added_free_prods']) && isset($ps_offers_info['applied_offers_free']) && is_array($ps_offers_info['applied_offers_free'])) {

			// let's delete free products added if any
			foreach ($products as $key => $cart_record) {
				if (isset($ps_offers_info['added_free_prods'][$cart_record['cartid']])) {
					unset($_products[$key]);
				}
			}

			if (empty($_products)) {

				//$ps_offers_info = array();
				//cw_session_unregister('ps_offers_info');

				// there are only products for free in the cart, so let's delete them
				//$products = $_products = array();

			    $offers_ids['to_delete'] = $ps_offers_info['applied_offers'];
			    $ps_offers_info['offers_ids'] = $offers_ids;

				return $offers_ids;

			}

		}


		if (isset($ps_offers_info['disc_prods']) && is_array($ps_offers_info['disc_prods']) && isset($ps_offers_info['applied_offers_discount']) && is_array($ps_offers_info['applied_offers_discount'])) {

			//let's delete all the special discounts applied
			foreach ($products as $key => $cart_record) {
				$_cartid = $cart_record['cartid'];
				if (isset($ps_offers_info['disc_prods'][$_cartid])) {
					$_products[$key]['price'] = $ps_offers_info['disc_prods'][$_cartid]['price'];
				}
			}

		}


		$offers = cw_ps_get_customer_offers($cart, $_products);


		if (empty($offers) || !is_array($offers)) {

			// no products for free are available after the cart updation, so let's delete the products for free added previously
			//$products = $_products;
			//$ps_offers_info['processed_data_hash'] = $current_hash;

			$ps_offers_info['hash'][$current_hash] = 1;
			//$ps_offers_info['hash'][md5(cw_ps_prods_str($products) . cw_ps_address_str($user_info))] = 2;
		}

		unset($_products);


		// we will not restore products for free which were deleted by a customer

		$new_offers = $offers_to_delete = $suitable_offers = array();

		if (isset($ps_offers_info['applied_offers']) && is_array($ps_offers_info['applied_offers']) && !empty($ps_offers_info['applied_offers'])) {
		    $new_offers = array_diff_key($offers, $ps_offers_info['applied_offers']);
		    $offers_to_delete = array_diff_key($ps_offers_info['applied_offers'], $offers);
		    $suitable_offers = array_diff_key($ps_offers_info['applied_offers'], $offers_to_delete);
		}


		$offers_ids = array(
		    'new' => $new_offers,
		    'to_delete' => $offers_to_delete,
		    'suitable' => $suitable_offers
		);


		if (empty($offers) || !is_array($offers)) {

		    $ps_offers_info['offers_ids'] = $offers_ids;

            //echo '<pre>', print_r($offers_ids), '</pre>';
            //echo '<pre>', print_r($ps_offers_info), '</pre>';
            //die;

		    return $offers_ids;
		}

    }


    if (empty($offers)) {
        //echo '<pre>checking offers: ', print_r($cart), print_r($products), '</pre>';
        $offers_ids['new'] = cw_ps_get_customer_offers($cart, $products);
        //echo '<pre>', print_r($offers_ids['new']), '</pre>';
        //die;
    }


    //echo '<pre>', print_r($offers_ids), '</pre>';
    //die;

    if (!empty($offers_ids['new']) || !empty($offers_ids['suitable'])) {
        $ps_offers_info['applied_offers'] = $offers_ids['new'] + $offers_ids['suitable'];
    }

    //$ps_offers_info['processed_data_hash'] = $current_hash;

    $ps_offers_info['hash'][$current_hash] = 1;
    $ps_offers_info['hash'][md5(cw_ps_prods_str($cart['products']) . cw_ps_address_str($user_info))] = 3;

    cw_ps_save('products', $cart['products']);

    if (!empty($offers_ids['new']) || !empty($offers_ids['suitable']) || !empty($offers_ids['to_delete'])) {
        $ps_offers_info['offers_ids'] = $offers_ids;
    }

    return $offers_ids;
}




function cw_ps_address_str(&$user_info) {

    if (empty($user_info) || !is_array($user_info) || !isset($user_info['current_address'])) {
        return null;
    }

    $shipping_address = array();
    $shipping_address_fields = array('country', 'state', 'zipcode', 'city', 'address');

    foreach ($shipping_address_fields as $field) {
        if (isset($user_info['current_address'][$field])) {
            $shipping_address[] = $user_info['current_address'][$field];
        }
    }

    return cw_ps_array2str($shipping_address);
}





function cw_ps_offers_set_hash($cart, $products, $user_info) {

    $ps_offers_info = &cw_session_register('ps_offers_info');

    if (empty($ps_offers_info)) {
        return;
    }

    if (!isset($ps_offers_info['offers_ids']) || empty($ps_offers_info['offers_ids'])) {
        $ps_offers_info = array();
        cw_session_unregister('ps_offers_info');
        return;
    }

    if (isset($ps_offers_info['offers_ids']['new']) && !empty($ps_offers_info['offers_ids']['new'])) {
        $ps_offers_info['offers_ids']['suitable'] += $ps_offers_info['offers_ids']['new'];
        $ps_offers_info['offers_ids']['new'] = array();
    }

    if (isset($ps_offers_info['offers_ids']['to_delete']) && !empty($ps_offers_info['offers_ids']['to_delete'])) {
        $ps_offers_info['offers_ids']['to_delete'] = array();
    }

    if (!isset($ps_offers_info['offers_ids']['suitable']) || empty($ps_offers_info['offers_ids']['suitable'])) {
        $ps_offers_info = array();
        cw_session_unregister('ps_offers_info');
        return;
    }

    //$ps_offers_info['hash'][md5(cw_ps_prods_str($products) . cw_ps_address_str($user_info))] = 2;
}





function cw_ps_save($item, $data = null) {
    static $hash;

    if ($data === true) {
        if (isset($hash[$item])) {
            return $hash[$item];
        } else {
            return null;
        }
    }

    if (!empty($data)) {
        if (!isset($hash[$item])) {
            $hash[$item] = array();
        }
        $hash[$item][] = array('data' => $data, 'hash' => md5(cw_ps_prods_str($data)));
    }

}





function cw_ps_update_shipping(&$cart, &$products, $user_info, &$return, $offers_ids = array()) {
    global $tables;

    if (empty($products) || empty($cart)) {
        return false;
    }

    if (empty($offers_ids) || !isset($offers_ids['new']) || !isset($offers_ids['to_delete']) || !isset($offers_ids['suitable'])) {
        return false;
    }

    $offers = $offers_ids['new'] + $offers_ids['suitable'];

    // check if the suitable products exist...

    $fields = $from_tbls = $query_joins = $where = $groupbys = $having = $orderbys = array();

    $fields = array("$tables[ps_bonuses].offer_id", "$tables[ps_bonuses].apply", "$tables[ps_bonuses].offer_id as offerid", "$tables[ps_bonuses].bonus_id");

    $from_tbls[] = 'ps_bonuses';

    $where[] = "$tables[ps_bonuses].offer_id IN ('" . implode("', '", array_keys($offers)) .  "')";
    $where[] = "$tables[ps_bonuses].type = '" . PS_FREE_SHIP .  "'";


    $search_query = cw_db_generate_query($fields, $from_tbls, $query_joins, $where, $groupbys, $having, $orderbys);
    $offers_data = cw_query_hash($search_query, 'offer_id', false);


    //echo '<pre>', print_r($offers_data), '</pre>';
    //die;


    /*$fields = $from_tbls = $query_joins = $where = $groupbys = $having = $orderbys = array();

    $fields = array("$tables[ps_bonuses].offer_id", 'object_id', 'quantity', "$tables[ps_bonuses].offer_id as offerid", "$tables[ps_bonuses].bonus_id");

    $from_tbls[] = 'ps_bonuses';

    $query_joins['ps_bonus_details'] = array(
        'on' => "$tables[ps_bonuses].bonus_id = $tables[ps_bonus_details].bonus_id",
   		'is_inner' => 1,
    );

    $where[] = "$tables[ps_bonuses].offer_id IN ('" . implode("', '", array_keys($offers)) .  "')";
    $where[] = "$tables[ps_bonuses].type = '" . PS_FREE_SHIP .  "'";
    $where[] = "$tables[ps_bonus_details].object_type = '" . PS_OBJ_TYPE_PRODS .  "'";


    $search_query = cw_db_generate_query($fields, $from_tbls, $query_joins, $where, $groupbys, $having, $orderbys);
    $free_products = cw_query_hash($search_query, 'object_id', false);

    if (empty($free_products) || !is_array($free_products)) {
        return false;
    }
    */


}
