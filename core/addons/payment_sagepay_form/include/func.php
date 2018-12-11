<?php

function cw_payment_sagepaygo_form_get_methods($params, $return) {

    if ($return['processor'] == sagepaygo_form_addon_name) {
        $return['ccinfo'] = false;
    }
    return $return;
}

function cw_payment_sagepaygo_form_run_processor($params, $return) {

	if ($params['payment_data']['processor'] == sagepaygo_form_addon_name) {
		global $config, $tables, $current_location, $cart;

		$payment_data 	= $params['payment_data'];
		$userinfo 		= $params['userinfo'];
		$doc_ids 		= $params['doc_ids'];
		$addon_name	= str_replace("-", "_", sagepaygo_form_addon_name);

		$bill_name	= cw_payment_sagepaygo_form_get_bill_name($userinfo);
		$ship_name	= cw_payment_sagepaygo_form_get_ship_name($userinfo);

		$cart = &cw_session_register('cart');
		$skey = cw_call('cw_payment_start');

		$pp_merch 	= $config[$addon_name]['spf_vendor_name'];
		$pp_pass 	= $config[$addon_name]['spf_encryption_password'];
		$pp_curr 	= $config[$addon_name]['spf_currency'];

		// Determine request URL (simulator, test server or live server)
		switch ($config[$addon_name]['spf_test_live_mode']) {
			case 'S':
				$pp_test = 'https://test.sagepay.com/Simulator/VSPFormGateway.asp';
				break;
			case 'Y':
				$pp_test = 'https://test.sagepay.com/gateway/service/vspform-register.vsp';
				break;
			default:
				$pp_test = 'https://live.sagepay.com/gateway/service/vspform-register.vsp';
		}
        $pp_shift = preg_replace("/[^\w\d_-]/S", '', $config[$addon_name]['spf_order_prefix']);

        $crypt = array();
		$crypt['VendorTxCode'] 	= substr($pp_shift, 0, 8) . $skey;
		$crypt['ReferrerID'] 	= sagepaygo_form_addon_partner_id;
		$crypt['Amount'] 		= price_format($cart['info']['total']);
		$crypt['Currency'] 		= $pp_curr;
		$crypt['Description'] 	= "Your Cart";
		$crypt['SuccessURL'] 	= $current_location . '/index.php?target=' . sagepaygo_form_addon_target;
		$crypt['FailureURL'] 	= $current_location . '/index.php?target=' . sagepaygo_form_addon_target;

		$crypt['CustomerName'] 	= $bill_name['name'];
		$crypt['CustomerEMail'] = $userinfo['email'];
		$crypt['VendorEMail'] 	= $config['Company']['orders_department'];
		$crypt['SendEMail'] 	= 1;

		// Billing information
		$crypt['BillingSurname'] 	= $bill_name['lastname'];
		$crypt['BillingFirstnames'] = $bill_name['firstname'];
		$crypt['BillingAddress1'] 	= $userinfo['main_address']['address'];

		if (!empty($userinfo['main_address']['address_2'])) {
			$crypt['BillingAddress2'] = $userinfo['main_address']['address_2'];
		}

		$crypt['BillingCity'] 		= $userinfo['main_address']['city'];
		$crypt['BillingPostCode'] 	= $userinfo['main_address']['zipcode'];
		$crypt['BillingCountry'] 	= $userinfo['main_address']['country'];

		if (
			$userinfo['main_address']['country'] == 'US' 
			&& !empty($userinfo['main_address']['state'])
        ) {
			$crypt['BillingState'] = $userinfo['main_address']['state'];
        }

		// Shipping information
		$crypt['DeliverySurname'] 		= $ship_name['lastname'];
		$crypt['DeliveryFirstnames'] 	= $ship_name['firstname'];
		$crypt['DeliveryAddress1'] 		= $userinfo['current_address']['address'];

		if (!empty($userinfo['current_address']['address_2'])) {
			$crypt['DeliveryAddress2'] = $userinfo['current_address']['address_2'];
		}

		$crypt['DeliveryCity'] 		= $userinfo['current_address']['city'];
		$crypt['DeliveryPostCode'] 	= $userinfo['current_address']['zipcode'];
		$crypt['DeliveryCountry'] 	= $userinfo['current_address']['country'];

		if (
			$userinfo['current_address']['country'] == 'US' 
			&& !empty($userinfo['current_address']['state']) 
		) {
			$crypt['DeliveryState'] = $userinfo['current_address']['state'];
		}

		$crypt['AllowGiftAid'] 	= '0';
		$crypt['ApplyAVSCV2'] 	= $config[$addon_name]['spf_avs_cv2_checks'];
		$crypt['Apply3DSecure']	= $config[$addon_name]['spf_3d_secure_checks'];

		// Tide up the entire values
		$crypt = cw_payment_sagepaygo_form_clean_crypt($crypt);

		$crypt_str = implode("&", $crypt);

		cw_func_call('cw_payment_create_form', 
			array(
				'url' => $pp_test, 
				'fields' => array(
					'VPSProtocol' 	=> '2.23',
					'Vendor' 		=> $pp_merch,
					'TxType' 		=> 'PAYMENT',
					'Crypt' 		=> base64_encode(cw_payment_sagepaygo_form_simple_xor($crypt_str, $pp_pass))
	            ), 
	            'name' => $payment_data['title']
			)
		);
        die();
    }
    return $return;
}

