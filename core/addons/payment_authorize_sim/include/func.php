<?php

function cw_payment_authorize_sim_get_methods($params, $return) {
	global $smarty, $userinfo;

    if ($return['processor'] == authorize_sim_addon_name) {
        $return['ccinfo'] = false;
        $return['code'] = 3;
        $return['payment_template'] = 'addons/' . authorize_sim_addon_name . '/payment_template.tpl';

        $card_types = array();
        foreach (array('Visa', 'MasterCard', 'American Express', 'Discover', 'Diners Club', 'JCB') as $ct) {
            $card_types[] = array('code'=>$ct, 'type'=>$ct);
        }
        $smarty->assign('card_types', $card_types);
//        $smarty->assign('userinfo', $userinfo);
    }
    return $return;
}

function cw_payment_authorize_sim_run_processor($params, $return) {

	if ($params['payment_data']['processor'] == authorize_sim_addon_name) {
		global $config, $tables, $current_location, $cart, $APP_SESS_ID;;

		$addon_name = str_replace("-", "_", authorize_sim_addon_name);

		$asim_api_login_id 		= $config[$addon_name]['asim_api_login_id'];
		$asim_transaction_key	= $config[$addon_name]['asim_transaction_key'];
		$asim_md5_hash 			= $config[$addon_name]['asim_md5_hash'];
		$asim_mode 				= $config[$addon_name]['asim_test_live_mode'];
		$asim_currency 			= $config[$addon_name]['asim_currency'];
		$asim_prefix 			= intval($config[$addon_name]['asim_prefix']);
		
		if (empty($asim_api_login_id) || empty($asim_transaction_key)) {
			$top_message = array("content" => "Enter your merchant credentials on settings page before running the payment.", "type" => "E");
			cw_header_location($current_location . "index.php?target=cart&mode=checkout");
        }

        define("AUTHORIZENET_API_LOGIN_ID", $asim_api_login_id);
		define("AUTHORIZENET_TRANSACTION_KEY", $asim_transaction_key);
		define("AUTHORIZENET_SANDBOX", ($asim_mode == "live" ? FALSE : TRUE));
		define("AUTHORIZENET_MD5_SETTING", $asim_md5_hash);

		$payment_data 	= $params['payment_data'];
		$userinfo 		= $params['userinfo'];
		$doc_ids 		= $params['doc_ids'];

		$cart = &cw_session_register('cart');
        $time = time();

        $asim_amount        = price_format($cart['info']['total']);
        $asim_fp_sequence   = $asim_prefix . $time;
        $asim_fp_hash       = AuthorizeNetDPM::getFingerprint(
        							$asim_api_login_id, 
        							$asim_transaction_key, 
        							$asim_amount, 
        							$asim_fp_sequence, 
        							$time
        					  );
        $asim_fp_timestamp  = $time;
        $asim_relay_url     = $current_location . '/index.php?target=' . authorize_sim_addon_target;

        // save $APP_SESS_ID
		$unique_id = strtolower(md5($asim_md5_hash . $asim_api_login_id . $userinfo['email']));
		db_query("INSERT INTO $tables[payment_data] (ref_id, session_id) VALUES ('$unique_id', '$APP_SESS_ID')");

        $card_expire_Month	= intval($_POST['card_expire_Month']);
        $card_expire_Year	= intval($_POST['card_expire_Year']);

        cw_func_call('cw_payment_create_form', 
			array(
				'url' => AUTHORIZENET_SANDBOX ? AuthorizeNetDPM::SANDBOX_URL : AuthorizeNetDPM::LIVE_URL, 
				'fields' => array(
					'x_relay_response'	=> "FALSE",
					'x_version'			=> "3.1",
					'x_delim_char'		=> ",",
					'x_delim_data'		=> "TRUE",
            		'x_amount'			=> $asim_amount,
            		'x_fp_sequence'		=> $asim_fp_sequence,
            		'x_fp_hash'			=> $asim_fp_hash,
            		'x_fp_timestamp'	=> $time,
            		'x_relay_response'	=> "TRUE",
            		'x_relay_url'		=> $asim_relay_url,
            		'x_login'			=> $asim_api_login_id,
            		'x_card_num'		=> $_POST['card_number'],
            		'x_exp_date'		=> date("m/y", mktime(0, 0, 0, $card_expire_Month, 1, $card_expire_Year)),
            		'x_card_code'		=> $_POST['card_cvv2'],
            		'x_first_name'		=> $_POST['first_name'],
            		'x_last_name'		=> $_POST['last_name'],
            		'x_address'			=> $_POST['address'],
            		'x_city'			=> $_POST['city'],
            		'x_state'			=> $_POST['state'],
            		'x_zip'				=> $_POST['zipcode'],
            		'x_country'			=> $_POST['country'],
            		'x_email'			=> $userinfo['email']
	            ), 
	            'name' => $payment_data['title']
			)
		);
        exit();
    }
    return $return;
}
