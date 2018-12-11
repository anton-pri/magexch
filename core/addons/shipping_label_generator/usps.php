<?php
cw_load('xml','http');

if (empty($order) || empty($order['products']))
	return false;

if (empty($option)) {
	$option = 3;
}

if (empty($from)) {
	$from = array(
		's_address' => $config['Company']['address'],
		's_city' => $config['Company']['city'],
		's_state' => $config['Company']['state'],
		's_country' => $config['Company']['country'],
		's_zipcode' => $config['Company']['zipcode'],
		's_firstname' => $config['Company']['company_name'],
		'company' => $config['Company']['company_name'],
		'phone' => $config['Company']['company_phone']);
}
# !from first name value equals from firm value
$service_type = cw_usps_check_shipping_id($order['order']['shipping_id']);
if (!$service_type) {
	$response['result'] = "wrong_shipping_id";
}
if ($service_type == "Error") {
	$response['result'] = 'error';
	$response['error'] = cw_get_langvar_by_name("lbl_shipping_label_error",false,false,true);
}

# Request shipping label
if (empty($response)) {
	$image_type = $config['shipping_label_generator']['usps_image_type'];

	$to = $order['userinfo'];
	$user_id = $config['shipping_label_generator']['usps_userid'];
	if(!empty($user_id)) {
		$usps_server = "https://secure.shippingapis.com/ShippingAPI.dll";
		if ($service_type == "ExpressMail") {
			if ($config['shipping_label_generator']['usps_sample_mode'] == 'Y') {
				$head = "ExpressMailLabelCertify";
				$api = $head;
			} else {
				$head = "ExpressMailLabel";
				$api = $head;
			}
		} elseif ($service_type == "GlobalExpress") {
			if ($config['shipping_label_generator']['usps_sample_mode'] == 'Y') {
				$head = "GlobalLabelExpressCertify";
                $api = $head;
			} else {
				$head = "GlobalLabelExpress";
				$api = $head;
			}
		} elseif ($service_type == "GlobalPriority") {
			if ($config['shipping_label_generator']['usps_sample_mode'] == 'Y') {
				$head = "GlobalLabelPriorityCertify";
				$api = $head;
			} else {
				$head = "GlobalLabelPriority";
				$api = $head;
			}
		} elseif ($service_type == "GlobalAir") {
			if ($config['shipping_label_generator']['usps_sample_mode'] == 'Y') {
				$head = "GlobalLabelAirmailCertify";
				$api = $head;
			} else {
				$head = "GlobalLabelAirmail";
				$api = $head;
			}
		} else  {
			if ($config['shipping_label_generator']['usps_sample_mode'] == 'Y') {
				$head = "DelivConfirmCertifyV3.0";
				$api = "DelivConfirmCertifyV3";
			} else {
				$head = "DeliveryConfirmationV3.0";
				$api = "DeliveryConfirmationV3";
			}
		}
	} else {
		return;
	}

	$weight_in_ounces = 0;
	foreach ($order['products'] as $product) {
		$weight_in_ounces += $product['weight']*$product['amount'];
	}

	$weight_in_ounces = ceil(round(cw_weight_in_grams($weight)/28.35,3));
	if ($weight_in_ounces < 1);
		$weight_in_ounces = 1;

	list($from['s_address1'], $from['s_address2']) = explode("\n", $from['s_address']);
	list($to['s_address1'], $to['s_address2']) = explode("\n", $to['s_address']);
	if (empty($from['s_address2'])) {
		$from['s_address2'] = $from['s_address1'];
		$from['s_address1'] = '';
	}
	if(empty($to['s_address2'])) {
		$to['s_address2'] = $to['s_address1'];
		$to['s_address1'] = '';
	}


	$from_fname = $from['s_firstname'];
	$from_lname = $from['s_lastname'];
	$from_firm = $from['company'];
	$from_addr1 = $from['s_address1'];
	$from_addr2 = $from['s_address2'];
	$from_city = $from['s_city'];
	$from_state = $from['s_state'];
	$from_zip4 = $from['s_zipcode4'];
	$from_zip5 = $from['s_zipcode'];
	$from_phone = preg_replace("|[^\d]|i","",$from['phone']);
	$to_fname = $to['s_firstname'];
	$to_lname = $to['s_lastname'];
	$to_firm = $to['company'];
	$to_addr1 = $to['s_address1'];
	$to_addr2 = $to['s_address2'];
	$to_phone = $to['phone'];
	$to_country = $to['s_countryname'];
	$to_city = $to['s_city'];
	$to_state = $to['s_state'];
	$to_zip4 = $to['s_zipcode4'];
	$to_zip5 = $to['s_zipcode'];
	$value = $to['total'];
	$description = "Order #".$to['doc_id'];
	$xml_head = $head."Request";
	if ($service_type == "ExpressMail") {
		$image_type = "PDF"; //PDF only
		$query=<<<EOT
<$xml_head USERID="$user_id">
<Option/>
<EMCAAccount/>
<EMCAPassword/>
<ImageParameters/>
<FromFirstName>$from_fname</FromFirstName>
<FromLastName>$from_lname</FromLastName>
<FromFirm>$from_firm</FromFirm>
<FromAddress1>$from_addr1</FromAddress1>
<FromAddress2>$from_addr2</FromAddress2>
<FromCity>$from_city</FromCity>
<FromState>$from_state</FromState>
<FromZip5>$from_zip5</FromZip5>
<FromZip4>$from_zip4</FromZip4>
<FromPhone>$from_phone</FromPhone>
<ToFirstName>$to_fname</ToFirstName>
<ToLastName>$to_lname</ToLastName>
<ToFirm/>
<ToAddress1>$to_addr1</ToAddress1>
<ToAddress2>$to_addr2</ToAddress2>
<ToCity>$to_city</ToCity>
<ToState>$to_state</ToState>
<ToZip5>$to_zip5</ToZip5>
<ToZip4>$to_zip4</ToZip4>
<ToPhone></ToPhone>
<WeightInOunces/>
<FlatRate>TRUE</FlatRate>
<NoWeekend>TRUE</NoWeekend>
<POZipCode/>
<ImageType>$image_type</ImageType>
</$xml_head>
EOT;
	} elseif ($service_type == "GlobalAir") {
		$query=<<<EOT
<$xml_head USERID="$user_id">
<FromFirstName>$from_fname</FromFirstName>
<FromLastName>$from_lname</FromLastName>
<FromFirm>$from_firm</FromFirm>
<FromAddress1>$from_addr1</FromAddress1>
<FromAddress2>$from_addr2</FromAddress2>
<FromCity>$from_city</FromCity>
<FromState>$from_state</FromState>
<FromZip5>$from_zip5</FromZip5>
<FromPhone>$from_phone</FromPhone>
<ToName>$to_fname $to_lname</ToName>
<ToFirm/>
<ToAddress1>$to_addr1</ToAddress1>
<ToAddress2>$to_addr2</ToAddress2>
<ToCity>$to_city</ToCity>
<ToProvince>$to_state</ToProvince>
<ToCountry>$to_country</ToCountry>
<ToPostalCode>$to_zip5</ToPostalCode>
<ToPhone>$to_phone</ToPhone>
<ShippingContents>
<ItemDetail>
<Description>$description</Description>
<Quantity>1</Quantity>
<Value>$value</Value>
<NetPounds/>
<NetOunces>$weight_in_ounces</NetOunces>
<HSTariffNumber/>
<CountryOfOrigin/>
</ItemDetail>
</ShippingContents>
<GrossPounds></GrossPounds>
<GrossOunces>$weight_in_ounces</GrossOunces>
<ContentType>Other</ContentType>
<ContentTypeOther>$description</ContentTypeOther>
<ImageType>$image_type</ImageType>
</$xml_head>
EOT;
	} elseif ($service_type == "GlobalExpress") {
		$query=<<<EOT
<$xml_head USERID="$user_id">
<FromFirstName>$from_fname</FromFirstName>
<FromLastName>$from_lname</FromLastName>
<FromFirm>$from_firm</FromFirm>
<FromAddress1>$from_addr1</FromAddress1> 
<FromAddress2>$from_addr2</FromAddress2>
<FromCity>$from_city</FromCity>
<FromState>$from_state</FromState>
<FromZip5>$from_zip5</FromZip5>
<FromPhone>$from_phone</FromPhone>
<ToName>$to_fname $to_lname</ToName>
<ToFirm/>
<ToAddress1>$to_addr1</ToAddress1>
<ToAddress2>$to_addr2</ToAddress2>
<ToCity>$to_city</ToCity>
<ToProvince>$to_state</ToProvince>
<ToCountry>$to_country</ToCountry>
<ToPostalCode>$to_zip5</ToPostalCode>
<ToPhone>$to_phone</ToPhone>
<ShippingContents>
<ItemDetail>
<Description>$description</Description>
<Quantity>1</Quantity>
<Value>$value</Value>
<NetPounds/>
<NetOunces>$weight_in_ounces</NetOunces>
<HSTariffNumber/>
<CountryOfOrigin/>
</ItemDetail>
</ShippingContents>
<GrossPounds></GrossPounds>
<GrossOunces>$weight_in_ounces</GrossOunces>
<ContentType>Other</ContentType>
<ContentTypeOther>$description</ContentTypeOther>
<ImageType>$image_type</ImageType>
</$xml_head>
EOT;
	} elseif ($service_type == "GlobalPriority") {
		$query=<<<EOT
<$xml_head USERID="$user_id">
<Option/>
<ImageParameters/>
<FromFirstName>$from_fname</FromFirstName>
<FromLastName>$from_lname</FromLastName>
<FromFirm>$from_firm</FromFirm>
<FromAddress1>$from_addr1</FromAddress1>
<FromAddress2>$from_addr2</FromAddress2>
<FromCity>$from_city</FromCity>
<FromState>$from_city</FromState>
<FromZip5>$from_zip5</FromZip5>
<FromPhone>$from_phone</FromPhone>
<ToName>$to_fname $to_lname</ToName>
<ToFirm/>
<ToAddress1>$to_addr1</ToAddress1>
<ToAddress2>$to_addr2</ToAddress2>
<ToCity>$to_city</ToCity>
<ToProvince>$to_state</ToProvince>
<ToCountry>$to_country</ToCountry>
<ToPostalCode>$to_zip5</ToPostalCode>
<ToPhone>$to_phone</ToPhone>
<ShippingContents>
<ItemDetail>
<Description>$description</Description>
<Quantity>1</Quantity>
<Value>$value</Value>
<NetPounds/>
<NetOunces>$weight_in_ounces</NetOunces>
<HSTariffNumber/>
<CountryOfOrigin/>
</ItemDetail>
</ShippingContents>
<GrossPounds></GrossPounds>
<GrossOunces>$weight_in_ounces</GrossOunces>
<ContentType>Other</ContentType>
<ContentTypeOther>$description</ContentTypeOther>
<ImageType>$image_type</ImageType>
</$xml_head>
EOT;
	} else {
		$query=<<<EOT
<$xml_head USERID="$user_id">	
<Option>1</Option>
<ImageParameters></ImageParameters>
<FromName>$from_fname</FromName>
<FromFirm>$from_firm</FromFirm>
<FromAddress1>$from_addr1</FromAddress1>
<FromAddress2>$from_addr2</FromAddress2>
<FromCity>$from_city</FromCity>
<FromState>$from_state</FromState>
<FromZip5>$from_zip5</FromZip5>
<FromZip4>$from_zip4</FromZip4>
<ToName>$to_fname $to_lname</ToName>
<ToFirm>$to_firm</ToFirm>
<ToAddress1>$to_addr1</ToAddress1>
<ToAddress2>$to_addr2</ToAddress2>
<ToCity>$to_city</ToCity>
<ToState>$to_state</ToState>
<ToZip5>$to_zip5</ToZip5>
<ToZip4>$to_zip4</ToZip4>
<WeightInOunces>$weight_in_ounces</WeightInOunces>
<ServiceType>$service_type</ServiceType>
<SeparateReceiptPage></SeparateReceiptPage>
<POZipCode></POZipCode>
<ImageType>$image_type</ImageType>
<LabelDate></LabelDate>
<CustomerRefNo></CustomerRefNo>
<AddressServiceRequested></AddressServiceRequested>
<SenderName></SenderName>
<SenderEMail></SenderEMail>
<RecipientName></RecipientName>
<RecipientEMail></RecipientEMail>
</$xml_head>
EOT;
}
	$query_prepared = urlencode($query);
	list($header, $return) = cw_https_request("GET", $usps_server."?API=$api&XML=".$query_prepared);

	if (defined('USPS_DEBUG'))
		cw_log_add('usps', $query . "\n\n" . $header . "\n\n" . $return);

	$response = array("result" => 'http_error');
	$res = cw_xml2hash($return);
	if ($res['Error']) {
		$response['result'] = 'error';
		$response['error'] = $res['Error']['Description'];
	}
	elseif ($res[$head.'Response']) {
		$response['result'] = 'ok';
		if (($service_type == "GlobalAir") || ($service_type == "GlobalExpress") || ($service_type == "GlobalPriority")) {
			$response['image'] = base64_decode(str_replace(array("\n"), array(""), $res[$head.'Response']['LabelImage']));	
		} elseif ($service_type == "ExpressMail") {
			$response['image'] = base64_decode(str_replace(array("\n"), array(""),$res[$head.'Response']['EMLabel']));
		} else {
			$response['image'] = base64_decode(str_replace(array("\n"), array(""), $res[$head.'Response']['DeliveryConfirmationLabel']));
		}
		
		switch($image_type) {
			case 'TIF': $response['image_type'] = "image/tiff"; break;
			case 'JPG': $response['image_type'] = "image/jpeg"; break;
			case 'PDF': $response['image_type'] = "application/pdf"; break;
			case 'GIF': $response['image_type'] = "image/gif"; break;
		}

		$response['dcn'] = $res[$head.'Response']['DeliveryConfirmationNumber'];
		}
		else {
			$response['result'] = 'wrong_response';
		}

		unset($res);

	if($response['result'] != 'ok') {
		$response['response'] = $host;
		$response['header'] = $header;
		$response['return'] = $return;
		$response['request'] = $host;
	}
}
?>
