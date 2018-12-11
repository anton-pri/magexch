<?php
namespace cw\custom_magazineexchange_sellers;

// use $mode and $action params to define subject and action to call

$action_function = $action;
// $action_function = $mode.'_'.$action;

// Default action
if (empty($action_function) || !function_exists('cw\\'.addon_name.'\\'.$action_function)) {
	$action_function = 'view';
}

// Call action
$action_result = cw_call('cw\\'.addon_name.'\\'.$action_function, array($product_id));

// Action can return instance of Error via error() function
// see docs/core.error.txt
if (is_error($action_result)) {
    cw_add_top_message($action_result->getMessage(), 'E');
}

return $action_result;

/* ================================================================================== */

/* Actions */

function view($product_id) {
    global  $smarty;
    
    $sellers_data = cw_call('cw\\'.addon_name.'\mag_product_sellers_data', array($product_id));
    
    $smarty->assign('sellers_data', $sellers_data);
}


