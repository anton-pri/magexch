<?php
namespace cw\export\orders;

const export_type = 'orders';

cw_load('doc');

function get_export_type() {
    return array(
        'name'          => 'Orders',
        'codename'      =>  export_type,
        'orderby'       => '100',
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
        'subtotal'      => array('field' => 'info.subtotal'),
        'discount'      => array('field' => 'info.discount'),
        'shipping_cost' => array('field' => 'info.shipping_cost'),
        'weight'        => array('field' => 'info.weight'),
        'tax'           => array('field' => 'info.tax'),
        'total'         => array('field' => 'info.total'),
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
    );
    
}

function get_schemas() {
    return array(
        'base' => array(
            'name' => 'Short',
            'fields' => 'doc_id,date,status,payment_id,shipping_id,subtotal,shipping_cost,total,email',
        ),
        'extended' => array(
            'name'   => 'Extended',
            'fields' => array(
                    'doc_id',
                    'date',         
                    'status',       
                    'tracking',     
                    'payment_id',   
                    'payment_label',
                    'shipping_id',  
                    'shipping_label',
                    'subtotal',     
                    'discount',     
                    'shipping_cost',
                    'weight',       
                    'tax',          
                    'total',        
                    'carrier',      
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
                    ),
        ),
    );
}

function get_key_fields($saved_search) {
    global $tables, $customer_id;
    if ($saved_search) {
        $query = "SELECT doc_id from $tables[docs] as d INNER JOIN $tables[objects_set] os 
                    ON d.doc_id=os.object_id AND os.customer_id=$customer_id AND os.set_type='O-{$saved_search}'";
    } else {
        $query = "SELECT doc_id FROM $tables[docs]";
    }
    return cw_query_column($query);
}

function get_data($key_field) {
    return cw_call('cw_doc_get',array($key_field,0));
}
