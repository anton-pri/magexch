<?php
namespace cw\export\orders_items;

const export_type = 'orders_items';

cw_load('doc');

function get_export_type() {
    return array(
        'name'          => 'Orders products',
        'codename'      =>  export_type,
        'orderby'       => '110',
        'saved_search'  => 'O',
        'fields'        => cw_call('cw\export\\'.export_type.'\get_fields'),
        'schemas'       => cw_call('cw\export\\'.export_type.'\get_schemas'),
    );
}

function get_fields() {
    return array(
        'doc_id'        => array('name' => 'Order ID'),
        'date'          => array('handler' => 'field_handler_general_date'),
        'status'        => array(),
        // Info
        'tracking'      => array('field' => 'info.tracking'),
        'payment_id'    => array('field' => 'info.payment_id'),
        'payment_label' => array('field' => 'info.payment_label'),
        'shipping_id'   => array('field' => 'info.shipping_id'),
        'shipping_label' => array('field' => 'info.shipping_label'),
        'carrier'       => array('field' => 'info.carrier.carrier'),
        // Userinfo
        'customer_id'   => array('field' => 'userinfo.customer_id'),
        'email'         => array('field' => 'userinfo.email'),
        'billing.firstname' => array('name' => 'Firstname (billing)', 'field' => 'userinfo.main_address.firstname'),
        'billing.lastname'  => array('name' => 'Lastname (billing)', 'field' => 'userinfo.main_address.lastname'),
        'billing.city'      => array('name' => 'city (billing)', 'field' => 'userinfo.main_address.city'),
        'billing.state'     => array('name' => 'state (billing)', 'field' => 'userinfo.main_address.state'),
        'billing.country'   => array('name' => 'country (billing)', 'field' => 'userinfo.main_address.country'),
        'billing.zipcode'   => array('name' => 'zipcode (billing)', 'field' => 'userinfo.main_address.zipcode'),
                       
        'shipping.firstname'=> array('name' => 'Firstname (shipping)', 'field' => 'userinfo.current_address.firstname'),
        'shipping.lastname' => array('name' => 'Lastname (shipping)', 'field' => 'userinfo.current_address.lastname'),         
        'shipping.city'     => array('name' => 'city (shipping)', 'field' => 'userinfo.current_address.city'),
        'shipping.state'    => array('name' => 'state (shipping)', 'field' => 'userinfo.current_address.state'),
        'shipping.country'  => array('name' => 'country (shipping)', 'field' => 'userinfo.current_address.country'),
        'shipping.zipcode'  => array('name' => 'zipcode (shipping)', 'field' => 'userinfo.current_address.zipcode'),
        // Product
        'product.product_id'        => array(),
        'product.productcode'       => array(),
        'product.product'           => array(),
        'product.weight'            => array(),
        'product.descr'             => array(),
        'product.fulldescr'         => array(),
        'product.shipping_freight'  => array(),
        'product.free_shipping'     => array(),
        'product.discount_avail'    => array(),
        'product.min_amount'        => array(),
        'product.dim_x'             => array(),
        'product.dim_y'             => array(),
        'product.dim_z'             => array(),
        'product.low_avail_limit'   => array(),
        'product.free_tax'          => array(),
        'product.product_type'      => array(),
        'product.return_time'       => array(),
        'product.features_text'     => array(),
        'product.specifications'    => array(),
        'product.shippings'         => array(),
        'product.eancode'           => array(),
        'product.manufacturer_code' => array(),
        'product.attribute_class_id'=> array(),
        'product.status'            => array(),
        'product.cost'              => array(),
        'product.size'              => array(),
        'product.sku'               => array(), 
        'product.item_id'           => array(),
        'product.variant_id'        => array(),
        'product.product_options'   => array(),
        'product.price'             => array(),
        'product.history_cost'      => array(),
        'product.amount'            => array(),
        'product.is_deleted'        => array(),
        'product.supplier_customer_id'  => array(),
        'product.product_options_txt'   => array(),
        'product.display_price'         => array(),
        'product.display_discounted_price'  => array(),
        'product.display_subtotal'  => array(),

    );
    
}

function get_schemas() {
    return array(
        'product' => array(
            'name' => 'Product accent',
            'fields' => array('doc_id','date','status','payment_id','shipping_id',
            'product.product_id',
            'product.productcode',
            'product.product',
            'product.weight',
            'product.eancode',
            'product.manufacturer_code',
            'product.cost',
            'product.sku',
            'product.item_id',
            'product.variant_id',
            'product.price',
            'product.history_cost',
            'product.amount',
            'product.product_options_txt',
            'product.display_price',
            'product.display_discounted_price',
            'product.display_subtotal',
            ),
        ),
        'customer' => array(
            'name'   => 'Customer accent',
            'fields' => array('doc_id','date','status','payment_id','shipping_id',
                    'customer_id',  
                    'email',        
                    'billing.firstname',
                    'billing.lastname', 
                    'billing.city',     
                    'billing.state',    
                    'billing.country',  
                    'billing.zipcode',  
                    'shipping.firstname',
                    'shipping.lastname',
                    'shipping.city',    
                    'shipping.state',   
                    'shipping.country', 
                    'shipping.zipcode', 
                    'product.product_id',
                    'product.productcode',
                    'product.product',
                    'product.product_options_txt',
                    'product.amount',
                    'product.display_price',
                    'product.display_subtotal',
                    ),
        ),
    );
}

/**
 * @return array('doc_id'=>doc_id, 'item_id'=>item_id)
 */
function get_key_fields($saved_search) {
    global $tables, $customer_id;
    if ($saved_search) {
        $query = "SELECT d.doc_id, d.item_id FROM $tables[docs_items] as d INNER JOIN $tables[objects_set] os 
                    ON d.doc_id=os.object_id AND os.customer_id=$customer_id AND os.set_type='O-{$saved_search}' 
                    ORDER BY doc_id";
    } else {
        $query = "SELECT doc_id, item_id FROM $tables[docs_items] ORDER BY doc_id";
    }
    return cw_query($query);
}

/**
 * Return slightly transformed doc info with one [product] only instead of multiple [products] array
 * 
 * @param $key_field = array(item_id, doc_id)
 */
function get_data($key_field) {
    static $current_doc;
    if ($key_field['doc_id'] != $current_doc['doc_id']) {
        $current_doc = cw_call('cw_doc_get',array($key_field['doc_id'],0));
        // reindex products in order to have item_id as key
        for ($i=0; $i<count($current_doc['products']); $i++) {
            $p = $current_doc['products'][$i];
            $current_doc['products'][$p['item_id']] = $p;
            unset($current_doc['products'][$i]);
        }
    }
    
    $doc_item = $current_doc;
    $doc_item['product'] = $current_doc['products'][$key_field['item_id']];
    unset($doc_item['products']);
    
    return $doc_item;
}
