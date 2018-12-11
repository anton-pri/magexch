<?php

function cw_payment_paypal_get_methods($params, $return) {
    if ($return['processor'] == 'paypal') {
        $return['ccinfo'] = false;
/*
        $return['payment_type'] = cc|ch|dd
        $return['payment_template'] = |<template path>
        $return['ccinfo'] = true|false for payment_type == 'cc'
*/
    }
    return $return;
}

function cw_payment_paypal_run_processor($params, $return) {
    if ($params['payment_data']['processor'] == 'paypal') {
        global $config, $tables, $current_location;

        $cart = &cw_session_register('cart');
        $skey = cw_call('cw_payment_start');

        cw_call('cw_payment_put_data', array($skey, array('state'=>'GO','doc_ids'=>$params['doc_ids'])));

        if ($params['userinfo']['current_address']['country'] == "US") $_customer_state = $params['userinfo']['current_address']['state'];
        else $_customer_state = $params['userinfo']['current_address']['statename'];

        $u_phone = preg_replace('![^\d]+!', '', $params['userinfo']['current_address']['phone']);

        $pp_ordr = $config['paypal']['prefix'].join("-",$params['doc_ids']);

        $fields = array(
            "charset" => 'UTF-8',
            "cmd" => "_ext-enter",
            "custom" => $skey,
            "invoice" => $pp_ordr,
            "redirect_cmd" => "_xclick",
            'item_name' => $config['paypal']['pp_payment_for'] . ' (Order #' . $pp_ordr . ')',
            "mrb" => "R-2JR83330TB370181P",
            "pal" => "RDGQCFJTT6Y6A",
            "rm" => "2",
            "email" => $params['userinfo']['email'],
            "first_name" => $params['userinfo']['current_address']['firstname'],
            "last_name" => $params['userinfo']['current_address']['firstname'],
            "country" => $params['userinfo']['current_address']['country'],
            "address1" => $params['userinfo']['current_address']['address'],
            "address2" => $params['userinfo']['current_address']['address_2'],
            "city" => $params['userinfo']['current_address']['city'],
            "zip" => $params['userinfo']['current_address']['zipcode'],
            "state" => $_customer_state,
            "day_phone_a" => substr($u_phone, -10, -7),
            "day_phone_b" => substr($u_phone, -7, -4),
            "day_phone_c" => substr($u_phone, -4),
            "night_phone_a" => substr($u_phone, -10, -7),
            "night_phone_b" => substr($u_phone, -7, -4),
            "night_phone_c" => substr($u_phone, -4),
            "business" => $config['paypal']['pp_account'],
            "item_name" => $config['paypal']['pp_payment_for'],
            "amount" => sprintf("%0.2f", $cart['info']['total']),
            "currency_code" => $config['paypal']['pp_currency'],
            // "return" => $current_location.'/index.php?target=order-message&doc_ids='.implode(',', $params['doc_ids']),
            "return" => $current_location.'/index.php?target=paypal&mode=success&secureid='.$skey,
            // "cancel_return" => $current_location.'/index.php?target=cart'
            'cancel_return' => $current_location.'/index.php?target=paypal&mode=cancel&secureid='.$skey,
            //'shopping_url'  => $current_location.'/index.php?target=paypal&mode=cancel&secureid='.$skey,
            "notify_url"    => $current_location.'/index.php?target=paypal',
            "bn" => "cartworks"
        );
        
        if ($config['paypal']['use_preauth'] == 'Y') {
            $fields['paymentaction'] = 'authorization';
        }
        
        cw_func_call('cw_payment_create_form', array('url' => 'https://'.($config['paypal']['test_mode'] == 'N' ? "www.paypal.com" : "www.sandbox.paypal.com").'/cgi-bin/webscr', 'fields' => $fields, 'name' => $params['payment_data']['title']));
        die();
    }
    return $return;
}

function cw_paypal_get_capture_orderid($txn_id) {
    global $tables;
    
    $res = cw_query_column("SELECT cs.doc_id 
    FROM $tables[docs_extras] as cs 
    INNER JOIN $tables[docs_extras] as txnid 
        ON txnid.khash = 'paypal_txnid' AND txnid.value = '$txn_id' 
    WHERE cs.khash = 'capture_status' AND cs.value = 'A' AND cs.doc_id = txnid.doc_id");

    return $res
        ? array_unique($res)
        : false;

}
