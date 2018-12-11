<?php

function cw_paypal_pro_request($params) {//$request, $regexp=false) {
    extract($params);
    global $config, $app_dir;

    if ($config['paypal_express']['test_mode'] == "N")
        $pp_url = $config['paypal_express']['auth_type'] == 'C' ? "https://api.paypal.com:443/2.0/" : "https://api-3t.paypal.com:443/2.0/";
    else
        $pp_url = $config['paypal_express']['auth_type'] ? "https://api.sandbox.paypal.com:443/2.0/" : "https://api-aa.sandbox.paypal.com:443/2.0/";

    $post = explode("\n",$request);

    if ($config['paypal_express']['auth_type'] == 'C')
        list($headers, $response) = cw_https_request("POST", $pp_url, $post, "", "", "text/xml", "", $app_dir.'/payment/certs/'.$config['paypal_express']['api_cert_path']);
    else
        list($headers, $response) = cw_https_request("POST", $pp_url, $post, "", "", "text/xml", "");

    if ($headers == "0") {
        return array(
            'success' => false,
            'error' => array('ShortMessage' => $response)
        );
    }

    $result = array (
        'headers' => $headers,
        'response' => $response
    );

    if (!empty($regexp)) {
        $matches = array();
        preg_match($regexp, $response, $matches);
        $result['matches'] = $matches;
    }

    #
    # Parse and fill common fields
    #
    $result['success'] = false;

    $ord_fields = array (
        'Ack',
        'TransactionID',
        'Token', # Note: expires after three hours (Express Checkout Integration Guide, p30)
        'AVSCode',
        'CVV2Code',
        'PayerID',
        'PayerStatus',
        'FirstName',
        'LastName',
        'ContactPhone',
        'TransactionType', # e.g. express-checokut
        'PaymentStatus', # e.g. Pending
        'PendingReason', # e.g. authorization
        'ReasonCode',
        'GrossAmount',
        'FeeAmount',
        'SettleAmount',
        'TaxAmount',
        'ExchangeRate'
    );

    foreach ($ord_fields as $field) {
        if (preg_match('!<'.$field.'(?: [^>]*)?>([^>]+)</'.$field.'>!', $response, $out)) {
            $result[$field] = $out[1];
        }
    }

    if (!strcasecmp($result['Ack'], 'Success') || !strcasecmp($result['Ack'], 'SuccessWithWarning'))
        $result['success'] = true;

    if (preg_match('!<Payer(?:\s[^>]*)?>([^>]+)</Payer>!', $response, $out)) {
        $result['Payer'] = $out[1]; # e-mail address
    }

    if (preg_match('!<Errors[^>]*>(.+)</Errors>!', $response, $out_err)) {
        $error = array();

        if (preg_match('!<SeverityCode[^>]*>([^>]+)</SeverityCode>!', $out_err[1], $out))
            $error['SeverityCode'] = $out[1];

        if (preg_match('!<ErrorCode[^>]*>([^>]+)</ErrorCode>!', $out_err[1], $out))
            $error['ErrorCode'] = $out[1];

        if (preg_match('!<ShortMessage[^>]*>([^>]+)</ShortMessage>!', $out_err[1], $out))
            $error['ShortMessage'] = $out[1];

        if (preg_match('!<LongMessage[^>]*>([^>]+)</LongMessage>!', $out_err[1], $out))
            $error['LongMessage'] = $out[1];

        $result['error'] = $error;
    }

    if (preg_match('!<Address[^>]*>(.+)</Address>!', $response, $out)) {
        $out_addr = $out[1];
        $address = array();

        if (preg_match('!<Name[^>]*>([^>]+)</Name>!', $out_addr, $out)) {
            $__name = explode(' ',$out[1], 2);
            $address['FirstName'] = $__name[0];
            $address['LastName'] = $__name[1];
            unset($__name);
        }

        if (preg_match('!<Street1[^>]*>([^>]+)</Street1>!', $out_addr, $out))
            $address['Street1'] = $out[1];
        if (preg_match('!<Street2[^>]*>([^>]+)</Street2>!', $out_addr, $out))
            $address['Street2'] = $out[1];

        if (preg_match('!<CityName[^>]*>([^>]+)</CityName>!', $out_addr, $out))
            $address['CityName'] = $out[1];

        if (preg_match('!<StateOrProvince[^>]*>([^>]+)</StateOrProvince>!', $out_addr, $out))
            $address['StateOrProvince'] = $out[1];

        if (preg_match('!<Country[^>]*>([^>]+)</Country>!', $out_addr, $out))
            $address['Country'] = $out[1];

        if (preg_match('!<PostalCode[^>]*>([^>]+)</PostalCode>!', $out_addr, $out))
            $address['PostalCode'] = $out[1];

        if (preg_match('!<AddressOwner[^>]*>([^>]+)</AddressOwner>!', $out_addr, $out))
            $address['AddressOwner'] = $out[1];

        if (preg_match('!<AddressStatus[^>]*>([^>]+)</AddressStatus>!', $out_addr, $out))
            $address['AddressStatus'] = $out[1];

        $result['address'] = $address;
    }

    return $result;
}

