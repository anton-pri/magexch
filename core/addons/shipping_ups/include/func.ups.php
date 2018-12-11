<?php
cw_load('http', 'xml', 'config');

// The same code in UPS response means different shipping method for different orig -> dest 
global $ups_services;
$ups_services = array(
    '1' => array(
        'US' => '20',
        'CA' => '104',
        'PR' => '20'
    ),
    '2' => array(
        'US' => '2',
        'CA' => '105',
        'PR' => '2'
    ),
    '3' => array(
        'US' => '1',
        'PR' => '1'
    ),
    '7' => array(
        'US' => '8',
        'EU' => '104',
        'CA' => '8',
        'PR' => '8',
        'MX' => '104',
        'OTHER_ORIGINS' => '104',
        'PL' => '104'
    ),
    '8' => array(
        'US' => '3',
        'EU' => '105',
        'CA' => '3',
        'PR' => '3',
        'MX' => '105',
        'OTHER_ORIGINS' => '3',
        'PL' => '105'

    ),
    '11' => array(
        'US' => '65',
        'EU' => '65',
        'CA' => '65',
        'MX' => '65',
        'PL' => '65',
        'OTHER_ORIGINS' => '65'
    ),
    '12' => array(
        'US' => '23',
        'CA' => '23'
    ),
    '13' => array(
        'US' => '22',
        'CA' => '68'
    ),
    '14' => array(
        'US' => '66',
        'CA' => '106',
        'PR' => '66'
    ),
    '54' => array(
        'US' => '67',
        'CA' => '67',
        'EU' => '67',
        'PR' => '67',
        'MX' => 11,
        'OTHER_ORIGINS' => '67',
        'PL' => '67'
    ),
    '59' => array(
        'US' => '18'
    ),
    '65' => array(
        'US' => '68',
        'EU' => '68',
        'CA' => '68',
        'PR' => '68',
        'MX' => '68',
        'OTHER_ORIGINS' => '68',
        'PL' => '68'
    ),
    '82' => array(
        'PL' => '139'
    ),
    '83' => array(
        'PL' => '140'
    ),
    '85' => array(
        'PL' => '142'
    ),
    '86' => array(
        'PL' => '143'
    )
);


