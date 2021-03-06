<?php
namespace cw\DataScraper;

// use $target, $mode and $action params to define subject and action to call
// e.g. $target_$mode_$action or $target_$mode or $target_$action
$action_function = join('_',array_filter(array($target,$mode,$action)));

// Default action
if (empty($action_function) || !function_exists('cw\\'.addon_name.'\\'.$action_function)) {
	$action_function = 'view';
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

function view() {
    global $request_prepared;
    global $smarty;    
/*
    if (!$request_prepared['id']) {
        return error('Invalid instance ID'); // return Error instance
    }
*/    
    /*
     * Do actions with object here
     */
    $sites_tbl_fields = cw_call('cw\\'.addon_name.'\\cw_datascraper_get_table_fields', array('sites_config'));

    $smarty->assign('sites_tbl_fields', $sites_tbl_fields);

    $smarty->assign('main', 'datascraper_sites');   
    return $object;
}

function addon_main_target_modify() {
}

function addon_main_target_add() {
}

function addon_main_target_delete() {
}

/* Service functions */

function get_data() {
}
