<?php
/*
 * Vendor: cw
 * addon: product_stages
 */

/*
 * init.php
 * this file only defines constants, variables, functinos, hooks and event hanlers
 * no real routine must be here on init stage
 */

// Use namespace for your own addon as vendor\addon_name
namespace cw\product_stages;

// Constants definition
// these constants are defined in scope of addon's namespace
const addon_name    = 'product_stages';       
const addon_target  = 'product_stages'; // Main target of addon, useful but of course addon can handle several targets

$cw_allowed_tunnels[] = 'cw\\'.addon_name.'\\cw_product_stages_get_doc_stages_history';

// New tables definition
$tables['product_stages_library'] = 'cw_product_stages_library';
$tables['product_stages_product_settings'] = 'cw_product_stages_product_settings';
$tables['docs_statuses_log'] = 'cw_docs_statuses_log';
$tables['product_stages_process_log'] = 'cw_product_stages_process_log';

// Include functions
cw_include('addons/'.addon_name.'/include/func.php');

// You can define different hooks depending on area or $target or use common init sequence. 
if (APP_AREA == 'admin') {
    // Define own controller which does not exists yet using EVENT_REPLACE
     cw_set_controller(APP_AREA.'/'.addon_target.'.php', 'addons/'.addon_name.'/admin/'.addon_target.'.php', EVENT_REPLACE);
     cw_set_controller('include/products/modify.php', 'addons/'.addon_name.'/admin/include/products/modify.php', EVENT_POST);
     cw_set_controller('include/products/modify.php', 'addons/'.addon_name.'/admin/include/products/modify.php', EVENT_PRE);

     cw_set_controller(APP_AREA.'/stage_email_test.php', 'addons/'.addon_name.'/admin/stage_email_test.php', EVENT_REPLACE);
}

cw_addons_set_hooks(
    array('post', 'cw_tabs_js_abstract', 'cw\\'.addon_name.'\\cw_product_stages_tabs_js_abstract')
);

// Cron handlers. See docs/core.cron.txt and core/cron/cron.php
cw_event_listen('on_cron_daily','cw\\'.addon_name.'\\on_cron_daily');
cw_event_listen('on_doc_change_status', 'cw\\'.addon_name.'\\cw_product_stages_on_doc_change_status');

// Hook templates
cw_addons_set_template(
    array('replace','admin/main/'.addon_target.'.tpl', 'addons/'.addon_name.'/admin/'.addon_target.'.tpl')
);

cw_addons_set_template(
    array('replace','admin/main/stage_email_test.tpl', 'addons/'.addon_name.'/admin/stage_email_test.tpl')
);

