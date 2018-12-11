<?php

function cw_payment_paypal_adaptive_get_methods($params, $return) {
    if ($return['processor'] == 'paypal_adaptive') {
        $return['ccinfo'] = false;
/*
        $return['payment_type'] = cc|ch|dd
        $return['payment_template'] = |<template path>
        $return['ccinfo'] = true|false for payment_type == 'cc'
*/
    }
    return $return;
}

function cw_payment_paypal_adaptive_run_processor($params, $return) {
    if ($params['payment_data']['processor'] == 'paypal_adaptive') {
	extract($params);

        global $config, $current_location, $top_message;

        $cart = &cw_session_register('cart');
	$skey = cw_call('cw_payment_start');
        $secure_oid = &cw_session_register('secure_oid');
	$memo = implode(',', $params['doc_ids'])."!".rand(10,99);
	$debug = 0;

        $u_phone = preg_replace('![^\d]+!', '', $params['userinfo']['current_address']['phone']);

	cw_load('http'); # why so?..
        $pp_url = "https://svcs.".($config['paypal_adaptive']['test_mode'] == "Y" ? "sandbox." : "")."paypal.com/AdaptivePayments";

        $post_headers = array(
                "X-PAYPAL-SECURITY-USERID" => $config['paypal_adaptive']['api_access'],
                "X-PAYPAL-SECURITY-PASSWORD" => $config['paypal_adaptive']['api_password'],
                "X-PAYPAL-SECURITY-SIGNATURE" => $config['paypal_adaptive']['api_signature'],
                "X-PAYPAL-DEVICE-IPADDRESS" => $_SERVER["REMOTE_ADDR"],
                "X-PAYPAL-REQUEST-DATA-FORMAT" => "JSON",
                "X-PAYPAL-RESPONSE-DATA-FORMAT" => "JSON",
                "X-PAYPAL-APPLICATION-ID" => "APP-54K5660378319012U", # predefined by PP
        );

        $receivers = array();
        $recs = cw_seller_get_payment_shares($cart);
        $amount2admin = $recs[0]; unset($recs[0]); # default amount to admin
        $pp_type = $config['paypal_adaptive']['pp_method']=="C"; # true - chain/C; false - parallel/P
        $setdetails = array();
        if($recs)
        foreach($recs as $customer_id => $amount)
        if($amount>0)
        {
                $custom = cw_user_get_custom_fields($customer_id,0,'','field');
                $email = $custom["pp_account"]; # // rename??
                if($email)
                {
                        $receivers[] = array(
                                "primary" => false,
                                "amount" => $amount,
                                "email" => $email,
                                "invoiceId" =>  $memo,
                        );
/*
                        list($ship,$items,$products) = cw_ppadapt_products($customer_id,$amount,$products);
                        if(empty($items))
                        $setdetails[] = array(
                                "receiver" => array(
                                        "email" => $email
                                ),
                                "invoiceData" => array(
                                        "totalTax" => 0,
                                        "totalShipping" => $ship,
                                        "item" => $items,
                                ),
                        );
*/
                }
                if($pp_type || !$email)
                        $amount2admin += $amount;

        }
        if($amount2admin>0)
        {
                $receivers[] = array(
                        "primary" => $pp_type && !empty($receivers),
                        "amount" => $amount2admin,
                        "invoiceId" =>  $memo,
                        "email" => $config['paypal_adaptive']['email_acc'], # default admin pp acc
                );
/*
                list($ship,$items,$products) = cw_ppadapt_products($customer_id,$amount,$products);
                if(empty($items))
                $setdetails[] = array(
                        "receiver" => array(
                                "email" => $email
                        ),
                        "invoiceData" => array(
                                "totalTax" => 0,
                                "totalShipping" => $ship,
                                "item" => $items,
                        ),
                );
*/
        }

	$pp_cancel_url = $current_location.'/index.php?target=cart';
        $post = array(
                "actionType" => $pp_type ? "PAY_PRIMARY" : "PAY", # CREATE(?)
                "currencyCode" => $config['paypal_adaptive']['currency'],
                "feesPayer" => $config['paypal_adaptive']['fee_payer'],
                "receiverList" => array("receiver" => $receivers),
                "ipnNotificationUrl" => $current_location.'/index.php?target=paypal_adaptive',
                "memo" =>  $memo,
                "trackingId" =>  $skey,
                "returnUrl" => $current_location.'/index.php?target=order-message&doc_ids='.implode(',', $params['doc_ids']),
                "cancelUrl" => $pp_cancel_url,
                "requestEnvelope" => array(
                        "errorLanguage" => "en_US",
                        "detailLevel" => "ReturnAll",
                ),
        );

        list($headers, $response) = cw_https_request("POST", $pp_url."/Pay", array(json_encode($post)), "", "", "application/json", "", "", "", $post_headers);
	if($debug)
	{
		print "<pre>";
		print_r(array($recs,$pp_url,$post_headers,$post,json_encode($post),$headers,$response));
	}

        if ($headers == "0")
                $result = array(
                        'success' => false,
                        'error' => array('ShortMessage' => $response)
                );
        else
        {
                $ret = json_decode($response,1);

                if($ret["error"]) # ret["responseEnvelope"]["ack"] != "Success" // ( == "Failure")
                {
                        $err = array();
                        foreach($ret["error"] as $e)
                                $err[] = $e["severity"].": ".$e["message"];
                        $result = array(
                                'success' => false,
                                'error' => array('ShortMessage' => join(";",$err))
                        );
                }
                elseif($ret["payKey"]) # ret["responseEnvelope"]["ack"] == "Success"
                {
                        # It's ok. Now let's add options...
                        $post = array(
                                "payKey" => $ret["payKey"],
                                "requestEnvelope" => array(
                                        "errorLanguage" => "en_US",
                                        "detailLevel" => "ReturnAll",
                                ),
                        );

                        if(!empty($setdetails)) # should not fire...
                                $post["receiverOptions"] = $setdetails;

                        $post["senderOptions"] = array(
                                "addressOverride" => true,
                                "shippingAddress" => array(
/*
                                        "phone" => array(
                                                "type" => "MOBILE",
                                                "phoneNumber" => $params['userinfo']['current_address']['phone'],
                                                "phoneNumber" => $params['userinfo']['current_address']['phone'],
                                                "countryCode" => +7 or +1... no idea how to guess it...
                                        ),
*/
                                ),
                        );

                        foreach(array(
                                        "addresseeName" => $params['userinfo']['current_address']['firstname']." ".$params['userinfo']['current_address']['lastname'],
                                        "street1" => $params['userinfo']['current_address']['address'],
                                        "street2" => $params['userinfo']['current_address']['address_2'],
                                        "city" => $params['userinfo']['current_address']['city'],
                                        "state" => ($params['userinfo']['current_address']['country'] == 'US' || $params['userinfo']['current_address']['country'] == 'CA' || $params['userinfo']['current_address']['state'] != '') ? $params['userinfo']['current_address']['state'] : 'Other',
                                        "zip" => $params['userinfo']['current_address']['zipcode'],
                                        "country" => $params['userinfo']['current_address']['country'],
                                ) as $k => $v) # filter empty values
                        if($v)
                                $post["senderOptions"]["shippingAddress"][$k] = $v;

                        list($headers, $response) = cw_https_request("POST", $pp_url."/SetPaymentOptions", array(json_encode($post)), "", "", "application/json", "", "", "", $post_headers);
			if($debug)
			{
				print "<hr><hr>";print_r(array($post,json_decode($response,1),$headers));
			}

                        $result = array (
                                'success' => true,
                                'payKey' => $ret["payKey"]
                        );
                }
        }

        $pp_customer_url = "https://www.".($config['paypal_adaptive']['test_mode'] == "Y" ? "sandbox." : "")."paypal.com";
	if($debug)
	{
		print_r(array($ret,$result));
        	if($result['success'] && !empty($result['payKey']))
                	die("<h1>".$pp_customer_url.'/webscr?cmd=_ap-payment&paykey='.$result['payKey']."</h1>");
		die($result['error']['ShortMessage']);
	}

        if ($result['success'] && !empty($result['payKey']))
                cw_header_location($pp_customer_url.'/webscr?cmd=_ap-payment&paykey='.$result['payKey']);

        $top_message = array('type' => 'E', 'content' => $result['error']['ShortMessage']);
        cw_header_location($pp_cancel_url);
    }
    return $return;
}

/*
function cw_ppadapt_products($customer_id,$amount,$products)
{
        $items = array();
        $total = 0;
        if ($products)
        foreach($products as $pi => $product)
        if($product["owner_id"]==$customer_id || !$customer_id)
        {
                $items[] = array(
                        "name" => $product['product'],
                        "itemPrice" => $product['price'],
                        "itemCount" => $product['amount'],
                );
                $total += $product['price']*$product['amount'];
                unset($products[$pi]);
        }

        $ship = $amount - $total;

        if($ship<0)
        {
                $ship = 0;
                $items = array();
        }
        return array($ship,$items,$products);
}
*/


