<?php
if (!isset($REQUEST_METHOD)) {
    $REQUEST_METHOD = $_SERVER['REQUEST_METHOD'];
}

$bill_output = array();
$addon_name = str_replace("-", "_", authorize_sim_addon_name);

if ($REQUEST_METHOD == 'POST' && count($_POST)) {	
	global $config;

	define("AUTHORIZENET_API_LOGIN_ID", $config[$addon_name]['asim_api_login_id']);
	define("AUTHORIZENET_TRANSACTION_KEY", $config[$addon_name]['asim_transaction_key']);
	define("AUTHORIZENET_MD5_SETTING", $config[$addon_name]['asim_md5_hash']);

	$response = new AuthorizeNetSIM;
	// get key
	$skey = strtolower(md5(AUTHORIZENET_MD5_SETTING . AUTHORIZENET_API_LOGIN_ID . $response->email));
    $data = cw_call('cw_payment_get_data', array($skey));
    $bill_output['sess_id'] = $data['session_id'];
    $bill_output['skey'] 	= $skey;

	if ($response->isAuthorizeNet()) {

		if ($response->approved) {
			// Transaction approved!
			$bill_output['code'] 	= 1;
        	$bill_output['billmes'] = "";
		}
		else {
			// There was a problem.
			$status = array(
				1 => "Approved", 
				2 => "Declined", 
				3 => "Error", 
				4 => "Held for Review"
			);
			$bill_output['code'] 	= 2;
			$bill_output['billmes'] = "The overall status of the transaction: " . $status[$response->response_code];
			$bill_output['billmes'] .= "<br />Reason: " . $response->response_reason_text;
		}
	}
	else {
		$bill_output['code'] 	= 2;
		$bill_output['billmes'] = "MD5 Hash failed. Check to make sure your MD5 Setting matches the one in config";
    }
}
else {
	$bill_output["billmes"] = "Wrong request method or empty data.";
	$bill_output['code'] 	= 2;
}

$return = cw_call('cw_payment_check_results', array($bill_output));

$cart = &cw_session_register('cart', array());
$top_message = &cw_session_register('top_message');

if ($return['bill_error']) {
	$top_message 	= array('type' => 'E', 'content' => $return['bill_error'] . ' ' . $return['reason']);
	$request 		= $app_catalogs['customer'] . '/index.php?target=cart&mode=checkout';
}
else {
	$_doc_ids 	= cw_get_urlencoded_doc_ids($return['doc_ids']);
	$request 	= $current_location . "/index.php?target=order-message&doc_ids=" . $_doc_ids;
	$cart 		= array();
	cw_session_save();
}

echo AuthorizeNetDPM::getRelayResponseSnippet($request);

exit();
