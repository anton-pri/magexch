<?php
namespace cw\clickatell_sms;


/* Actions */

function clickatell_sms_order_send() {
    global $request_prepared, $HTTP_REFERER;
    
    cw_load('doc');
    
    $doc_id = intval($request_prepared['doc_id']);
    
    $doc_data = cw_call('cw_doc_get', array($doc_id, 8192));
    
    $doc_id = $doc_data['doc_id'];
    
    if (!$doc_id) {
        return error('Invalid order ID', array('redirect'=>'index.php?target=docs_O')); // return Error instance
    }
    
    $sms_id = cw_call('cw\\'.addon_name.'\\on_doc_change_status', array($doc_data, $doc_data['status']));
    
    if (is_error($sms_id)) {
        
        $sms_id->setAdditionalParams(array('redirect'=>'index.php?target=docs_O&mode=details&doc_id='.$doc_id));
        return $sms_id;
        
    }
    
    $send_result = cw_call('cw\\'.addon_name.'\\sms_spool_send_one', array($sms_id)); // Send immediately
    
    if (is_error($send_result)) {
        
        $send_result->setAdditionalParams(array('redirect'=>'index.php?target=docs_O&mode=details&doc_id='.$doc_id));
        return $send_result;
        
    }
   
    cw_add_top_message('SMS has been sent successfully');
   
    return error('',array('redirect'=>'index.php?target=docs_O&mode=details&doc_id='.$doc_id));
}


function clickatell_sms_spool() {
    global $tables, $smarty;

    $messages = cw_query("SELECT * FROM $tables[sms_spool] ORDER BY sms_id");
    
    $smarty->assign_by_ref('messages', $messages);
    
}

function clickatell_sms_spool_clean() {
    global $tables, $request_prepared;
    
    $where = "1";
    if ($request_prepared['type'] == 'expired') {
        $where = "date_send > 0 AND date_send < ".constant('CURRENT_TIME');
    }
    db_query("DELETE FROM $tables[sms_spool] WHERE $where");
    
    cw_add_top_message("SMS queue has been cleaned");
    
    return error('',array('redirect'=>'index.php?target='.addon_target.'&mode=spool'));
}

function clickatell_sms_spool_send() {
    global $tables, $request_prepared, $config;

    $sms_id = intval($request_prepared['sms_id']);
    
    if (!$sms_id) return error("SMS entry #$sms_id not found");
    
    $config[addon_name]['sms_pause'] = ''; // Force sending
    
    $send_result = cw_call('cw\\'.addon_name.'\\sms_spool_send_one', array($sms_id)); // Send immediately
    
    if (is_error($send_result)) {
        $send_result->setAdditionalParams(array('redirect'=>'index.php?target='.addon_target.'&mode=spool'));
        return $send_result;
    } 

    cw_add_top_message('SMS has been sent successfully.');

    cw_ajax_add_block(array(
        'id' => 'sms_'.$sms_id,
        'action' => 'remove',
    ));
    
    
}

function clickatell_sms_spool_delete() {
    global $tables, $request_prepared;

    $sms_id = intval($request_prepared['sms_id']);
    
    if (!$sms_id) return error("SMS entry #$sms_id not found");
    
    db_query("DELETE FROM $tables[sms_spool] WHERE sms_id='$sms_id'");
    
    cw_ajax_add_block(array(
        'id' => 'sms_'.$sms_id,
        'action' => 'remove',
    ));
    
    cw_add_top_message("SMS entry #$sms_id has been deleted");
    
}
/* Service functions */

