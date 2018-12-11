<?php
function cw_shipper_USPS($weight, $customer_id, $address, $debug, $cart) {
	global $config, $tables, $current_language;

	$USPS_username = $config['Shipping']['USPS_username'];
	$USPS_password = $config['Shipping']['USPS_password'];
	$USPS_servername = $config['Shipping']['USPS_servername'];

	$use_usps_https = false;

	if (empty($USPS_username) || empty($USPS_servername))
		return;

	$USPS_FOUND = false;
	if (is_array($allowed_shipping_methods)) {
		foreach ($allowed_shipping_methods as $key=>$value) {
			if ($value['code'] == "USPS") {
				$USPS_FOUND = true;
				break;
			}
		}
	}

	if (!$USPS_FOUND)
		return;

	cw_load('http','xml');

	$pounds = 0;
	$ounces = ceil(round(cw_weight_in_grams($weight)/28.35,3));
	if ($ounces < 1)
		$ounces = 1;


	$mailtype = $params['param00'];
	$package_size = $params['param01'];
	$machinable = $params['param02'];
	$container_express = $params['param03'];
	$container_priority = $params['param04'];
	if (!empty($container_express) && $container_express != 'None') {
		$container_express = "<Container>".$container_express."</Container>";
	} else {
		$container_express = "";
	}
	if (!empty($container_priority) && $container_priority != 'None') {
		$container_priority = "<Container>".$container_priority."</Container>";
	} else {
		$container_priority = "";
	}

	if (($address['country'] == 'PR') || ($address['country'] == 'GU') || ($address['country'] == 'VI')) {
		$address['country'] = 'US';
	}
	$dst_country = USPS_get_country($address['country']);
	if (empty($dst_country)) {
		$dst_country = cw_query_first_cell("SELECT value FROM $tables[languages] WHERE name = 'country_".$address['country']."' AND code = '$current_language'");
	}

	$USPS_file = ($USPS_servername=="testing.shippingapis.com")? "/ShippingAPITest.dll" : "/ShippingAPI.dll";

	$hash = array();

	if ($address['country'] != $config['Company']['country']) {

		# International shipping
		$query=<<<EOT
<IntlRateRequest USERID="$USPS_username" PASSWORD="$USPS_password">
<Package ID="0">
<Pounds>$pounds</Pounds>
<Ounces>$ounces</Ounces>
<MailType>$mailtype</MailType>
<Country>$dst_country</Country>
</Package>
</IntlRateRequest>
EOT;

		$md5_request = md5($query);
		if ((cw_is_shipping_result_in_cache($md5_request)) &&  ($debug != "Y")){
			$rates = cw_get_shipping_result_from_cache($md5_request);
			return;
		}
		if ($use_usps_https) {

			$post = array(
				"API=IntlRate",
				"XML=".urlencode($query)
			);
			list($header, $result) = cw_https_request("GET", "https://".$USPS_servername.":443".$USPS_file."?API=IntlRate&XML=".urlencode($query));

		} else {
			list($header, $result) = cw_http_get_request($USPS_servername, $USPS_file, "API=IntlRate&XML=".urlencode($query));
		}

		$xml = cw_xml_parse($result, $err);

		# Get <Error> elemenet
		$err = cw_array_path($xml, "IntlRateResponse/Package/Error");
		if (empty($err)) {

			# Get <Service> elements
			$packages = cw_array_path($xml, "IntlRateResponse/Package/Service");
			if (!empty($packages) && is_array($packages)) {
				foreach ($packages as $p) {

					# Get shipping method name
					$sname = cw_array_path($p, "SvcDescription/0/#");

					# Get rate
					$rate = cw_array_path($p, "Postage/0/#");

					# Get comment
					#$comment = cw_array_path($p, "SvcCommitments/0/#");
					if (empty($sname) || zerolen($rate))
						continue;

					# Define shipping method
					$is_found = false;
					foreach ($allowed_shipping_methods as $sm) {
						if ($sm['code'] == "USPS" && $sm['destination'] == "I" && preg_match("/^".preg_quote($sm['shipping'], "/")."/S", "USPS ".$sname)) {
							if (!in_array($sm['subcode'], $hash)) {
								$rates[] = array(
									"methodid" => $sm['subcode'],
									"rate" => $rate,
									"warning" => ""
								);
								$hash[] = $sm['subcode'];
							}
							$is_found = true;
							break;
						}
					}

					if (!$is_found) {

						# Add new shipping method
						cw_add_new_smethod("USPS ".$sname, "USPS", array("destination" => "I"));
					}
				}
				if ($debug != "Y")
					cw_save_shipping_result_to_cache($md5_request, $rates);
			}
		}

	} else {

		# Domestic shipping
		$ZO = $config['Company']['zipcode'];
		$ZD = $address['zipcode'];

		$query =<<<EOT
<RateV2Request USERID="$USPS_username">
	<Package ID="0">
		<Service>EXPRESS</Service>
		<ZipOrigination>$ZO</ZipOrigination>
		<ZipDestination>$ZD</ZipDestination>
		<Pounds>$pounds</Pounds>
		<Ounces>$ounces</Ounces>
		$container_express
		<Size>$package_size</Size>
	</Package>
	<Package ID="1">
		<Service>FIRST CLASS</Service>
		<ZipOrigination>$ZO</ZipOrigination>
		<ZipDestination>$ZD</ZipDestination>
		<Pounds>$pounds</Pounds>
		<Ounces>$ounces</Ounces>
		<Container>None</Container>
		<Size>$package_size</Size>
	</Package>
	<Package ID="2">
		<Service>PRIORITY</Service>
		<ZipOrigination>$ZO</ZipOrigination>
		<ZipDestination>$ZD</ZipDestination>
		<Pounds>$pounds</Pounds>
		<Ounces>$ounces</Ounces>
		$container_priority
		<Size>$package_size</Size>
	</Package>
	<Package ID="3">
		<Service>PARCEL</Service>
		<ZipOrigination>$ZO</ZipOrigination>
		<ZipDestination>$ZD</ZipDestination>
		<Pounds>$pounds</Pounds>
		<Ounces>$ounces</Ounces>
		<Container>None</Container>
		<Size>$package_size</Size>
		<Machinable>$machinable</Machinable>
	</Package>
	<Package ID="4">
		<Service>BPM</Service>
		<ZipOrigination>$ZO</ZipOrigination>
		<ZipDestination>$ZD</ZipDestination>
		<Pounds>$pounds</Pounds>
		<Ounces>$ounces</Ounces>
		<Container>None</Container>
		<Size>$package_size</Size>
	</Package>
	<Package ID="5">
		<Service>LIBRARY</Service>
		<ZipOrigination>$ZO</ZipOrigination>
		<ZipDestination>$ZD</ZipDestination>
		<Pounds>$pounds</Pounds>
		<Ounces>$ounces</Ounces>
		<Container>None</Container>
		<Size>$package_size</Size>
	</Package>
	<Package ID="6">
		<Service>MEDIA</Service>
		<ZipOrigination>$ZO</ZipOrigination>
		<ZipDestination>$ZD</ZipDestination>
		<Pounds>$pounds</Pounds>
		<Ounces>$ounces</Ounces>
		<Container>None</Container>
		<Size>$package_size</Size>
	</Package>
</RateV2Request>
EOT;
		$md5_request = md5($query);
		if ((cw_is_shipping_result_in_cache($md5_request)) &&  ($debug != "Y")){
			$rates = cw_get_shipping_result_from_cache($md5_request);
			return;
		}

		if ($use_usps_https) {

			$post = array(
				"API=RateV2",
				"XML=".urlencode($query)
			);
			list($header, $result) = cw_https_request("GET", "https://".$USPS_servername.":443".$USPS_file."?API=RateV2&XML=".urlencode($query));

        } else {
			list($header, $result) = cw_http_get_request($USPS_servername, $USPS_file, "API=RateV2&XML=".urlencode($query));
		}

		$xml = cw_xml_parse($result, $err);

		# Get <Package> elements
		$packages = cw_array_path($xml, "RateV2Response/Package");
		
		if (is_array($packages)) {
		
		foreach ($packages as $p) {

			# Get <Error> element
			$err = cw_array_path($p, "Error");
			if (!empty($err))
				continue;

			# Get shipping method name
			$sname = cw_array_path($p, "Postage/MailService/0/#");

			# Get rate
			$rate = cw_array_path($p, "Postage/Rate/0/#");
			if (empty($sname) || zerolen($rate))
				continue;

			# Define shipping method
			$is_found = false;
			foreach ($allowed_shipping_methods as $sm) {
				if ($sm['code'] == "USPS" && $sm['destination'] == "L" && preg_match("/^".preg_quote($sm['shipping'], "/")."/S", "USPS ".$sname)) {
					if (!in_array($sm['subcode'], $hash)) {
						$rates[] = array(
							"methodid" => $sm['subcode'],
							"rate" => $rate,
							"warning" => ""
						);
						$hash[] = $sm['subcode'];
					}

					$is_found = true;
					break;
				}
			}

			if (!$is_found) {

				# Add new shipping method
				cw_add_new_smethod("USPS ".$sname, "USPS", array("destination" => "L"));
			}
		}
			if ($debug != "Y")
				cw_save_shipping_result_to_cache($md5_request, $rates);

		} // if (is_array($packages))

	}
}

