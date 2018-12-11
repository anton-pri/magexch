<?php
namespace cw\custom_magazineexchange_sellers;

// use $target, $mode and $action params to define subject and action to call
// e.g. $target_$mode_$action or $target_$mode or $target_$action
$action_function = join('_',array_filter(array($target,$mode,$action)));

// Default action
if (empty($action_function) || !function_exists('cw\\'.addon_name.'\\'.$action_function)) {
    $action_function = 'docs_O_details';
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

function docs_O_details() {
    global $doc_data, $smarty;
    
    $seller_info = cw_call('cw_seller_get_info', array($doc_data['info']['warehouse_customer_id']));

    $smarty->assign('seller_info', $seller_info);
    
}

 /* Service function */


