<?php
if (!isset($REQUEST_METHOD)) {
	$REQUEST_METHOD = $_SERVER['REQUEST_METHOD'];
}

$bill_output = array();
$addon_name = str_replace("-", "_", sagepaygo_server_addon_name);

if (
	$REQUEST_METHOD == 'POST' 
	&& $_POST['Status'] 
	&& $_POST['VendorTxCode']
) {
	global $config;

	$pp_shift = substr($config[$addon_name]['sps_order_prefix'], 0, 8);
	$skey = str_replace($pp_shift, "", $_POST['VendorTxCode']);
    $data = cw_call('cw_payment_get_data', array($skey));
    $bill_output['sess_id'] = $data['session_id'];
    $bill_output['skey'] 	= $skey;

    if(in_array($_POST['Status'], array('OK', 'OK REPEATED'))) {
        // Signature string
        // VPSTxId+VendorTxCode+Status+TxAuthNo+VendorName+AVSCV2+SecurityKey+AddressResult+PostCodeResult+CV2Result+GiftAid+3DSecureStatus+CAVV+AddressStatus+PayerStatus+CardType+ Last4Digits
        $sign_string = $_POST['VPSTxId']
        . $_POST['VendorTxCode']
        . $_POST['Status']
        . $_POST['TxAuthNo']
        . strtolower($config[$addon_name]['sps_vendor_name'])
        . $_POST['AVSCV2']
        . $skey
        . $_POST['AddressResult']
        . $_POST['PostCodeResult']
        . $_POST['CV2Result']
        . $_POST['GiftAid']
        . $_POST['3DSecureStatus']
        . $_POST['CAVV']
        . $_POST['AddressStatus']
        . $_POST['PayerStatus']
        . $_POST['CardType']
        . $_POST['Last4Digits'];

        $sign = strtoupper(md5($sign_string));

        $bill_output['code'] 	= $_POST['VPSSignature'] == $sign ? 1 : 3;
        $bill_output['billmes'] = ($_POST['VPSSignature'] == $sign ? '' : 'VPSSignature is incorrect! ') . 'AuthNo: ' . $_POST['TxAuthNo'];
    }
    else {
        $bill_output['code'] 	= 2;
        $bill_output['billmes'] = 'Status: ' . $_POST['StatusDetail'] . ' ('.$_POST['Status'].') ';
    }

    $arr = array(
        'TxID'              => 'VPSTxID',
        'AVS/CVV2'          => 'AVSCV2',
        'AddressResult'     => 'AddressResult',
        'PostCodeResult'    => 'PostCodeResult',
        'CV2Result'         => 'CV2Result',
        '3DSecureStatus'    => '3DSecureStatus',
        'CAVV'              => 'CAVV',
        'PayerStatus'       => 'PayerStatus',
        'CardType'          => 'CardType',
        'Last4Digits'       => 'Last4Digits',
    );

    foreach ($arr as $k => $v) {

        if (!empty($_POST[$v])) {
            $bill_output['billmes'] .= "\n\r" . $k . ': ' . $_POST[$v];
        }
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
	$request 		= $app_catalogs['customer'].'/index.php?target=cart&mode=checkout';
}
else {
	$_doc_ids 	= cw_get_urlencoded_doc_ids($return['doc_ids']);
	$request 	= $current_location . "/index.php?target=order-message&doc_ids=" . $_doc_ids;
	$cart 		= array();
	cw_session_save();
}

echo "Status=OK\r\n"
    . "RedirectURL=" . $request . "\r\n"
    . "StatusDetail=\r\n";

exit();

