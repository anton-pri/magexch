<?php
// use $mode and $action params to define subject and action to call

$action_function = 'cw_payment_process_'.$mode;

// No default action
if (empty($action_function) || !function_exists($action_function)) {
    // $mode handler is not in this controller
    return false;
}

if (empty($request_prepared['doc_id'])) {
    return error('Param doc_id is empty');
}

cw_load('doc');

$order = cw_call('cw_doc_get', array($request_prepared['doc_id']));

if (!$order) {
    return error(cw_get_langvar_by_name('lbl_order_not_found'));
}

// Call action
$action_result = cw_call($action_function, array($order));

if (is_error($action_result)) {
    cw_add_top_message($action_result->getMessage(), 'E');
}

cw_header_location("index.php?target=docs_O&mode=details&js_tab=process&doc_id=" . $doc_id);


/* ================================================================================== */

/* Actions */

// Capture pre-authorized payment transaction
function cw_payment_process_capture($order) {
    global $config;

    $doc_id = $order['doc_id'];
    $result = cw_call('cw_payment_do_capture', array($order));
    
    if (!is_error($result)) {
       cw_call('cw_doc_place_extras_data',  array($doc_id, array('capture_status'=>'C','captured_amount'=>$order['info']['total'],'capture_pnref'=>$result['pnref'])));
       cw_call('cw_doc_change_status',      array($doc_id, cw_call('cw_payment_doc_status_after_capture', array($result))));
       cw_add_top_message($config['General']['currency_symbol'].$order['info']['total'].' successfully captured, order status is changed to Processed');
    }
    
    return $result;
    
} 

// Void pre-authorized payment transaction
function cw_payment_process_void ($order) {

  global $config;

    $doc_id = $order['doc_id'];
    
    $result = cw_call('cw_payment_do_void', array($order));
    
    if (!is_error($result)) {
       cw_call('cw_doc_place_extras_data',  array($doc_id, array('capture_status'=>'V')));
       cw_call('cw_doc_change_status',      array($doc_id, 'D'));
       cw_add_top_message('Payment voided, order status is changed to Declined');
    }
    
    return $result;

}
