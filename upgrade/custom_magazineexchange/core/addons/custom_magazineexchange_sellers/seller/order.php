<?php
namespace cw\custom_magazineexchange_sellers;

// use $target, $mode and $action params to define subject and action to call
// e.g. $target_$mode_$action or $target_$mode or $target_$action
$action_function = join('_',array_filter(array($target,$mode,$action)));

// Default action
if (empty($action_function) || !function_exists('cw\\'.addon_name.'\\'.$action_function)) {
    docs_O_access_denied();
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

/* action = status_change */
function docs_O_status_change() {
    global $REQUEST_METHOD, $request_prepared, $status;
   
    cw_load('doc');

    if (APP_AREA != 'seller' || $REQUEST_METHOD!='POST') return null;

    $request_prepared['status'] = $status = '';

    $doc_id = $request_prepared['doc_id'];

    $doc_data = cw_call('cw_doc_get', array($request_prepared['doc_id'], 0));

/*print('<pre>');print_r(array($REQUEST_METHOD, $request_prepared, $status, $doc_data));print('</pre>');die;*/

    if ($doc_data['status'] != 'S' && $request_prepared['make_despatch'] == 'Y') { 
        cw_call('cw_doc_change_status', array($doc_id, 'S'));  
        cw_add_top_message('Order #'.$doc_data['display_id'].' has been despatched', 'I');  
        cw_header_location('index.php?target=docs_O&mode=details&doc_id='.$doc_data['doc_id']); 
    }
    
    if (!empty($doc_data['info']['tracking'])) $request_prepared['tracking'] = $doc_data['info']['tracking']; // Seller can't change tracking
 
    if ($request_prepared['tracking'] != '' && 
        $doc_data['info']['tracking']=='' &&
        in_array($doc_data['status'], array('P','Q'))
    ) {
       cw_call('cw_doc_change_status', array($doc_id, 'C'));
    }
    
}

function docs_O() {

    global $current_area, $allowed_seller_display_order_statuses, $doc_id;

    $doc_data = cw_call('cw_doc_get', array($doc_id, 0));

    if ($current_area == 'V') {
        if (!in_array($doc_data['status'], $allowed_seller_display_order_statuses))
            docs_O_access_denied();
    } 

    global $REQUEST_METHOD;
    if ($REQUEST_METHOD != 'GET') docs_O_access_denied();
}
function docs_O_details() {
    return docs_O();
}
function docs_O_print() {
//    return docs_O();
    global $smarty;
    $smarty->assign('is_email_invoice', 'Y');
}

 /* Service function */
function docs_O_access_denied() {
    cw_header_location("index.php?target=error_message&error=access_denied&id=40");
}

function docs_O_print_label() {
}
