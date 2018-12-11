<?php
namespace cw\product_stages;

// use $mode and $action params to define subject and action to call

$action_function = $action;

// Default action
if (empty($action_function) || !function_exists('cw\\'.addon_name.'\\'.$action_function)) {
    $action_function = 'product_stages_view';
}

// Call action
cw_call('cw\\'.addon_name.'\\'.$action_function);


function product_stages_view() {
    global $smarty, $product_id, $tables;

    $product_stages = cw_call('cw\\'.addon_name.'\\cw_product_stages_get_product_settings', array($product_id));

    $lib_stages = cw_query("select * from $tables[product_stages_library] order by title");

    $smarty->assign('product_stages', $product_stages);
    $smarty->assign('lib_stages', $lib_stages);
}

function product_stages_modify() {
    global $posted_data, $default_status, $REQUEST_METHOD, $product_id;

    if ($REQUEST_METHOD == "POST") {
        foreach ($posted_data as $setting_id => $stage_data) {
            $update_stage_data = array(
                'period' => $stage_data['period'],
                'active' => (!empty($stage_data['active']))?1:0
            );
            if ($default_status[$setting_id]) {
                $update_stage_data['status'] = -1;
            } else {
                $update_stage_data['status'] = serialize($stage_data['status']);
            }

            cw_array2update('product_stages_product_settings', $update_stage_data, "setting_id='$setting_id'");
        }
        cw_header_location("index.php?target=products&mode=details&product_id=$product_id&js_tab=product_stages");
    }
}

function product_stages_delete() {
    global $to_delete, $REQUEST_METHOD, $product_id, $tables;
    if ($REQUEST_METHOD == "POST") {
        if (!empty($to_delete)) {
            $delete_ids = array_keys($to_delete);
            db_query("delete from $tables[product_stages_product_settings] where product_id = '$product_id' and setting_id in ('".implode("','", $delete_ids)."')"); 
        } 
        cw_header_location("index.php?target=products&mode=details&product_id=$product_id&js_tab=product_stages");
    }
}

function product_stages_add() {
    global $new_product_stage, $REQUEST_METHOD, $product_id, $default_status;
    if ($REQUEST_METHOD == "POST") {
        $new_stage_data = array(
            'product_id' => $product_id,
            'stage_lib_id' => $new_product_stage['stage_lib_id'],
            'period' => $new_product_stage['period'],
            'status' => (!empty($default_status['new_product_stage']))?'-1':((!empty($new_product_stage['status']))?serialize($new_product_stage['status']):''),
            'active' => (!empty($new_product_stage['active']))?1:0
        );
        cw_array2insert('product_stages_product_settings', $new_stage_data);
        cw_header_location("index.php?target=products&mode=details&product_id=$product_id&js_tab=product_stages");
    }
}

