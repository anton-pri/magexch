<?php
namespace cw\custom_magazineexchange_sellers;

/** =============================
 ** Addon functions, API
 ** =============================
 **/
function mag_product_seller_item_data($seller_item_id) {
    global $tables, $edited_language;
    
    $seller_item_id = intval($seller_item_id);
    $data = cw_query_first("SELECT * FROM $tables[magazine_sellers_product_data] 
    WHERE  seller_item_id='$seller_item_id'");
    if ($data['is_digital']) {
        $data['attributes'] = cw_func_call('cw_attributes_get', array('item_id' => $seller_item_id, 'item_type' => 'SP', 'language' => $edited_language));
    } 
   
    return $data;
}
 
function mag_product_seller_data($product_id, $seller_id, $order_by = " seller_item_id DESC") {
    global $tables, $target;
    
        $product_id = intval($product_id);
        $seller_id = intval($seller_id);
        $data = cw_query("SELECT * FROM $tables[magazine_sellers_product_data] 
        WHERE  product_id='$product_id' AND seller_id='$seller_id' AND is_digital='".(($target=='digital_products')?1:0)."' ORDER BY $order_by");
    
    return $data;
}

function mag_product_sellers_data($product_id) {
    global $tables;
    
    $product_id = intval($product_id);
    $data = cw_query("SELECT * FROM $tables[magazine_sellers_product_data] 
        WHERE  product_id='$product_id' ");

    $data = array_filter($data, function($d) {
         static $sellers_disabled;

         if (!isset($sellers_disabled))
             $sellers_disabled = array(); 

         if (!isset($sellers_disabled[$d['seller_id']])) {
             $seller_custom_fields = cw_user_get_custom_fields($d['seller_id'],0,'','field');

             $sellers_disabled[$d['seller_id']] = $seller_custom_fields['products_disabled'];
         }
         return ($sellers_disabled[$d['seller_id']]!="Y");
    });
 
    return $data;
}

function mag_order_owed($doc_id) {
    static $seller;
    $order = cw_call('cw_doc_get', array($doc_id, 0));
  
    $seller_id = $order['info']['warehouse_customer_id'];
    if (!in_array($order['status'], array('Q','P','C',MAG_PAIDOUT_ORDER_STATUS, 'S'))) return 0;
    if (empty($seller_id)) return 0;

    if (empty($seller) || empty($seller[$seller_id])) {
        $seller_info = cw_call('cw_user_get_info', array($seller_id));
        $seller[$seller_id] = array('fees'=>cw_call('cw\custom_magazineexchange_sellers\mag_membership_fees', array($seller_info['membership_id'])), 'flat_charges'=>cw_call('cw\custom_magazineexchange_sellers\mag_membership_flat_charges', array($seller_info['membership_id'])));
    }
    
    $fees = $seller[$seller_id]['fees'];
    $flat_charges = $seller[$seller_id]['flat_charges'];

    cw_log_add(__FUNCTION__, [$seller_id, $seller_info['membership_id'], $fees, $flat_charges]);
    
    $products = $order['products'];
    $surcharge = 0;
    foreach ($products as $p) {
        //$surcharge += ($p['price']+$fees['item']*$fees['percent']/100)*$p['amount'];

        $flat_charge = 0.00;
        if (!empty($flat_charges)) {
            foreach ($flat_charges as $charge) {
                if ($charge['range_from']<$p['price'] && $charge['range_to']>=$p['price']) { 
                    $flat_charge = $charge['value'];  
                }    
            }
        }

        $surcharge += (($fees['item']*$fees['percent']/100)*$p['amount']+$flat_charge*$p['amount']); 
        cw_log_add(__FUNCTION__, ['product_id'=>$p['product_id'], $fees, $flat_charge, $p['amount'], $p['price'], $surcharge]);
    }

    $surcharge = $order['info']['total'] - $surcharge; 

    return $surcharge;
    
}

/**
 * Re-calc summary in-stock level for a product as sum of all available quantity of sellers
 */
function mag_product_update_stock($product_id) {
    global $tables;

    $is_digital = cw_query_first_cell("SELECT COUNT(*) FROM $tables[magazine_sellers_product_data] WHERE product_id='$product_id' and is_digital=1");

    if (!$is_digital) {

        $in_stock = cw_query_first_cell("SELECT SUM(quantity) FROM $tables[magazine_sellers_product_data] WHERE product_id='$product_id' and is_digital=0 AND quantity>=0");
        db_query("UPDATE $tables[products_warehouses_amount] 
            SET avail='$in_stock' 
            WHERE product_id='$product_id' AND variant_id=0 AND warehouse_customer_id='0'");
    } else {
        $in_stock = 32767;
        db_query("update $tables[products_warehouses_amount] set avail=32767 where product_id=$product_id");
    }

    return $in_stock;
}

function mag_membership_fees($membership_id) {
    global $config;
    return $config['custom_magazineexchange_sellers']['mag_seller_fees'][$membership_id];
}

function mag_membership_flat_charges($membership_id) {
    global $config;

    $mag_seller_flat_charges = array();
    if (is_string($config['custom_magazineexchange_sellers']['mag_seller_flat_charges']))
        $mag_seller_flat_charges = unserialize($config['custom_magazineexchange_sellers']['mag_seller_flat_charges']);

    if (is_array($config['custom_magazineexchange_sellers']['mag_seller_flat_charges'])) 
        $mag_seller_flat_charges = $config['custom_magazineexchange_sellers']['mag_seller_flat_charges'];


    return (isset($mag_seller_flat_charges[$membership_id])?$mag_seller_flat_charges[$membership_id]:array());
} 

/** =============================
 ** Hooks
 ** =============================
 **/

/**
 * Add correct price and seller_item_id to product in cart
 * 
 * @see PRE cw.cart_process.php:cw_add_to_cart()
 * 
 * @return replace $product_data array
 */
function cw_add_to_cart(&$cart, $product_data) {
    global $request_prepared;
    
    if (empty($request_prepared['seller_item_id']) || empty($product_data['product_id'])) return null;
    
    $seller_data = cw_call('cw\custom_magazineexchange_sellers\mag_product_seller_item_data', array($request_prepared['seller_item_id']));
    
    $product_data['price']          = $seller_data['price'];
    $product_data['seller_id']      = $seller_data['seller_id'];
    $product_data['seller_item_id'] = $seller_data['seller_item_id'];

    // Replace input parameters for main fuction
    return new \EventReturn(null, array(&$cart, $product_data));
}


/**
 *  Update qty in cart basing on available amount of specific seller.
 * 
 * @see PRE cw.cart_process.php:cw_update_quantity_in_cart()
 * 
 * @return replace $productindexes array
 */
function cw_update_quantity_in_cart (&$cart, $productindexes, $warehouse_selection = array()) {
    
    foreach ($cart['products'] as $k=>$v) {
        $seller_data = cw_call('cw\custom_magazineexchange_sellers\mag_product_seller_item_data', array($v['seller_item_id']));
        $new_qty = min($productindexes[$v['cartid']],  $seller_data['quantity']);
        if ($productindexes[$v['cartid']] != $new_qty) {
            cw_add_top_message("Sorry, seller can provide a maximum of $new_qty items.",'W');
            $productindexes[$v['cartid']] = $new_qty;
        }
    }

    // Replace input parameters for main fuction
    return new \EventReturn(null, array(&$cart, $productindexes, $warehouse_selection));
}

/**
 * Adjust shipping cost.
 * Add seller fee per each item in cart. Fee depends on membership.
 * 
 * @note old-fashion POST hook
 * @see POST cw_shipping_get_rates()
 * @return array $return - shipping rates
 */
function cw_shipping_get_rates($params, $return) {
    
    $products = $params['products'];
    $surcharge = 0;
    foreach ($products as $p) {
        if (empty($p['seller_id'])) continue;
        
        $seller_info = cw_call('cw_user_get_info', array($p['seller_id']));
        $fees = cw_call('cw\custom_magazineexchange_sellers\mag_membership_fees', array($seller_info['membership_id']));
        $fee = $fees['item'];
        
        $surcharge += $fee*$p['amount'];
    }

    if (!empty($return)) 
        foreach($return as $k=>$v) {
            $return[$k]['original_rate'] += $surcharge;
        }

    return $return;
}

/**
 * Store condition and comments of purchased product in order item data
 * in case seller changes them or deletes after order placement
 *
 * @see POST cw_doc_prepare_doc_item_extra_data()
 * 
 * @return array $extra_data - adjusted extra_data array for cw_docs_items table
 */
function cw_doc_prepare_doc_item_extra_data($product) {
    
    $extra_data = cw_get_return();
    
    $seller_data = cw_call('cw\custom_magazineexchange_sellers\mag_product_seller_item_data', array($product['seller_item_id']));

    $extra_data['seller_item'] = array(
        'seller_item_id' => $seller_data['seller_item_id'], // Just for reference  which can be inconsistent later
        'condition' => $seller_data['condition'],           // Saved state of condition and comments
        'comments' => $seller_data['comments']
    );
    
    return $extra_data;
}

function cw_auth_check_security_targets() {
    global $target;
    $return = cw_get_return();

    if (AREA_TYPE == 'C') {
        if ($target == 'docs_O')  
            return true;
    }

    return $return;
}

function cw_get_file_storage_locations() {

    global $customer_id;

    $seller_data = cw_call('cw_user_get_info', array($customer_id, 0));

    $seller_init_dir = $seller_data['email'];

    $storage_locations = cw_get_return();

    $storage_locations = array(array('code'=>'AS3', 'title' => 'Amazon S3', 'init_dir' => $seller_init_dir));
    return $storage_locations;
}

/**
 * Store additional field "username" when seller profile created
 *
 * @see POST cw_user_create_profile()
 * 
 * @param $register - profile fields
 * 
 * @return $customer_id - unchanged return of main function
 */
function cw_user_create_profile($register) {
    $customer_id = cw_get_return();

    global $tables;
    $field_id = cw_query_first_cell("SELECT field_id FROM $tables[register_fields] WHERE field='username'");

    $data = array(
        'field_id' => $field_id,
        'customer_id' => $customer_id,
        'value' => $register['username'],
    );
    cw_array2insert('register_fields_values', $data, true);

    return $customer_id;
}

/*
 * Validate 'username' profile field - it must be unique
 * Function name must have pattern cw_check_user_field_<field>
 * 
 * @see cw_check_user_field_validate()
 * 
 * @param $customer_id - customer_id of edited profile or 0 for new
 * @param $uname - username value
 * 
 * @return bool false - validation is OK
 * @return string message - error message
 */
function cw_check_user_field_username($customer_id, $uname) {
    global $tables;

    if (preg_match('/[^a-zA-Z0-9\.\-\_]/', $uname)) {
        return cw_get_langvar_by_name('lbl_username_contains_unallowed_chars');
    }

    $field_id = cw_query_first_cell("SELECT field_id FROM $tables[register_fields] WHERE field='username'");
    if (empty($field_id)) return false;
    $is_user = cw_query_first_cell($q="select count(*) from $tables[register_fields_values] 
        where value='$uname' and field_id='$field_id' and customer_id!='$customer_id'");

    //cw_log_add(__FUNCTION__, [$is_user, $q]);

    if ($is_user) {
        return cw_get_langvar_by_name('lbl_username_already_used');
    }
    return false;
}


/**
 * Replace seller first last name to username
 * 
 * @see cw_seller_get_info()
 * @param $seller_id - customer_id of a seller
 * @return array $seller - seller info as returned by cw_seller_get_info() with replaced 'name'
 */
function cw_seller_get_info($seller_id) {
    $seller = cw_get_return();

    if (!empty($seller)) {
        $fields = cw_user_get_custom_fields($seller_id,0,'','field');
        $seller['fullname'] = $seller['name'];
        $seller['name'] = $fields['username'];
    }
    return $seller;
}

/** =============================
 ** Events handlers
 ** =============================
 **/

function on_accounting_update_stock($product, $variant_id, $field, $change) {
    global $tables;

    //cw_log_add(__FUNCTION__, array($product, $variant_id, $field, $change));

    if ($field == 'avail') {

        if (isset($product['extra_data']['seller_item']))
            $seller_item_id = $product['extra_data']['seller_item']['seller_item_id'];

        if (!$seller_item_id)
            $seller_item_id = $product['extra_data']['seller_item_id'];

        db_query("UPDATE $tables[magazine_sellers_product_data] 
                SET quantity=quantity+$change
                WHERE seller_item_id='$seller_item_id' AND is_digital=0");
        mag_product_update_stock($product['product_id']);
    }
}
 
/**
 * seller_item_id is part of hash for differ products in cart and for another hash to differ products by orders
 */
function on_build_cart_product_hash($product) {
    return 'MAGS'.$product['seller_item_id'];
}
/**
 * seller_id is part of hash to dist products by orders
 */
function on_build_order_hash($product) {
    $seller_data = cw_call('cw\custom_magazineexchange_sellers\mag_product_seller_item_data', array($product['seller_item_id']));
    return 'MAGS'.$seller_data['seller_id'];
}
/**
 * Override stored price by seller price when product retrieved by cart
 */
function on_product_from_scratch(&$product_data) {
    if (empty($product_data['seller_item_id']) || empty($product_data['product_id'])) return null;
    
    $seller_data = cw_call('cw\custom_magazineexchange_sellers\mag_product_seller_item_data', array($product_data['seller_item_id']));
    $product_data['price'] = $seller_data['price'];
    $product_data['seller'] = array('id' => $seller_data['seller_id'],'seller_item_id'=>$seller_data['seller_item_id'], 'is_digital'=>$seller_data['is_digital']);
    $product_data['warehouse_customer_id'] = $seller_data['seller_id'];

    if ($product_data['seller']['is_digital']) 
        $product_data['free_shipping'] = 'Y';
}

function mag_get_seller_digital_product_sale($seller_item_id, $customer_id) {
    global $tables, $config, $app_catalogs;
    global $reset_digital;
    $return = array();

    $seller_data = cw_call('cw\custom_magazineexchange_sellers\mag_product_seller_item_data', array($seller_item_id));

 
    if (!($seller_data['is_digital'])) return $return;

    if ($seller_data) {

        //$return['download_link_pending'] = true;

        //$link_active_status_sql = "inner join cw_order_statuses oi on oi.code=d.status and oi.inventory_decreasing=1";
 
        $doc_items = cw_query($sql = "select di.* from cw_docs_items di inner join cw_docs d on d.doc_id=di.doc_id inner join cw_docs_user_info dui on dui.doc_info_id=d.doc_info_id and dui.customer_id = '".intval($customer_id)."' where di.product_id = '$seller_data[product_id]'"); 

        if ($doc_items) {
            foreach ($doc_items as $di)  {
                $extra_data = unserialize($di['extra_data']);
                if (!empty($extra_data['seller_item'])) {
                    if ($extra_data['seller_item']['seller_item_id'] != $seller_item_id || $reset_digital) continue;

                    if (empty($extra_data['seller_item']['download_link']) || 1) {
                        //$extra_data['seller_item']['download_link'] = cw_call('cw_ppd_as3_real_url', array($seller_data['attributes']['seller_product_main_file']['value'], $config['ppd']['ppd3_aws_item_lifetime']));
                        $extra_data['seller_item']['download_link'] = $app_catalogs['customer'].'/index.php?target=seller_getfile&doc_id='.$di['doc_id'].'&seller_item_id='.$seller_item_id;
                        $extra_data['seller_item']['download_link_expire_date'] = time() + $config['ppd']['ppd3_aws_item_lifetime']*60; 

                        cw_array2update('docs_items', array('extra_data' => addslashes(serialize($extra_data))), "item_id = '$di[item_id]'");
                    } 

                    $extra_data['seller_item']['download_link_expired'] = (time() > $extra_data['seller_item']['download_link_expire_date']);

                    $extra_data['seller_item']['data'] = $seller_data;                    
                       
                    $return = $extra_data['seller_item'];
                } 
            } 
        }  
    }

    return $return;
}

function mag_check_digital_seller_product_in_cart($seller_item_id) {
    global $cart;
    $result = false;
    if (!empty($cart)) 
        if (!empty($cart['products']))  
            foreach($cart['products'] as $cp) {
                if (!empty($cp['seller'])) {
                    if ($cp['seller']['seller_item_id'] == $seller_item_id) {
                        $result = true;
                        break; 
                    }  
                }
            } 

    return $result; 
}

function mag_get_shopfront($seller_id) {
    global $tables, $current_area;

    cw_load('image');

    $shopfront_data = cw_query_first("SELECT * FROM $tables[magexch_sellers_shopfront] WHERE seller_id='$seller_id'");
    $shopfront_data['image'] = cw_image_get('shopfront_images', $seller_id);

    if ($current_area == 'C') {
       $shopfront_data['image']['image_x'] = 0;
       $shopfront_data['image']['image_y'] = 0; 
    }

    return $shopfront_data;
}

function mag_delete_shopfront($seller_id, $delete_image = false) {
    global $tables;

    db_query("delete from $tables[magexch_sellers_shopfront] where seller_id='$seller_id'");

    if ($delete_image) 
        cw_image_delete($seller_id, 'shopfront_images');
}

/**
 * Validate username field
 */
function on_register_validate($register, $usertype) {
     $err = cw_check_user_field_username(0, $register['username']);
    return ($err === false)?false:array('username'=>$err);
}

/**
 * Store sellers pages
 */
function on_profile_modify() {
    global $request_prepared, $tables, $user, $usertype;

    if ($usertype != 'V') return null;

    $promopages = array_filter(array_map('intval',$request_prepared['promopages']));
    
    db_query("DELETE FROM $tables[magazine_sellers_pages] WHERE customer_id='$user'");
    
    $data = array('customer_id'=>$user);
    foreach ($promopages as $page_id) {
        $data['contentsection_id'] = $page_id;
        cw_array2insert('magazine_sellers_pages', $data);
    }
    
}

function on_prepare_statuses_list(&$statuses_list) {
    global $current_area, $allowed_seller_display_order_statuses;

    if ($current_area != 'V') return null;

    $filtered_statuses_list = array();
    foreach ($statuses_list as $sl_item) {

        if (in_array($sl_item['code'], $allowed_seller_display_order_statuses)) 
            $filtered_statuses_list[] = $sl_item;

    }
    $statuses_list = $filtered_statuses_list;
}

function on_prepare_search_products($params, &$fields, &$from_tbls, &$query_joins, &$where, &$groupbys, &$having, &$orderbys) {

    global $customer_id, $tables;
    $seller_custom_fields = cw_user_get_custom_fields($customer_id,0,'','field');

    if ($seller_custom_fields['allow_non_marketplace_products'] != 'Y') {

        $attr_id = cw_query_first_cell("SELECT attribute_id FROM $tables[attributes] WHERE field='is_non_marketplace_product'");
        if ($attr_id) {
            $non_marketplace_attr_alias = 'non_marketplace_attr';
            $query_joins[$non_marketplace_attr_alias] = array(
                'tblname' => 'attributes_values',
                'on' => "$tables[products].product_id=$non_marketplace_attr_alias.item_id and $non_marketplace_attr_alias.item_type='P' and $non_marketplace_attr_alias.attribute_id = '$attr_id'",
            );
            $where[] = "COALESCE($non_marketplace_attr_alias.value,0) != 1";
        }   

    }   

}

function cw_seller_delete_profile($customer_id, $userinfo) {
    global $tables;

    $customer_id = isset($customer_id) ? (int) $customer_id : 0;

    cw_log_add(__FUNCTION__, array($customer_id, $userinfo, $tables['magazine_sellers_product_data']));

    if (!empty($customer_id)) {
        $query = 'DELETE FROM ' . $tables['magazine_sellers_product_data'] . ' WHERE seller_id = \'' . $customer_id . '\'';
        db_query($query);
    }
}

function dashboard_get_sections_list() {

    return array(
        'messages_dashboard' => Array (
            'pos' => 10,
            'active' => 1
        ),
        'graph' => Array (
            'pos' => 30,
            'active' => 1 
        ),
        'last_orders' => Array (
            'pos' => 20,
            'active' => 1
        ),
        'system_info' => Array (
            'pos' => 60,
            'active' => 0 
        )
    );
 
/*
    [example] => Array
        (
            [pos] => -999
            [active] => 1
        )

    [last_orders] => Array
        (
            [pos] => 20
            [active] => 1
        )

    [products] => Array
        (
            [pos] => 20
            [active] => 1
        )

    [search] => Array
        (
            [pos] => 10
            [active] => 0
        )

    [graph] => Array
        (
            [pos] => 30
            [active] => 1
        )

    [crontab] => Array
        (
            [pos] => 0
            [active] => 1
        )

    [awaiting_actions] => Array
        (
            [pos] => 50
            [active] => 1
        )

    [system_info] => Array
        (
            [pos] => 60
            [active] => 1
        )

    [news_info] => Array
        (
            [pos] => 70
            [active] => 0
        )

    [system_messages] => Array
        (
            [pos] => 0
            [active] => 1
        )

    [messages_dashboard] => Array
        (
            [pos] => 10
            [active] => 1
        )
*/
}
