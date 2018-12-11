<?php
function cw_shipper_AP($weight, $customer_id, $address, $debug, $cart) {
	global $config, $tables;

	if ($config['Company']['country'] != 'AU' || !is_array($allowed_shipping_methods) || empty($allowed_shipping_methods)) {
		return false;
	}

	$stypes = array(
		1001 => "STANDARD",
		1002 => "EXPRESS",
		1003 => "AIR",
		1005 => "SEA",
		1006 => "ECI_D",
		1007 => "ECI_M",
		1008 => "EPI"
	);

	$ap_host = "drc.edeliver.com.au";
	$ap_url = "/ratecalc.asp";

	$zipcode = preg_replace("|[^\d\w]|i", "", $address['zipcode']);
	$post = "Pickup_Postcode=".$config['Company']['zipcode'] .
		"&Destination_Postcode=".$zipcode .
		"&Country=".$address['country'] .
		"&Weight=".cw_weight_in_grams($weight) .
		"&Length=".$options['param00'] .
		"&Width=".$options['param01'] .
		"&Height=".$options['param02'] .
		"&Quantity=1";

    if ($debug == "Y") {
    
        # Display debug info (header)
        print "<h1>Australia Post Debug Information</h1>";
		$is_display_debug = false;
	}

	foreach ($allowed_shipping_methods as $value) {
		if ($value['code'] != "APOST" || !isset($stypes[$value['service_code']]))
			continue;

		if (($address['country'] != 'AU' && $value['destination'] == "L") || ($address['country'] == 'AU' && $value['destination'] == "I")) {
			continue;
		}
		
		$md5_request = md5($post.$stypes[$value['service_code']]);
		if ((!cw_is_shipping_result_in_cache($md5_request)) ||  ($debug == "Y")){
			list ($header, $result) = cw_http_get_request ($ap_host, $ap_url, $post."&Service_type=".$stypes[$value['service_code']]);
			if (empty($result))
				continue;

			$return = array();
			if (preg_match_all("/^([^=]+)=(.*)$/Sm", $result, $preg)) {
				foreach($preg[1] as $k => $v) {
					$return[$v] = trim($preg[2][$k]);
				}
			}

			if ($return['err_msg'] == "OK") {
				$rates[$value['subcode']] = array(
					"methodid" => $value['subcode'],
					"rate" => $return['charge'],
					"shipping_time" => $return['days']
				);
				$cached_value = array(
					"methodid" => $value['subcode'],
					"rate" => $return['charge'],
					"shipping_time" => $return['days']
				);
				if ($debug != "Y")
					cw_save_shipping_result_to_cache($md5_request, $cached_value);
			}
		} else {
			$rates[] = cw_get_shipping_result_from_cache($md5_request);
		}
    }
}

