<?php

function cw_payment_paypalpro_payflow_get_methods($params, $return) {
    if ($return['processor'] == 'paypal_pro_payflow') {
        $return['ccinfo'] = true;
        $return['payment_type'] = 'cc';
    }
    return $return;
}

function cw_payment_paypalpro_payflow_run_processor($params, $return) {
        global $config;
        global $pp_final_action;

    if ($params['payment_data']['processor'] == 'paypal_pro_payflow') {
        
        $pp_final_action = ($config['paypal_pro_payflow']['use_preauth'] == 'Y') ? 'Authorization' : 'Sale';

        $result = cw_func_call('cw_paypal_pro_payflow_request', $params);

        if ($result['status'] == 'success') {
            $return['code'] = 1;
            $bill_message = 'Accepted ('.$result['respmsg'].')';
        } 
        else {
            $return['code'] = 2;
            $return['hide_mess'] = true;
            $bill_message = "Reason: ".$result['error'];

            if (isset($result['error_code']) && in_array($result['error_code'], array('12','22','23','24')))
                $return['hide_mess'] = false;
            else
                $return['is_error'] = true;
        }

        $bill_message .= "\nPayPal method: PayPal Website Payments Pro PayFlow Edition / Direct Payment";

        $return["billmes"] = $bill_message;

        $return['avsmes'] = '';

        if (isset($result['avsaddr']))
            $return['avsmes'] .= "AVS address: ".(empty($avs_codes[$result['avsaddr']]) ? "Code: ".$result['avsaddr'] : $avs_codes[$result['avsaddr']])."; ";

        if (isset($result['avszip']))
            $return['avsmes'] .= "AVS zipcode: ".(empty($avs_codes[$result['avszip']]) ? "Code: ".$result['avszip'] : $avs_codes[$result['avszip']])."; ";

        if (isset($result['cvv2match']))
            $return['cvvmes'] = (empty($cvv_codes[$result['cvv2match']]) ? "Code: ".$result['cvv2match'] : $cvv_codes[$result['cvv2match']]);

        if ($pp_final_action != 'Sale')
            $return['is_preauth'] = true;


        $cart = &cw_session_register('cart');
            
        $return['extra_order_data'] = array(
            "pnref" => $result['pnref'],
            "paypal_type" => "UKDP",
            "capture_status" => $pp_final_action != 'Sale' ? 'A' : '',
            'transaction_amount' => $cart['info']['total'], 
        );

    }
    return $return;
}

