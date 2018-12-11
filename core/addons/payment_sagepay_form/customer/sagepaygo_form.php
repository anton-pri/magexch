<?php
if (!isset($REQUEST_METHOD)) {
    $REQUEST_METHOD = $_SERVER['REQUEST_METHOD'];
}

$bill_output = array();
$addon_name = str_replace("-", "_", sagepaygo_form_addon_name);

if ($REQUEST_METHOD == 'GET' && isset($_GET['crypt'])) {
	global $config;

    $pass = $config[$addon_name]['spf_encryption_password'];
    $crypt = $_GET['crypt'];
    $response = array();

    parse_str(cw_payment_sagepaygo_form_simple_xor(cw_payment_sagepaygo_form_base64_decode($crypt), $pass), $response);

    $pp_shift = preg_replace("/[^\w\d_-]/S", '', $config[$addon_name]['spf_order_prefix']);
    $pp_shift = substr($pp_shift, 0, 8);
	$skey = str_replace($pp_shift, "", $response['VendorTxCode']);
    $data = cw_call('cw_payment_get_data', array($skey));
    $bill_output['sess_id'] = $data['session_id'];
    $bill_output['skey'] 	= $skey;

    if (trim($response['Status']) == "OK") {
        $bill_output['code'] = 1;
        $bill_output['billmes'] = "AuthNo: " . $response['TxAuthNo'] . ";\n";
    }
    else {
        $bill_output['code'] = 2;
        $bill_output['billmes'] = "Status: " . $response['StatusDetail'] . " (" . trim($response['Status']) . ")\n";
    }

    if (!empty($response['VPSTxId'])) {
        $bill_output['billmes'] .= " (TxID: " . trim($response['VPSTxId']) . ")\n";
    }

    if (!empty($response['AVSCV2'])) {
        $bill_output['billmes'] .= " (AVS/CVV2: {" . trim($response['AVSCV2']) . "})\n";
    }

    if (!empty($response['AddressResult'])) {
        $bill_output['billmes'] .= " (Address: {" . trim($response['AddressResult']) . "})\n";
    }

    if (!empty($response['PostCodeResult'])) {
        $bill_output['billmes'] .= " (PostCode: {" . trim($response['PostCodeResult']) . "})\n";
    }

    if (!empty($response['CV2Result'])) {
        $bill_output['billmes'] .= " (CV2: {" . trim($response['CV2Result']) . "})\n";
    }

    if (!empty($response['3DSecureStatus'])) {
        $bill_output['billmes'] .= " (3D Result: {" . trim($response['3DSecureStatus']) . "})\n";
    }

    if (isset($response['Amount'])) {
        $payment_return = array(
            'total' => str_replace(",", '', $response['Amount'])
        );
    }
}
else {
	$bill_output["billmes"] = "Wrong request method or empty data.";
	$bill_output['code'] 	= 2;
}

$return = cw_call('cw_payment_check_results', array($bill_output));
cw_call('cw_payment_stop', array($return));
