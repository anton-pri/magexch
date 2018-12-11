<?php

function cw_payment_sagepaygo_server_get_methods($params, $return) {

    if ($return['processor'] == sagepaygo_server_addon_name) {
        $return['ccinfo'] = false;
    }
    return $return;
}

function cw_payment_sagepaygo_server_run_processor($params, $return) {

	if ($params['payment_data']['processor'] == sagepaygo_server_addon_name) {
		global $config, $tables, $current_location, $cart, $REMOTE_ADDR;

		cw_load('http');

		$payment_data 	= $params['payment_data'];
		$userinfo 		= $params['userinfo'];
		$doc_ids 		= $params['doc_ids'];
		$addon_name	= str_replace("-", "_", sagepaygo_server_addon_name);

		$cart = &cw_session_register('cart');
		$skey = cw_call('cw_payment_start');

		$bill_name	= cw_payment_sagepaygo_server_get_bill_name($userinfo);
		$ship_name	= cw_payment_sagepaygo_server_get_ship_name($userinfo);

	    $pp_merch 	= $config[$addon_name]['sps_vendor_name'];
	    $pp_curr 	= $config[$addon_name]['sps_currency'];

	    // Determine request URL (simulator, test server or live server)
	    switch ($config[$addon_name]['sps_test_live_mode']) {
	        case 'S':
	            $pp_test = 'https://test.sagepay.com:443/Simulator/VSPServerGateway.asp?Service=VendorRegisterTx';
	            break;
	        case 'Y':
	            $pp_test = 'https://test.sagepay.com:443/gateway/service/vspserver-register.vsp';
	            break;
	        default:
	            $pp_test = 'https://live.sagepay.com:443/gateway/service/vspserver-register.vsp';
	    }

	    $pp_shift = $config[$addon_name]['sps_order_prefix'];

	    $post = array();
	    $post['VPSProtocol'] 		= '2.23';
	    $post['TxType'] 			= $config[$addon_name]['sps_action_on_order_placement'] == 'Y' ? 'DEFERRED' : 'PAYMENT';
	    $post['Vendor'] 			= substr($pp_merch, 0, 15);
	    $post['VendorTxCode'] 		= substr($pp_shift, 0, 8) . $skey;
	    $post['ReferrerID'] 		= sagepaygo_server_addon_partner_id;
	    $post['Amount'] 			= $cart['info']['total'];
	    $post['Currency'] 			= $pp_curr;
	    $post['Description'] 		= 'Your Cart';
	    $post['NotificationURL'] 	= $current_location . '/index.php?target=' . sagepaygo_server_addon_target;
	    $post['Profile'] 			= 'LOW';

	    // Billing information
	    $post['BillingSurname'] 	= $bill_name['lastname'];;
	    $post['BillingFirstnames'] 	= $bill_name['firstname'];
	    $post['BillingAddress1'] 	= $userinfo['main_address']['address'];

	    if (!empty($userinfo['main_address']['address_2'])) {
			$post['BillingAddress2'] = $userinfo['main_address']['address_2'];
	    }

	    $post['BillingCity'] 		= $userinfo['main_address']['city'];
	    $post['BillingPostCode'] 	= $userinfo['main_address']['zipcode'];
	    $post['BillingCountry'] 	= $userinfo['main_address']['country'];

	    if (
	    	$userinfo['main_address']['country'] == 'US' 
	    	&& !empty($userinfo['main_address']['state'])
	    ) {
			$post['BillingState'] 	= $userinfo['main_address']['state'];
	    }

	    // Shipping information
	    $post['DeliverySurname'] 	= $ship_name['lastname'];
	    $post['DeliveryFirstnames'] = $ship_name['firstname'];
	    $post['DeliveryAddress1'] 	= $userinfo['current_address']['address'];

	    if (!empty($userinfo['current_address']['address_2'])) {
	        $post['DeliveryAddress2'] 	= $userinfo['current_address']['address_2'];
	    }

	    $post['DeliveryCity'] 		= $userinfo['current_address']['city'];
	    $post['DeliveryPostCode'] 	= $userinfo['current_address']['zipcode'];
	    $post['DeliveryCountry'] 	= $userinfo['current_address']['country'];

	    if (
		    $userinfo['current_address']['country'] == 'US' 
		    && !empty($userinfo['current_address']['state'])
	    ) {
			$post['DeliveryState'] = $userinfo['current_address']['state'];
	    }

	    $post['CustomerEMail'] 		= $userinfo['email'];
	    $post['GiftAidPayment'] 	= '0';
	    $post['ApplyAVSCV2'] 		= $config[$addon_name]['sps_avs_cv2_checks'];
	    $post['Apply3DSecure'] 		= $config[$addon_name]['sps_3d_secure_checks'];

	    // Tide up the entire values
	    $post = cw_payment_sagepaygo_server_clean_post($post);

	    // Send initial request and obtain the key
	    list($a, $return) = cw_https_request('POST', $pp_test, $post);

	    // Parse response
	    $ret 		= str_replace("\r\n", '&', $return);
	    $ret_arr 	= explode('&', $ret);
	    $response 	= array();

	    foreach ($ret_arr as $ret) {

	        if (preg_match('/([^=]+?)=(.+)/', $ret, $matches)) {
	            $response[$matches[1]] = $matches[2];
	        }
	    }

	    if (
	    	$response['Status'] == 'OK' 
	    	&& $response['NextURL']
	    ) {
	        // Redirect to SagePay
	        cw_header_location($response['NextURL']);
	        exit();
	    }
	    else {
	    	global $app_catalogs;

	    	$data = cw_call('cw_payment_get_data',array($skey));	    	
	        // Return with error
	        $bill_output['code'] 	= 2;
	        $bill_output['sessid'] 	= $data['session_id'];
	        $bill_output['skey'] 	= $skey;
	
	        $bill_output['billmes'] = 'Status: ' . $response['StatusDetail'] . ' (' . $response['Status'] . ')';

	        if (!empty($response['VPSTxID'])) {
	            $bill_output['billmes'] .= ' (TxID: ' . $response['VPSTxID'] . ')';
	        }

            $return = cw_call('cw_payment_check_results', array($bill_output));
			$top_message = &cw_session_register('top_message');

			$top_message 	= array('type' => 'E', 'content' => $return['bill_error'] . ' ' . $return['reason']);
			$request 		= $app_catalogs['customer'] . '/index.php?target=cart&mode=checkout';

			cw_header_location($request);
			exit();
	    }
    }
    return $return;
}

