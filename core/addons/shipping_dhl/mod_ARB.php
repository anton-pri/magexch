<?php
$dhl_ext_countries = array();
if (
	$address['country'] != 'US' &&
	!empty($config['Shipping']['ARB_id']) &&
	!empty($config['Shipping']['ARB_password']) &&
	!empty($config['Shipping']['ARB_account'])
) {
	switch ($address['country']) {
		case "AN":
			$dhl_ext_countries = array(
				"AN-ST. MAARTEN",
				"AN-BONAIRE",
				"AN-CURACAO",
				"ST. EUSTATIUS"
			);
			break;

		case "GB":
			$dhl_ext_countries = array(
				"UK-ENGLAND",
				"UK-NORTHERN IRELAND",
				"UK-SCOTLAND",
				"UK-WALES",
				"JERSEY",
			);
			break;

		case "IL":
			$dhl_ext_countries = array(
				"IL-GAZA STRIP",
				"IL-WEST BANK"
			);
			break;

		case "GP":
			$dhl_ext_countries = array(
				"ST. BARTHELEMY"
			);
			break;

		case "ES":
			$dhl_ext_countries = array(
				"CANARY ISLANDS"
			);
			break;

	}
}

function cw_shipper_ARB($weight, $customer_id, $address, $debug, $cart) {
	global $config, $tables;

	$airborne_account = &cw_session_register("airborne_account");

	$ARB_FOUND = false;
	if (is_array($allowed_shipping_methods)) {
		foreach ($allowed_shipping_methods as $key=>$value) {
			if ($value['code'] == "ARB") {
				$ARB_FOUND = true;
				break;
			}
		}
	}

	if (!$ARB_FOUND)
		return;

	cw_load('http','xml');

	$ab_id = $config['Shipping']['ARB_id'];
	$ab_password = $config['Shipping']['ARB_password'];
	$ab_ship_accnum = $config['Shipping']['ARB_account'];
	$ab_testmode = $config['Shipping']['ARB_testmode'];

	#
	# Currently shipping only from US is supported
	#
	if (empty($ab_id) || empty($ab_password) || empty($ab_ship_accnum) || $config['Company']['country'] != "US")
		return;

	if ($ab_testmode == 'Y')
		$ab_url = "https://ecommerce.airborne.com:443/ApiLandingTest.asp";
	else
		$ab_url = "https://ecommerce.airborne.com:443/ApiLanding.asp";

	$ab_ship_key = ab_get_ship_key($ab_url, $ab_id, $ab_password, $ab_ship_accnum, $config['Company']['zipcode'], $ab_testmode, ($address['country'] != 'US'));
	if (empty($ab_ship_key)) {
		if ($debug == "Y")
			ab_show_faults();

		ab_conv_faults();
		return;
	}

	$ship_weight = max(1,round(cw_weight_in_grams($weight)/453.6,0));
	$ship_weight_oz = round(cw_weight_in_grams($weight)/28.3,0);


	$ab_packaging = $params['param00'];
	$ab_ship_length = $params['param02'];
	$ab_ship_width = $params['param03'];
	$ab_ship_height = $params['param04'];
	$ab_ship_prot_code = $params['param05'];
	$ab_ship_prot_value = $params['param06'];
	$ab_ship_codpmt = $params['param08'];
	$ab_ship_codval = (float)$params['param09'];
	# options
	list($ab_ship_haz,$ab_ship_own_account) = explode(',',$params['param07']);

	$_ship_date = date("Y-m-d", time() + $params['param01']*86400);

	$mod_AB_ship_flags = array (
		109 => array('code'=>'G', 'sub'=>''),		# Airborne Ground
		31  => array('code'=>'S', 'sub'=>''),		# Airborne Second Day Service
		33  => array('code'=>'N', 'sub'=>''),		# Airborne Next Afternoon
		32  => array('code'=>'E', 'sub'=>''),		# Airborne Express
		124 => array('code'=>'E', 'sub'=>'1030'),	# Airborne Express 10:30 AM
		125 => array('code'=>'E', 'sub'=>'SAT'),	# Airborne Express Saturday
		32	=> array('code'=>'IE','sub'=>'')		# Airborne International Express
	);

	if ($address['country'] != 'US')
		return ab_int_ratings(
			$allowed_shipping_methods,
			$address,
			$weight,	
			$debug,
			array(
				"package" => $ab_packaging,
				"length" => $ab_ship_length,
				"width" => $ab_ship_width,
				"height" => $ab_ship_height,
				"id" => $ab_id,
				"password" => $ab_password,
				"account" => $ab_ship_accnum,
				"testmode" => $ab_testmode,
				"url" => $ab_url,
				"skey" => $config['Shipping']['ARB_shipping_key_intl'],
				"weight" => $ship_weight,
				"weight_oz" => $ship_weight_oz,
				"ship_date" => $_ship_date
			)
		);

	if (cw_use_arb_account($params) && isset($airborne_account) && trim($airborne_account) != "") {
		$_party_code = "R";
		$_party_account = "<AccountNbr>".trim($airborne_account)."</AccountNbr>";
	}
	else {
		$_party_code = "S";
		$_party_account = "";
	}

	$shipments = ""; $cnt = 0;
	$ship_reqs = array();
	foreach ($allowed_shipping_methods as $method) {
		if ($method['code'] == "ARB" && ($ship_weight < $method['weight_limit'] || $method['weight_limit'] == 0.00) && isset($mod_AB_ship_flags[$method['subcode']]) && $method['destination'] == 'L') {

			$_ship_srv_key = $mod_AB_ship_flags[$method['subcode']]['code'];
			$_ship_srv_sub = $mod_AB_ship_flags[$method['subcode']]['sub'];

			if ($_ship_srv_key == "G" && $ab_packaging == 'L') {
				# Letter express is not allowed with Ground Shipments. (Code=4119)
				continue;
			}

			if ($_ship_srv_key == "G" && $_ship_srv_sub == "SAT") {
				# Saturday pickup service is not available for Ground shipments. (Code=4105).
				continue;
			}

			$_shipproc_instr = "";
			$_secial_express = "";
			if ($_ship_srv_key == 'E') {
				# Express Saturday & Express 10:30AM services are not compatible within "Hazardous Materials"
				if ($ab_ship_haz == "Y" && $_ship_srv_sub != "")
					continue;

				if ($_ship_srv_sub == "SAT") {
					$_shipproc_instr = "<ShipmentProcessingInstructions><Overrides><Override><Code>ES</Code></Override></Overrides></ShipmentProcessingInstructions>";
					$_secial_express = "<SpecialServices><SpecialService><Code>SAT</Code></SpecialService></SpecialServices>";
				}
				elseif ($_ship_srv_sub == "1030") {
					$_secial_express = "<SpecialServices><SpecialService><Code>1030</Code></SpecialService></SpecialServices>";
				}
			}

			$_additional_protection = '';
			if ($ab_ship_prot_code == 'AP') {
				$_additional_protection = "<AdditionalProtection><Code>$ab_ship_prot_code</Code><Value>$ab_ship_prot_value</Value></AdditionalProtection>";
			}

			$_secial_haz = "";
			if ($ab_ship_haz == "Y") {
				$_secial_haz = "<SpecialServices><SpecialService><Code>HAZ</Code></SpecialService></SpecialServices>";
			}

			$_cod_payment = "";
			if ($ab_ship_codval > 0 && $_party_code == "S") {
				# When using COD service freight charges must be billed to sender. (Code=4116)
				$_cod_payment = "<CODPayment><Code>$ab_ship_codpmt</Code><Value>$ab_ship_codval</Value></CODPayment>";
			}

			$_dimensions = '';
			if ($ab_packaging == 'P') {
				$_dimensions = "<Weight>$ship_weight</Weight><Dimensions><Width>$ab_ship_width</Width><Height>$ab_ship_height</Height><Length>$ab_ship_length</Length></Dimensions>";
			}
			else {
				if ($ship_weight_oz > 8) {
					# Shipment exceeds allowable weight for Letter. (Code=4118)
					# Letter Express packages must be in Letter Express envelopes and weigh 8 ounces or less.
					continue;
				}
			}

			$shipment =<<<EOT
	<Shipment action='RateEstimate' version='1.0'>
		<ShippingCredentials>
			<ShippingKey>$ab_ship_key</ShippingKey>
			<AccountNbr>$ab_ship_accnum</AccountNbr>
		</ShippingCredentials>
		<ShipmentDetail>
			<ShipDate>$_ship_date</ShipDate>
			<Service>
				<Code>$_ship_srv_key</Code>
			</Service>
			<ShipmentType>
				<Code>$ab_packaging</Code>
			</ShipmentType>
			$_secial_express
			$_secial_haz
			$_dimensions
			$_additional_protection
		</ShipmentDetail>
		<Billing>
			$_cod_payment
			<Party>
				<Code>$_party_code</Code>
			</Party>
			$_party_account
		</Billing>
		<Receiver>
			<Address>
				<City>$address[city]</City>
				<State>$address[state]</State>
				<Country>$address[country]</Country>
				<PostalCode>$address[zipcode]</PostalCode>
			</Address>
		</Receiver>
		$_shipproc_instr
	</Shipment>
EOT;
			$shipments .= $shipment;
			$cnt++;
			if ($cnt >= 5) {
				$cnt = 0;
				if ($shipments != "") $ship_reqs[] = $shipments;
				$shipments = "";
			}
		}

	}

	if ($shipments != "") $ship_reqs[] = $shipments;

	if (count($ship_reqs)>0) {
		$ab_request = "";

		foreach ($ship_reqs as $req)
			ab_rate_estimate($ab_url, $ab_id, $ab_password, $debug, $req);
	}
}

