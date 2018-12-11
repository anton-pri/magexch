<?php
function cw_usps_parce_result($result, $xml_head, $type) {
	
	$response['type'] = $type;
	$response['error'] = false;
	$res = cw_xml2hash($result);
	
	if ($res['Error']) {
		$response['error'] = true;
		$response['type'] = "txt";
		$response['data'] = $res['Error']['Description'];
		$response['error_code'] = $res['Error']['Number'];
		
		return $response;
	} 
	if ($res[$xml_head.'Response']) {
		if (!empty($res[$xml_head.'Response']['DeliveryConfirmationLabel'])) {
			$response['data'] = base64_decode(str_replace(array("\n"), array(""), $res[$xml_head.'Response']['DeliveryConfirmationLabel']));
		} elseif (!empty($res[$xml_head.'Response']['LabelImage'])) {
			$response['data'] = base64_decode(str_replace(array("\n"), array(""), $res[$xml_head.'Response']['LabelImage']));
		} elseif (!empty($res[$xml_head.'Response']['EMConfirmationNumber']) && $type == 'txt') {
			$response['data'] = $res[$xml_head.'Response']['EMConfirmationNumber'];
		}
	}

	return $response;
}

function cw_usps_save_response($data, $method, $num) {
	global $app_main_dir;
	
	if (!is_dir("$app_main_dir/var/tmp/usps_test_labels/")) {
		if (!cw_mkdir("$app_main_dir/var/tmp/usps_test_labels/")) {
			return false;
		}
	}
	
	$fp = fopen("$app_main_dir/var/tmp/usps_test_labels/usps_$method($num).".$data['type'], "w");
	if (!$fp) {
		return false;
	}
	fputs($fp, $data['data']);
	fclose($fp);

	return true;
}

#
# Check shipping_id:
#	1. Is it U.S.P.S shipping_id?
#	2. Is it valid shipping_id?
#
function cw_usps_check_shipping_id($shipping_id) {
	global $tables;

	$shipping = cw_query_first("SELECT * FROM $tables[shipping] WHERE code = 'USPS' AND shipping_id = '".$shipping_id."'");
	if(empty($shipping))
		return false;

	$service_type = false;
	switch ($shipping['shipping']) {
		case 'USPS Express Mail':
			$service_type = 'ExpressMail';
			break;
		case 'USPS Global Express Mail (EMS)':
		case 'USPS Global Express Guaranteed Non-Document Service':
		case 'USPS Global Express Guaranteed Document Service':
			$service_type = 'GlobalExpress';
			break;
		case 'USPS Global Priority Mail - Flat-rate Envelope (Small)':
		case 'USPS Global Priority Small service':
		case 'USPS Global Priority Mail - Variable Weight (Single)':
			$service_type = 'GlobalPriority';
			break;
		case 'USPS Global AirMail Parcel':
		case 'USPS Airmail Letter Post':
			$service_type = 'GlobalAir';
		break;
		case 'USPS Priority Mail':
			$service_type = 'Priority';
			break;
		case 'USPS First Class':
		case 'USPS First-Class Mail':
			$service_type = 'First Class';
			break;
		case 'USPS Parcel Post':
		case 'USPS Economy (Surface) Parcel Post':
		case 'USPS Airmail Parcel Post':
			$service_type = 'Parcel Post';
			break;

		case 'USPS BPM':
			$service_type = 'Bound Printed Matter';
			break;

		case 'USPS Bound Printed Matter':
			$service_type = 'Bound Printed Matter';
			break;
		case 'USPS Media':
			$service_type = 'Media Mail';
			break;

		case 'USPS Library':
			$service_type = 'Library Mail';
			break;
		case 'USPS Library Mail':
			$service_type = 'Library Mail';
			break;
		default: 
			$service_type = "Error";
			break;
	}

	return $service_type;
}

#
# Check shipping_id:
#	1. Is it UPS shipping_id?
#	2. Is it valid shipping_id?
#
function cw_ups_check_shipping_id($shipping_id) {
	global $tables;

	$shipping = cw_query_first("SELECT * FROM $tables[shipping] WHERE code = 'UPS' AND shipping_id = '".$shipping_id."'");
	if(empty($shipping))
		return false;

	$service_type = false;
	switch ($shipping['shipping']) {
		case 'UPS Ground':
			$service_type = 'Ground';
			break;
		case 'UPS 3 Day Select##SM##':
			$service_type = '3 Day Select';
			break;
		case 'UPS 2nd Day Air##R##':
			$service_type = '2nd Day Air';
			break;
		case 'UPS 2nd Day Air A.M.##R##':
			$service_type = '2nd Day Air AM';
			break;
		case 'UPS Next Day Air Saver##R##':
			$service_type = 'Next Day Air Saver';
			break;
		case 'UPS Next Day Air##R##':
			$service_type = 'Next Day Air';
			break;
		case 'UPS Next Day Air##R## Early A.M.##R##':
			$service_type = 'Next Day Air Early AM';
			break;
		case 'UPS Worldwide Express Plus##SM##':
			$service_type = 'Worlwide Express Plus';
			break;
		case 'UPS Worldwide Express##SM##':
			$service_type = 'Worlwide Express';
			break;
		case 'UPS Worldwide Expedited##SM##':
			$service_type = 'Worlwide Expedited';
			break;
	}

	return $service_type;
}

#
# Get addon name by shipping_id
#
function cw_get_shipping_addon($shipping_id) {
	global $tables, $slg_addons;

	if(empty($shipping_id))
		return false;

	$code = cw_query_first_cell("SELECT code FROM $tables[shipping] WHERE shipping_id = '$shipping_id'");
	if(!empty($slg_addons[$code])) {
		return $slg_addons[$code];
	}
	return false;
}

#
# Detect UPS sevice type
#
function cw_ups_service_type($order) {
	global $tables;

	if(empty($order['order']['shipping_id']))
		return false;
	$shipping = cw_query_first_cell("SELECT shipping FROM $tables[shipping] WHERE code = 'UPS' AND shipping_id = '".$order['order']['shipping_id']."'");
	if(empty($shipping))
		return false;

	$str = false;
	if(strpos($shipping, "UPS Next Day Air A.M.") === 0 || (strpos($shipping, "UPS Next Day Air") === 0 && strpos($shipping, "Early A.M.") !== false)) {
		$str = "Next Day Air Early AM";
	} elseif(strpos($shipping, "UPS Next Day Air Saver") === 0) {
		$str = "Next Day Air Saver";
	} elseif(strpos($shipping, "UPS Next Day Air") === 0) {
		$str = "Next Day Air";
	} elseif(strpos($shipping, "UPS 2nd Day Air A.M.") === 0) {
		$str = "2nd Day Air AM";
	} elseif(strpos($shipping, "UPS 2nd Day Air") === 0) {
		$str = "2nd Day Air";
	} elseif(strpos($shipping, "UPS 3 Day Select") === 0) {
		$str = "3 Day Select";
	} elseif(strpos($shipping, "UPS Ground") === 0) {
		$str = "Ground";
	}
	return $str;
}
