<?php
namespace cw\product_stages;

// use $mode and $action params to define subject and action to call

$action_function = $action;
// $action_function = $mode.'_'.$action;

// Default action
if (empty($action_function) || !function_exists('cw\\'.addon_name.'\\'.$action_function)) {
	$action_function = 'view';
}

// Call action
cw_call('cw\\'.addon_name.'\\'.$action_function);


/* Actions */

function view() {
    global $tables, $smarty;

    $library_stages = cw_query("select $tables[product_stages_library].* from $tables[product_stages_library] order by $tables[product_stages_library].pos");

    if (!empty($library_stages)) { 

        foreach ($library_stages as $ls_k => $ls_v) 
            if (!empty($ls_v['default_status']))
                $library_stages[$ls_k]['default_status'] = unserialize($ls_v['default_status']);

        $smarty->assign('library_stages', $library_stages);
    }
//    print("cw_product_stages_send_emails ".function_exists('cw\\'.addon_name.'\\cw_product_stages_send_emails'));
//    cw_call('cw\\'.addon_name.'\\cw_product_stages_send_emails'); 
    return $library_stages;
}

function modify() {
    global $posted_data, $REQUEST_METHOD;
    if ($REQUEST_METHOD == "POST") {
        foreach ($posted_data as $lib_id => $stage_data) {
            if (!empty($stage_data['default_status']) && is_array($stage_data['default_status'])) {
                $stage_data['default_status'] = serialize($stage_data['default_status']);
            }
            cw_array2update('product_stages_library', $stage_data, "stage_lib_id='$lib_id'"); 
        }
        cw_header_location("index.php?target=product_stages");       
    }
}

function add() {
    global $added_data, $REQUEST_METHOD;
    if ($REQUEST_METHOD == "POST") {
        $added_data['default_status'] = serialize($added_data['default_status']);
        cw_array2insert('product_stages_library', $added_data);
        cw_header_location("index.php?target=product_stages");
    }
}

function delete() {
    global $posted_data, $REQUEST_METHOD, $tables;
    if ($REQUEST_METHOD == "POST") {
        foreach ($posted_data as $lib_id => $stage_data) {
            if (!$stage_data['deleted']) continue;
            db_query("delete from $tables[product_stages_library] where stage_lib_id='$lib_id'");
            db_query("delete from $tables[product_stages_product_settings] where stage_lib_id='$lib_id'");
        }   
        cw_header_location("index.php?target=product_stages");
    }
}

$smarty->assign('main', 'product_stages');