function ab_rate_estimate($ab_url, $ab_id, $ab_password, $debug, $req) {

	$ab_request =<<<EOT
<?xml version='1.0'?>
<eCommerce action="Request" version="1.1">
	<Requestor>
		<ID>$ab_id</ID>
		<Password>$ab_password</Password>
	</Requestor>
	$req
</eCommerce>
EOT;

	$post = preg_split("/(\r\n|\r|\n)/",$ab_request, -1, PREG_SPLIT_NO_EMPTY);
	$md5_request = md5($ab_request);
	$is_cached = cw_is_shipping_result_in_cache($md5_request);
	
	if ((!$is_cached) ||  ($debug == "Y")) {	
		list ($a, $ab_response) = cw_https_request("POST", $ab_url, $post, "","","text/xml");

		ab_parse_response($ab_response);

		if ($debug == "Y") {
			print "<h1>DHL/Airborne Debug Information</h1>";
			print "<h2>DHL/Airborne Request</h2>";
			print "<pre>".htmlspecialchars(cw_arb_prepare_debug($ab_request))."</pre>";
			print "<h2>DHL/Airborne Response</h2>";
			print "<pre>".htmlspecialchars(cw_arb_prepare_debug($ab_response))."</pre>";
		} else {
			cw_save_shipping_result_to_cache($md5_request, $rates);
		}

		if (!empty($mod_AB_faults)) {
			if ($debug == "Y") ab_show_faults();

			ab_conv_faults();
		}
	} else {
		$rates = cw_get_shipping_result_from_cache($md5_request);
	}
}

