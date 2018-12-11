<?php
namespace cw\clickatell_sms;
use Clickatell\Rest;
use Clickatell\ClickatellException;

/** =============================
 ** Addon functions, API
 ** =============================
 **/

/**
 * Add message to spool
 * 
 * @return int $new_sms_id
 */
function sms_spool_add($content, $phone, $doc_id, $doc_status) {
    $new_sms_id = cw_array2insert('sms_spool', array('sms_to'=>$phone, 'body'=>$content, 'date_sent'=>0, 'date_added'=>time(), 'doc_id'=>$doc_id, 'doc_status'=>$doc_status));
    return $new_sms_id;
} 

/**
 * Get either phone or custom field from order data
 * 
 * @return string $phone
 */
function sms_phone_define($doc_data) {
    global $config, $tables;

    if (!empty($config[addon_name]['sms_custom_field'])) { 
        if (strpos($config[addon_name]['sms_custom_field'],'.') === false) {
            $section = 'main_address';
            $field = $config[addon_name]['sms_custom_field'];
        } else {
            list($section,$field) = explode('.',$config[addon_name]['sms_custom_field']);
        }
    } else {
            $section = 'main_address';
            $field = 'phone';
    }

    if (isset($doc_data['userinfo'][$section][$field])) {
        $return = $doc_data['userinfo'][$section][$field];
    } else {
        $field_id = cw_query_first_cell("select field_id from $tables[register_fields] where field='".$config[addon_name]['sms_custom_field']."'");
        $return = $doc_data['userinfo'][$section]['custom_fields'][$field_id];
    }

    return $return;
}

function sms_phone_define_seller($doc_data) {

//    cw_log_add(__FUNCTION__, $doc_data['info']);

    global $config, $tables;

    if ($doc_data['info']['warehouse_customer_id']) {
        $seller_address_data = cw_call('cw_user_get_info', array($doc_data['info']['warehouse_customer_id'], (2048+1024+1)));

        if (!empty($config[addon_name]['sms_custom_field'])) {
            if (strpos($config[addon_name]['sms_custom_field'],'.') === false) {
                $section = 'main_address';
                $field = $config[addon_name]['sms_custom_field'];
            } else {
                list($section,$field) = explode('.',$config[addon_name]['sms_custom_field']);
            }
        } else {
            $section = 'main_address';
            $field = 'phone';
        }  

        if (isset($seller_address_data[$section][$field])) {
            $return = $seller_address_data[$section][$field];
        }
        cw_log_add(__FUNCTION__, array($seller_address_data, $section, $field, $return));
 
    }

    if (!empty($config[addon_name]['sms_debug_phone_number'])) 
        $return = $config[addon_name]['sms_debug_phone_number'];

    return $return;
}

/**
 * Send collected messages from spool
 * 
 * @return array $log - log of process
 * 
 */
