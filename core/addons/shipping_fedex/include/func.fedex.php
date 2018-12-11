<?php
cw_load('xml', 'http', 'map');

function cw_fedex_shipping_get_rates($params, $return) {
    global $config;

    if (!$config['shipping_fedex']['account_number'] || !$config['shipping_fedex']['key'] || !$config['shipping_fedex']['password']) return $return;

    extract($params);
# kornev, is there any fedex shipping enabled for us?
# kornev, TOFIX speed up is possible
/*
    $weight_condition = "weight_min<='$weight' AND (weight_limit='0' OR weight_limit>='$weight')";
    $shipping = cw_func_call('cw_shipping_search', array('data' => array('active' => 1, 'addon' => 'shipping_fedex', 'where' => array($weight_condition))));
    if (!$shipping) return $return;
*/
    $shipping = $return;

    # Default FedEx shipping options
    if ($config['shipping_fedex']['carrier_codes'][0] == 'N') unset($config['shipping_fedex']['carrier_codes']);

    $fedex_options = array (
        'carrier_codes' => array('FDXE', 'FDXG', 'FXSP'),
        'dropoff_type'     => 'REGULAR_PICKUP',
        'packaging'     => 'FEDEX_ENVELOPE',
        'list_rate'     => 'false',
        'ship_date'     => 0,
        'package_count' => 1,
        'currency_code' => 'USD',
    );
    $fedex_options = cw_array_merge($fedex_options, $config['shipping_fedex']);

    $fedex_host = ($config['shipping_fedex']['is_test_mode'] == 'Y' ? 'gatewaybeta.fedex.com:443/web-services' : 'gateway.fedex.com:443/web-services');


    $fedex_options['declared_value'] = $cart['info']['subtotal'];

    $supportHome = array('US'=>1,'CA'=>1);
    $supportGrnd = array('US'=>1,'CA'=>1,'PR'=>1);

    if ($supportHome[$address['country']])
        $fedex_options['residential_delivery'] = 'false';
	
    $carrier_codes = $fedex_options['carrier_codes'];
	
    if (!empty($carrier_codes) && is_array($carrier_codes)) {

        $fedex_rates = array();

//      $packages = array(array('weight' => $weight));
        $packages = cw_call('cw_shipping_get_packages', array($params));

        $xml_query = cw_fedex_prepare_xml_query($packages, $weight, $fedex_options, $to_address, $from_address);

        $data = preg_split("/(\r\n|\r|\n)/",$xml_query, -1, PREG_SPLIT_NO_EMPTY);
        $host = "https://".$fedex_host;

        list($header, $result) = cw_https_request("POST", $host, $data,"","","text/xml");

        $parse_error = false;
        $options = array(
            'XML_OPTION_CASE_FOLDING' => 1,
            'XML_OPTION_TARGET_ENCODING' => 'ISO-8859-1'
        );

       # kornev, make it easy
       $result = preg_replace('/v7:/', '', $result);
/*
header('Content-type: text/xml');
echo $result;
die;
*/

       $parsed = cw_xml_parse($result, $parse_error, $options);

       $error = array();

       if (empty($parsed)) {
           $error['msg'] = "FedEx addon (rates): Received data could not be parsed correctly.";
           return $return;
       }

       $parsed = cw_array_path($parsed, 'SOAP-ENV:ENVELOPE/SOAP-ENV:BODY/RATEREPLY');

       $error['code'] = cw_array_path($parsed, "NOTIFICATIONS/CODE/0/#");
       $error['severity'] = cw_array_path($parsed, "NOTIFICATIONS/SEVERITY/0/#");

       if (!empty($error['code']) && $error['severity'] == 'ERROR') {
           $error['msg'] = cw_array_path($parsed, "NOTIFICATIONS/MESSAGE/0/#");
       } else {
           $entries = cw_array_path($parsed, 'RATEREPLYDETAILS');


           if (is_array($entries))
               foreach ($entries as $k=>$entry) {
                   $service_type = cw_array_path($entry, 'SERVICETYPE/0/#');
# kornev, won't use that for now
//                 $estimated_time = cw_array_path($entry, 'COMMITDETAILS/COMMITTIMESTAMP/0/#');

                    if ($estimated_time) {
                        $curtime = time();
                        $_time = strtotime($estimated_time);
                        if ($_time > $curtime) 
                            $estimated_time = ceil(($_time - $curtime)/86400);
                        else 
                            $estimated_time = 0;
                    } else 
                        $estimated_time = 0;

                    $estimated_rate = 
                        cw_array_path($entry, 'RATEDSHIPMENTDETAILS/SHIPMENTRATEDETAIL/TOTALNETCHARGE/AMOUNT/0/#');

                    $variable_handling_charge = 
                        cw_array_path($entry, 'RATEDSHIPMENTDETAILS/SHIPMENTRATEDETAIL/TOTALVARIABLEHANDLINGCHARGES/VARIABLEHANDLINGCHARGE/AMOUNT/0/#');
                if (doubleval($variable_handling_charge) > 0) 
                     $estimated_rate += $variable_handling_charge;

                $fedex_rates[$service_type] = array('original_rate' => $estimated_rate, 'shipping_time' => $estimated_time);
            }
        }

        // save rates defined manually in order to apply them below as markups

        $surcharges = array(); 
        foreach ($return as $shipping_id => $surcharge) {
            foreach ($fedex_rates as $service_code => $fedex_rate) {
                if ($service_code == $surcharge['code']) {
                    $surcharges[$shipping_id] = $surcharge;
                }   
            }  
/*
            if (!isset($surcharges[$shipping_id]) && $surcharge['carrier_id'] == 28) { 
                $surcharges[$shipping_id] = $surcharge;
            }
*/
        }    

        $total = $params['what_to_ship_params'];
        $return = array();
        if ($fedex_rates) {
            foreach($shipping as $sh) {
                if (empty($fedex_rates[$sh['code']]) && $sh['carrier_id'] != 28) continue;
# kornev, groud has got the same code

                if (!empty($fedex_rates[$sh['code']]))          
                    $sh = array_merge($sh, $fedex_rates[$sh['code']]);

                $return[$sh['shipping_id']] = $sh;
                foreach ($surcharges as $surcharge) {
                    if ($surcharge['shipping_id'] == $sh['shipping_id']) {
                        $apply_to = ($surcharge['apply_to'] == 'ST') ? 'ST' : 'DST';
                        $return[$sh['shipping_id']]['original_rate'] += $surcharge['rate'] +
                            $total['apply']['weight'] * $surcharge['weight_rate'] +
                            $total['apply']['items'] * $surcharge['item_rate'] +
                            $total['apply'][$apply_to] * $surcharge['rate_p'] / 100;

                        if ($surcharge['overweight'] > 0 and $surcharge['overweight'] < $total['apply']['weight'] and 
                            $surcharge['overweight_rate'] > 0) {
                            $weight_diff = $total['apply']['weight'] - $surcharge['overweight'];
                            $return[$sh['shipping_id']]['original_rate'] += $weight_diff * $surcharge['overweight_rate'];
                        }
                    }
                }   
            }
        }
    }
    return $return;
}

