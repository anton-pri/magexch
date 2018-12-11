<?php

// Returns linked products
// link_type = [0 - upsel | 1 - accessory]
function cw_ac_get_linked_products($product_id, $link_type=0) {
    global $tables, $config, $user_account, $current_area;

    $data = array();

    if ($config['General']['disable_outofstock_products'] == 'Y') {
        $data['avail_min'] = 0;
    }

    $add_data = array();
    $add_data['query_joins']['linked_products'] = array (
            'on' => "$tables[products].product_id=".$tables['linked_products'].".linked_product_id AND ".$tables['linked_products'].".product_id='$product_id' AND link_type = '$link_type'",
            'is_inner' => 1,
        );
    $add_data['fields'][] = "$tables[linked_products].orderby";
    $add_data['fields'][] = "$tables[linked_products].active";
    $data['sort_condition'] = $tables['linked_products'].".orderby, $tables[products].product";

    list($products, $navigation) = cw_func_call('cw_product_search', array('data' => $data, 'user_account' => $user_account, 'info_type' => 0,'current_area' => $current_area), $add_data);

    $result = array();
    foreach ($products as $prod) {
      $product = cw_ac_get_product_info($prod['product_id']);
      $product['orderby'] = $prod['orderby'];
      $product['active'] = $prod['active'];
      $product['is_bidirectional_link'] = cw_ac_is_bidirectional($product_id, $prod['product_id'], $link_type);
      $product['manufacturer'] = $prod['manufacturer'];
      $result[$prod['product_id']] = $product;
    }

    return $result;

}

function cw_ac_is_bidirectional($id1, $id2, $link_type=0) {
    global $tables;
    return (cw_query_first_cell("SELECT count(*) FROM $tables[linked_products] WHERE ((product_id=$id1 AND linked_product_id=$id2) OR (product_id=$id2 AND linked_product_id=$id1)) AND link_type='$link_type'") == 2);
}

function cw_ac_get_recommended_smarty($product_id) {
    $recommended_products = cw_call('cw_ac_get_recommended',array($product_id));
/*
    $key = '_only_cleanup_rebuild_';
    if (!($recommended_products = cw_cache_get($key, 'recommended_products_'.$product_id))) {
        $recommended_products = cw_call('cw_ac_get_recommended',array($product_id)); 
        cw_cache_save($recommended_products, $key, 'recommended_products_'.$product_id);
    }
*/
    return $recommended_products;
}

function cw_ac_get_recommended_products_limit() {
    global $config;
    $recommended_products_limit = intval($config['accessories']['ac_rec_products_limit']);

    if ($recommended_products_limit < 1) {
      $recommended_products_limit = 1;
    }
    elseif ($recommended_products_limit > 100) {
      $recommended_products_limit = 100;
    }
    return $recommended_products_limit;
}