function cw_payment_sagepaygo_server_get_bill_name($userinfo) {
	$address['firstname'] 	= "";
	$address['lastname'] 	= "";

	if (isset($userinfo['main_address'])) {
		$address = $userinfo['main_address'];
	}
	
	$bill_firstname	= $address['firstname'];	
	$bill_lastname	= $address['lastname'];	
	$bill_name 		= $bill_firstname;
	
	if (!empty($bill_lastname)) {
	    $bill_name .= (
	        empty($bill_firstname)
	            ? ''
	            : " "
	    )
	    . $bill_lastname;
	}
	
	return array(
		"name" 		=> $bill_name,
		"firstname" => $bill_firstname,
		"lastname" 	=> $bill_lastname
	);
}

function cw_payment_sagepaygo_server_get_ship_name($userinfo) {
	$address['firstname'] 	= "";
	$address['lastname'] 	= "";

	if (isset($userinfo['current_address'])) {
		$address = $userinfo['current_address'];
	}

	$ship_firstname	= $address['firstname'];	
	$ship_lastname	= $address['lastname'];	
	$ship_name = $ship_firstname;
	
	if (!empty($ship_lastname)) {
	    $ship_name .= (
	        empty($ship_firstname)
	            ? ''
	            : " "
	    )
	    . $ship_lastname;
	}
	
	return array(
		"name" 		=> $ship_name,
		"firstname" => $ship_firstname,
		"lastname" 	=> $ship_lastname
	);
}

