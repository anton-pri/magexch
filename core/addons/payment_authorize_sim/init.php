<?php
/*
 * Vendor: CW
 * addon: Authorize.net - SIM
 */

const authorize_sim_addon_name 			= 'payment_authorize_sim';
const authorize_sim_addon_target 			= 'authorize_sim';

cw_include('addons/' . authorize_sim_addon_name . '/include/anet_php_sdk/AuthorizeNet.php');
cw_include('addons/' . authorize_sim_addon_name . '/include/func.php');

cw_addons_set_controllers(
    array(
    	'replace', 
    	'customer/' . authorize_sim_addon_target . '.php', 
    	'addons/' . authorize_sim_addon_name . '/customer/' . authorize_sim_addon_target . '.php'
    )
);

cw_addons_set_hooks(
    array('post', 'cw_payment_get_methods', 	'cw_payment_authorize_sim_get_methods'),
    array('post', 'cw_payment_run_processor', 'cw_payment_authorize_sim_run_processor')
);
