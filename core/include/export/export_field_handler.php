<?php
namespace cw\export;

/**
 * Main handler.
 * for $field = 'aaa.bbb.ccc' returns value of $object['aaa']['bbb']['ccc']
 */
function field_handler_general($object, $field) {
    
    $fields = explode('.',$field);
    $r = $object;
    foreach ($fields as $f) {
        $r = $r[$f];
    }
    
    return $r;
}

/**
 * same as field_handler_general but in human date format
 */
function field_handler_general_date($object, $field) {
    $r = cw_call('cw\export\field_handler_general',array($object, $field));
    return date("Y-m-d H:i:s", $r);
}

/**
 * If array - join into string
 */
function field_handler_general_join($object, $field) {
    $r = cw_call('cw\export\field_handler_general',array($object, $field));
    if (is_array($r)) 
        return join(',',$r);
    else 
        return $r;
}

/**
 * For clean_url only.
 * Prepend full http location to URL
 */
function field_handler_clean_url($object, $field) {
    global $current_location;
    $r = cw_call('cw\export\field_handler_general',array($object, $field));
    if (empty($r)) $r = 'index.php?target=product&amp;product_id='.$object['product_id'];
    return $current_location.'/'.$r;
}

/**
 * Field is pre-defined constant
 * e.g. 
 *  'condition_const_new' => array('handler'=>'field_handler_const', 'value'=>'New'),
 */
function field_handler_const($object, $field, $export_type) {
    return $export_type['fields'][$field]['value'];
}
