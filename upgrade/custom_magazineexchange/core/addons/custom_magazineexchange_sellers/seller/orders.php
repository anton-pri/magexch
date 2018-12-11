<?php
namespace cw\custom_magazineexchange_sellers;

// use $mode and $action params to define subject and action to call

//$action_function = $action;
$action_function = $mode.(!empty($action)?'_'.$action:'');
// Default action
if (empty($action_function) || !function_exists('cw\\'.addon_name.'\\'.$action_function)) {
    return false;
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

/* !!! ATTENTION !!! */
/* THIS CONTROLLER IS CALLED FOR BOTH SELLER AND ADMIN AREAS. CHECK ALL CHANGES IN BOTH AREAS. */

/* Actions */
function search() {
    global $smarty, $orders;
    
    if (!empty($orders)) {

        $total_owed = $total_paid_out = $total_owed_remain = 0;

        foreach ($orders as $k => $v) {
            $orders[$k]['owed'] = cw_call('cw\custom_magazineexchange_sellers\mag_order_owed', array($v['doc_id']));
            $total_owed += $orders[$k]['owed'];
            if ($v['status']==MAG_PAIDOUT_ORDER_STATUS) {
                $total_paid_out += $orders[$k]['owed'];
            }
        }
        
        $total_owed_remain = $total_owed - $total_paid_out;
        
        $smarty->assign('orders', $orders);
        $smarty->assign('total_owed', $total_owed);
        $smarty->assign('total_paid_out', $total_paid_out);
        $smarty->assign('total_owed_remain', $total_owed_remain);
    }
    
}