function cw_ups_shipping_get_rates($params, $return) {
    global $config;
    global $ups_services;
    
    if (empty($config['shipping_ups']['username']) || empty($config['shipping_ups']['password']) || empty($config['shipping_ups']['accesskey'])) return $return;

    extract($params);

    $weight_condition = "weight_min<='$weight' AND (weight_limit='0' OR weight_limit>='$weight')";
    $shipping = cw_func_call('cw_shipping_search', array('data' => array('active' => 1, 'addon' => 'shipping_ups', 'where' => array($weight_condition))));
    if (!$shipping) return $return;

    $ups_parameters = $config['shipping_ups'];

    switch ($ups_parameters['account_type']) {
    case "01":
        $ups_parameters['customer_classification_code'] = "01";
        $ups_parameters['pickup_type'] = "01";
        break;
    case "02":
        $ups_parameters['customer_classification_code'] = "03";
        $ups_parameters['pickup_type'] = "03";
        break;
    case "03":
    default:
        $ups_parameters['customer_classification_code'] = "04";
        $ups_parameters['pickup_type'] = "11";
    }

    $src_country_code = $from_address['country'];
    $src_city = cw_ups_xml_quote($from_address['city']);
    $src_zipcode = $from_address['zipcode'];
    $src_state = $from_address['state'];

    $dest_code = $dst_country_code = $to_address['country'];
    if (($to_address['state'] == "PR") || ($to_address['state'] == "VI"))
        $dest_code = $dst_country_code = $to_address['state'];
    $dst_city = cw_ups_xml_quote($to_address['city']);
    $dst_zipcode = $to_address['zipcode'];

    if ($src_country_code == "US" && !empty($ups_parameters['customer_classification_code'])) {
        $customer_classification_code = $ups_parameters['customer_classification_code'];
        $customer_classification_query=<<<EOT
    <CustomerClassification>
        <Code>$customer_classification_code</Code>
    </CustomerClassification>
EOT;
    }

    #
    # Pickup Type and Packaging Type
    #
    $pickup_type = $ups_parameters['pickup_type'];
    $packaging_type = $ups_parameters['packaging_type'];

    $insvalue = round(doubleval($ups_parameters['iv_amount']),2);
    $pkgopt = array();
    if ($insvalue > 0.1) {
        $pkgopt[] =<<<EOT
                <InsuredValue>
                    <CurrencyCode>$ups_parameters[iv_currency]</CurrencyCode>
                    <MonetaryValue>$insvalue</MonetaryValue>
                </InsuredValue>

EOT;
    }
    
    $delivery_conf = intval($ups_parameters['delivery_conf']);
    if ($delivery_conf > 0 && $delivery_conf < 4) {
        $pkgopt[] =<<<EOT
                <DeliveryConfirmation>
                    <DCISType>$delivery_conf</DCISType>
                </DeliveryConfirmation>

EOT;
    }

    $codvalue = round(doubleval($ups_parameters['codvalue']),2);
    $cod_is_allowed = false;
    $cod_is_allowed |= (($src_country_code == "US" || $src_country_code == "PR") && ($dst_country_code == "US" || $dst_country_code == "PR"));
    $cod_is_allowed |= ($src_country_code == "CA" && (($dst_country_code == "US" || $dst_country_code == "CA")));
    if ($cod_is_allowed && $codvalue > 0.1) {
        $pkgopt[] =<<<EOT
                <COD>
                    <CODCode>3</CODCode>
                    <CODFundsCode>$ups_parameters[cod_funds_code]</CODFundsCode>
                    <CODAmount>
                        <CurrencyCode>$ups_parameters[cod_currency]</CurrencyCode>
                        <MonetaryValue>$codvalue</MonetaryValue>
                    </CODAmount>
                </COD>

EOT;
    }

    $pkgparams = (count($pkgopt) > 0)?"\t\t\t<PackageServiceOptions>\n".join("",$pkgopt)."\t\t\t</PackageServiceOptions>\n":"";

    $srvopts = array();
    if ($ups_parameters['options'])
    foreach ($ups_parameters['options'] as $opt) {
        switch($opt) {
            case "AH": $pkgparams .= "\t\t\t<AdditionalHandling/>"; break;
            case "SP": $srvopts[] = "\t\t\t<SaturdayPickupIndicator/>\n"; break;
            case "SD": $srvopts[] = "\t\t\t<SaturdayDeliveryIndicator/>\n"; break;
        }
    }

    if (!empty($ups_parameters['shipper_number'])) {
        $shipper_number_xml=<<<EOT
            <ShipperNumber>$ups_parameters[shipper_number]</ShipperNumber>
EOT;
    }
    else
        $shipper_number_xml = "";

    $shipment_options_xml = $negotiated_rates_xml = '';
    if (count($srvopts)>0)
        $shipment_options_xml = "\t\t<ShipmentServiceOptions>\n".join("", $srvopts)."\t\t</ShipmentServiceOptions>";

    $weight = cw_units_convert(cw_weight_in_grams($weight), "g", "lbs", 1);
    if (!$weight) $weight = 0.1;


    // Fill the packages
    $packages_xml = '';
    $packages = cw_call('cw_shipping_get_packages', array($params));
    foreach ($packages as $pack) {
        $packages_xml .= <<<EOT
        <Package>
            <PackagingType>
                <Code>{$packaging_type}</Code>
            </PackagingType>
            <PackageWeight>
                <UnitOfMeasurement>
                    <Code>LBS</Code>
                </UnitOfMeasurement>
                <Weight>{$pack['weight']}</Weight>
            </PackageWeight>
    {$dimensions_query}
    {$pkgparams}
        </Package>

EOT;
    }

    if (!empty($ups_parameters['negotiated_rates'])) {
        $negotiated_rates_xml =<<<EOT
        <RateInformation>
            <NegotiatedRatesIndicator/>
        </RateInformation>
EOT;
    }


    $query=<<<EOT
<?xml version='1.0'?>
<AccessRequest xml:lang='en-US'>
    <AccessLicenseNumber>{$config['shipping_ups']['accesskey']}</AccessLicenseNumber>
    <UserId>{$config['shipping_ups']['username']}</UserId>
    <Password>{$config['shipping_ups']['password']}</Password>
</AccessRequest>
<?xml version='1.0'?>
<RatingServiceSelectionRequest xml:lang='en-US'>
    <Request>
        <TransactionReference>
            <CustomerContext>Rating and Service</CustomerContext>
            <XpciVersion>1.0001</XpciVersion>
        </TransactionReference>
        <RequestAction>Rate</RequestAction>
        <RequestOption>shop</RequestOption>
    </Request>
    <PickupType>
        <Code>$pickup_type</Code>
    </PickupType>
$customer_classification_query
    <Shipment>
        <Shipper>
$shipper_number_xml
            <Address>
                <City>$src_city</City>
                <PostalCode>$src_zipcode</PostalCode>
                <StateProvinceCode>$src_state</StateProvinceCode>
                <CountryCode>$src_country_code</CountryCode>
            </Address>
        </Shipper>
        <ShipFrom>
            <Address>
                <City>$src_city</City>
                <PostalCode>$src_zipcode</PostalCode>
                <StateProvinceCode>$src_state</StateProvinceCode>
                <CountryCode>$src_country_code</CountryCode>
            </Address>
        </ShipFrom>
        <ShipTo>
            <Address>
                <City>$dst_city</City>
                <PostalCode>$dst_zipcode</PostalCode>
                <CountryCode>$dst_country_code</CountryCode>
            </Address>
        </ShipTo>
$packages_xml
$shipment_options_xml       
$negotiated_rates_xml
    </Shipment>
</RatingServiceSelectionRequest>
EOT;
    $parsed = cw_ups_process($query, 'Rate');

    $ups_rates = array();

    $error['code'] = cw_array_path($parsed, 'RATINGSERVICESELECTIONRESPONSE/RESPONSE/ERROR/ERRORCODE/0/#');
    if ($error['code']) {
            $error['msg'] = cw_array_path($parsed, 'RATINGSERVICESELECTIONRESPONSE/RESPONSE/ERROR/ERRORDESCRIPTION/0/#');
    }
    else {
        $origin_code = cw_ups_get_origin_code($src_country_code);
        $dest_code = cw_ups_get_origin_code($dest_code);

        $entries = cw_array_path($parsed, 'RATINGSERVICESELECTIONRESPONSE/RATEDSHIPMENT');
        if (is_array($entries))
        foreach ($entries as $k=>$entry) {
            $service_type = intval(cw_array_path($entry, 'SERVICE/CODE/0/#'));
# kornev, don't use that for now
//              $estimated_time = cw_array_path($entry, 'GUARANTEEDDAYSTODELIVERY/0/#');

            if ($estimated_time) {
                    $curtime = time();
                    $_time = strtotime($estimated_time);
                    if ($_time > $curtime) $estimated_time = ceil(($_time - $curtime)/86400);
                    else $estimated_time = 0;
            }
            else $estimated_time = 0;

            $estimated_rate = cw_array_path($entry, 'TOTALCHARGES/MONETARYVALUE/0/#');
            if ($ups_services[$service_type][$origin_code]) {
                if ($service_type == '11' && $origin_code == 'US' && $dest_code == 'CA')
                    $service_type = '110'; // UPS Standard to Canada
                elseif ($service_type == '65') {
                    if ($origin_code == 'US' || $origin_code == 'PR')
                        $service_type = '145'; // UPS Worldwide Saver (SM)
                    elseif (($origin_code == 'CA' && ($dest_code == 'US' || $dest_code == 'CA')) || ($origin_code == 'EU' && $dest_code == 'EU'))
                        $service_type = '146'; // UPS Express Saver (SM)
                    else
                        $service_type = '144'; // UPS Worldwide Express Saver (SM)
                }
                else
                    $service_type = $ups_services[$service_type][$origin_code];
            }

            $ups_rates[$service_type] = array('original_rate' => $estimated_rate, 'shipping_time' => $estimated_time);
        }
   }
   if ($ups_rates)
   foreach($shipping as $sh) {
        if (!$ups_rates[$sh['code']]) continue;
        
        $sh = array_merge($sh, $ups_rates[$sh['code']]);
        $return[$sh['shipping_id']] = $sh;
    }

    return $return;
}


