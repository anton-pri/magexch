<?php

namespace cw\shipping_system;

// use $mode and $action params to define subject and action to call

$action_function = $action;
// $action_function = $mode.'_'.$action;

// Default action
if (empty($action_function) || !function_exists('cw\shipping_system\\'.$action_function)) {
    $action_function = 'order_tracking_redirect';
}

// Call action
$action_result = cw_call('cw\shipping_system\\'.$action_function);

return $action_result;

/* ================================================================================== */

/* Actions */

function order_tracking_redirect() {
    global $smarty, $request_prepared;
    
    cw_load('doc');
    
    $order = cw_call('cw_doc_get', array($request_prepared['doc_id'],0));

    if (empty($order) || $order['info']['tracking'] != $request_prepared['tracking']) {
        cw_header_location('index.php?target=error_message&error=access_denied&id=59');
    }
    
    $smarty->assign('order', $order);

    $form = cw_display('addons/shipping_system/tracking.tpl', $smarty, false);

    echo $form;
    exit();
}

