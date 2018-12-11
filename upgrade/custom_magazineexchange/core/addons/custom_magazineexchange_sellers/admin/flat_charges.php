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
    global $smarty, $config, $tables, $membership_id;

    $all_flat_charges = $config['custom_magazineexchange_sellers']['mag_seller_flat_charges'];
     
    $flat_charges = array();
    if (!empty($all_flat_charges)) {
        $all_flat_charges = unserialize($all_flat_charges);

        if (is_array($all_flat_charges))
            if (isset($all_flat_charges[$membership_id]))
                $flat_charges = $all_flat_charges[$membership_id];  
    }

    $smarty->assign('flat_charges', $flat_charges);       
    $smarty->assign('membership', $membership = cw_query_first("select * from $tables[memberships] where membership_id = '$membership_id' and area='V'"));
    $smarty->assign('max_flat_charge_id', count($flat_charges));
    $smarty->assign('main', 'magexch_flat_charges');

    return $membership;
}

function update_flat_charges() {
    global $config, $tables, $posted_data, $seller_membership_id;

    $all_flat_charges = $config['custom_magazineexchange_sellers']['mag_seller_flat_charges']; 

    $new_charge_id = end(array_keys($posted_data)); 
    if ($posted_data[$new_charge_id]['range_to']<=0 && $posted_data[$new_charge_id]['range_from']<=0)
        unset($posted_data[$new_charge_id]); 

    if (!empty($all_flat_charges)) {
        $all_flat_charges = unserialize($all_flat_charges);
        $all_flat_charges[$seller_membership_id] = $posted_data; 
    } else {
        $all_flat_charges = array($seller_membership_id=>$posted_data);
    }

    db_query("UPDATE $tables[config] SET value ='".serialize($all_flat_charges)."' WHERE name='mag_seller_flat_charges' AND type=''");

    if (count($posted_data)) {
        if (count($posted_data) == 1)  
            cw_add_top_message('1 entry has been added or updated successfully', 'I');
        else
            cw_add_top_message(count($posted_data).' entries have been added or updated successfully', 'I');
    } else
        cw_add_top_message('No entries are added or updated. The price range limits must exceed 0.00', 'E');  

    cw_header_location("index.php?target=magexch_flat_charges&membership_id=$seller_membership_id");
}

function delete_flat_charges() {
    global $config, $tables, $seller_membership_id, $del;

    if (!empty($del)) {
        $all_flat_charges = $config['custom_magazineexchange_sellers']['mag_seller_flat_charges'];

        if (!empty($all_flat_charges)) {
            $all_flat_charges = unserialize($all_flat_charges);
            if (isset($all_flat_charges[$seller_membership_id])) {
                foreach ($del as $delete_id=>$one) {
                    if (isset($all_flat_charges[$seller_membership_id][$delete_id]))
                        unset($all_flat_charges[$seller_membership_id][$delete_id]);
                }
            }
            db_query("UPDATE $tables[config] SET value ='".serialize($all_flat_charges)."' WHERE name='mag_seller_flat_charges' AND type=''");
        }
        if (count($del)==1) 
            cw_add_top_message('1 entry has been deleted successfully', 'I');
        else
            cw_add_top_message(count($del).' entries have been deleted successfully', 'I');
    } else {
        cw_add_top_message('No entries are selected for delete', 'E');
    }

    cw_header_location("index.php?target=magexch_flat_charges&membership_id=$seller_membership_id");
}