// The functions below are based on the examples from the PHP Integration
// Kits, which were downloaded from the official Sage Pay website www.sagepay.com.
// Filters unwanted characters out of an input string.  Useful for tidying up FORM field inputs.
function cw_payment_sagepaygo_server_clean_input($strRawText, $strType, $maxChars=false, $customPattern=false) {

    switch ($strType) {
        case 'Number':
            $strClean 		= '0123456789.';
            $bolHighOrder 	= false;
            break;
        case 'Digits':
            $strClean 		= '0123456789';
            $bolHighOrder 	= false;
            break;
        case 'Text':
            $strClean 	= " ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789.,'/{}@():?-_&Ј$=%~<>*+\"";
            $bolHighOrder 	= true;
            break;
        case 'Custom':
            $strClean 		= $customPattern;
            $bolHighOrder 	= false;
            break;
        default:
            break;
    }

    $strCleanedText = '';
    $iCharPos = 0;

    do {
        // Only include valid characters
        $chrThisChar = substr($strRawText, $iCharPos, 1);

        if (strspn($chrThisChar, $strClean, 0, strlen($strClean)) > 0) {
            $strCleanedText = $strCleanedText . $chrThisChar;
        }
        elseif ($bolHighOrder == true) {
            // Fix to allow accented characters and most high order bit chars which are harmless
            if (bin2hex($chrThisChar) >= 191) {
                $strCleanedText = $strCleanedText . $chrThisChar;
            }
        }

        $iCharPos = $iCharPos+1;
    }
	while ($iCharPos<strlen($strRawText));

	$cleanInput = ltrim($strCleanedText);

	if ($maxChars && strlen($cleanInput) > $maxChars) {
		$cleanInput = substr($cleanInput, 0, $maxChars);
	}

	return $cleanInput;
}

// Function tides up the values in accordance with the fields specification
function cw_payment_sagepaygo_server_clean_post($data) {
    $fields_specs = cw_payment_sagepaygo_server_get_allowed_fields();

    foreach ($fields_specs as $field => $spec) {
        if (!isset($data[$field]) || isset($spec['skip']))
            continue;

        if (isset($fields_specs[$field]['allowed_values'])) {
            if ( !in_array($data[$field], $spec['allowed_values'])) {
                cw_unset($data, $field);
            }
            continue;
        }
        $pattern = ($spec['filter'] == 'Custom') ? $spec['pattern'] : false;
        $data[$field] = cw_payment_sagepaygo_server_clean_input($data[$field], $spec['filter'], $spec['max'], $pattern);
    }

    $_data = array();
    foreach($data as $k => $v) {
        $_data[] = $k . "=" . $v;
    }

    return $_data;
}

/**
 * Function returns an array of allowed fields
 *  max: max length of the string for Text and Digits filters,
 *  filter: filter to be applied in the cleanInput function
 *  pattern: pattern for Custom filter
 *  skip: skip checking of this input, since it is already perfomed in CW
 */