function cw_ups_process($request, $tool) {

    $post = explode("\n", $request);

    list ($a, $result) = cw_https_request('POST', 'https://www.ups.com:443/ups.app/xml/'. $tool, $post, '', '', 'text/xml');

/*
header('Content-type: text/xml');
echo $result;
die;
*/

    $parse_error = false;
    $options = array(
        'XML_OPTION_CASE_FOLDING' => 1,
        'XML_OPTION_TARGET_ENCODING' => 'ISO-8859-1'
    );

    return cw_xml_parse($result, $parse_error, $options);
}

# kornev, UPS registration functions

function cw_ups_get_license() {
    $dl = UPS_LICENCE;

    $request=<<<EOT
<?xml version='1.0' encoding='ISO-8859-1'?>
<AccessLicenseAgreementRequest>
    <Request>
        <TransactionReference>
            <CustomerContext>License Test</CustomerContext>
            <XpciVersion>1.0001</XpciVersion>
        </TransactionReference>
        <RequestAction>AccessLicense</RequestAction>
        <RequestOption></RequestOption>
    </Request>
    <DeveloperLicenseNumber>{$dl}</DeveloperLicenseNumber>
    <AccessLicenseProfile>
        <CountryCode>US</CountryCode>
        <LanguageCode>EN</LanguageCode>
    </AccessLicenseProfile>
    <OnLineTool>
        <ToolID>TrackXML</ToolID>
        <ToolVersion>1.0</ToolVersion>
    </OnLineTool>
</AccessLicenseAgreementRequest>
EOT;
    $ret = cw_ups_process($request, 'License');

    return cw_array_path($ret, 'ACCESSLICENSEAGREEMENTRESPONSE/ACCESSLICENSETEXT/0/#');
}

