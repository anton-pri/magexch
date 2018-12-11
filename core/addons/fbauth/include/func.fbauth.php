<?php
function cw_fbauth_get_ssl_page($url) {
	$out = array('error' => '', 'result' => '');

	// @TODO remove check after cURL installed
	if  (!in_array('curl', get_loaded_extensions())) {
		return $out;
	}

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 	FALSE);
	curl_setopt($ch, CURLOPT_HEADER, 			false);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 	true);
	curl_setopt($ch, CURLOPT_URL, 				$url);
	curl_setopt($ch, CURLOPT_REFERER, 			$url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 	TRUE);
	$result = curl_exec($ch);

	if ($result === false) {
		$out['error'] = curl_error($ch);
	}
	curl_close($ch);

	$out['result'] = $result;

	return $out;
}

function cw_fbauth_user_login($user) {
	global $tables, $current_area;

	cw_load('crypt', 'user', 'mail');

	$user_data = cw_query_first("SELECT customer_id, email, password
									FROM $tables[customers] 
									WHERE oauth_uid='" . $user['id'] . "' AND oauth_type='F'  
										AND usertype='" . $current_area . "' 
										AND status='Y'");

	if (!empty($user_data)) {	// login user
		global $email, $password, $action;

		$email		= $user_data['email'];
		$password	= text_decrypt($user_data['password']);

		$action 	= 'login';
		cw_include('include/login.php');
	}
	else {						// create user
		$register = array();
		$register['email'] = $user['email'];

		if (strpos($user['email'], "proxymail.facebook.com") !== FALSE) {
			cw_header_location("index.php?target=fb_auth_get_email", TRUE);
		}

		$register['password'] = $register['password2'] = md5(uniqid('cw_', TRUE), TRUE);
		$register['usertype'] = $current_area;

		$partner_membership = &cw_session_register('partner_membership');
		$register['membership_id'] = $partner_membership;

        $customer_id = cw_user_create_profile($register);

		$identifiers = &cw_session_register('identifiers', array());
		$identifiers[$current_area] = array (
			'customer_id' => $customer_id,
		);

		$customer = array('oauth_uid' => $user['id']);
		cw_array2update('customers', $customer, "customer_id='$customer_id'");

		$address = array(
			'main' 		=> 1,
			'firstname' => $user['first_name'],
			'lastname' 	=> $user['last_name']
		);
		$additional_info = array(
			'sex' => ($user['gender'] == 'male' ? 1 : 0)
		);
		$userinfo = array(
			'addresses' 		=> $address,
			'additional_info' 	=> $additional_info
		);

		cw_user_update($userinfo, $customer_id, $customer_id);
        cw_user_send_modification_mail($customer_id, TRUE);
	}
}

function cw_fbauth_user_logout() {
	global $action;

	$fb_access_token 	= cw_session_register('fb_access_token');
	$fb_referer 		= cw_session_register('fb_referer');

	$referer_url = !empty($fb_referer) ? $fb_referer : 'index.php';
	cw_session_unregister('fb_referer');

	if ($fb_access_token) {
		$graph_url = "https://graph.facebook.com/me/permissions?method=delete&access_token=" 
						. $fb_access_token;
	
		$result = cw_fbauth_get_ssl_page($graph_url);

		if ($result['error']) {
			$top_message = array('type' => 'E', 'content' => $result['error']);
			cw_header_location($referer_url, TRUE);
		}

		$result = json_decode($result['result']);

		if ($result) {
			cw_session_unregister('fb_access_token');
		}
	}
}