function cw_ac_get_recommended_ids($product_id) {
    global $tables, $config, $user_account, $current_area;

    $possible_statuses = cw_ac_get_processable_order_statuses();

    $recommended_products_limit = cw_call('cw_ac_get_recommended_products_limit');

    if (function_exists('cw_md_product_search'))
        $mdm = cw_md_product_search(null,null);

    if ($config['accessories']['ac_rec_list_source'] == 'T') {
      $query = "SELECT dibt.product_id, SUM(dibt.amount) AS total_purchased 
                FROM cw_docs_items dibt 
                INNER JOIN cw_docs d ON 
                   d.doc_id=dibt.doc_id AND d.type = 'O' AND d.status IN ('Q','P','B','C','H','W','A','R','AP') 
                INNER JOIN cw_docs_items dio ON dio.doc_id=d.doc_id AND dio.product_id='$product_id' 
                INNER JOIN cw_products p ON p.product_id=dibt.product_id AND p.status=1 ".
(!empty($mdm)?" INNER JOIN $tables[attributes_values] ON ".str_replace($tables['products'], 'p',$mdm['query_joins']['attributes_values']['on']):'') 
             ." GROUP BY dibt.product_id 
                HAVING dibt.product_id != '$product_id' 
                ORDER BY total_purchased DESC LIMIT 0, ".$recommended_products_limit;

    }
    elseif ($config['accessories']['ac_rec_list_source'] == 'S') {
      $query = "SELECT di.product_id, SUM(di.amount) AS total_purchased " .
               "FROM $tables[docs] AS d " .
               "LEFT JOIN $tables[docs_items] AS di " .
                 "ON d.doc_id = di.doc_id" .
                 (!empty($mdm)?" INNER JOIN $tables[attributes_values] ON ".str_replace($tables['products'], 'di',$mdm['query_joins']['attributes_values']['on']):'').
               " WHERE d.type = 'O' AND d.status IN ('".implode("','", $possible_statuses)."') " .
               "  AND di.product_id != '".$product_id."' AND NOT ISNULL(di.product_id) " .
               "GROUP BY di.product_id " .
               "ORDER BY total_purchased DESC " .
               "LIMIT 0, ".$recommended_products_limit;
    }
    $recommended_product_ids = cw_query($query);
    return $recommended_product_ids; 
}


function cw_ac_get_recommended($product_id) {

    $recommended_product_ids = cw_call('cw_ac_get_recommended_ids',array($product_id)); 

    if (!empty($recommended_product_ids) && is_array($recommended_product_ids)) {
      $recommended_products = array();
      foreach ($recommended_product_ids as $recommended_product) {
        $pid = $recommended_product['product_id'];
        $recommended_products[$pid] = cw_ac_get_product_info($pid);
        if (empty($recommended_products[$pid]['product_id'])) {
            unset($recommended_products[$pid]);
        }
      }
    }

    return $recommended_products;
}

function cw_ac_get_processable_order_statuses() {
  cw_load('doc'); 
  return cw_doc_get_inventory_decreasing_statuses();
}

function cw_ac_get_product_info($product_id) {

  global $user_account, $addons, $tables, $config;

  $product_id = intval($product_id);
  if (empty($product_id)) return false;
  $product = cw_func_call('cw_product_get',array('id'=>$product_id,'info_type'=>8|32|64|128|2048,'user_account'=>$user_account));
  if (!(!empty($product) && is_array($product))) return false;

  if (!empty($addons['wholesale_trading'])) {
    $allowed_membership_ids = array(0);
    $membership_id = intval($user_account['membership_id']);
    if (!empty($membership_id)) $allowed_membership_ids[] = $membership_id;
    $query = "SELECT pp.quantity, pp.price " .
             "FROM $tables[products_prices] AS pp " .
             "WHERE pp.product_id = '".$product_id."' AND pp.membership_id IN ('".implode("','", $allowed_membership_ids)."') " .
               "AND pp.quantity > 1 AND pp.variant_id = 0 " .
             "GROUP BY pp.quantity " .
             "ORDER BY pp.quantity";
    $wholesale_prices = cw_query($query);
    if (!empty($wholesale_prices) && is_array($wholesale_prices)) {
      $query = "SELECT MIN(pp.price) " .
               "FROM $tables[products_prices] AS pp " .
               "WHERE pp.quantity = 1 AND pp.membership_id IN ('".implode("','", $allowed_membership_ids)."') " .
                 "AND pp.variant_id = 0 AND pp.product_id = '".$product_id."'";
      $min_price = doubleval(cw_query_first_cell($query));
      $prev_key = false;
      foreach ($wholesale_prices as $k => $wholesale_price) {
        if (doubleval($wholesale_price['price']) > $min_price) {
          unset($wholesale_prices[$k]);
          continue;
        }
        $min_price = doubleval($wholesale_price['price']);
        $wholesale_taxes = cw_tax_price(intval($wholesale_price['price']), $user_account, $product_id);
        $wholesale_prices[$k]['taxed_price'] = $wholesale_taxes['taxed_price'];
        $wholesale_prices[$k]['taxes'] = $wholesale_taxes['taxes'];
        if ($prev_key !== false && isset($wholesale_prices[$prev_key])) {
          $wholesale_prices[$prev_key]['next_quantity'] = intval($wholesale_price['quantity']) - 1;
          if (intval($product_accessory['min_amount']) > intval($wholesale_prices[$prev_key]['next_quantity'])) {
            unset($wholesale_prices[$prev_key]);
          }
          elseif (intval($product_accessory['min_amount']) > intval($wholesale_price['quantity'])) {
            $wholesale_prices[$prev_key]['quantity'] = intval($product_accessory['min_amount']);
          }
        }
        $prev_key = $k;
      }
      $wholesale_prices = array_values($wholesale_prices);
      $product['product_wholesale'] = $wholesale_prices;
    }
  }
  return $product;
}

