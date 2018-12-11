<?php
/*
 * Vendor: CW
 * addon: SagePay Go - Server protocol
 */

const sagepaygo_server_addon_name 			= 'payment_sagepay_server';
const sagepaygo_server_addon_target 		= 'sagepaygo_server';
const sagepaygo_server_addon_version 		= '0.1';
const sagepaygo_server_addon_partner_id	= '';	//If you are a Sage Pay Partner and wish to flag the transactions with your unique partner id set it here.

if (!empty($addons[sagepaygo_server_addon_name])) {
	cw_include('addons/' . sagepaygo_server_addon_name . '/include/func.php');
	
	cw_addons_set_controllers(
	    array(
	    	'replace', 
	    	'customer/' . sagepaygo_server_addon_target . '.php', 
	    	'addons/' . sagepaygo_server_addon_name . '/customer/' . sagepaygo_server_addon_target . '.php'
	    )
	);
	
	cw_addons_set_hooks(
	    array('post', 'cw_payment_get_methods', 	'cw_payment_sagepaygo_server_get_methods'),
	    array('post', 'cw_payment_run_processor', 'cw_payment_sagepaygo_server_run_processor')
	);
}
