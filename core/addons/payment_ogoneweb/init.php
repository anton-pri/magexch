<?php
/*
 * Vendor: cw
 * addon: payment_ogoneweb
 */

/*
 * init.php
 * this file only defines constants, variables, functinos, hooks and event hanlers
 * no real routine must be here on init stage
 */

// Use namespace for your own addon as vendor\addon_name
namespace cw\payment_ogoneweb;

define('OGONE_DBG', 1);

// Constants definition
// these constants are defined in scope of addon's namespace
const addon_name    = 'payment_ogoneweb';       
const addon_target  = 'ogoneweb'; // Main target of addon

// Include functions
cw_include('addons/'.addon_name.'/include/func.php');

/* Place comment here with description of functionality provided by this hook or how it alters default function */
cw_addons_set_hooks(
    array('post', 'cw_payment_get_methods', 'cw\\'.addon_name.'\\cw_payment_get_methods'),
    array('post', 'cw_payment_run_processor', 'cw\\'.addon_name.'\\cw_payment_run_processor')
);

if (APP_AREA == 'customer') {

    /* Callbacks */
    cw_set_controller(APP_AREA.'/ogoneweb.php', 'addons/'.addon_name.'/customer/ogoneweb.php', EVENT_REPLACE);

}
