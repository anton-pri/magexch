<?php
cw_load('xml','http');

function cw_shipper($weight, $address, $debug="N", $cart=false) {
	global $allowed_shipping_methods, $rates;
	global $tables;
	global $config;

	$__intershipper_userinfo = $address;

	$rates = array ();

	$intershipper_countries = array (
		'IE' => 'IR',	# IRELAND
		'VA' => 'IT',	# ITALY AND VATICAN CITY STATE
		'FX' => 'FR',	# FRANCE
		'PR' => 'US'	# PUERTO RICO
	);

	#
	# Intershipper depends on XML parser (EXPAT extension)
	#
	if (test_expat() == "")
		return;

	if (empty($address)) {
        if ($config['General']['apply_default_country']=="Y" || $debug=="Y")
            $__intershipper_userinfo = cw_user_get_default_address();
    	else
		    return array();
	}

	$pounds=cw_weight_in_grams($weight)/453;
	$pounds=sprintf("%.2f",round((double)$pounds+0.00000000001,2));
	if($pounds<0.1) $pounds=0.1;

	$servername="www.intershipper.com";
	$scriptname="/Shipping/Intershipper/XML/v2.0/HTTP.jsp";

	$username=$config['Shipping']['intershipper_username'];
	$password=$config['Shipping']['intershipper_password'];

	$delivery=$params['param00'];
	$shipmethod=$params['param01'];

	$CO=$config['Company']['country'];
	$ZO=urlencode($config['Company']['zipcode']);

	$CD=$__intershipper_userinfo['country'];
	$ZD=urlencode($__intershipper_userinfo['zipcode']);

	if (!empty($intershipper_countries[$CD])) $CD = $intershipper_countries[$CD];
	if (!empty($intershipper_countries[$CO])) $CO = $intershipper_countries[$CO];

	$__intershipper_userinfo['country'] = $CD;
	$config['Company']['country'] = $CO;

	$length=(double)$params['param02'];
	$width=(double)$params['param03'];
	$height=(double)$params['param04'];
	$dunit=$params['param05'];

	$packaging=$params['param06'];
	$contents=$params['param07'];

	$codvalue=(double)$params['param08'];
	$insvalue=(double)$params['param09'];
	$queryid=substr(uniqid(rand()),0,15);
	$wunit=strtoupper(trim($config['General']['weight_symbol']));
	if (strlen($wunit) > 2) $wunit = substr($wunit,0,2);

	$allowed_shipping_methods = cw_query ("SELECT * FROM $tables[shipping] WHERE active=1");

	$carriers = cw_query_column("SELECT DISTINCT(code) FROM $tables[shipping] WHERE code<>'' AND intershipper_code!='' AND active=1");

	if (!$carriers || !$username || !$password)
		return array();

	$post[] = "Version=2.0.0.0";
	$post[] = "ShipmentID=1";
	$post[] = "QueryID=1";
	$post[] = "Username=$username";
	$post[] = "Password=$password";
	$post[] = "TotalClasses=4";
	$post[] = "ClassCode1=GND";
	$post[] = "ClassCode2=1DY";
	$post[] = "ClassCode3=2DY";
	$post[] = "ClassCode4=3DY";
	$post[] = "DeliveryType=$delivery";
	$post[] = "ShipMethod=$shipmethod";
	$post[] = "OriginationPostal=$ZO";
	$post[] = "OriginationCountry=$CO";
	$post[] = "DestinationPostal=$ZD";
	$post[] = "DestinationCountry=$CD";
	$post[] = "Currency=USD";				// Currently, supported only "USD". maxlen=3
	$post[] = "TotalPackages=1";
	$post[] = "BoxID1=box1";
	$post[] = "Weight1=$pounds";
	$post[] = "WeightUnit1=LB";
	$post[] = "Length1=$length";
	$post[] = "Width1=$width";
	$post[] = "Height1=$height";
	$post[] = "DimensionalUnit1=$dunit";	// DimensionalUnit	::= CM | IN
	$post[] = "Packaging1=$packaging";		// Packaging		::= BOX | ENV | LTR | TUB
	$post[] = "Contents1=$contents";
	$post[] = "Cod1=$codvalue";
	$post[] = "Insurance1=$insvalue";
	$post[] = "TotalCarriers=".count($carriers);

	foreach ($carriers as $k => $v) {
		if ($v == 'CPC')
			$v = 'CAN';
		$post[] = "CarrierCode".($k+1)."=".$v;
	}

	$query = join('&', $post);
	$md5_request = md5($query);
	if ((cw_is_shipping_result_in_cache($md5_request)) &&  ($debug != "Y")){
		return cw_get_shipping_result_from_cache($md5_request);
	}
	list($header, $result) = cw_http_get_request($servername, $scriptname, $query);

	$result = preg_replace("/^<\?xml\s+[^>]+>/s", "", trim($result));

	$parse_errors = false;
	$options = array(
		'XML_OPTION_CASE_FOLDING' => 1,
		'XML_OPTION_TARGET_ENCODING' => 'ISO-8859-1'
	);

	$parsed = cw_xml_parse($result, $parse_errors, $options);

	$destination = ($__intershipper_userinfo['country']==$config['Company']['country'])?"L":"I";

	$packages =& cw_array_path($parsed, 'SHIPMENT/PACKAGE');
	if (is_array($packages)) {
		$rates = array();
		foreach ($packages as $pkginfo) {
			if (empty($pkginfo['#']) || !is_array($pkginfo['#']))
				continue;

			foreach ($pkginfo['#']['QUOTE'] as $quote) {
				$carrier = cw_array_path($quote, 'CARRIER/CODE/0/#');
				if ($carrier == 'USP')
					$carrier = 'USPS';

				$service = cw_array_path($quote, 'SERVICE/NAME/0/#');
				$sn = cw_array_path($quote, 'SERVICE/CODE/0/#');
				$rate = cw_array_path($quote, 'RATE/AMOUNT/0/#') / 100.0;

				if (!$carrier || !($service || $sn) || !$rate) {
					continue;
				}

				$saved = -1;

				foreach ($allowed_shipping_methods as $sk=>$sv) {
					if ($sv['code'] != $carrier || $sv['destination'] != $destination)
						continue;

					if ($sv['intershipper_code'] == 'CPC')
						$sv['intershipper_code'] = 'CAN';

					if ((!$sn || $sv['intershipper_code'] != $sn) && (!$service || !stristr($sv['shipping'],$service)))
						continue;

					# Suppressing duplicates
					if ($saved < 0 || strlen($allowed_shipping_methods[$saved]['shipping']) > strlen($sv['shipping']))
						$saved = $sk;
				}

				if ($saved >= 0)
					$rates[$allowed_shipping_methods[$saved]['subcode']] = $rate;
			}
		}

		if (!empty($rates)) {
			foreach ($rates as $k=>$v) {
				$rates[$k]= array ("methodid"=>$k, "rate"=>$v);
			}
			if ($debug != "Y")
				cw_save_shipping_result_to_cache($md5_request, $rates);
		}
	}

	return $rates;
}