function ab_get_ship_key($ab_url, $ab_id, $ab_password, $ab_ship_accnum, $zipcode, $ab_testmode, $is_int = false) {
	global $config, $tables;

	if ($is_int) {
		if (!empty($config['Shipping']['ARB_shipping_key_intl']))
			return $config['Shipping']['ARB_shipping_key_intl'];

	} else {
		if (!empty($config['Shipping']['ARB_shipping_key']))
			return $config['Shipping']['ARB_shipping_key'];
	}

	#
	# Request new Shipping Key
	#

	$request =<<<EOT
<?xml version='1.0'?>
<eCommerce action="Request" version="1.1">
	<Requestor>
		<ID>$ab_id</ID>
		<Password>$ab_password</Password>
	</Requestor>
	<Register action='ShippingKey' version='1.0'>
		<AccountNbr>$ab_ship_accnum</AccountNbr>
		<PostalCode>$zipcode</PostalCode>
	</Register>
</eCommerce>
EOT;

	$post = preg_split("/(\r\n|\r|\n)/",$request, -1, PREG_SPLIT_NO_EMPTY);
	list ($a, $result) = cw_https_request("POST", $ab_url, $post, "","","text/xml");

	ab_parse_response($result);
	if (!empty($mod_AB_faults)) return "";

	$config['Shipping']['ARB_shipping_key'] = $mod_AB_shipkey;

	db_query("UPDATE $tables[config] SET value='".addslashes($mod_AB_shipkey)."' WHERE name='ARB_shipping_key'");

	return $mod_AB_shipkey;
}

