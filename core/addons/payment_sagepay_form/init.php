<?php
/*
 * Vendor: CW
 * addon: SagePay Go - Form protocol
 */

const sagepaygo_form_addon_name 		= 'payment_sagepay_form';
const sagepaygo_form_addon_target 		= 'sagepaygo_form';
const sagepaygo_form_addon_version 	= '0.1';
const sagepaygo_form_addon_partner_id	= '';	//If you are a Sage Pay Partner and wish to flag the transactions with your unique partner id set it here.

if (!empty($addons[sagepaygo_form_addon_name])) {
	cw_include('addons/' . sagepaygo_form_addon_name . '/include/func.php');
	
	cw_addons_set_controllers(
	    array(
	    	'replace', 
	    	'customer/' . sagepaygo_form_addon_target . '.php', 
	    	'addons/' . sagepaygo_form_addon_name . '/customer/' . sagepaygo_form_addon_target . '.php'
	    )
	);
	
	cw_addons_set_hooks(
	    array('post', 'cw_payment_get_methods', 	'cw_payment_sagepaygo_form_get_methods'),
	    array('post', 'cw_payment_run_processor', 'cw_payment_sagepaygo_form_run_processor')
	);
}
