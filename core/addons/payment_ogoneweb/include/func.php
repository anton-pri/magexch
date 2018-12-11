<?php
namespace cw\payment_ogoneweb;

/** =============================
 ** Addon functions, API
 ** =============================
 **/



/** =============================
 ** Hooks
 ** =============================
 **/

/**
 * Return basic configuration of ogone payment method
 * 
 * @see POST hook for cw_payment_get_methods()
 * 
 */
function cw_payment_get_methods($params, $return) {
    if ($return['processor'] == addon_name) {
        $return['ccinfo'] = false;
    }
    return $return;
}

/**
 * Run payment processing
 * 
 * @see POST hook for cw_payment_run_processor()
 * 
 */
function cw_payment_run_processor($params, $return) {

    if ($params['payment_data']['processor'] != addon_name)  return $return;

    global $config, $current_location, $current_language, $cart, $APP_SESS_ID;;
    global $tables;

    $payment_data   = $params['payment_data'];
    $userinfo       = $params['userinfo'];
    $doc_ids        = $params['doc_ids'];

    $cart = &cw_session_register('cart');
    
    $pp_merch   = $config['payment_ogoneweb']['ogoneweb_pspid'];
    $pp_secret  = $config['payment_ogoneweb']['ogoneweb_sign'];
    $pp_curr    = $config['payment_ogoneweb']['ogoneweb_cur'];
    $pp_url    = ($config['payment_ogoneweb']['ogoneweb_test']=='Y') ?
        "https://secure.ogone.com:443/ncol/test/orderstandard.asp" :
        "https://secure.ogone.com:443/ncol/prod/orderstandard.asp";
    $pp_tp      = trim($config['payment_ogoneweb']['ogoneweb_tp']);

    $display_doc_ids = cw_query_column("SELECT display_doc_id FROM $tables[docs] WHERE doc_id in ('".implode("','", $doc_ids)."') order by doc_id asc");

    $ordr = trim($config['payment_ogoneweb']['ogoneweb_prefix']).join('-',$display_doc_ids);

    $skey = cw_call('cw_payment_start');
    cw_call('cw_payment_put_data', array($skey, array('state'=>'GO','doc_ids'=>$doc_ids)));
    
    $l = array(
        'en' => 'en_US',
        'fr' => 'fr_FR',
        'nl' => 'nl_NL',
        'it' => 'it_IT',
        'de' => 'de_DE',
        'es' => 'es_ES',
        'no' => 'no_NO'
    );
    
    $current_language = strtolower($current_language);
    
    $post = array(
        'PSPID'     => $pp_merch,
        'orderID'   => $ordr,
        'amount'    => (100*price_format($cart['info']['total'])),
        'currency'  => $pp_curr,
        'EMAIL'     => $userinfo['email'],
        'Owneraddress' => $userinfo['main_address']['address'],
        'OwnerZip'  => $userinfo['main_address']['zipcode'],
        'language'  => isset($l[$current_language]) ? $l[$current_language] : 'en_US'
    );
    
    if (!empty($pp_tp)) $post['tp'] = $pp_tp;

    $post['accepturl'] = $post['declineurl'] = $post['exceptionurl'] = $post['cancelurl'] = $current_location."/index.php?target=ogoneweb&secureid=$skey&mode=";
    $post['accepturl']      .= 'accept';
    $post['cancelurl']      .= 'cancel';
    $post['exceptionurl']   .= 'exception';
    $post['declineurl']     .= 'decline';

    // For security checking
    $post['COMPLUS'] = $skey;

    // Generate SHAsignature based on previous defined $post var
    $post['SHASign'] = ogone_generate_signature($post, 'associative_array', $pp_secret);

    if (constant('OGONE_DBG')) cw_log_add('ogone', array('request',$post), false);
        
    cw_func_call('cw_payment_create_form', array(
        'url' => $pp_url, 
        'fields' => $post, 
        'name' => $payment_data['title']
    ));
    
    exit();

}


function ogone_generate_signature($post, $format, $pp_secret) {

    if ($format == 'simple_array') {

        $params = array();
        foreach ($post as $val) {
            list($k, $v) = explode('=', $val, 2);
            $params[$k] = $v;
        }

    } else {
        $params = $post;

    }


    $_post = array();
    foreach ($params as $k => $v) {
        if (strtoupper($k) != 'SHASIGN' && $v != '') 
            $_post[strtoupper($k)] = $v;
    }

    ksort($_post);
    $signature = '';
    foreach ($_post as $_k => $_v) {
        $signature .= $_k . '=' . $_v . $pp_secret;
    }

    return strtoupper(sha1($signature));
}
