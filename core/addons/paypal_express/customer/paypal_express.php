<?php
cw_load('xml', 'http', 'profile_fields', 'cart', 'doc', 'attributes', 'mail');

$top_message = &cw_session_register('top_message');
$paypal_express_details = &cw_session_register('paypal_express_details');
$cart = &cw_session_register('cart');

$pp_signature_txt = $config['paypal_express']['auth_type'] == 'C' ? '' : "<Signature>".$config['paypal_express']['api_signature']."</Signature>";
$pp_locale_codes = array("AU","DE","FR","GB","IT","JP","US");

if ($config['paypal_express']['test_mode'] == "N")
    $pp_customer_url = "https://www.paypal.com";
else 
    $pp_customer_url = "https://www.sandbox.paypal.com";

$pp_subject = '';
if (!empty($config['paypal_express']['paypal_express_email'])) {
    $pp_subject = '<Subject>' . $config['paypal_express']['paypal_express_email'] . '</Subject>';
}
$pp_final_action = 'Authorization';

if ($action == 'start') {

	$paypal_token = &cw_session_register('paypal_token');

	$pp_return_url = $current_location.'/index.php?target=paypal_express&amp;action=express_return';
	$pp_cancel_url = $current_location.'/index.php?target=cart';

	$paypal_begin_express = &cw_session_register('paypal_begin_express');
	$paypal_begin_express = false;

	$paypal_mode = &cw_session_register("paypal_mode");

	$paypal_mode = 'express';

    if ($pp_subject) {
        $config['paypal_express']['api_access'] = '';
        $config['paypal_express']['api_password'] = '';
        $config['paypal_express']['auth_type'] = false;
        $pp_signature_txt = '';
        $pp_final_action = 'Sale';
    }

	if (!empty($do_return) && !empty($paypal_token))
		$str_token = "<Token>$paypal_token</Token>";

	$pp_locale_code = "US";
	if (in_array($shop_language, $pp_locale_codes))
		$pp_locale_code = $shop_language;

	$address = '';
	$address_override = '';

	if ($customer_id) {
        $userinfo = cw_user_get_info($customer_id, 65535);

		if (!empty($userinfo)) {
			$userinfo = cw_array_map("cw_xml_escape", $userinfo);

			$state = ($userinfo['current_address']['country'] == 'US' || $userinfo['current_address']['country'] == 'CA' || $userinfo['current_address']['state'] != '') ? $userinfo['current_address']['state'] : 'Other';

			$address = <<<ADDR
<Address>
	<Name>{$userinfo['current_address']['firstname']} {$userinfo['current_address']['lastname']}</Name>
	<Street1>{$userinfo['current_address']['address']}</Street1>
	<Street2>{$userinfo['current_address']['address_2']}</Street2>
	<CityName>{$userinfo['current_address']['city']}</CityName>
	<StateOrProvince>{$state}</StateOrProvince>
	<PostalCode>{$userinfo['current_address']['zipcode']}</PostalCode>
	<Country>{$userinfo['current_address']['country']}</Country>
	<Phone>{$userinfo['current_address']['phone']}</Phone>
</Address>
ADDR;
		    $address_override = "<AddressOverride>1</AddressOverride>";
        }
	}


    
    // Get totals
    $pp_paypal_totals = cw_call('cw_paypal_get_totals', array($cart));
    if (!empty($pp_paypal_totals)) {
        $products = cw_call('cw_products_in_cart',array($cart, !empty($user_account["membershipid"]) ? $user_account["membershipid"] : ""));
        $products_str = '';
        if ($products)
        foreach($products as $product) {
            $products_str .= '<PaymentDetailsItem><Name><![CDATA['.$product['product'].']]></Name><Amount currencyID="'.$config['paypal_express']['currency'].'">'.$product['price'].'</Amount><Quantity>'.$product['amount'].'</Quantity></PaymentDetailsItem>';
        }
    } else {
        $products_str = '';
        $pp_paypal_totals = array(
            'OrderTotal' => $cart['info']['total'],
            'ItemTotal' => $cart['info']['total'],
            'ShippingTotal' => 0,
            'TaxTotal' => 0,
            'HandlingTotal' => 0
        );
    }
    
	$request = <<<EOT
<?xml version="1.0" encoding="UTF-8"?>
<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema">
  <soap:Header>
    <RequesterCredentials xmlns="urn:ebay:api:PayPalAPI">
      <Credentials xmlns="urn:ebay:apis:eBLBaseComponents">
        <Username>{$config['paypal_express']['api_access']}</Username>
        <ebl:Password xmlns:ebl="urn:ebay:apis:eBLBaseComponents">{$config['paypal_express']['api_password']}</ebl:Password>
        $pp_signature_txt
        $pp_subject
      </Credentials>
    </RequesterCredentials>
  </soap:Header>
  <soap:Body>
    <SetExpressCheckoutReq xmlns="urn:ebay:api:PayPalAPI">
      <SetExpressCheckoutRequest>
        <Version xmlns="urn:ebay:apis:eBLBaseComponents">60.0</Version>
        <SetExpressCheckoutRequestDetails xmlns="urn:ebay:apis:eBLBaseComponents">
          <PaymentDetails>
              <OrderTotal currencyID="{$config['paypal_express']['currency']}">{$pp_paypal_totals['OrderTotal']}</OrderTotal>
              <ItemTotal currencyID="{$config['paypal_express']['currency']}">{$pp_paypal_totals['ItemTotal']}</ItemTotal>
              <ShippingTotal currencyID="{$config['paypal_express']['currency']}">{$pp_paypal_totals['ShippingTotal']}</ShippingTotal>
              <TaxTotal currencyID="{$config['paypal_express']['currency']}">{$pp_paypal_totals['TaxTotal']}</TaxTotal>
              <HandlingTotal currencyID="{$config['paypal_express']['currency']}">{$pp_paypal_totals['HandlingTotal']}</HandlingTotal>
                $products_str
                $address
          </PaymentDetails>
          <ReturnURL>$pp_return_url</ReturnURL>
          <CancelURL>$pp_cancel_url</CancelURL>
          <PaymentAction>$pp_final_action</PaymentAction>
		  $str_token
		  <LocaleCode>$pp_locale_code</LocaleCode>
        </SetExpressCheckoutRequestDetails>
      </SetExpressCheckoutRequest>
    </SetExpressCheckoutReq>
  </soap:Body>
</soap:Envelope>
EOT;
	$result = cw_func_call('cw_paypal_express_request', array('request' => $request));

	# receive SetExpressCheckoutResponse
	if ($result['success'] && !empty($result['Token'])) {
		$paypal_token = $result['Token'];
		cw_header_location($pp_customer_url.'/webscr?cmd=_express-checkout&token='.$result['Token']);
	}

	$top_message = array('type' => 'E', 'content' => $result['error']['ShortMessage'] . (!empty($result['error']['LongMessage']) ? ": " . $result['error']['LongMessage'] : ""));
	cw_header_location($pp_cancel_url);
}
elseif ($REQUEST_METHOD == "GET" && $action == 'express_return' && !empty($_GET['token'])) {
	# return from PayPal
    
    if ($pp_subject) {
        $config['paypal_express']['api_access'] = '';
        $config['paypal_express']['api_password'] = '';
        $config['paypal_express']['auth_type'] = false;
        $pp_signature_txt = '';
        $pp_final_action = 'Sale';
    }
    
	# send GetExpressCheckoutDetailsRequest
	$token = $_GET['token'];
	$request =<<<EOT
<?xml version="1.0" encoding="UTF-8"?>
<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema">
  <soap:Header>
    <RequesterCredentials xmlns="urn:ebay:api:PayPalAPI">
      <Credentials xmlns="urn:ebay:apis:eBLBaseComponents">
        <Username>{$config['paypal_express']['api_access']}</Username>
        <ebl:Password xmlns:ebl="urn:ebay:apis:eBLBaseComponents">{$config['paypal_express']['api_password']}</ebl:Password>
		$pp_signature_txt
        $pp_subject
      </Credentials>
    </RequesterCredentials>
  </soap:Header>
  <soap:Body>
    <GetExpressCheckoutDetailsReq xmlns="urn:ebay:api:PayPalAPI">
      <GetExpressCheckoutDetailsRequest>
        <Version xmlns="urn:ebay:apis:eBLBaseComponents">1.00</Version>
        <Token>$token</Token>
      </GetExpressCheckoutDetailsRequest>
    </GetExpressCheckoutDetailsReq>
  </soap:Body>
</soap:Envelope>
EOT;
	$result = cw_func_call('cw_paypal_express_request', array('request' => $request));

	$state_err = 0;

	$address = array (
        'firstname' => empty($result['address']['FirstName']) ? $result['FirstName'] : $result['address']['FirstName'],
        'lastname'  => empty($result['address']['LastName']) ? $result['LastName'] : $result['address']['LastName'],
        'address'   => preg_replace('![\s\n\r]+!s', ' ', $result['address']['Street1'])."\n".preg_replace('![\s\n\r]+!s', ' ', @$result['address']['Street2']),
        'city'      => $result['address']['CityName'],
        'country'   => $result['address']['Country'],
        'zipcode'   => $result['address']['PostalCode'],
        'phone'     => empty($result['address']['Phone']) ? $result['ContactPhone'] : $result['address']['Phone'],
	'state' => cw_paypal_express_detect_state($result['address']['Country'], $result['address']['StateOrProvince'], $state_err),
	);

	if ($config["General"]["use_counties"] == "Y") {
		$default_county = cw_default_county($address['state'], $address['country']);
		$address['county'] = empty($default_county) ? $result['address']['StateOrProvince'] : $default_county;
	}

	$customer_id = &cw_session_register('customer_id');

	if ($customer_id) {
        $address_id = cw_query_first_cell("select address_id from $tables[customers_addresses] where customer_id='$customer_id' and current=1");
        cw_user_update_address($customer_id, $address_id, cw_addslashes($address));
	}
    else {
        $profile_values = array(
            'email' => $result['Payer'],
            'current_address' => $address,
        );
        $customer_id = cw_user_create_profile(array('usertype'=>'C'));
        $profile_values['status'] = 'Y';
        cw_user_update($profile_values, $customer_id, $customer_id);

        $identifiers = &cw_session_register("identifiers", array());
        $identifiers['C'] = array('customer_id' => $customer_id);
	}

	$paypal_express_details = $result;

	switch ($state_err) {
		case 1:
			$top_message = array( "type" => "W", "content" => cw_get_langvar_by_name("lbl_paypal_wrong_country_note"));
			break;
		case 2:
			$top_message = array( "type" => "W", "content" => cw_get_langvar_by_name("lbl_paypal_wrong_state_note"));
	}

# kornev, re-calculate the cart with the address and place the order
    $cart['userinfo'] = $userinfo = cw_user_get_info($customer_id, 65535);
    $products = cw_call('cw_products_in_cart',array($cart, $userinfo));
    $cart = cw_func_call('cw_cart_calc', array('cart' => $cart, 'products' => $products, 'userinfo' => $userinfo));

    $location[] = array(cw_get_langvar_by_name('lbl_paypal_express_confirma_payment'), '');

    cw_include('customer/cart.php');

    $smarty->assign('home_style', 'popup');
    $smarty->assign('current_main_dir', 'addons/paypal_express');
    $smarty->assign('current_section_dir', 'customer');
    $smarty->assign('main', 'confirmation');
}
elseif ($action == 'place_order') {

     // finish ExpressCheckout

    if ($pp_subject) {
        $config['paypal_express']['api_access'] = '';
        $config['paypal_express']['api_password'] = '';
        $config['paypal_express']['auth_type'] = false;
        $pp_signature_txt = '';
        $pp_final_action = 'Sale';
    }
        
    $cart['info']['payment_id'] = -20;

    $doc_ids = cw_func_call('cw_doc_place_order', array('order_type'=>'O', 'order_status' => 'I', 'order_details' => $order_details, 'userinfo' => $cart['userinfo'], 'prefix' => $config['paypal_express']['prefix'], 'extras' => $extras));
    $secure_oid = &cw_session_register("secure_oid");
    $secure_oid = $doc_ids;

    $pp_ordr = join("-", $doc_ids);

    # finish ExpressCheckout
    $request =<<<EOT
<?xml version="1.0" encoding="UTF-8"?>
<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema">
  <soap:Header>
    <RequesterCredentials xmlns="urn:ebay:api:PayPalAPI">
      <Credentials xmlns="urn:ebay:apis:eBLBaseComponents">
        <Username>{$config['paypal_express']['api_access']}</Username>
        <ebl:Password xmlns:ebl="urn:ebay:apis:eBLBaseComponents">{$config['paypal_express']['api_password']}</ebl:Password>
        $pp_signature_txt
        $pp_subject
      </Credentials>
    </RequesterCredentials>
  </soap:Header>
  <soap:Body>
    <DoExpressCheckoutPaymentReq xmlns="urn:ebay:api:PayPalAPI">
      <DoExpressCheckoutPaymentRequest>
        <Version xmlns="urn:ebay:apis:eBLBaseComponents">1.00</Version>
        <DoExpressCheckoutPaymentRequestDetails xmlns="urn:ebay:apis:eBLBaseComponents">
          <PaymentAction>Sale</PaymentAction>
          <Token>$paypal_express_details[Token]</Token>
          <PayerID>$paypal_express_details[PayerID]</PayerID>
          <PaymentDetails>
            <OrderTotal currencyID="{$config['paypal_express']['currency']}">{$cart['info']['total']}</OrderTotal>
            <ButtonSource>CW_shoppingcart_EC_US</ButtonSource>
            <NotifyURL>{$current_location}/index.php?target=paypal_express</NotifyURL>
            <InvoiceID>$pp_ordr</InvoiceID>
            <Custom>$pp_ordr</Custom>
          </PaymentDetails>
        </DoExpressCheckoutPaymentRequestDetails>
      </DoExpressCheckoutPaymentRequest>
    </DoExpressCheckoutPaymentReq>
  </soap:Body>
</soap:Envelope>
EOT;

    $result = cw_func_call('cw_paypal_express_request', array('request' => $request));

    $bill_output['code'] = 2;

    if (!strcasecmp($result['PaymentStatus'],'Completed') || !strcasecmp($result['PaymentStatus'],'Processed')) {
        $bill_output['code'] = 1;
        $bill_message = 'Accepted';
    }
    else
    if (!strcasecmp($result['PaymentStatus'],'Pending')) {
        $bill_output['code'] = 3;
        $bill_message = 'Queued';
    }
    else {
        $bill_message = 'Declined';
    }

    $bill_message .= " Status: ".$result['PaymentStatus'];
    if (!empty($result['PendingReason']) && strtolower(trim($result['PendingReason'])) != 'none')
        $bill_message .= ' Reason: '.$result['PendingReason'];

    $additional_fields = array();
    foreach (array('TransactionID','TransactionType','PaymentType','GrossAmount','FeeAmount','SettleAmount','TaxAmount','ExchangeRate') as $add_field) {
        if (isset($result[$add_field]) && strlen($result[$add_field]) > 0)
            $additional_fields[] = ' '.$add_field.': '.$result[$add_field];
    }

    if (!empty($additional_fields))
        $bill_message .= ' ('.implode(', ', $additional_fields).')';

    if (!empty($result['error'])) {
        $bill_message .= sprintf (
            " Error: %s (Code: %s, Severity: %s)",
            $result['error']['LongMessage'],
            $result['error']['ErrorCode'],
            $result['error']['Severity']);
    }

    $bill_output["billmes"] = $bill_message;

    $bill_output['extra_order_data'] = array(
        "paypal_type" => "USEC",
        "paypal_txnid" => $result['TransactionID'],
        "capture_status" => '',
    );

    $return = cw_call('cw_payment_check_results', array($bill_output));
    cw_call('cw_payment_stop', array($return));
}
else exit(0);