function cw_ac_on_add_to_cart(&$cart, $added_product) {
    $recommended_products = cw_ac_get_linked_products($added_product['product_id'], 0);
    if (empty($recommended_products)) $recommended_products = cw_ac_get_linked_products($added_product['product_id'], 1);
    if (empty($recommended_products)) $recommended_products = cw_ac_get_recommended($added_product['product_id']);

    global $smarty, $config;
    if (is_array($recommended_products))  
        $smarty->assign('recommended_products', array_slice($recommended_products, 0, 3));

    $config['Appearance']['products_per_row'] = 4;
}


function cw_ac_rv_product_set_cookie($product_id) {

    $recent_product_ids = array();

    if(!empty($_COOKIE['recent_product_ids']))
        $recent_product_ids = unserialize(($_COOKIE['recent_product_ids']));
    if(($key = array_search($product_id, $recent_product_ids)) !== false){
        unset($recent_product_ids[$key]);
    }
    array_push($recent_product_ids, $product_id);

    if(count($recent_product_ids)>=10)
        $recent_product_ids = array_slice($recent_product_ids,  count($recent_product_ids)-10);

    return  cw_set_cookie('recent_product_ids', serialize($recent_product_ids), time()+(30*24*60*60), '/');

}

function cw_ac_rv_product_get_cookie() {

    if (empty($_COOKIE['recent_product_ids'])) return false;
    $recent_product_ids = unserialize(stripcslashes($_COOKIE['recent_product_ids']));

    return $recent_product_ids;
}

function cw_ac_cab_get_recommended($product_ids){
    global $tables, $config;

    $recommended_products_limit = $config['accessories']['ac_cab_max_products'];

    $docs_ids =  cw_query_column("SELECT DISTINCT doc_id FROM $tables[docs_items] WHERE product_id IN (".implode(", ", $product_ids).")");

    $query ="SELECT p.product_id, di.doc_id, count(p.product_id) AS total_purchased ".
            "FROM $tables[docs_items]  AS di ".
            "INNER JOIN cw_products AS p ON p.product_id = di.product_id AND p.status=1 ".
            "WHERE di.doc_id IN ('".implode("','", $docs_ids)."') " .
            "GROUP BY p.product_id ".
            "HAVING p.product_id NOT IN (".implode(", ", $product_ids).") AND NOT ISNULL(p.product_id) ".
            "LIMIT 0, ".$recommended_products_limit;

    $recommended_product_ids = cw_query($query);

    if (!empty($recommended_product_ids) && is_array($recommended_product_ids)) {
        $recommended_products = array();
        foreach ($recommended_product_ids as $recommended_product) {
            $pid = $recommended_product['product_id'];
            $recommended_products[$pid] = cw_ac_get_product_info($pid);
            if (empty($recommended_products[$pid]['product_id'])) {
                unset($recommended_products[$pid]);
            }
        }
    }

    return $recommended_products;
}