function cw_ups_register($license, $userinfo) {
    global $tables;

    $version = cw_query_first_cell("SELECT version FROM $tables[addons] WHERE addon='shipping_ups'");
	$version = substr($version, 0, 16);

	$userinfo_orig = $userinfo;
    $userinfo = cw_ups_xml_quote($userinfo);

    $dl = UPS_LICENCE;

    $userinfo['software_installer'] = $userinfo['software_installer'] == 'Y'?'Y':'N';

	$request=<<<EOT
<?xml version='1.0' encoding='ISO-8859-1'?>
<AccessLicenseRequest xml:lang='en-US'>
	<Request>
		<TransactionReference>
			<CustomerContext>License Test</CustomerContext>
			<XpciVersion>1.0001</XpciVersion>
		</TransactionReference>
		<RequestAction>AccessLicense</RequestAction>
		<RequestOption>AllTools</RequestOption>
	</Request>
	<CompanyName>$userinfo[company]</CompanyName>
	<Address>
		<AddressLine1>$userinfo[address]</AddressLine1>
		<City>$userinfo[city]</City>
		<StateProvinceCode>$userinfo[state]</StateProvinceCode>
		<PostalCode>$userinfo[postal_code]</PostalCode>
		<CountryCode>$userinfo[country]</CountryCode>
	</Address>
	<PrimaryContact>
		<Name>$userinfo[contact_name]</Name>
		<Title>$userinfo[title_name]</Title>
		<EMailAddress>$userinfo[email]</EMailAddress>
		<PhoneNumber>$userinfo[phone]</PhoneNumber>
	</PrimaryContact>
	<CompanyURL>$userinfo[url]</CompanyURL>
	<ShipperNumber>$userinfo[shipper_number]</ShipperNumber>
	<DeveloperLicenseNumber>$dl</DeveloperLicenseNumber>
	<AccessLicenseProfile>
		<CountryCode>US</CountryCode>
		<LanguageCode>EN</LanguageCode>
		<AccessLicenseText>$license</AccessLicenseText>
	</AccessLicenseProfile>
	<OnLineTool>
		<ToolID>RateXML</ToolID>
		<ToolVersion>1.0</ToolVersion>
	</OnLineTool>
	<OnLineTool>
		<ToolID>TrackXML</ToolID>
		<ToolVersion>1.0</ToolVersion>
	</OnLineTool>
	<ClientSoftwareProfile>
		<SoftwareInstaller>$userinfo[software_installer]</SoftwareInstaller>
		<SoftwareProductName>Ars</SoftwareProductName>
		<SoftwareProvider>CartWorks</SoftwareProvider>
		<SoftwareVersionNumber>$version</SoftwareVersionNumber>
	</ClientSoftwareProfile>
</AccessLicenseRequest>
EOT;
		
    $ret = cw_ups_process($request, 'License');

    $error = cw_array_path($ret, 'ACCESSLICENSERESPONSE/RESPONSE/ERROR/ERRORCODE/0/#');

    if ($error) return cw_array_path($ret, 'ACCESSLICENSERESPONSE/RESPONSE/ERROR/ERRORDESCRIPTION/0/#');

    $accesskey = cw_array_path($ret, 'ACCESSLICENSERESPONSE/ACCESSLICENSENUMBER/0/#');

	$ups_userinfo = $userinfo_orig;

    $post_counter = 0;
	$suggest = "suggest";

	while ($post_counter < 10) {
    	$username = cw_ups_generate_unique_string(0, 12);
	    $password = cw_ups_generate_unique_string(16, 10);

        $request=<<<EOT
<?xml version='1.0'?>
<RegistrationRequest>
	<Request>
		<TransactionReference>
			<CustomerContext>x893</CustomerContext>
			<XpciVersion>1.0001</XpciVersion>
		</TransactionReference>
		<RequestAction>Register</RequestAction>
		<RequestOption>$suggest</RequestOption>
	</Request>
	<UserId>$username</UserId>
	<Password>$password</Password>
	<RegistrationInformation>
		<UserName>$userinfo[contact_name]</UserName>
		<Title>$userinfo[title_name]</Title>
		<CompanyName>$userinfo[company]</CompanyName>
		<Address>
			<AddressLine1>$userinfo[address]</AddressLine1>
			<City>$userinfo[city]</City>
			<StateProvinceCode>$userinfo[state]</StateProvinceCode>
			<PostalCode>$userinfo[postal_code]</PostalCode>
			<CountryCode>$userinfo[country]</CountryCode>
		</Address>
		<PhoneNumber>$userinfo[phone]</PhoneNumber>
		<EMailAddress>$userinfo[email]</EMailAddress>
	</RegistrationInformation>
</RegistrationRequest>
EOT;

        $ret = cw_ups_process($request, 'Register');
        $error = cw_array_path($ret, 'REGISTRATIONRESPONSE/RESPONSE/RESPONSESTATUSCODE/0/#');

        if ($error == 1) break;
        $post_counter++;
    }

    if ($error == 1) {
        cw_config_update('shipping_ups', array('accesskey' => $accesskey, 'username' => $username, 'password' => $password));
        return;
    }

    return cw_array_path($ret, 'REGISTRATIONRESPONSE/RESPONSE/ERROR/ERRORDESCRIPTION/0/#');
}

