<?php
/*
 * Vendor: cw
 * addon: clickatell_sms
 */

/*
 * init.php
 * this file only defines constants, variables, functinos, hooks and event hanlers
 * no real routine must be here on init stage
 */

// Use namespace for your own addon as vendor\addon_name
namespace cw\clickatell_sms;

// Constants definition
// these constants are defined in scope of addon's namespace
const addon_name    = 'clickatell_sms';       
const addon_target  = 'clickatell_sms'; // Main target of addon, useful but of course addon can handle several targets

// Time limit to send sms spool at once
define('SMS_SPOOL_TIMEOUT', 40); // sec

// New tables definition
$tables['sms_spool'] = 'cw_sms_spool';

// Include functions
cw_include_once('addons/'.addon_name.'/include/func.php');

/** FEATURES **/

/**
 * FEATURE order_notification: Notify customer when order status changed
 */
if (is_feature('order_notification')) {

    // You can define different hooks depending on area or $target or use common init sequence. 
    if (APP_AREA == 'admin') {
        
        // Resend SMS
        cw_set_controller(APP_AREA.'/'.addon_target.'.php','addons/'.addon_name.'/admin/'.addon_target.'.php', EVENT_REPLACE);
        
        // Order statuses edit page
        cw_addons_set_template(
//            array('pre', 'admin/attributes/object_modify.tpl', 'addons/'.addon_name.'/admin/order_statuses.tpl','admin/main/order_statuses.tpl')
            array('post', 'admin/main/order_statuses.tpl@order_statuses_edit', 'addons/' . addon_name . '/admin/order_statuses.tpl')
        ); 
        
        cw_addons_set_template(
            array('post', 'main/docs/additional_actions.tpl', 'addons/'.addon_name.'/admin/doc_actions.tpl')
        );        
    }

    if (APP_AREA == 'customer') {
        // Nothing
    }

    // Event handlers
    // Add SMS to queue when status changed
    cw_event_listen('on_doc_change_status', 'cw\\'.addon_name.'\\on_doc_change_status');
    cw_event_listen('on_doc_change_status', 'cw\\'.addon_name.'\\on_doc_change_status_seller');

    // Cron handlers. See docs/core.cron.txt and core/cron/cron.php
    // Send SMS from queue via Clickatell
    cw_event_listen('on_cron_regular', 'cw\\'.addon_name.'\\sms_spool_send');

} // FEATURE order_notification.

/** SERVICE FUNCTION **/
// List of hardcoded enabled features
function is_feature($feature) {
    $features = array(
        'order_notification' => 1,
    );

    return $features[$feature];
}

