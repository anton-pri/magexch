<?php

/**
 * Subscription wrapper for Mailchimp service  (listGrowthHistory method)
 *
 * @param mixed $listid id of Mailchimp accout
 * @param mixed $apikey apikey of Mailchimp accout
 *
 * @return array
 * @see    ____func_see____
 * @since  1.0.0
 */
function cw_mailchimp_list_history($listid = false, $apikey = false) {
    global $config;

    if (!$apikey) {
        $apikey = $config['mailchimp_subscription']['mailchimp_apikey'];
    }

    if (!$listid) {
        $listid = $config['mailchimp_subscription']['mailchimp_id'];
    }

    $mailchimp_api = new MCAPI($apikey);
    $mailchimp_return = $mailchimp_api->listGrowthHistory($listid);

    if ($mailchimp_api->errorCode) {
        $mailchimp_response['Error_code']         = $mailchimp_api->errorCode;
        $mailchimp_response['Error_message']     = $mailchimp_api->errorMessage;
    }
    else {
        $mailchimp_response['Response']         = $mailchimp_return;
    }
    
    return $mailchimp_response;
}

/**
 * Subscription wrapper for Mailchimp service  (listSubscribe method)
 *
 * @param string $email_address E-mail
 * @param mixed  $listid        id of Mailchimp account
 * @param mixed  $apikey        apikey of Mailchimp account
 *
 * @return array
 * @see    ____func_see____
 * @since  1.0.0
 */
function cw_mailchimp_subscribe($userinfo, $listid = false, $apikey = false) {
    global $config;

    if (false === $apikey) {
        $apikey = $config['mailchimp_subscription']['mailchimp_apikey'];
    }

    if (false === $listid) {
        $listid = $config['mailchimp_subscription']['mailchimp_id'];
    }

    $mailchimp_api = new MCAPI($apikey);

    $mailchimp_merge_vars = array('');

    $mailchimp_merge_vars = array(
        'FName' => isset($userinfo['firstname']) ? $userinfo['firstname'] : 
            (isset($userinfo['main_address']['firstname']) ? $userinfo['main_address']['firstname'] :
            (isset($userinfo['current_address']['firstname']) ? $userinfo['current_address']['firstname'] : '')),
        'LName' => isset($userinfo['lastname']) ? $userinfo['lastname'] :
            (isset($userinfo['main_address']['lastname']) ? $userinfo['main_address']['lastname'] :
            (isset($userinfo['current_address']['lastname']) ? $userinfo['current_address']['lastname'] : '')),
        'email' => $userinfo['email'],
        'phone' => $userinfo['phone'],
        'address' => array(
                       'addr1'   => $userinfo['main_address']['title'],
                       'city'    => $userinfo['main_address']['city'],
                       'state'   => $userinfo['main_address']['statename'],
                       'zip'     => $userinfo['main_address']['zipcode'],
                       'country' => $userinfo['main_address']['countryname']
                     )
    );

    $mailchimp_return = $mailchimp_api->listSubscribe($listid, $userinfo['email'], $mailchimp_merge_vars);

    if ($mailchimp_api->errorCode) {
        $mailchimp_response['Error_code']         = $mailchimp_api->errorCode;
        $mailchimp_response['Error_message']     = $mailchimp_api->errorMessage;
    }
    else {
        $mailchimp_response['Response']         = $mailchimp_return;
    }

    return $mailchimp_response;
}

/**
 * Subscription wrapper for Mailchimp service (post for listSubscribe method)
 *
 * @param mixed  $params        params array from cw_func_call 'cw_payment_run_processor'
 *
 * @return array
 * @see    ____func_see____
 * @since  1.0.0
 */
function cw_post_mailchimp_subscribe($params) {
    global $config, $mailchimp_subscription;

    if (
    	!empty($config['mailchimp_subscription']['mailchimp_apikey'])
    	&& !empty($config['mailchimp_subscription']['mailchimp_id'])
    	&& !empty($mailchimp_subscription)
    ) {    
		$mailchimp_response = cw_mailchimp_subscribe($params['userinfo']);
    }
}
?>
