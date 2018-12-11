<?php
cw_load('xml','http');

function cw_shipper_CPC($weight, $customer_id, $address, $debug, $cart) {
	global $config, $tables;

	if ($config['Company']['country'] != "CA" || empty($config['Shipping']['CPC_merchant_id']))
		return;

	$cpc_methods = array();
	foreach ($allowed_shipping_methods as $v) {
		if ($v['code']=="CPC"){
			$cpc_methods[] = $v;
		}
	}

	if (empty($cpc_methods)) return;


	$cp_merchant=$config['Shipping']['CPC_merchant_id'];
	$cp_language="en";
	$cp_qnty="1";
	$cp_packed = true;

	$cp_weight=round(cw_weight_in_grams($weight)/1000,3);
	if ($cp_weight<0.1) $cp_weight=0.1;

	$cp_description = $params['param00'];
	$cp_length = $params['param01'];
	$cp_width = $params['param02'];
	$cp_height = $params['param03'];

	$cp_currency_rate = $params['param05'];
	$cp_insured_value = $params['param04'];

	$cp_dest_country = $address['country'];
	$cp_dest_city = $address['city'];
	$cp_dest_zip = $address['zipcode'];
	$cp_dest_state = empty($address['state']) ? "NA" : $address['state'];

	$cp_orig_zip=$config['Company']['zipcode'];

	# Server DNS; if does not work, use 'cybervente.postescanada.ca:30000'
	$cp_host = "sellonline.canadapost.ca:30000";

	if (isset($cart['discounted_subtotal']))
		$itemsPrice = "<itemsPrice>$cart[discounted_subtotal]</itemsPrice>";
	elseif (!empty($cp_insured_value))
		$itemsPrice = "<itemsPrice>$cp_insured_value</itemsPrice>";
	else
		$itemsPrice = "";

	$cp_request =
		"<?xml version=\"1.0\" ?>\n".
		"<eparcel>".
		"<language>$cp_language</language>\n".
		"<ratesAndServicesRequest>\n".
		"  <merchantCPCID>$cp_merchant</merchantCPCID>\n".
		"  <fromPostalCode>$cp_orig_zip</fromPostalCode>\n".
		"  $itemsPrice\n".
		//"  <turnAroundTime> 24 </turnAroundTime>\n".
		"  <lineItems>\n".
		"    <item>\n".
		"      <quantity>$cp_qnty</quantity>\n".
		"      <weight>$cp_weight</weight>\n".
		"      <length>$cp_length</length>\n".
		"      <width>$cp_width</width>\n".
		"      <height>$cp_height</height>\n".
		"      <description>$cp_description</description>\n".
		($cp_packed?"      <readyToShip/>\n":"").
		"    </item>\n".
		"  </lineItems>\n".
		"  <city>$cp_dest_city</city>\n".
		"  <provOrState>$cp_dest_state</provOrState>\n".
		"  <country>$cp_dest_country</country>\n".
		"  <postalCode>$cp_dest_zip</postalCode>\n".
		"</ratesAndServicesRequest>\n".
		"</eparcel>";
	$md5_request = md5($cp_request);
	if ((!cw_is_shipping_result_in_cache($md5_request)) ||  ($debug == "Y")) {
		list($a,$result) = cw_http_post_request($cp_host, "/", $cp_request);

		$parse_errors = false;
		$options = array(
			'XML_OPTION_CASE_FOLDING' => 1,
			'XML_OPTION_TARGET_ENCODING' => 'ISO-8859-1'
		);

		$parsed = cw_xml_parse($result, $parse_errors, $options);

		$products =& cw_array_path($parsed, 'EPARCEL/RATESANDSERVICESRESPONSE/PRODUCT');

		if (is_array($products)) {
			foreach ($products as $product) {
				$pid = $product['@']['ID'];
				$rate = cw_array_path($product,'RATE/0/#');
				if ($pid === false || $rate === false) continue;

				$is_found = false;
				foreach ($cpc_methods as $v) {
					if ($v['service_code'] == $pid){
						$rates[] = array("methodid"=>$v['subcode'], "rate"=>$rate*$cp_currency_rate);
						$is_found = true;
						break;
					}
				}
				
				if (!empty($pid) && !$is_found) {
					$tmp_name = cw_array_path($product,"NAME/0/#");
					cw_add_new_smethod($tmp_name, "CPC", array("service_code" => $pid));
				}
			}
		}
		if ($debug != "Y") {
			cw_save_shipping_result_to_cache($md5_request, $rates);
		}

		$error_code = cw_array_path($parsed, 'EPARCEL/ERROR/STATUSCODE/0/#');
		if ($error_code !== false) {
			$error_msg  = cw_array_path($parsed, 'EPARCEL/ERROR/STATUSMESSAGE/0/#');
		}
	} else {
		$rates = cw_get_shipping_result_from_cache($md5_request);
	}
}