function cw_fedex_prepare_xml_query($packages, $weight, $fedex_options, $to_address, $from_address) {
    global $config;

    $fedex_weight = cw_units_convert(cw_weight_in_grams($weight), "g", "lbs", 1);
    if ($fedex_weight < 1)
        $fedex_weight = 1;

    $_time = time() + $config['Appearance']['timezone_offset'] + intval($fedex_options['ship_date'])*24*3600;
    $fedex_options['ship_date_ready'] = date("Y-m-d", $_time)."T".date("H:i:s", $_time);

    $fedex_options['original_country_code'] = $from_address["country"];
    if (in_array($fedex_options['original_country_code'], array('US', 'CA'))) {
        $fedex_options['original_postal_code'] = preg_replace("/[^A-Za-z0-9]/", "", $from_address["zipcode"]);
        $fedex_options['original_state_code'] = $from_address["state"];
    }
    else {
        $fedex_options['original_postal_code'] = preg_replace("/[^A-Za-z0-9]/", "", $from_address["zipcode"]);
        $fedex_options['original_state_code'] = '';
    }

    $fedex_options['destination_country_code'] = $to_address["country"];
    $fedex_options['destination_postal_code'] = preg_replace("/[^A-Za-z0-9]/", "", $to_address["zipcode"]);

    if (in_array($fedex_options['destination_country_code'], array('US', 'CA'))) {
        $fedex_options['destination_state_code'] = $to_address["state"];
    }
/*
    $return_transit_and_commit = <<<OUT
    <q0:ReturnTransitAndCommit>true</q0:ReturnTransitAndCommit>
OUT;
*/
    // Carrier codes

    $carriers_xml = '';
    foreach ($fedex_options['carrier_codes'] as $carrier) {
        $carriers_xml .= <<<OUT
    <q0:CarrierCodes>{$carrier}</q0:CarrierCodes>
OUT;
    }

    // Special services

    $special_services_types = $special_services = array(
        'package'     => array(),
        'shipment'     => array()
    );

    if (!empty($fedex_options['cod_value']) && doubleval($fedex_options['cod_value']) > 0) {
        $special_services['shipment'][] = <<<OUT
            <q0:CodDetail>
                <q0:CollectionType>{$fedex_options['cod_type']}</q0:CollectionType>
            </q0:CodDetail>
OUT;
            $special_services['shipment'][] = <<<OUT
        <q0:CodCollectionAmount>
            <q0:Currency>{$fedex_options['currency_code']}</q0:Currency>
            <q0:Amount>{$fedex_options['cod_value']}</q0:Amount>
        </q0:CodCollectionAmount>
OUT;
            $special_services_types['shipment'][] = 'COD';
    }

    if ($fedex_options['hold_at_location'] == 'Y') {
        $special_services_types['shipment'][] = 'HOLD_AT_LOCATION';
        $special_services['shipment'][] = "<q0:HoldAtLocationDetail><q0:PhoneNumber>$to_address[phone]</q0:PhoneNumber></q0:HoldAtLocationDetail>";
    }

    if (!empty($fedex_options['dg_accessibility'])) {
        $special_services['package'][] = <<<OUT
        <q0:DangerousGoodsDetail>
            <q0:Accessibility>{$fedex_options['dg_accessibility']}</q0:Accessibility>
        </q0:DangerousGoodsDetail>
OUT;
        $special_services_types['package'][] = 'DANGEROUS_GOODS';
    }

    if ($fedex_options['dry_ice'] == 'Y') {
        $special_services['package'][] = <<<OUT
        <q0:DryIceWeight>
            <q0:Units>LB</q0:Units>
            <q0:Value>{$fedex_weight}</q0:Value>
        </q0:DryIceWeight>
OUT;
        $special_services_types['package'][] = 'DRY_ICE';
    }
    if ($fedex_options['inside_pickup'] == 'Y')
        $special_services_types['shipment'][] = 'INSIDE_PICKUP';

    if ($fedex_options['inside_delivery'] == 'Y')
        $special_services_types['shipment'][] = 'INSIDE_DELIVERY';

    if ($fedex_options['saturday_pickup'] == 'Y')
        $special_services_types['shipment'][] = 'SATURDAY_PICKUP';

    if ($fedex_options['saturday_delivery'] == 'Y')
        $special_services_types['shipment'][] = 'SATURDAY_DELIVERY';

    if ($fedex_options['nonstandard_container'] == "Y")
        $special_services_types['package'][] = 'NON_STANDARD_CONTAINER';

    if (!empty($fedex_options['signature']))
        $special_services['package'][] = <<<OUT
        <q0:SignatureOptionDetail>
            <q0:OptionType>{$fedex_options['signature']}</q0:OptionType>
        </q0:SignatureOptionDetail>
OUT;

    foreach ($special_services_types as $k => $ss_types) {
        if (!empty($ss_types)) {
            foreach ($ss_types as $key => $ss_type) {
                $special_services_types[$k][$key] = "<q0:SpecialServiceTypes>".$ss_type."</q0:SpecialServiceTypes>";
            }
        }
        $special_services[$k] = cw_array_merge($special_services_types[$k], $special_services[$k]);
    }

    foreach ($special_services as $k => $ss) {
        if (!empty($ss)) {
            $special_services_xml[$k] = '';
            foreach ($ss as $_service)
                $special_services_xml[$k] .= "\t\t".$_service."\n";
            $special_services_xml[$k] = "<q0:SpecialServicesRequested>".$special_services_xml[$k]."</q0:SpecialServicesRequested>";
        }
        else
            $special_services_xml[$k] = '';
    }

    // Packages query

    $package_count = count($packages);

    $i = 1;
    $items_xml = '';
/*
global $REMOTE_ADDR;
if ($REMOTE_ADDR == "85.130.76.171") {
    cw_log_add("fedex_shipping_package", array($packages));
}
*/
    foreach ($packages as $pack) {
        $dimensions_xml = cw_fedex_prepare_dimensions_xml($pack, $fedex_options);

        // Declared value
        $declared_value_xml = '';

        if ($fedex_options['send_insured_value']=='Y' && !empty($pack['price']) && doubleval($pack['price']) > 0) {
            $declared_value_xml = <<<OUT
            <q0:InsuredValue>
                <q0:Currency>{$fedex_options['currency_code']}</q0:Currency>
                <q0:Amount>{$pack['price']}</q0:Amount>
            </q0:InsuredValue>
OUT;
        }

//        $pack['weight'] = cw_units_convert(cw_weight_in_grams($pack[weight]), "g", "lbs", 1);
//        if (!$pack['weight']) $pack['weight'] = 1;

        $items_xml .= <<<EOT
        <q0:RequestedPackageLineItems>
            <q0:SequenceNumber>{$i}</q0:SequenceNumber>
            {$declared_value_xml}
            <q0:Weight>
                <q0:Units>LB</q0:Units>
                <q0:Value>{$pack[weight]}</q0:Value>
            </q0:Weight>
            {$dimensions_xml}
            {$special_services_xml['package']}
        </q0:RequestedPackageLineItems>
EOT;
        $i++;
    }

    $residential = ($fedex_options['residential_delivery'] == 'Y') ? "<q0:Residential>true</q0:Residential>" : "";

    // Handling charges

    if (!empty($fedex_options['handling_charges_amount']) && doubleval($fedex_options['handling_charges_amount']) > 0) {
        $_handling_type = ($fedex_options['handling_charges_type'] == "FIXED_AMOUNT") ? "<q0:FixedValue><q0:Currency>$fedex_options[currency_code]</q0:Currency><q0:Amount>$fedex_options[handling_charges_amount]</q0:Amount></q0:FixedValue>" : "<q0:PercentValue>$fedex_options[handling_charges_amount]</q0:PercentValue>";

        $handling_charges_xml = <<<OUT
    <q0:VariableHandlingChargeDetail>
        <q0:VariableHandlingChargeType>{$fedex_options['handling_charges_type']}</q0:VariableHandlingChargeType>
        $_handling_type
    </q0:VariableHandlingChargeDetail>
OUT;
    }
    else
        $handling_charges_xml = '';

    // Prepare the XML request

    $xml_query = <<<OUT
<?xml version="1.0" encoding="UTF-8" ?>
<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:q0="http://fedex.com/ws/rate/v7" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
<soapenv:Body>
<q0:RateRequest>
    <q0:WebAuthenticationDetail>
        <q0:UserCredential>
            <q0:Key>{$fedex_options['key']}</q0:Key>
            <q0:Password>{$fedex_options['password']}</q0:Password>
        </q0:UserCredential>
    </q0:WebAuthenticationDetail>

    <q0:ClientDetail>
        <q0:AccountNumber>{$fedex_options['account_number']}</q0:AccountNumber>
        <q0:MeterNumber>{$fedex_options['meter_number']}</q0:MeterNumber>
    </q0:ClientDetail>

    <q0:TransactionDetail>
        <q0:CustomerTransactionId>Basic Rate</q0:CustomerTransactionId>
    </q0:TransactionDetail>

    <q0:Version>
        <q0:ServiceId>crs</q0:ServiceId>
        <q0:Major>7</q0:Major>
        <q0:Intermediate>0</q0:Intermediate>
        <q0:Minor>0</q0:Minor>
    </q0:Version>

    {$return_transit_and_commit}

    {$carriers_xml}
    
    <q0:RequestedShipment>
        <q0:ShipTimestamp>{$fedex_options['ship_date_ready']}</q0:ShipTimestamp>
        <q0:DropoffType>{$fedex_options['dropoff_type']}</q0:DropoffType>
        <q0:PackagingType>{$fedex_options['packaging']}</q0:PackagingType>

        <q0:Shipper>
            <q0:Address>
                <q0:StateOrProvinceCode>{$fedex_options['original_state_code']}</q0:StateOrProvinceCode>
                <q0:PostalCode>{$fedex_options['original_postal_code']}</q0:PostalCode>
                <q0:CountryCode>{$fedex_options['original_country_code']}</q0:CountryCode>
            </q0:Address>
        </q0:Shipper>

        <q0:Recipient>
            <q0:Address>
                <q0:StateOrProvinceCode>{$fedex_options['destination_state_code']}</q0:StateOrProvinceCode>
                <q0:PostalCode>{$fedex_options['destination_postal_code']}</q0:PostalCode>
                <q0:CountryCode>{$fedex_options['destination_country_code']}</q0:CountryCode>
                {$residential}
            </q0:Address>
        </q0:Recipient>

        <q0:ShippingChargesPayment>
            <q0:PaymentType>SENDER</q0:PaymentType>
            <q0:Payor>
                <q0:AccountNumber>{$fedex_options['account_number']}</q0:AccountNumber>
                <q0:CountryCode>{$fedex_options['original_country_code']}</q0:CountryCode>
            </q0:Payor>
        </q0:ShippingChargesPayment>

        {$special_services_xml['shipment']}

        {$handling_charges_xml}

        <q0:RateRequestTypes>ACCOUNT</q0:RateRequestTypes>
        <q0:PackageCount>{$package_count}</q0:PackageCount>
        <q0:PackageDetail>INDIVIDUAL_PACKAGES</q0:PackageDetail>

        {$items_xml}

    </q0:RequestedShipment>
</q0:RateRequest>
</soapenv:Body>
</soapenv:Envelope>
OUT;

    return $xml_query;
}

