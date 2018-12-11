<?php
namespace cw\custom_magazineexchange_sellers;

// use $mode and $action params to define subject and action to call

//$action_function = $action;
$action_function = $mode.(!empty($action)?'_'.$action:'').'_pre';
// Default action
if (empty($action_function) || !function_exists('cw\\'.addon_name.'\\'.$action_function)) {
    //return false;
    cw_header_location('index.php?target=docs_O&mode=search');
}

// Call action
$action_result = cw_call('cw\\'.addon_name.'\\'.$action_function);

// Action can return instance of Error via error() function
// see docs/core.error.txt
if (is_error($action_result)) {
    cw_add_top_message($action_result->getMessage(), 'E');
}

return $action_result;

function search_pre() {
    global $search_data, $current_area, $allowed_seller_display_order_statuses;

    if ($current_area != 'V') return;
 
    if (!empty($search_data['orders']['O']['basic']['status'])) {
        $search_data['orders']['O']['basic']['status'] = array_intersect($allowed_seller_display_order_statuses, $search_data['orders']['O']['basic']['status']);
        $search_data['orders']['O']['search_sections']['tab_search_orders'] = 1;
    }    

    if (empty($search_data['orders']['O']['basic']['status'])) {
        $search_data['orders']['O']['basic']['status'] = $allowed_seller_display_order_statuses;
        $search_data['orders']['O']['search_sections']['tab_search_orders'] = 1;    
    }

}