function sms_spool_send () {
    global $config, $tables;

    $start_time = $end_time = cw_core_get_time();
    $log = array();

    if ($config[addon_name]['sms_pause'] == "Y") return array("Warning: SMS sending is paused in addon settings");
    
    while ($end_time - $start_time < constant('SMS_SPOOL_TIMEOUT')) {

        $sms = cw_query_first("select * from $tables[sms_spool] where 
            date_send<=$start_time AND (date_send-date_added)<=".($config[addon_name]['sms_lifetime']*constant('SECONDS_PER_DAY'))."
            limit 1");
        
        if (empty($sms)) break;
        
        $result = cw_call('cw\\'.addon_name.'\sms_spool_send_one', array($sms['sms_id']));
        
        if (!is_error($result) && !empty($result)) {
            $log[] = "Ok {$sms['sms_id']}: {$sms['sms_to']} - {$sms['sms_body']}";
        } elseif (is_error($result)) {
            $log[] = "Error {$sms['sms_id']}: {$sms['sms_to']} - {$sms['sms_body']}\n".$result->getMessage();
        }
    
        $end_time = cw_core_get_time();
    }
        
    return $log;
}

/**
 * Send one message from spool
 * 
 * @param int $sms_id
 * 
 * @return string apiMessageId - clickatell api call ID
 * @return object CW_Error
 * 
 */
function sms_spool_send_one($sms_id) {
    global $tables, $config;

    if ($config[addon_name]['sms_pause'] == "Y") return error("SMS sending is paused in addon settings");
    
    $sms_id = intval($sms_id);
    $sms = cw_query_first("select * from $tables[sms_spool] where sms_id='$sms_id' limit 1");
    
    if (empty($sms)) return error('Invalid sms id');
    
    if (!empty($sms['sms_to']) && !empty($sms['body'])) {
        
        $result = cw_call('cw\\'.addon_name.'\\sms_clickatell_send', array($sms['body'], $sms['sms_to']));
        
        if (is_error($result)) {
            $postpone = constant('CURRENT_TIME')+60*60; // Postpone to 60 min
            $error_msg = db_escape_string($result->getMessage());
            db_query("update $tables[sms_spool] set date_send=$postpone where sms_id='$sms_id'");
            db_query("update $tables[sms_spool] set error='$error_msg'  where sms_id='$sms_id'");            
            cw_log_add('sms_error', "Error: can't send sms \n#$sms_id: {$sms['sms_to']} - {$sms['body']}\nError message: $error_msg\n [Postponed].", false);  
        } else {
            db_query("delete from $tables[sms_spool] where sms_id='$sms[sms_id]'");
        }
    } else {
        // Delete empty SMS
        cw_log_add('sms_error', "Error: can't send sms \n#$sms_id: {$sms['sms_to']} - {$sms['body']}\nError message: phone or body empty\n [Deleted].", false);  
        db_query("delete from $tables[sms_spool] where sms_id='$sms[sms_id]'");
    }
    
    return $result;
}

/**
 * Actually send email via Clickatell API
 * 
 * @return string apiMessageId - clickatell api call ID
 * @return object CW_Error
 */
function sms_clickatell_send($content, $phone) {
    cw_log_add('sms_messages', array('to'=>$phone,'body'=>$content),false);

    //return rand(1,10000); // emulation of response

    global $config;
    
    cw_include_once('addons/'.addon_name.'/include/clickatell/src/Rest.php');
    cw_include_once('addons/'.addon_name.'/include/clickatell/src/ClickatellException.php');
    
    if (empty($config[addon_name]['sms_api_key'])) return error("Clickatell API key is empty");
    
    $clickatell = new \Clickatell\Rest($config[addon_name]['sms_api_key']);

    // Full list of support parameters can be found at https://www.clickatell.com/developers/api-documentation/rest-api-request-parameters/
    try {
        $messageData = array('to' => [$phone], 'content' => $content);
        if (!empty($config[addon_name]['sms_from_number'])) {
            $messageData['from'] = $config[addon_name]['sms_from_number']; // use 2-way integration
        }
        $result = $clickatell->sendMessage($messageData);

        // $result - array of results for each phone
        
        cw_log_add('sms_debug', $result, false);

        $message = reset($result);

        if ($message['errorCode']) {
            global $smarty;
            $smarty->assign(array('result'=>$message, 'content'=>$content, 'phone'=>$phone));
            cw_call('cw_send_mail', array($config['Company']['site_administrator'], $config['Company']['site_administrator'],
                    'addons/clickatell_sms/mail/sms_error_subj.tpl', 'addons/clickatell_sms/mail/sms_error.tpl', $config['default_admin_language'], true));
        }
 
        /*
        [
            'apiMessageId'  => null|string,
            'accepted'  => boolean,
            'to'        => string,
            'error'     => null|string
        ]
        */
       
        if (empty($message['error']) && $message['accepted']) {
            return $message['apiMessageId'];
        } else {
            return error($message['error']);
        }

    } catch (ClickatellException $e) {
        // Any API call error will be thrown and should be handled appropriately.
        // The API does not return error codes, so it's best to rely on error descriptions.
        
        $warn_msg = "Clickatell SMS: <a href='index.php?target=clickatell_sms&amp;mode=spool'>".$e->getMessage()."</a>";
        cw_call('cw_system_messages_add',array(addon_name, $warn_msg, SYSTEM_MESSAGE_COMMON, SYSTEM_MESSAGE_ERROR));

        return error($e->getMessage());
    }

}

/**
 * Prepare content of SMS for certain doc data
 * 
 * @return string $content - compiled order status template
 */
function sms_content_prepare($doc_data, $new_status, $template_text) {
    global $smarty;
    global $current_language;
    $language = $language ? $language : $current_language;

    $sms_content_vars = array('doc'=>$doc_data, 'info'=>$doc_data['info'], 'userinfo'=>$doc_data['userinfo'], 'products'=>$doc_data['products'], 'status'=>$new_status, 'content'=>$template_text);

    $smarty->assign($sms_content_vars);
/*
    $smarty->assign('doc', $doc_data);
    $smarty->assign('info', $doc_data['info']);
    $smarty->assign('userinfo', $doc_data['userinfo']);
    $smarty->assign('products', $doc_data['products']);
    $smarty->assign('status', $new_status);
    $smarty->assign('content', $template_text); 
*/
//    cw_log_add(__FUNCTION__, $sms_content_vars);
 
    $template = "addons/".addon_name."/sms_message_body.tpl"; 

    $result = cw_display($template, $smarty, false, $language);
    //cw_log_add('sms_messages', array('sms_content_prepare'=>array($doc_data, $new_status, $template_text)));

    return $result;
}


/** =============================
 ** Hooks
 ** =============================
 **/



/** =============================
 ** Events handlers
 ** =============================
 **/

function on_doc_change_status($doc_data, $new_status) {

    global $tables, $edited_language;

    $order_status = cw_query_first("select sms_customer, sms_message from $tables[order_statuses] where code='$new_status'");
   
    $result = 0;

    if (empty($order_status))  return error("Wrong order status $new_status",array('code'=>'SMS_BAD_STATUS')); 

//    cw_log_add('sms_messages',array('on_doc_change_status'=>array($order_status_id, $doc_data, $new_status)));

    $phone = cw_call('cw\\'.addon_name.'\\sms_phone_define',array($doc_data));

    if (empty($phone)) {
        return error("No phone to send sms to",array('code'=>'SMS_PHONE_EMPTY'));
    } 
//cw_var_dump($order_status);die();
    if ($order_status['sms_customer'] && !empty($order_status['sms_message'])) {

        $sms_content = cw_call('cw\\'.addon_name.'\\sms_content_prepare', array($doc_data, $new_status, $order_status['sms_message']));
        if (!empty($sms_content)) { 
            $result = cw_call('cw\\'.addon_name.'\\sms_spool_add', array($sms_content, $phone, $doc_data['doc_id'], $new_status));
        } else {
            $result = error("Sms message content is empty",array('code'=>'SMS_TEXT_EMPTY'));
        }
    } else {
        $result = error("No sms notification is defined for status ".$new_status,array('code'=>'SMS_DISABLED_STATUS'));
    }   
    return $result;
}

function on_doc_change_status_seller($doc_data, $new_status) {

    global $tables, $edited_language;

    $order_status = cw_query_first("select sms_seller, sms_seller_message from $tables[order_statuses] where code='$new_status'");

    $result = 0;

    if (empty($order_status))  return error("Wrong order status $new_status",array('code'=>'SMS_BAD_STATUS'));

    $phone = cw_call('cw\\'.addon_name.'\\sms_phone_define_seller',array($doc_data));

    if (empty($phone)) {
        return error("No phone to send sms to",array('code'=>'SMS_PHONE_EMPTY'));
    }

    if ($order_status['sms_seller'] && !empty($order_status['sms_seller_message'])) {

        $sms_content = cw_call('cw\\'.addon_name.'\\sms_content_prepare', array($doc_data, $new_status, $order_status['sms_seller_message']));
        if (!empty($sms_content)) {
            $result = cw_call('cw\\'.addon_name.'\\sms_spool_add', array($sms_content, $phone, $doc_data['doc_id'], $new_status));
        } else {
            $result = error("Sms message content is empty",array('code'=>'SMS_TEXT_EMPTY'));
        }
    } else {
        $result = error("No sms notification is defined for status ".$new_status,array('code'=>'SMS_DISABLED_STATUS'));
    }
    return $result;
}
