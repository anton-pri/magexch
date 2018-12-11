<?php
/*
 * Vendor: cw
 * addon: api_server
 * 
 * Base for implementation API Server via child addons
 */

/*
 * init.php
 * this file only defines constants, variables, functinos, hooks and event hanlers
 * no real routine must be here on init stage
 */

// Use namespace for your own addon as vendor\addon_name
namespace cw\api_server;

// Constants definition
// these constants are defined in scope of addon's namespace
const addon_name    = 'api_server';       
const addon_target  = 'api'; // Main target of addon, useful but of course addon can handle several targets

// Common functions which allow to work with another same API Server
cw_include('addons/'.addon_name.'/include/cw.api_server.php');



// ALL FOLLOWING CODE FOR API SERVER ONLY

if ($target != addon_target) return false;

// Include functions
cw_include('addons/'.addon_name.'/include/func.php');



/** FEATURES **/

/**
 * FEATURE api: main api server listener
 */
if (is_feature('api')) {

    if (APP_AREA == 'customer') {
        /* API server can be called via URL http://domain.com/cw_dir/index.php?target=api */
         cw_set_controller(APP_AREA.'/'.addon_target.'.php','addons/'.addon_name.'/api/'.addon_target.'.php', EVENT_REPLACE);
    }

} // FEATURE api.


/**
 * FEATURE echo_api: implementation of test echo api
 */
if (is_feature('echo_api')) {

    cw_include('addons/'.addon_name.'/include/func.echo_api.php');
    cw_set_hook('cw\api_server\api_method_exec_echo',   'cw\api_server\echo_api\api_method_exec',   EVENT_REPLACE);
    cw_set_hook('cw\api_server\api_is_key_valid',       'cw\api_server\echo_api\api_is_key_valid',  EVENT_POST);
    cw_set_hook('cw\api_server\api_secret_get',         'cw\api_server\echo_api\api_secret_get',    EVENT_POST);


} // FEATURE echo_api.


/** SERVICE FUNCTION **/
// List of hardcoded enabled features
function is_feature($feature) {
    $features = array(
        'api' => 1,
        'echo_api' => 1,
    );

    return $features[$feature];
}

