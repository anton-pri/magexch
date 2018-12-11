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
    global $smarty, $tables;    
/*
    if (!$request_prepared['id']) {
        return error('Invalid instance ID'); // return Error instance
    }
*/    
    /*
     * Do actions with object here
     */

    $curr_site_id = &cw_session_register('curr_site_id',0);
    global $_site_id;
    if ($_site_id)
        $curr_site_id = $_site_id;   

    if (!$curr_site_id)
        $curr_site_id = cw_query_first_cell("SELECT siteid FROM $tables[datascraper_sites_config] ORDER BY name LIMIT 1");   
    $smarty->assign('curr_site_id', $curr_site_id);  

    $results_tbl_fields = cw_call('cw\\'.addon_name.'\\cw_datascraper_get_table_fields', array('result_values_'.$curr_site_id));
    $smarty->assign('results_tbl_fields', $results_tbl_fields);

    $curr_site_results_count = cw_query_first_cell("SELECT COUNT(*) FROM $tables[datascraper_result_values]$curr_site_id"); 
    $smarty->assign('curr_site_results_count', $curr_site_results_count);

    $ds_sites = cw_query_hash("SELECT * FROM $tables[datascraper_sites_config] ORDER BY name", 'siteid', 0,0);
    $smarty->assign('ds_sites', $ds_sites);

    $smarty->assign('main', 'datascraper_results');   
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
