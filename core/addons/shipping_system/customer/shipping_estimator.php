<?php

namespace cw\shipping_system;

// use $mode and $action params to define subject and action to call

$action_function = $action;
// $action_function = $mode.'_'.$action;

// Default action
if (empty($action_function) || !function_exists('cw\shipping_system\\'.$action_function)) {
    $action_function = 'view_shipping_estimator';
}

// Call action
$action_result = cw_call('cw\shipping_system\\'.$action_function);

return $action_result;

/* ================================================================================== */

/* Actions */

function view_shipping_estimator() {

    cw_add_ajax_block(array(
        'id' => 'script',
        'content' => 'sm("shipping_estimator_dialog", 0, 0, true, "Estimate shipping")',
    ));
}

function estimate_cart() {
    global $request_prepared;
    $user_address = &cw_session_register('user_address', array());    // Declare session var for addresses
    $user_address['current_address']['zipcode'] = $request_prepared['zipcode'];
    $user_address['current_address']['state']   = $request_prepared['state'];
    $user_address['current_address']['country'] = $request_prepared['country'];
    if (empty($user_address['current_address']['state']) && $user_address['current_address']['country']=='US') {
        cw_load('map');
        $user_address['current_address']['state'] = cw_call('cw_map_get_state_by_zip', array($user_address['current_address']['zipcode']));
    }

    cw_header_location('index.php?target=cart');
}