function cw_paypal_pro_payflow_request($params) {
    global $REMOTE_ADDR;
    global $pp_final_action;

    extract($params);

    $is_extcard = false;
    $cardtype = 8;
    if (cw_payment_cc_is_visa($userinfo["card_number"]))
        $cardtype = 0;
    elseif (cw_payment_cc_is_mc($userinfo["card_number"]))
        $cardtype = 1;
    elseif (cw_payment_cc_is_dc($userinfo["card_number"]))
        $cardtype = 2;
    elseif (cw_payment_cc_is_amex($userinfo["card_number"]))
        $cardtype = 3;
    elseif (cw_payment_cc_is_diners($userinfo["card_number"]))
        $cardtype = 4;
    elseif (cw_payment_cc_is_jcb($userinfo["card_number"]))
        $cardtype = 5;
    elseif (cw_payment_cc_is_solo($userinfo["card_number"])) {
        $is_extcard = true;
        $cardtype = "S";
    } elseif (cw_payment_cc_is_switch($userinfo["card_number"])) {
        $is_extcard = true;
        $cardtype = 9;
    }

    $cart = &cw_session_register('cart');
    $secure_oid = &cw_session_register('secure_oid');

    $post = array(
        "tender" => "C",
        "trxtype" => ($pp_final_action == 'Sale' ? 'S' : 'A'),
        "acct" => $userinfo["card_number"],
        "accttype" => $cardtype,
        "amt" => $cart['info']['total'],
        "street" => $userinfo['main_address']['address'].($userinfo['main_address']['address_2']?$userinfo['main_address']['address_2']:''),
        "city" => $userinfo['main_address']['city'],
        "state" => $userinfo['main_address']['state'],
        "country" => $userinfo['main_address']['country'],
        "zip" => $userinfo['main_address']['zipcode'],
        "buttonsource" => '',
        "clientip" => cw_get_valid_ip($REMOTE_ADDR),
        "currency" => true,
        "custom" => implode(',', $secure_oid),
        "cvv2" => $userinfo['card_cvv2'],
        "email" => $userinfo['email'],
        "expdate" => $userinfo['card_expire'],
        "invnum" => $doc_ids[0],
#       "notifyurl" => true,
        "shiptostreet" => $userinfo['current_address']['address'].($userinfo['current_address']['address_2']?$userinfo['current_address']['address_2']:''),
        "shiptocity" => $userinfo['current_address']['city'],
        "shiptostate" => $userinfo['current_address']['state'],
        "shiptocountry" => $userinfo['current_address']['country'],
        "shiptozip" => $userinfo['current_address']['zipcode'],
        "firstname" => $userinfo['current_address']['firstname'],
        "lastname" => $userinfo['current_address']['lastname'],
    );

    if ($is_extcard) {
        $post['cardissue'] = $userinfo['card_issue_no'];
        $post['cardstart'] = $userinfo["card_valid_from"];
    }

    //cw_log_add('payflow_pro', array('post'=>$post));

    $res = cw_paypal_pro_payflow_do($post);

    $err = cw_paypal_pro_payflow_prepare_errors($res);
    if ($err)
        return $err;

    $res = $res[2];
    $res['status'] = 'success';

    return $res;
}

function cw_paypal_pro_payflow_do($post) {
    global $config;
    $str = array();

    $str['vendor'] = $config['paypal_pro_payflow']['vendor'];
    $str['partner'] = $config['paypal_pro_payflow']['partner'];
    $str['user'] = $config['paypal_pro_payflow']['user'];
    $str['pwd'] = $config['paypal_pro_payflow']['password'];

    $requestid = isset($post['requestid']) ? $post['requestid'] : md5(serialize($post).cw_core_microtime());
    cw_unset($post, "requestid");

    if ($config['paypal_pro_payflow']['currency'])
        $post['currency'] = $config['paypal_pro_payflow']['currency'];

    if (isset($post['invnum']))
        $post['invnum'] = $config['paypal_pro_payflow']['prefix'].$post['invnum'];

    $post['reqconfirmshipping'] = $config['paypal_pro_payflow']['is_confirmed_address'] == 'Y' ? 1 : 0;

    if ($config['paypal_pro_payflow']['page_style'])
        $post['page_style'] = $config['paypal_pro_payflow']['page_style'];

    if ($config['paypal_pro_payflow']['header_image_url'])
        $post['hdrimg'] = $config['paypal_pro_payflow']['header_image_url'];

    if (isset($post['notifyurl'])) {
        global $current_location;
        $post['notifyurl'] = $current_location.'/payment/index.php?target=paypal_pro-vendor';
    }

    $str = cw_array_merge($str, $post);

    $data = array();
    foreach($str as $k => $v)
        $data[] = strtoupper($k). "=" . $v;

    #$url = $config['paypal_pro_payflow']['test_mode'] ? "https://pilot-payflowpro.verisign.com:443/transaction" : "https://payflowpro.verisign.com:443/transaction";
    $url = ($config['paypal_pro_payflow']['test_mode'] == 'Y') ? "https://pilot-payflowpro.paypal.com:443/" : "https://payflowpro.paypal.com:443/";

    $headers = array(
        "X-VPS-REQUEST-ID" => $requestid,
        "X-VPS-VIT-CLIENT-CERTIFICATION-ID" => "7894b92104f04ffb4f38a8236ca48db3"
    );

    //cw_log_add('payflow_pro', array('url'=>$url, 'data'=>$data, 'headers'=>$headers));

# kornev, we are making the implode here - because we don't need the urlencode
    list($headers, $response) = cw_https_request("POST", $url, array(implode('&', $data)), "", "", "application/x-www-form-urlencoded", "", "", "", $headers);

    //cw_log_add('payflow_pro', array('headers'=>$headers, 'response'=>$response));

    if (empty($response))
        return array($headers, $response);

    $result = array();
    $tmp = array();
    parse_str($response, $tmp);
    if (empty($tmp) || !is_array($tmp))
        return array($headers, $response);

    foreach($tmp as $k => $v) {
        $result[strtolower($k)] = urldecode($v);
    }

    return array($headers, $response, $result);
}