function ab_show_faults() {

	echo "<h1>DHL/Airborne request faults</h1>";
	$code = array();
	foreach ($mod_AB_faults as $fault) {
		if (isset($code[$fault['CODE']])) continue;

		echo $fault['DESC']." (Code=".$fault['CODE'].") <br />";
		$code[$fault['CODE']] = true;
	}
}

function ab_conv_faults() {
	static $code = array();

	$str = "";
	foreach ($mod_AB_faults as $fault) {
		if (isset($code[$fault['CODE']])) continue;

		$str .= $fault['DESC']." (Code=".$fault['CODE']."). ";
		$code[$fault['CODE']] = true;
	}
}

#
# Functions to parse XML-response
#
function ab_parse_response($result) {

	$parse_errors = false;
	$options = array(
		'XML_OPTION_CASE_FOLDING' => 1,
		'XML_OPTION_TARGET_ENCODING' => 'ISO-8859-1'
	);

	$parsed = cw_xml_parse($result, $parse_errors, $options);


	$r = cw_array_path($parsed, 'ECOMMERCE/REGISTER/SHIPPINGKEY/0/#');
	if ($r !== false) {
		$mod_AB_shipkey = $r;
	}

	ab_add_faults($parsed, 'ECOMMERCE/FAULTS/FAULT');
	ab_add_faults($parsed, 'ECOMMERCE/REGISTER/FAULTS/FAULT');

	$shipments =& cw_array_path($parsed, 'ECOMMERCE/SHIPMENT');

	if (is_array($shipments)) {
		foreach ($shipments as $shipment) {
			ab_add_faults($shipment, 'FAULTS/FAULT');

			$mod_AB_SRVCODE = cw_array_path($shipment,'ESTIMATEDETAIL/SERVICE/CODE/0/#');
			$mod_AB_SRVSUBCODE = "";

			$desc = cw_array_path($shipment, 'ESTIMATEDETAIL/SERVICELEVELCOMMITMENT/DESC/0/#');
			if (!empty($desc) && $mod_AB_SRVCODE == 'E') {
				if (stristr($desc,"Saturday")!==false)
					$mod_AB_SRVSUBCODE = 'SAT';
				elseif (strstr($desc,"10:30")!==false)
					$mod_AB_SRVSUBCODE = '1030';
			}

			$rate = cw_array_path($shipment, 'ESTIMATEDETAIL/RATEESTIMATE/TOTALCHARGEESTIMATE/0/#');
			if ($rate === false || (float)trim($rate) < 0.001)
				continue;

			$shipping_time = cw_array_path($shipment, 'ESTIMATEDETAIL/SERVICELEVELCOMMITMENT/DESC/0/#');

			foreach ($allowed_shipping_methods as $method) {
				if ($method['code'] != 'ARB' || empty($mod_AB_ship_flags[$method['subcode']]))
					continue;

				$method_flags = $mod_AB_ship_flags[$method['subcode']];

				if ($method_flags['code'] == $mod_AB_SRVCODE && $method_flags['sub'] == $mod_AB_SRVSUBCODE) {
					$current_rate = array (
						'methodid' => $method['subcode'],
						'rate' => trim($rate)
					);
					if ($shipping_time !== false)
						$current_rate['shipping_time'] = $shipping_time;

					$rates[$current_rate['methodid']] = $current_rate;
					break;
				}
			}
		}
	}
}