function cw_fedex_prepare_dimensions_xml($pack, $fedex_options) {
    global $config;

    if ($fedex_options['packaging'] == 'YOUR_PACKAGING') {
        $dims = array($pack['length'], $pack['width'], $pack['height']);

        foreach($dims as $k=>$v)
//              $dims[$k] = $v;
            $dims[$k] = intval(cw_units_convert(cw_dim_in_centimeters($v), 'cm', $config['General']['dim_units'], 1));

        list($dim_length, $dim_width, $dim_height) = $dims;

        $dimensions_xml = <<<OUT
    <q0:Dimensions>
        <q0:Length>{$dim_length}</q0:Length>
        <q0:Width>{$dim_width}</q0:Width>
        <q0:Height>{$dim_height}</q0:Height>
        <q0:Units>IN</q0:Units>
    </q0:Dimensions>
OUT;
    } else {
        $dimensions_xml = '';
    }

    return $dimensions_xml;
}

function cw_fedex_get_meter_number($userinfo, &$error) {
    global $config;

    // FedEx host
    $fedex_host = ($config['shipping_fedex']['is_test_mode'] == 'Y' ? 'gatewaybeta.fedex.com/GatewayDC' : 'gateway.fedex.com/GatewayDC');

    $xml_contact_fields = array();
    $xml_address_fields = array();

    $userinfo = array_map('htmlspecialchars', $userinfo);

    if (!empty($userinfo['company_name']))
        $xml_contact_fields[] = "<CompanyName>{$userinfo['company_name']}</CompanyName>";

    if (!empty($userinfo['pager_number']))
        $xml_contact_fields[] = "<PagerNumber>{$userinfo['pager_number']}</PagerNumber>";

    if (!empty($userinfo['fax_number']))
        $xml_contact_fields[] = "<FaxNumber>{$userinfo['fax_number']}</FaxNumber>";

    if (!empty($userinfo['email']))
        $xml_contact_fields[] = "<E-MailAddress>{$userinfo['email']}</E-MailAddress>";

    if (!empty($userinfo['address_2']))
        $xml_address_fields[] = "<Line2>{$userinfo['address_2']}</Line2>";

    $xml_contact_fields_str = implode("\n\t\t", $xml_contact_fields);
    $xml_address_fields_str = implode("\n\t\t", $xml_address_fields);

    if (!empty($userinfo['state']) && in_array($userinfo['country'], array("US", "CA", "PR")))
        $state = "<StateOrProvinceCode>{$userinfo['state']}</StateOrProvinceCode>";
    else
        $state = '';

    $xml_query = <<<OUT
<?xml version="1.0" encoding="UTF-8" ?>
<FDXSubscriptionRequest xmlns:api="http://www.fedex.com/fsmapi" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="FDXSubscriptionRequest.xsd">
    <RequestHeader>
        <CustomerTransactionIdentifier>1</CustomerTransactionIdentifier>
        <AccountNumber>{$config['shipping_fedex']['account_number']}</AccountNumber>
    </RequestHeader>
    <Contact>
        <PersonName>{$userinfo['person_name']}</PersonName>
        <PhoneNumber>{$userinfo['phone_number']}</PhoneNumber>
$xml_contact_fields_str
    </Contact>
    <Address>
        <Line1>{$userinfo['address_1']}</Line1>
$xml_address_fields_str
        <City>{$userinfo['city']}</City>
        $state
        <PostalCode>{$userinfo['zipcode']}</PostalCode>
        <CountryCode>{$userinfo['country']}</CountryCode>
    </Address>
</FDXSubscriptionRequest>
OUT;

    $data = explode("\n", $xml_query);
    $host = "https://".$fedex_host;
    list($header, $result) = cw_https_request('POST', $host, $data,'','','text/xml');

    $parse_error = false;
    $options = array(
        'XML_OPTION_CASE_FOLDING' => 1,
        'XML_OPTION_TARGET_ENCODING' => 'UTF-8'
    );

    $parsed = cw_xml_parse($result, $parse_error, $options);

    $error = array();

    if (empty($parsed)) {
        $error['msg'] = cw_get_langvar_by_name("msg_fedex_meter_number_incorrect_data_err");
        return false;
    }

    $type = key($parsed);

    $meter_number = cw_array_path($parsed, $type."/METERNUMBER/0/#");

    if (empty($meter_number)) {

        $error['code'] = cw_array_path($parsed, $type."/ERROR/CODE/0/#");
        $error['msg'] = cw_array_path($parsed, $type."/ERROR/MESSAGE/0/#");

        if (empty($error['code'])) {
            $error['code'] = cw_array_path($parsed, "ERROR/CODE/0/#");
            $error['msg'] = cw_array_path($parsed, "ERROR/MESSAGE/0/#");
        }

        if (!empty($error['code']))
            $error['msg'] = "FedEx addon error: [{$error['code']}] {$error['msg']}";
        else
            $error['msg'] = cw_get_langvar_by_name("msg_fedex_meter_number_empty_err");

        return false;

    }

    return $meter_number;
}
