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
    global $request_prepared, $smarty, $config;

    $fees = $config['custom_magazineexchange_sellers']['mag_seller_fees'];

    $memberships = $smarty->get_template_vars('memberships');
    foreach ($memberships['V'] as $k=>$v) {
        $memberships['V'][$k]['fees'] = $fees[$v['membership_id']];
    }
    
    $smarty->assign('memberships', $memberships);

    return $memberships;
}

function seller_fees() {
    global $tables, $posted_data;
        
    db_query("UPDATE $tables[config] SET value ='".serialize($posted_data)."' WHERE name='mag_seller_fees' AND type=''");
    cw_header_location("index.php?target=memberships");
}
