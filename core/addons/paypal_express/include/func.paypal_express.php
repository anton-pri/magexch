<?php

function cw_paypal_express_request($params) {//$request, $regexp=false) {
    extract($params);
    global $config, $app_dir;

    if ($config['paypal_express']['test_mode'] == "N")
        $pp_url = $config['paypal_express']['auth_type'] == 'C' ? "https://api.paypal.com:443/2.0/" : "https://api-3t.paypal.com:443/2.0/";
    else
        $pp_url = $config['paypal_express']['auth_type'] == 'C' ? "https://api.sandbox.paypal.com:443/2.0/" : "https://api-3t.sandbox.paypal.com:443/2.0/";

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

function cw_paypal_express_detect_state($country, $state, &$err) {
    global $tables;

    if (empty($state))
        return '';

    $state = cw_addslashes($state);
    $country = cw_addslashes($country);

    $state_exists = (cw_query_first_cell("SELECT COUNT(*) FROM $tables[map_states] WHERE country_code = '$country' AND code = '$state'") > 0);
    if ($state_exists)
        return $state;

    $country_data = cw_query_first("SELECT code, display_states FROM $tables[map_countries] WHERE code = '$country' AND active = 'Y'");
    if (empty($country_data)) {
        $err = 1;
        return '';
    }

    if ($country_data['display_states'] != 'Y')
        return $state;

    $has_states = (cw_query_first_cell("SELECT COUNT(*) FROM $tables[map_states] WHERE country_code = '$country'") > 0);
    if (!$has_states)
        return $state;

    $state_code = cw_query_first_cell("SELECT code FROM $tables[map_states] WHERE state = '$state' AND country_code = '$country'");
    if (!empty($state_code))
        return $state_code;

    $err = 2;
    return cw_query_first_cell("SELECT code FROM $tables[mapp_states] WHERE country_code = '$country' LIMIT 1");
}

function cw_paypal_express_payment_get_label($params, $return) {
    if ($params['payment_id'] == -20) return 'PayPal Express';
    return $return;
}

function cw_paypal_express_payment_search($params, $return) {
    global $target;
    if ($target == 'docs_O') {
        $return[] = array(
            'payment_id' => -20,
            'title' => 'PayPal Express',
        );
    }
    return $return;
}

/*
 * Check if ItemTotal = sum(Amount*Quantity) and
 * OrderTotal = ItemTotal + ShippingTotal + TaxTotal + HandlingTotal
 */
function cw_paypal_get_totals($cart)
{
    if (empty($cart))
        return array();

    $pp_total = $cart['info']['total'];

    $result = array(
        'OrderTotal' => $pp_total,
        'ItemTotal' => $pp_total,
        'ShippingTotal' => 0,
        'TaxTotal' => 0,
        'HandlingTotal' => 0
    );

    if (isset($cart['info']['display_subtotal']))
        $result['ItemTotal'] = $cart['info']['display_subtotal'];
   
    if (isset($cart['info']['display_shipping_cost']))
        $result['ShippingTotal'] = $cart['info']['display_shipping_cost'];

    $delta = 0.0000000001;
    $result['TaxTotal'] = $result['OrderTotal'] - $result['ItemTotal'] - $result['ShippingTotal'];

    if (abs($result['TaxTotal']) < $delta)
        $result['TaxTotal'] = 0;

    settype($cart['products'], 'array');
    $products_total = 0;
    foreach ($cart['products'] as $p) {
        $price = $p["display_price"];
        $products_total += $price * $p['amount'];
    }

    if (
        $result['TaxTotal'] < 0
        || abs($products_total - $result['ItemTotal']) > $delta
    ) {
        return array();
    } else {
        return $result;
    }
}
