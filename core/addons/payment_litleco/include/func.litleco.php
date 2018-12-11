<?php

# vim: set ts=4 sw=4 sts=4 et:
# insert into cw_addons set addon='litleco', descr='LitleCo', active=1, status=0, parent='payment_system', version='0.1', orderby=0;
# insert into cw_config_categories set config_category_id=151, category='litleco', is_local=0;
# insert into cw_config set name='id', comment='ID', value='123', config_category_id=151,orderby=10,type='text';

# sandbox
#Desired Response                   Credit Card Number
#000: Approved                      4470330769941000
#010: Partially Approved            4658512425423010
#100: Proc. Network Unavailable     4886883711815100
#101: Issuer Unavailable            4215176886320101
#110: Insufficient Funds            4488282659650110

# to setup (to create litle_SDK_config.ini), you need to run "cd addons/litleco/include/litle/sdk; php Setup.php"

function cw_payment_litleco_get_methods($params, $return) {
    if ($return['processor'] == litleco_addon_name) {
        $return['ccinfo'] = true;
        $return['payment_type'] = 'cc';
    }
    return $return;
}

function cw_payment_litleco_run_processor($params, $return) {
    global $config, $xcart_dir, $REMOTE_ADDR;

    if ($params['payment_data']['processor'] != litleco_addon_name)
        return $return;

    extract($params);

    $cardtype = '';
    if (cw_payment_cc_is_visa($userinfo["card_number"]))
        $cardtype = 'VI';
    elseif (cw_payment_cc_is_mc($userinfo["card_number"]))
        $cardtype = 'MC';
    elseif (cw_payment_cc_is_dc($userinfo["card_number"]))
        $cardtype = 'DC';
    elseif (cw_payment_cc_is_amex($userinfo["card_number"]))
        $cardtype = 'AX';
    elseif (cw_payment_cc_is_diners($userinfo["card_number"]))
        $cardtype = 'DI';

    $cart = &cw_session_register('cart');
    $secure_oid = &cw_session_register('secure_oid');

    $post = array(
        'orderId' => $doc_ids[0],
        'amount' => 100*$cart['info']['total'],
        'id'=> $config[litleco_addon_name]['litleco_id'],
        'orderSource'=>'ecommerce',
        'billToAddress'=>array(
            'name' => $userinfo['main_address']['firstname']." ".$userinfo['main_address']['lastname'],
            'addressLine1' => $userinfo['main_address']['address'],
            'addressLine2' => $userinfo['main_address']['address_2'],
            'city' => $userinfo['main_address']['city'],
            'state' => $userinfo['main_address']['state'],
            'zip' => $userinfo['main_address']['zipcode'],
            'country' => $userinfo['main_address']['country'],
            'email' => $userinfo['email'],
            'phone' => $userinfo['main_address']['phone'],
         ),
        'shipToAddress'=>array(
            'name' => $userinfo['current_address']['firstname']." ".$userinfo['current_address']['lastname'],
            'addressLine1' => $userinfo['current_address']['address'],
            'addressLine2' => $userinfo['current_address']['address_2'],
            'city' => $userinfo['current_address']['city'],
            'state' => $userinfo['current_address']['state'],
            'zip' => $userinfo['current_address']['zipcode'],
            'country' => $userinfo['current_address']['country'],
         ),
        'card'=>array(
            'number' => $userinfo["card_number"],
            'expDate' => $userinfo['card_expire'],
            'cardValidationNum' => $userinfo['card_cvv2'],
            'type' => $cardtype,
        ),
        'user' => $config[litleco_addon_name]['litleco_user'],
        'password' => $config[litleco_addon_name]['ptleco_assword'],
        'merchantId' => $config[litleco_addon_name]['litleco_mid'],
        'url' => $config[litleco_addon_name]['litleco_test'] ? 'https://www.testlitle.com/sandbox/communicator/online' : 'https://payments.litle.com/vap/communicator/online',
        'tcp_ssl' => 1,
    );

    //cw_log_add('payment_litleco_test', array('post'=>$post, 'doc_ids'=>$doc_ids, 'cart'=>$cart));

    if(!function_exists('__autoload')) {
        function __autoload($class)
        {
            $class = '/'.strtr($class, array('\\'=>'/'));
            require_once realpath(dirname(__FILE__)) . $class . '.php';
        }
    }

    $initilaize = new litle\sdk\LitleOnlineRequest();
    $dom = $initilaize->saleRequest($post); # saleRequest/authorizationRequest

    # debug
    # print "<pre>";
    # print_r($post);
    # print $dom->saveXML();

    /*
    OK:
    <litleOnlineResponse xmlns="http://www.litle.com/schema" version="9.00" response="0" message="Valid Format">
      <authorizationResponse id="456" reportGroup="Default Report Group" customerId="">
        <litleTxnId>210204889228876000</litleTxnId>
        <orderId>296</orderId>
        <response>000</response>
        <responseTime>2014-12-16T18:21:52</responseTime>
        <message>Approved</message>
        <authCode>41044</authCode>
      </authorizationResponse>
    </litleOnlineResponse>

    NOK:
    <litleOnlineResponse xmlns="http://www.litle.com/schema" version="9.00" response="0" message="Valid Format">
      <authorizationResponse id="456" reportGroup="Default Report Group" customerId="">
        <litleTxnId>421271745975805101</litleTxnId>
        <orderId>296</orderId>
        <response>101</response>
        <responseTime>2014-12-16T18:23:36</responseTime>
        <message>Issuer Unavailable</message>
      </authorizationResponse>
    </litleOnlineResponse>
    */

    $res = array();
    foreach(array('litleTxnId','response','message','authCode') as $k)
        $res[$k] = litle\sdk\XmlParser::getNode($dom, $k);

    if(!$res["litleTxnId"]) # ERR
        return array(
            'code' => 2,
            'billmes' => 'Reason: Unknown error'
        );

    if($res["response"]=="000") # OK
        return array(
            'code' => 1,
            'billmes' => $res['message']." (TxnId: ".$res["litleTxnId"]."; AuthCode: ".$res["authCode"].")",
        );

    # NOK
    return array(
        'code' => 2,
        'billmes' => "Reason: ".$res['message']." (TxnId: ".$res["litleTxnId"].")",
        'hide_mess' => $res["response"],
    );
}

