<?php
namespace cw\custom_magazineexchange_external_links;

// use $target, $mode and $action params to define subject and action to call
// e.g. $target_$mode_$action or $target_$mode or $target_$action
$action_function = join('_',array_filter(array($target,$mode,$action)));

// Default action
if (empty($action_function) || !function_exists('cw\\'.addon_name.'\\'.$action_function)) {
	$action_function = 'product';
}

if (!$product_id) {
    return error('Invalid product instance'); // return Error instance
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

/**
 * View product details
 * [target:product][mode:][action:]
 */
function product($product_id) {
    global $request_prepared, $smarty;
    
    $external_links = cw_call('cw\custom_magazineexchange_external_links\get_by_product_id', array($product_id));
    
    $smarty->assign('external_links',$external_links);

    return $external_links;
}

/* Service functions */