function cw_payment_paypalpro_get_methods($params, $return) {
    if ($return['processor'] == 'paypal_pro') {
        $return['ccinfo'] = true;
        $return['payment_type'] = 'cc';
/*
        $return['payment_type'] = cc|ch|dd
        $return['payment_template'] = |<template path>
        $return['ccinfo'] = true|false for payment_type == 'cc'
*/
    }
    return $return;
}

function cw_payment_paypalpro_run_processor($params, $return) {
    if ($params['payment_data']['processor'] == 'paypal_pro') {
        extract($params);

        global $config, $current_location;

        $cart = &cw_session_register('cart');
        $secure_oid = &cw_session_register('secure_oid');

        $pp_total = sprintf("%0.2f", $cart['info']['total']);

        $pp_final_action = ($config['paypal_pro']['use_preauth'] == 'Y') ? 'Authorization' : 'Sale';

        $pp_username = $config['paypal_pro']['api_access'];
        $pp_password = $config['paypal_pro']['api_password'];
        $pp_currency = $config['paypal_pro']['currency'];

        $pp_cert_file = $app_dir.'/'.$config['paypal_pro']['api_cert_path'];
        $pp_signature = $config['paypal_pro']['api_signature'];

        $notify_url = $current_location.'/payment/index.php?target=paypal_pro';

        $pp_use_cert = ($config['paypal_pro']['auth_type'] == 'C');
        $pp_signature_txt = $pp_use_cert ? "" : "<Signature>".$pp_signature."</Signature>";

        if ($config['paypal_pro']['test_mode'] == "N") {
            $pp_url = $config['paypal_pro']['auth_type'] == 'C' ? "https://api.paypal.com:443/2.0/" : "https://api-3t.paypal.com:443/2.0/";
            $pp_customer_url = "https://www.paypal.com";
        } 
        else {
            $pp_url = $config['paypal_pro']['auth_type'] == 'C' ? "https://api.sandbox.paypal.com:443/2.0/" : "https://api-aa.sandbox.paypal.com:443/2.0/";
            $pp_customer_url = "https://www.sandbox.paypal.com";
        }

        $avs_codes = array (
                "A" => "Address Address only (no ZIP)",
                "B" => "International 'A'. Address only (no ZIP)",
                "C" => "International 'N'",
                "D" => "International 'X'. Address and Postal Code",
                "E" => "Not allowed for MOTO (Internet/Phone) transactions",
                "F" => "UK-specific X Address and Postal Code",
                "G" => "Global Unavailable",
                "I" => "International Unavailable",
                "N" => "None",
                "P" => "Postal Code only (no Address)",
                "R" => "Retry",
                "S" => "Service not Supported",
                "U" => "Unavailable",
                "W" => "Nine-digit ZIP code (no Address)",
                "X" => "Exact match. Address and five-digit ZIP code",
                "Y" => "Address and five-digit ZIP",
                "Z" => "Five-digit ZIP code (no Address)"
        );

        $cvv_codes = array (
                "M" => "Match",
                "N" => "No match",
                "P" => "Not Processed",
                "S" => "Service not Supported",
                "U" => "Unavailable",
                "X" => "No response"
        );


        if (cw_payment_cc_is_visa($userinfo["card_number"])) $pp_cardtype = "Visa";
        if (cw_payment_cc_is_mc($userinfo["card_number"])) $pp_cardtype = "MasterCard";
        if (cw_payment_cc_is_dc($userinfo["card_number"])) $pp_cardtype = "Discover";
        if (cw_payment_cc_is_amex($userinfo["card_number"])) $pp_cardtype = "Amex";

        if (empty($pp_cardtype)) {
            $top_message = array( "content" => cw_get_langvar_by_name("txt_paypal_us_wrong_cc_type"), "type" => "E");
            cw_header_location($current_location."index.php?target=cart&mode=checkout");
        }

        $payer = $userinfo;
        foreach ($userinfo as $k=>$v) {
            if (is_array($v)) continue;
            $payer[$k] = htmlspecialchars($v);
        }

        $payer['main_address']['state'] = ($payer['main_address']['country'] == 'US' || $payer['main_address']['country'] == 'CA' || $payer['main_address']['state'] != "") ? $payer['main_address']['state'] : 'Other';
        $payer['current_address']['state'] = ($payer['current_address']['country'] == 'US' || $payer['current_address']['country'] == 'CA' || $payer['current_address']['state'] != "") ? $payer['current_address']['state'] : 'Other';

        $payer_ipaddress = cw_get_valid_ip($REMOTE_ADDR);

        $skey = cw_call('cw_payment_start');

        $pp_exp_month = (int)substr($userinfo["card_expire"],0,2);
        $pp_exp_year = (2000+substr($userinfo["card_expire"],2,2));

        $s_name = "";
        if (!empty($payer['current_address']['firstname']))
            $s_name = $payer['current_address']['firstname'];
        if (!empty($payer['current_address']['lastname']))
            $s_name .= (empty($s_name) ? "" : " ").$payer['current_address']['lastname'];

        if (!empty($s_name)) $s_name = substr($s_name, 0, 32);

        if (empty($payer['main_address']['firstname']))
            $payer['main_address']['firstname'] = "Unknown";
        if (empty($payer['main_address']['lastname'])) 
            $payer['main_address']['b_lastname'] = "Unknown";

        $oid = implode(',', $secure_oid);

        $request=<<<EOT
<?xml version="1.0" encoding="$pp_charset"?>
<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema">
  <soap:Header>
    <RequesterCredentials xmlns="urn:ebay:api:PayPalAPI">
      <Credentials xmlns="urn:ebay:apis:eBLBaseComponents">
        <Username>$pp_username</Username>
        <ebl:Password xmlns:ebl="urn:ebay:apis:eBLBaseComponents">$pp_password</ebl:Password>
        $pp_signature_txt
      </Credentials>
    </RequesterCredentials>
  </soap:Header>
  <soap:Body>
    <DoDirectPaymentReq xmlns="urn:ebay:api:PayPalAPI">
      <DoDirectPaymentRequest>
        <Version xmlns="urn:ebay:apis:eBLBaseComponents">1.00</Version>
        <DoDirectPaymentRequestDetails xmlns="urn:ebay:apis:eBLBaseComponents">
          <PaymentAction>$pp_final_action</PaymentAction>
          <PaymentDetails>
            <OrderTotal currencyID="$pp_currency">$pp_total</OrderTotal>
            <ButtonSource>DP_US</ButtonSource>
            <NotifyURL>$notify_url</NotifyURL>
            <ShipToAddress>
              <Name>$s_name</Name>
              <Street1>{$payer['current_address']['adress']}</Street1>
              <Street2>{$payer['current_address']['address_2']}</Street2>
              <CityName>{$payer['current_address']['city']}</CityName>
              <StateOrProvince>{$payer['current_address']['state']}</StateOrProvince>
              <PostalCode>{$payer['current_address']['zipcode']}</PostalCode>
              <Country>{$payer['current_address']['country']}</Country>
            </ShipToAddress>
            <InvoiceID>$skey</InvoiceID>
            <Custom>$oid</Custom>
          </PaymentDetails>
          <CreditCard>
            <CreditCardType>$pp_cardtype</CreditCardType>
            <CreditCardNumber>$payer[card_number]</CreditCardNumber>
            <ExpMonth>$pp_exp_month</ExpMonth>
            <ExpYear>$pp_exp_year</ExpYear>
            <CardOwner>
              <PayerStatus>verified</PayerStatus>
              <Payer>$payer[email]</Payer>
              <PayerName>
                <FirstName>{$payer['main_address']['firstname']}</FirstName>
                <LastName>{$payer['main_address']['lastname']}</LastName>
              </PayerName>
              <PayerCountry>{$payer['main_address']['country']}</PayerCountry>
              <Address>
                <Street1>{$payer['main_address']['address']}</Street1>
                <Street2>{$payer['main_address']['address_2']}</Street2>
                <CityName>{$payer['main_address']['city']}</CityName>
                <StateOrProvince>{$payer['main_address']['state']}</StateOrProvince>
                <Country>{$payer['main_address']['country']}</Country>
                <PostalCode>{$payer['main_address']['zipcode']}</PostalCode>
              </Address>
            </CardOwner>
            <CVV2>$payer[card_cvv2]</CVV2>
          </CreditCard>
          <IPAddress>$payer_ipaddress</IPAddress>
        </DoDirectPaymentRequestDetails>
      </DoDirectPaymentRequest>
    </DoDirectPaymentReq>
  </soap:Body>
</soap:Envelope>
EOT;

    $result = cw_func_call('cw_paypal_express_request', array('request' => $request));


    if ($result['success']) {
        $return['code'] = 1;
        $bill_message = 'Accepted';
    }
    else {
        $bill_message = 'Declined';
        $return['code'] = 2;
    }

    $additional_fields = array();
    foreach (array('TransactionID') as $add_field) {
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

    $return["billmes"] = $bill_message;

    if (isset($result['AVSCode']))
        $return['avsmes'] = (empty($avs_codes[$result['AVSCode']]) ? "Code: ".$result['AVSCode'] : $avs_codes[$result['AVSCode']]);

    if (isset($result['CVV2Code']))
        $return['cvvmes'] = (empty($cvv_codes[$result['CVV2Code']]) ? "Code: ".$result['CVV2Code'] : $cvv_codes[$result['CVV2Code']]);

    if ($pp_final_action != 'Sale')
        $return['is_preauth'] = true;

    $return['extra_order_data'] = array(
        "paypal_type" => "USDP",
        "paypal_txnid" => $result['TransactionID'],
        "capture_status" => $pp_final_action != 'Sale' ? 'A' : '',
        'transaction_amount' => $pp_total,
    );

    }
    return $return;
}