function ab_add_faults($parsed, $path) {

	$faults = cw_array_path($parsed, $path);
	if (!is_array($faults))
		return;

	foreach ($faults as $fault) {
		$desc = cw_array_path($fault,'DESC/0/#');
		if ($desc === false) {
			$mod_AB_faults[] = array (
				'CODE' => cw_array_path($fault,'CODE/0/#'),
				'DESCRIPTION' => cw_array_path($fault,'DESC/0/#'),
				'CONTEXT' => cw_array_path($fault,'CONTEXT/0/#')
			);
		}
		else {
			$mod_AB_faults[] = array (
				'CODE' => cw_array_path($fault,'CODE/0/#'),
				'DESC' => $desc,
				'SOURCE' => cw_array_path($fault,'SOURCE/0/#')
			);
		}
	}
}

function ab_int_ratings($allowed_shipping_methods, $address, $weight, $debug, $params) {
	global $config, $tables;

	if ($config['Company']['country'] != 'US' || $address['country'] == 'US')
		return array();

	cw_load('xml','http');

	# Define transaction parameters
	$siteid = $params['id'];
	$pass = $params['password'];
	$skey = $params['skey'];
	$anumber = $params['account'];

	$sh_email = $config['Company']['orders_department'];
	$sh_address = $config['Company']['address'];
	$sh_city = $config['Company']['city'];
	$sh_state = $config['Company']['state'];
	$sh_zipcode = $config['Company']['zipcode'];
	$sh_country = $config['Company']['country'];

	$rc_address = $address['address'];
	$rc_address_2 = $address['address_2'];
	$rc_city = $address['city'];
	$rc_state = $address['state'];
	$rc_zipcode = $address['zipcode'];
	$rc_country = $address['country'];
	$rc_phone = $address['phone'];
	$rc_email = $address['email'];

	$p_width = intval($params['length']);
	$p_height = intval($params['height']);
	$p_depth = intval($params['width']);
	$p_type = $params['package'];

	$c_name = $config['Company']['company_name'];
	$c_phone = $config['Company']['company_phone'];
	$c_fax = $config['Company']['company_fax'];

	$ship_date = $params['ship_date'];

	# Correct United Kingdom code - UK
	if ($rc_country == 'GB')
		$rc_country = 'UK';

	# Define request header
	$post = <<<REQ
<?xml version='1.0'?><ECommerce action='Request' version='1.1'>
	<Requestor>
		<ID>$siteid</ID>
		<Password>$pass</Password>
	</Requestor>
REQ;

	$ship_weight = $params['weight'];
	$ship_weight_oz = $params['weight_oz'];
	if ($ship_weight_oz > 8 && $p_type == 'L')
		return;

	# Define request body
	$i = 0;
	foreach ($allowed_shipping_methods as $method) {
		if ($method['code'] != "ARB" || ($params['weight'] > $method['weight_limit'] && $method['weight_limit'] > 0) || empty($method['subcode']) || $method['destination'] == 'L' || !isset($mod_AB_ship_flags[$method['subcode']]))
			continue;

		$scode = $mod_AB_ship_flags[$method['subcode']]['code'];
		$i++;

		$dims = '';
		if ($p_type == 'P') {
			$dims = <<<DIMS
		<Weight>$ship_weight</Weight>
		<Dimensions>
			<Length>$p_width</Length>
			<Width>$p_height</Width>
			<Height>$p_depth</Height>
		</Dimensions>
DIMS;
		}

		$post .= <<<REQ
 <IntlShipment action = 'RateEstimate' version = '1.0'>
	<ShippingCredentials>
		<ShippingKey>$skey</ShippingKey>
		<AccountNbr>$anumber</AccountNbr>
	</ShippingCredentials>
	<ShipmentDetail>
		<ShipDate>$ship_date</ShipDate>
		<Service>
			<Code>$scode</Code>
		</Service>
		<ShipmentType>
			<Code>$p_type</Code>
		</ShipmentType>
		<ContentDesc>Big Box</ContentDesc>
		$dims
	</ShipmentDetail>
	<Dutiable>
		<DutiableFlag>N</DutiableFlag>
		<CustomsValue>0</CustomsValue>
	</Dutiable>
	<Billing>
		<Party>
			<Code>S</Code>
		</Party>
		<DutyPaymentType>S</DutyPaymentType>
	</Billing>
	<Sender>
		<Address>
			<CompanyName>$c_name</CompanyName>
			<Street>$sh_address</Street>
			<City>$sh_city</City>
			<State>$sh_state</State>
			<PostalCode>$sh_zipcode</PostalCode>
			<Country>$sh_country</Country>
		</Address>
		<PhoneNbr>$c_phone</PhoneNbr>
		<Email>$sh_email</Email>
	</Sender>
	<Receiver>
		<Address>
			<Street>$rc_address</Street>
			<StreetLine2>$rc_address_2</StreetLine2>
			<City>$rc_city</City>
			<PostalCode>$rc_zipcode</PostalCode>
			<State>$rc_state</State>
			<Country>$rc_country</Country>
		</Address>
		<PhoneNbr>$rc_phone</PhoneNbr>
		<Email>$rc_email</Email>
	</Receiver>
</IntlShipment>
</ECommerce>
REQ;

	}

	if (empty($i))
		return array();

	# Request
	list($a, $result) = cw_https_request("POST", $params['url'], array($post), "", "", "text/xml");	

	$parse_errors = false;
	$options = array(
		'XML_OPTION_CASE_FOLDING' => 1,
		'XML_OPTION_TARGET_ENCODING' => 'ISO-8859-1'
	);

	$parsed = cw_xml_parse($result, $parse_errors, $options);

	# Detect common errors
	$errors = cw_array_path($parsed, "ECOMMERCE/FAULT");

	if (!empty($errors)) {
		if ($debug == 'Y')
			echo "<h1>DHL/Airborne request faults</h1>\n";

		foreach ($errors as $k => $v) {
			$errors[$k] = cw_array_path($v, "#/CODE/0/#").": ".cw_array_path($v, "#/DESCRIPTION/0/#");
			if ($debug == 'Y')
				echo $errors[$k]."<br />\n";
		}
		return array();
	}

	# Detect rates
	$methods = cw_array_path($parsed, "ECOMMERCE/INTLSHIPMENT");
	if (empty($methods))
		return array();

	foreach ($methods as $m) {

		# Detect rate error
		$errs = cw_array_path($m, "#/FAULTS");
		if (!empty($errs)) {
			$errors = array();

			foreach ($errs as $e) {
				$suberrors = cw_array_path($e, "#/FAULT");
				if (!empty($suberrors)) {
					foreach($suberrors as $se) {
						$errors[] = cw_array_path($se, "#/CODE/0/#").": ".cw_array_path($se, "#/DESC/0/#");
					}
				}
			}

			continue;
		}

		# Detect rate
		$id = trim(cw_array_path($m, "ESTIMATEDETAIL/SERVICE/CODE/0/#"));
		$rate = doubleval(trim(cw_array_path($m, "ESTIMATEDETAIL/RATEESTIMATE/0/#/TOTALCHARGEESTIMATE/0/#")));

		# Save rate
		foreach ($allowed_shipping_methods as $method) {
			if (
				$method['code'] != "ARB" ||
				($weight > $method['weight_limit'] && $method['weight_limit'] > 0) ||
				$mod_AB_ship_flags[$method['subcode']]['code'] != $id ||
				$method['destination'] == 'L'
			)
				continue;

			$rates[] = array(
				"methodid" => $method['subcode'],
				"rate" => $rate,
				"shipping_time" => trim(cw_array_path($m, "ESTIMATEDETAIL/SERVICELEVELCOMMITMENT/DESC/0/#")),
			);

		}
	}
}

#
# Format XML string for displaying in debug info
#
function cw_arb_prepare_debug($query) {
	$query = preg_replace("|<ID>.*</ID>|iUS","<ID>xxx</ID>",$query);
	$query = preg_replace("|<Password>.*</Password>|iUS", "<Password>xxx</Password>", $query);
	$query = preg_replace("|<ShippingKey>.*</ShippingKey>|iUS", "<ShippingKey>xxx</ShippingKey>", $query);
	$query = preg_replace("|<AccountNbr>.*</AccountNbr>|iUS", "<AccountNbr>xxx</AccountNbr>", $query);

	cw_load("xml");
	return cw_xml_format($query);
}
