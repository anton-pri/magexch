<?php
namespace cw\custom_magazineexchange_sellers;

// use $mode and $action params to define subject and action to call

//$action_function = $action;
$action_function = $mode.(!empty($action)?'_'.$action:'');

// Default action
if (empty($action_function) || !function_exists('cw\\'.addon_name.'\\'.$action_function)) {
    return false;
}

// Call action
$action_result = cw_call('cw\\'.addon_name.'\\'.$action_function);

// Action can return instance of Error via error() function
// see docs/core.error.txt
if (is_error($action_result)) {
    cw_add_top_message($action_result->getMessage(), 'E');
}

return $action_result;

/* ================================================================================== */

/* Actions */

/**
 * Products search. Add seller data to each found product.
 * target=products&mode=search
 */
function search() {
    global $request_prepared;
    global $products, $customer_id;
   
    global $target, $edited_language;
 
    if (!empty($customer_id) && is_array($products)) {
        foreach ($products as $k=>$v) {
            $products[$k]['seller_data'] = cw_call('cw\custom_magazineexchange_sellers\mag_product_seller_data', array($v['product_id'], $customer_id));
            if ($target == 'digital_products' && !empty($products[$k]['seller_data'])) {
                foreach ($products[$k]['seller_data'] as $sd_k => $seller_data) {
                    $products[$k]['seller_data'][$sd_k]['attributes'] = cw_func_call('cw_attributes_get', array('item_id' => $seller_data['seller_item_id'], 'item_type' => 'SP', 'language' => $edited_language)); 

                }
            } 
        }
    }
    
    return true;
}

/**
 * Products update. Update seller data in custom table.
 * target=products&mode=process&action=update
 */
function process_update() {
    global $request_prepared, $action, $customer_id, $tables, $target, $edited_language;
    
    $posted_data = $request_prepared['posted_data'];
    $seller_data = $request_prepared['seller_data'];
     
    //$product_ids = $request_prepared['product_ids'];
    $delete_seller_ids = (array)($request_prepared['delete_seller_ids']);

    $updated_products = array();

    if ($target == 'digital_products') { 

        $sp_attributes = cw_func_call('cw_attributes_get', array('item_id' => 0, 'item_type' => 'SP', 'language' => $edited_language)); 

        if (is_array($seller_data)) {
            foreach ($seller_data as $k=>$v) {

                $updated_product_id = cw_query_first_cell("SELECT product_id FROM $tables[magazine_sellers_product_data] WHERE seller_item_id='$k'");

                if (!in_array($updated_product_id, $updated_products))
                    $updated_products[] = $updated_product_id;

                if ($v['price'] && !in_array($k, array_keys($delete_seller_ids))) {
                    $data = array(
                        'comments'      => cw_strip_tags($v['comments']),
                        'price'         => floatval($v['price']),
                    );
                    cw_array2update('magazine_sellers_product_data',$data,"seller_item_id='$k'");

                    $update_sp_attributes = array();
  
                    foreach ($sp_attributes as $sp_attribute_field => $sp_attribute) {
                        $update_sp_attributes[$sp_attribute_field] = $v[$sp_attribute_field];
                    } 
                    cw_call('cw_attributes_save', array('item_id' => $k, 'item_type' => 'SP', 'attributes' => $update_sp_attributes, 'language' => $edited_language, array('update_posted_only'=>true, 'is_default' => false)));

                } else {
                    db_query("DELETE FROM $tables[magazine_sellers_product_data] WHERE seller_item_id='$k' AND seller_id='$customer_id'");
                    db_query("DELETE FROM $tables[attributes_values] WHERE item_id='$k' AND item_type='SP'");
                }
            }
        }

        if (is_array($posted_data)) {
            foreach ($posted_data as $k=>$v) {

                if (!in_array($k, $updated_products))
                    $updated_products[] = $k;

                if ($v['price']) {
                    $data = array(
                        'product_id'    => intval($k),
                        'seller_id'     => intval($customer_id),
                        'comments'      => cw_strip_tags($v['comments']),
                        'price'         => floatval($v['price']),
                        'is_digital'    => 1,
                        'quantity'      => 32767
                    );
                    $seller_item_id = cw_array2insert('magazine_sellers_product_data',$data, true);

                    $update_sp_attributes = array();

                    foreach ($sp_attributes as $sp_attribute_field => $sp_attribute) {
                        $update_sp_attributes[$sp_attribute_field] = $v[$sp_attribute_field];
                    }
                    cw_call('cw_attributes_save', array('item_id' => $seller_item_id, 'item_type' => 'SP', 'attributes' => $update_sp_attributes, 'language' => $edited_language, array('update_posted_only'=>true, 'is_default' => false)));

                }
//              cw_call('cw\custom_magazineexchange_sellers\mag_product_update_stock', array($k));
            }
        }

    } else {

        if (is_array($seller_data)) {
            foreach ($seller_data as $k=>$v) {

                $updated_product_id = cw_query_first_cell("SELECT product_id FROM $tables[magazine_sellers_product_data] WHERE seller_item_id='$k'");

                if (!in_array($updated_product_id, $updated_products))
                    $updated_products[] = $updated_product_id;

                if (($v['quantity'] || $v['price']) && !in_array($k, array_keys($delete_seller_ids))) {
                    $data = array(
                        'comments'      => cw_strip_tags($v['comments']),
                        'price'         => floatval($v['price']),
                        'quantity'      => intval($v['quantity']),
                        'condition'     => intval($v['condition']),
                    );
                    cw_array2update('magazine_sellers_product_data',$data,"seller_item_id='$k'");
                } else {
                    db_query("DELETE FROM $tables[magazine_sellers_product_data] WHERE seller_item_id='$k' AND seller_id='$customer_id'");
                }
            }
        }
        
        if (is_array($posted_data)) {
            foreach ($posted_data as $k=>$v) {

                if (!in_array($k, $updated_products)) 
                    $updated_products[] = $k;

                if ($v['quantity'] || $v['price']) {
                    $data = array(
                        'product_id'    => intval($k),
                        'seller_id'     => intval($customer_id),
                        'comments'      => cw_strip_tags($v['comments']),
                        'price'         => floatval($v['price']),
                        'quantity'      => intval($v['quantity']),
                        'condition'     => intval($v['condition']),
                        'is_digital'    => 0
                    );
                    cw_array2insert('magazine_sellers_product_data',$data, true);
                }
//                cw_call('cw\custom_magazineexchange_sellers\mag_product_update_stock', array($k));
            }
        }
    }

    if (!empty($updated_products)) {
        foreach($updated_products as $product_id) {
            cw_call('cw\custom_magazineexchange_sellers\mag_product_update_stock', array($product_id));   
        }  
    } 
  /* 
    if (!empty($delete_seller_ids)) {
        db_query("DELETE FROM $tables[magazine_sellers_product_data] WHERE seller_item_id IN ('".implode("','", array_keys($delete_seller_ids))."')");
        db_query("DELETE FROM $tables[attributes_values] WHERE item_id IN ('".implode("','", array_keys($delete_seller_ids))."') AND item_type='SP'");
    }
  */
    $action = ''; // Prevent standard processing
    
    return true;
}

function details() {
    return products_access_denied();
}
/* Service functions */

function products_access_denied() {
    cw_header_location("index.php?target=error_message&error=access_denied&id=40");
}
