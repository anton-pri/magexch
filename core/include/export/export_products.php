<?php
namespace cw\export\products;

const export_type = 'products';

cw_load('product');

function get_export_type() {
    return array(
        'name'          => 'Products',
        'codename'      =>  export_type,
        'orderby'       => '50',
        'saved_search'  => 'P',
        'fields'        => cw_call('cw\export\\'.export_type.'\get_fields'),
        'schemas'       => cw_call('cw\export\\'.export_type.'\get_schemas'),
    );
}

function get_fields() {
    return array(
    'product_id' => array(),
    'productcode' => array(),
    'product' => array(),
    'distribution' => array(),
    'weight' => array(),
    'descr' => array(),
    'fulldescr' => array(),
    'shipping_freight' => array(),
    'free_shipping' => array(),
    'discount_avail' => array(),
    'min_amount' => array(),
    'dim_x' => array(),
    'dim_y' => array(),
    'dim_z' => array(),
    'low_avail_limit' => array(),
    'free_tax' => array(),
    'product_type' => array(),
    'eancode' => array(),
    'manufacturer_code' => array(),
    'attribute_class_id' => array(),
    'status' => array(),
    'cost' => array(),
    'size' => array(),
    'avail' => array(),
    'is_variants' => array(),
    'views_stats' => array(),
    'sales_stats' => array(),
    'del_stats' => array(),
    'add_to_cart' => array(),
    'price' => array(),
    'list_price' => array(),
        'system.creation_customer_id' => array(),
        'system.creation_date' => array(),
        'system.modification_customer_id' => array(),
        'system.modification_date' => array(),
        'system.supplier_customer_id' => array(),
    'attribute_class_ids' => array('handler'=>'field_handler_general_join'),
    'membership_ids' => array('handler'=>'field_handler_general_join'),
    'image_thumb' => array('field'=>'image_thumb.tmbn_url'),
    'image_det' => array('field'=>'image_det.tmbn_url'),
    'tags' => array('handler'=>'field_handler_general_join'),
    'rating' => array(),
    'comments' => array(),
    'manufacturer' => array(),
    'url' => array('field'=>'attributes.clean_url', 'handler'=>'field_handler_clean_url'),

    // Custom attributes are dynamic.
    //
    // For attributes you can just put field into the schema as 'attributes.<attr_name>'
    );
    
}

function get_schemas() {
    return array(
        'standard' => array(
            'name'   => 'Standard',
            'fields' => array(
                'product_id',
                'productcode',
                'product',
                'weight',
                'descr',
                'eancode',
                'manufacturer_code',
                'status',
                'cost',
                'size',
                'avail',
                'views_stats',
                'sales_stats',
                'del_stats',
                'add_to_cart',
                'price',
                'list_price',
                'image_thumb',
                'image_det',
                'manufacturer',
                'url',
                    ),
        ),
    );
}

function get_key_fields($saved_search) {
    global $tables, $customer_id;
    if ($saved_search) {
        $query = "SELECT product_id from $tables[products] as main INNER JOIN $tables[objects_set] os 
                    ON main.product_id=os.object_id AND os.customer_id=$customer_id AND os.set_type='P-{$saved_search}'";
    } else {
        $query = "SELECT product_id FROM $tables[products]";
    }
    return cw_query_column($query);
}

function get_data($key_field) {
    global $tables;
    static  $attr;
    if (is_null($attr)) $attr = cw_call('cw_attributes_filter', array(array('item_type'=>'P'),false,'attribute_id'));

    $p = cw_func_call('cw_product_get',array('id'=>$key_field,'info_type'=>8|64|128|256|512|2048));
    $p['attributes'] = cw_query_hash("select a.field, IF(ISNULL(ad.value),av.value,ad.value) as value 
                    FROM $tables[attributes_values] av 
                    LEFT JOIN $tables[attributes_default] ad on attribute_value_id=av.value and ad.attribute_id=av.attribute_id
                    LEFT JOIN $tables[attributes] as a ON av.attribute_id=a.attribute_id
                    WHERE item_id=$key_field AND av.attribute_id IN ('".implode("','",$attr)."')",'field',false, true);
    return $p;
}