#
# Get USPS country code
#
function USPS_get_country($code) {
	static $usps_countries = array(
		'AE' => 'United Arab Emirates',
		'PG' => 'Papua New Guinea',
		'AF' => 'Afghanistan',
		'NZ' => 'New Zealand',
		'FI' => 'Finland',
		'AL' => 'Albania',
		'DZ' => 'Algeria',
		'AD' => 'Andorra',
		'AO' => 'Angola',
		'AI' => 'Anguilla',
		'AG' => 'Antigua and Barbuda',
		'AR' => 'Argentina',
		'AM' => 'Armenia',
		'AW' => 'Aruba',
		'AU' => 'Australia',
		'AT' => 'Austria',
		'AZ' => 'Azerbaijan',
		'BS' => 'Bahamas',
		'BH' => 'Bahrain',
		'BD' => 'Bangladesh',
		'BB' => 'Barbados',
		'BY' => 'Belarus',
		'BE' => 'Belgium',
		'BZ' => 'Belize',
		'BJ' => 'Benin',
		'BM' => 'Bermuda',
		'BT' => 'Bhutan',
		'BO' => 'Bolivia',
		'BA' => 'Bosnia-Herzegovina',
		'BW' => 'Botswana',
		'BR' => 'Brazil',
		'VG' => 'British Virgin Islands',
		'BN' => 'Brunei Darussalam',
		'BG' => 'Bulgaria',
		'BF' => 'Burkina Faso',
		'MM' => 'Burma',
		'BI' => 'Burundi',
		'KH' => 'Cambodia',
		'CM' => 'Cameroon',
		'CA' => 'Canada',
		'CV' => 'Cape Verde',
		'KY' => 'Cayman Islands',
		'CF' => 'Central African Rep.',
		'TD' => 'Chad',
		'CL' => 'Chile',
		'CN' => 'China',
		'CO' => 'Colombia',
		'KM' => 'Comoros',
		'CG' => 'Congo, Democratic Republic of the',
		'CR' => 'Costa Rica',
		'CI' => 'Cte d\'Ivoire',
		'HR' => 'Croatia',
		'CU' => 'Cuba',
		'CY' => 'Cyprus',
		'CZ' => 'Czech Republic',
		'DK' => 'Denmark',
		'DJ' => 'Djibouti',
		'DM' => 'Dominica',
		'DO' => 'Dominican Republic',
		'EC' => 'Ecuador',
		'EG' => 'Egypt',
		'SV' => 'El Salvador',
		'GQ' => 'Equatorial Guinea',
		'ER' => 'Eritrea',
		'EE' => 'Estonia',
		'ET' => 'Ethiopia',
		'FK' => 'Falkland Islands',
		'FO' => 'Faroe Islands',
		'FJ' => 'Fiji',
		'FR' => 'France',
		'GF' => 'French Guiana',
		'PF' => 'French Polynesia',
		'GA' => 'Gabon',
		'GM' => 'Gambia',
		'GE' => 'Georgia, Republic of',
		'DE' => 'Germany',
		'GH' => 'Ghana',
		'GI' => 'Gibraltar',
		'GB' => 'Great Britain and Northern Ireland',
		'GR' => 'Greece',
		'GL' => 'Greenland',
		'GD' => 'Grenada',
		'GP' => 'Guadeloupe',
		'GU' => 'Guam',
		'GT' => 'Guatemala',
		'GN' => 'Guinea',
		'GW' => 'Guinea-Bissau',
		'GY' => 'Guyana',
		'HT' => 'Haiti',
		'HN' => 'Honduras',
		'HK' => 'Hong Kong',
		'HU' => 'Hungary',
		'IS' => 'Iceland',
		'IN' => 'India',
		'ID' => 'Indonesia',
		'IR' => 'Iran',
		'IQ' => 'Iraq',
		'IE' => 'Ireland',
		'IL' => 'Israel',
		'IT' => 'Italy',
		'JM' => 'Jamaica',
		'JP' => 'Japan',
		'JO' => 'Jordan',
		'KZ' => 'Kazakhstan',
		'KE' => 'Kenya',
		'KI' => 'Kiribati',
		'KP' => 'Korea, Democratic People\'s Republic of',
		'KR' => 'Korea, Republic of',
		'KW' => 'Kuwait',
		'KG' => 'Kyrgyzstan',
		'LA' => 'Laos',
		'LV' => 'Latvia',
		'LB' => 'Lebanon',
		'LS' => 'Lesotho',
		'LR' => 'Liberia',
		'LY' => 'Libya',
		'LI' => 'Liechtenstein',
		'LT' => 'Lithuania',
		'LU' => 'Luxembourg',
		'MO' => 'Macao',
		'MK' => 'Macedonia',
		'MG' => 'Madagascar',
		'MW' => 'Malawi',
		'MY' => 'Malaysia',
		'MV' => 'Maldives',
		'ML' => 'Mali',
		'MT' => 'Malta',
		'MH' => 'Marshall Islands',
		'MQ' => 'Martinique',
		'MR' => 'Mauritania',
		'MU' => 'Mauritius',
		'MX' => 'Mexico',
		'FM' => 'Micronesia, Federated States of',
		'MD' => 'Moldova',
		'MN' => 'Mongolia',
		'MS' => 'Montserrat',
		'MA' => 'Morocco',
		'MZ' => 'Mozambique',
		'NA' => 'Namibia',
		'NR' => 'Nauru',
		'NP' => 'Nepal',
		'NL' => 'Netherlands',
		'AN' => 'Netherlands Antilles',
		'NC' => 'New Caledonia',
		'NI' => 'Nicaragua',
		'NE' => 'Niger',
		'NG' => 'Nigeria',
		'MP' => 'Northern Mariana Islands, Commonwealth',
		'NO' => 'Norway',
		'OM' => 'Oman',
		'AS' => 'American Samoa',
		'PK' => 'Pakistan',
		'PW' => 'Palau',
		'PA' => 'Panama',
		'PY' => 'Paraguay',
		'PE' => 'Peru',
		'PH' => 'Philippines',
		'PN' => 'Pitcairn Island',
		'PL' => 'Poland',
		'PT' => 'Portugal',
		'PR' => 'Puerto Rico',
		'QA' => 'Qatar',
		'RE' => 'Reunion',
		'RO' => 'Romania',
		'RU' => 'Russia',
		'RW' => 'Rwanda',
		'KN' => 'Saint Christopher (St. Kitts) and Nevis',
		'SH' => 'Saint Helena',
		'LC' => 'Saint Lucia',
		'PM' => 'Saint Pierre and Miquelon',
		'VC' => 'Saint Vincent and the Grenadines',
		'WS' => 'Samoa, American',
		'SM' => 'San Marino',
		'ST' => 'Sao Tome and Principe',
		'SA' => 'Saudi Arabia',
		'SN' => 'Senegal',
		'SC' => 'Seychelles',
		'SL' => 'Sierra Leone',
		'SG' => 'Singapore',
		'SK' => 'Slovak Republic',
		'SI' => 'Slovenia',
		'SB' => 'Solomon Islands',
		'SO' => 'Somalia',
		'ZA' => 'South Africa',
		'ES' => 'Spain',
		'LK' => 'Sri Lanka',
		'SD' => 'Sudan',
		'SR' => 'Suriname',
		'SZ' => 'Swaziland',
		'SE' => 'Sweden',
		'CH' => 'Switzerland',
		'SY' => 'Syrian Arab Republic',
		'TW' => 'Taiwan',
		'TJ' => 'Tajikistan',
		'TZ' => 'Tanzania',
		'TH' => 'Thailand',
		'TG' => 'Togo',
		'TO' => 'Tonga',
		'TT' => 'Trinidad and Tobago',
		'TN' => 'Tunisia',
		'TR' => 'Turkey',
		'TM' => 'Turkmenistan',
		'TC' => 'Turks and Caicos Islands',
		'TV' => 'Tuvalu',
		'UG' => 'Uganda',
		'UA' => 'Ukraine',
		'UY' => 'Uruguay',
		'UZ' => 'Uzbekistan',
		'VU' => 'Vanuatu',
		'VA' => 'Vatican City',
		'VE' => 'Venezuela',
		'VN' => 'Vietnam',
		'VI' => 'Virgin Islands U.S.',
		'WF' => 'Wallis and Futuna Islands',
		'YE' => 'Yemen',
		'YU' => 'Yugoslavia',
		'ZM' => 'Zambia',
		'ZW' => 'Zimbabwe',
		'CC' => 'Cocos Island',
		'CK' => 'Cook Islands',
		'TP' => 'East Timor',
		'YT' => 'Mayotte',
		'MC' => 'Monaco',
		'NU' => 'Niue',
		'NF' => 'Norfolk Island',
		'TK' => 'Tokelau (Union) Group',
		'UK' => 'United Kingdom',
		'CX' => 'Christmas Island',
		'US' => 'United States',
	);

	if (isset($usps_countries[$code]))
		return $usps_countries[$code];

	return false;
}

?>