function cw_payment_sagepaygo_form_get_bill_name($userinfo) {
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

function cw_payment_sagepaygo_form_get_ship_name($userinfo) {
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
function cw_payment_sagepaygo_form_clean_input($strRawText, $strType, $maxChars=false, $customPattern=false) {

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
        else if ($bolHighOrder == true) {
            // Fix to allow accented characters and most high order bit chars which are harmless
            if (bin2hex($chrThisChar) >= 191) {
                $strCleanedText = $strCleanedText . $chrThisChar;
            }
        }

        $iCharPos = $iCharPos+1;
    }
    while ($iCharPos<strlen($strRawText));

      $cleanInput = ltrim($strCleanedText);

    if ($maxChars && strlen($cleanInput) > $maxChars)
        $cleanInput = substr($cleanInput, 0, $maxChars);

    return $cleanInput;

}

// Base 64 decoding function
function cw_payment_sagepaygo_form_base64_decode($scrambled) {
    // Fix plus to space conversion issue
    $scrambled = str_replace(" ", "+", $scrambled);

    // Do encoding
    $output = base64_decode($scrambled);

    // Return the result
    return $output;
}

// The SimpleXor encryption algorithm
function cw_payment_sagepaygo_form_simple_xor($InString, $Key) {
    // Initialise key array
    $KeyList = array();

    // Initialise out variable
    $output = '';

    // Convert $Key into array of ASCII values
    for($i = 0; $i < strlen($Key); $i++){
        $KeyList[$i] = ord(substr($Key, $i, 1));
    }

    // Step through string a character at a time
    for($i = 0; $i < strlen($InString); $i++) {
        // Get ASCII code from string, get ASCII code from key (loop through with MOD), XOR the two, get the character from the result
        // % is MOD (modulus), ^ is XOR
        $output .= chr(ord(substr($InString, $i, 1)) ^ ($KeyList[$i % strlen($Key)]));
    }

    // Return the result
    return $output;
}

// Function tides up the values in accordance with the fields specification
function cw_payment_sagepaygo_form_clean_crypt($data) {
    $fields_specs = cw_payment_sagepaygo_form_get_allowed_fields();

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
        $data[$field] = cw_payment_sagepaygo_form_clean_input($data[$field], $spec['filter'], $spec['max'], $pattern);
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
function cw_payment_sagepaygo_form_get_allowed_fields() {
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