# kornev, additional function

function cw_ups_xml_quote($arg) {
    if (is_array($arg)) {
        foreach ($arg as $k=>$v) {
            if ($k == 'phone')
                $arg[$k] = preg_replace('/[^0-9]/', "", $v);
            elseif (is_string($v))
                $arg[$k] = htmlspecialchars($v);
        }
        return $arg;

    }
    elseif (is_string($arg))
        return htmlspecialchars($arg);
}

function cw_ups_generate_unique_string($pos, $length) {
    $str = md5(uniqid(rand()));
    return substr($str, $pos, $length);
}

function cw_ups_get_origin_code($code) {

    $origin_code = '';

    // EU members (Poland is also EU member, but has different location in $ups_services)

    $eu_members = array('AT', // Austria
                        'BE', // Belgium
                        'BU', // Bulgaria
                        'CY', // Cyprus
                        'CZ', // Czech Republic
                        'DK', // Denmark
                        'EE', // Estonia
                        'FI', // Finland
                        'FR', // France
                        'DE', // Germany
                        'GR', // Greece
                        'HU', // Hungary
                        'IE', // Ireland
                        'IT', // Italy
                        'LV', // Latvia
                        'LT', // Lithuania
                        'LU', // Luxembourg
                        'MT', // Malta
                        'MC', // Monaco
                        'NL', // Netherlands
                        'PT', // Portugal
                        'RO', // Romania
                        'SK', // Slovakia
                        'SI', // Slovenia
                        'ES', // Spain
                        'SE', // Sweden
                        'GB' // United Kingdom
                    );

    if (in_array($code, array('US','CA','PR','MX','PL'))) {

        // Origin is US, Canada, Puerto Rico or Mexico
        $origin_code = $code;

    } elseif (in_array($code, $eu_members)) {

        // Origin is European Union
        $origin_code = 'EU';

    } else {

        // Origin is other countries
        $origin_code = 'OTHER_ORIGINS';
    }

    return $origin_code;
}