function cw_paypal_pro_payflow_prepare_errors($res) {
    if (empty($res)) {
        return array(
            "status" => "error",
            "error" => "Unknown error"
        );

    } elseif (!isset($res[2])) {
        return array(
            "status" => "error",
            "error" => "Unknown error: ".$res[1]
        );

    } elseif (isset($res[2]['result']) && $res[2]['result'] > 0) {
        return array(
            "status" => "error",
            "error" => $res[2]['result']."# ".$res[2]['respmsg'],
            "error_code" => $res[2]['result']
        );
    }

    return false;
}

function cw_paypal_pro_payflow_do_capture($order) {
    global $tables;
    
    $is_paypal_pro_payflow = cw_query_first_cell("SELECT payment_id FROM $tables[payment_settings] WHERE payment_id = '{$order['info']['payment_id']}' AND processor='paypal_pro_payflow'");
    if (!$is_paypal_pro_payflow) return null; // todo: ignore this return in chain of hooks
    
    if (!cw_payment_is_authorized($order) || $order['info']['extras']['paypal_type']!='UKDP') {
        return error('CW: Payment is not authorized');
    }

    if ($order['info']['extras']['transaction_amount']>0 && $order['info']['total']/$order['info']['extras']['transaction_amount'] > 1.15) {
//        return error('CW: PayPal does not allow charge higher 115% of authorized amount');
    }
    
    $post = array(
        'tender'     => 'C',
        'trxtype'    => 'D',
        'origid'     => $order['info']['extras']['pnref'],
    );

    if ($order['info']['extras']['transaction_amount']>0 && $order['info']['extras']['transaction_amount']!=$order['info']['total']) {
        $post['amt'] = price_format($order['info']['total']);
    }

    $res = cw_paypal_pro_payflow_do($post); // list($headers, $response, $result) = $res
    $err = cw_paypal_pro_payflow_prepare_errors($res);
    if ($err)
        return error('PayFlow processor: '.$err['error']);

    $res = $res[2];
    $res['status'] = 'success';

    return $res;

}

function cw_paypal_pro_payflow_do_void($order) {
    global $tables;

    $is_paypal_pro_payflow = cw_query_first_cell("SELECT payment_id FROM $tables[payment_settings] WHERE payment_id = '{$order['info']['payment_id']}' AND processor='paypal_pro_payflow'");
    if (!$is_paypal_pro_payflow) return null; // todo: ignore this return in chain of hooks

    if (!cw_payment_is_authorized($order) || $order['info']['extras']['paypal_type']!='UKDP') {
        return error('Payment is not authorized');
    }
    
    $post = array(
        'tender'     => 'C',
        'trxtype'    => 'V',
        'origid'     => $order['info']['extras']['pnref'],
    );

    $res = cw_paypal_pro_payflow_do($post); // list($headers, $response, $result) = $res
    $err = cw_paypal_pro_payflow_prepare_errors($res);
    if ($err)
        return error($err['error']);

    $res = $res[2];
    $res['status'] = 'success';

    return $res;
    
}