function cw_payment_sagepaygo_server_get_allowed_fields() {
    $fields_specification = array(
        'VendorTxCode' => array(
            'max' => 40,
            'filter' => 'Custom',
            'pattern' => "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789-_."
        ),
        'Amount' => array(
            'skip' => true,
        ),
        'Currency' => array(
            'skip' => true
        ),
        'Description' => array(
            'max' => 100,
            'filter' => 'Text'
        ),
        'SuccessURL' => array(
            'max' => 2000,
            'filter' => 'Text'
        ),
        'FailureURL' => array(
            'max' => 2000,
            'filter' => 'Text'
        ),
        'CustomerName' => array(
            'max' => 100,
            'filter' => 'Text'
        ),
        'CustomerEMail' => array(
            'max' => 255,
            'filter' => 'Text'
        ),
        'VendorEMail' => array(
            'max' => 255,
            'filter' => 'Text'
        ),
        'SendEMail' => array(
            'allowed_values' => array(0,1,2,3)
        ),
        'eMailMessage' => array(
            'max' => 7500,
            'filter' => 'Text'
        ),
        'BillingSurname' => array(
            'max' => 20,
            'filter' => 'Text'
        ),
        'BillingFirstnames' => array(
            'max' => 20,
            'filter' => 'Text'
        ),
        'BillingAddress1' => array(
            'max' => 100,
            'filter' => 'Text'
        ),
        'BillingAddress2' => array(
            'max' => 100,
            'filter' => 'Text'
        ),
        'BillingCity' => array(
            'max' => 40,
            'filter' => 'Text'
        ),
        'BillingPostCode' => array(
            'max' => 10,
            'filter' => 'Text'
        ),
        'BillingCountry' => array(
            'skip' => true
        ),
        'BillingState'=> array(
            'skip' => true
        ),
        'BillingPhone' => array(
            'max' => 20,
            'filter' => 'Text'
        ),
        'DeliverySurname' => array(
            'max' => 20,
            'filter' => 'Text'
        ),
        'DeliveryFirstnames' => array(
            'max' => 20,
            'filter' => 'Text'
        ),
        'DeliveryAddress1' => array(
            'max' => 100,
            'filter' => 'Text'
        ),
        'DeliveryAddress2' => array(
            'max' => 100,
            'filter' => 'Text'
        ),
        'DeliveryCity' => array(
            'max' => 40,
            'filter' => 'Text'
        ),
        'DeliveryPostCode' => array(
            'max' => 10,
            'filter' => 'Text'
        ),
        'DeliveryCountry' => array(
            'skip' => true
        ),
        'DeliveryState' => array(
            'skip' => true
        ),
        'DeliveryPhone' => array(
            'max' => 20,
            'filter' => 'Text'
        ),
        'Basket' => array(
            'max' => 7500,
            'filter' => 'Text'
        ),
        'AllowGiftAid' => array(
            'allowed_values' => array('0','1')
        ),
        'ApplyAVSCV2' => array(
            'allowed_values' => array('0','1','2','3')
        ),
        'Apply3DSecure' => array(
            'allowed_values' => array('0','1','2','3')
        ),
        'TxType' => array(
            'allowed_values' => array('PAYMENT','DEFERRED','AUTHENTICATE','RELEASE','AUTHORISE','CANCEL','ABORT','MANUAL','REFUND','REPEAT','REPEATDEFERRED','VOID','PREAUTH','COMPLETE')
        ),
        'NotificationURL' => array(
            'max' => 255,
            'filter' => 'Text'
        ),
        'Vendor' => array(
            'max' => 15,
            'filter' => 'Text'
        ),
        'Profile' => array(
            'allowed_values' => array('LOW','NORMAL')
        ),
        'CardHolder' => array(
            'max' => 50,
            'filter' => 'Text'
        ),
        'CardNumber' => array(
            'max' => 20,
            'filter' => 'Digits'
        ),
        'StartDate' => array(
            'max' => 4,
            'filter' => 'Digits'
        ),
        'ExpiryDate' => array(
            'max' => 4,
            'filter' => 'Digits'
        ),
        'IssueNumber' => array(
            'max' => 2,
            'filter' => 'Digits'
        ),
        'CV2' => array(
            'max' => 4,
            'filter' => 'Digits'
        ),
        'CardType' => array(
            'allowed_values' => array('VISA','MC','DELTA','SOLO','MAESTRO','UKE','AMEX','DC','JCB','LASER','PAYPAL')
        ),
        'PayPalCallbackURL' => array(
            'max' => 255,
            'filter' => 'Text'
        ),
        'GiftAidPayment' => array(
            'allowed_values' => array('0','1')
        ),
        'ClientIPAddress' => array(
            'max' => 15,
            'filter' => 'Text'
        ),
        'MD' => array(
            'max' => 35,
            'filter' => 'Text'
        ),
        'PARes' => array(
            'max' => 7500,
            'filter' => 'Text'
        ),
        'VPSTxID' => array(
            'max' => 38,
            'filter' => 'Text'
        ),
        'Accept' => array(
            'allowed_values' => array('Yes','No')
        ),
        'Crypt' => array(
            'max' => 16384,
            'filter' => 'Text'
        ),
        'AccountType' => array(
            'allowed_values' => array('E','M','C')
        )
    );

    return $fields_specification;
}
