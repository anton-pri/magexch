<?php
// get clean hostname
function cw_mobile_get_host() {
	global $HTTP_HOST, $CLIENT_IP, $REMOTE_ADDR;

	$host = $HTTP_HOST;
	
	if (empty($host)) {
		$host = gethostbyaddr($CLIENT_IP);		
	}

	if (empty($host)) {
		$host = gethostbyaddr($REMOTE_ADDR);		
	}
	
	return strtolower($host);
}

// get mobile host by domain id
function cw_mobile_get_mobile_host_attr_by_domain_id($domain_id) {
	global $tables;

	$mobile_mobile_flag = cw_session_register('mobile_mobile_flag');

	cw_load('attributes');

	$current_language = cw_query_first_cell("SELECT value FROM $tables[config] WHERE name='default_customer_language'");
	$attributes = cw_func_call(
		'cw_attributes_get', 
		array(
			'item_id' => $domain_id, 
			'item_type' => 'DM', 
			'language' => $current_language
		)
	);

	return !empty($attributes['mobile_host']['value']) ? 
			trim($attributes['mobile_host']['value']) : 
			($mobile_mobile_flag == 'on' && $_GET['mobile_version'] != 'off' ? cw_mobile_get_host() : '');
}

// get domain data by mobile host attribute
function cw_mobile_get_domain_by_mobile_host_attr($host) {
	global $tables, $HTTPS;

	cw_load('attributes');

	$mobile_attributes = cw_func_call('cw_attributes_get_attributes_by_field', array('field' => 'mobile_host'));

	if (!empty($mobile_attributes['DM'])) {
		$attribute_id = intval($mobile_attributes['DM']);
		// get domain data
		return cw_query_first("SELECT d.*, av.value as mobile_host 
								FROM $tables[domains] d
								LEFT JOIN $tables[attributes_values] av ON av.item_id = d.domain_id
								LEFT JOIN $tables[attributes] a ON a.attribute_id = av.attribute_id
								WHERE av.attribute_id = '" . $attribute_id . "'
									AND av.value = '" . $host . "' 
									AND a.active = 1");
	}
	else {
		$mobile_mobile_flag = cw_session_register('mobile_mobile_flag');

		if ($mobile_mobile_flag == 'on' && $_GET['mobile_version'] != 'off') {
			$result = cw_query_first("SELECT *, http_host as mobile_host
					FROM $tables[domains]
					WHERE " . ($HTTPS ? "https_host" : "http_host") . " ='" . $host . "'");
			$result['skin'] .= mobile_addon_skin_prefix;
			
			return $result;
		}
	}

	return array();
}

// get domain data hook
function cw_mobile_domain_get($params, $return) {
	global $app_dir;

	if (isset($return['domain_id']) && !isset($return['mobile_host'])) {
		$return['mobile_host'] = cw_mobile_get_mobile_host_attr_by_domain_id($return['domain_id']);
		$host_value = cw_mobile_get_host();

		if (
			$return['mobile_host'] == $host_value
			&& is_dir($app_dir . $return['skin'] . mobile_addon_skin_prefix)
		) {
			$return['skin'] .= mobile_addon_skin_prefix;
		}
	}

	return $return;
}

// get domain data
function cw_mobile_get_domain_data() {
	global $tables, $HTTPS, $app_dir;

	$current_domain = cw_session_register('current_domain', -1);
	$mobile_mobile_flag = cw_session_register('mobile_mobile_flag');

	if (is_numeric($current_domain) && $current_domain > 0 && !empty($tables['domains'])) {
		$domain = cw_query_first("SELECT * FROM $tables[domains] WHERE domain_id = " . $current_domain);		
		$domain['mobile_host'] = cw_mobile_get_mobile_host_attr_by_domain_id($current_domain);

		if ((($mobile_mobile_flag == 'on' && $_GET['mobile_version'] != 'off')	// if mobile version set by link and not turn off now
			|| $_GET['mobile_version'] == 'on'									// if mobile version select now
			|| (																// if set mobile host
				$mobile_mobile_flag != 'on' 
				&& !empty($domain['mobile_host']) 
				&& $domain['mobile_host'] == cw_mobile_get_host()
			))
			&& is_dir($app_dir . $domain['skin'] . mobile_addon_skin_prefix)	// and mobile skin exist
		) {
			$domain['skin'] .= mobile_addon_skin_prefix;
		}

		return $domain;
	}

	$host_value = cw_mobile_get_host();
	$result = cw_mobile_get_domain_by_mobile_host_attr($host_value);

	return $result;
}

// check if mobile host is specified
function cw_mobile_check_mobile_host_is_specified() {
	$mobile_mobile_flag = &cw_session_register('mobile_mobile_flag');
	$save_mobile_mobile_flag = $mobile_mobile_flag;	
	$mobile_mobile_flag = '';

	$data = cw_mobile_get_domain_data();

	$mobile_mobile_flag = $save_mobile_mobile_flag;

	return !empty($data['mobile_host']);
}

// function try get domain data by host alias
function cw_mobile_get_domain_data_by_alias(&$host_data) {
	global $tables, $app_dir;

	if (isset($host_data['mobile_host'])) {
		return $host_data;
	}

	$host_value = cw_mobile_get_host();

	if (isset($host_data['domain_id'])) {
		$host_data['mobile_host'] = cw_mobile_get_mobile_host_attr_by_domain_id($host_data['domain_id']);
	}
	else {
		// Get domain by HTTP_HOST
		$result = cw_mobile_get_domain_by_mobile_host_attr($host_value);
	}

	if (!empty($result)) {
		$host_data = $result;
	}

	if (
		$host_data['mobile_host'] == $host_value 
		&& is_dir($app_dir . $host_data['skin'] . mobile_addon_skin_prefix)
	) {
        $host_data['http_host']=$host_data['mobile_host'];
		$host_data['skin'] .= mobile_addon_skin_prefix;
	}

	return $host_data;
}

// place order_from_mobile_host parameter
function cw_mobile_doc_place_order($params, $return = null) {

    if (!$return) return array();

	cw_load('doc');

	$mobile_select_type = cw_session_register('mobile_select_type');

	if (
		is_array($return) 
		&& count($return)
		&& $mobile_select_type == 1
	) {
		foreach ($return as $value) {
			cw_call('cw_doc_place_extras_data', array($value, array('order_from_mobile_host' => 1)));
		}		
	}

    return $return;
}

// use like alt skin
function cw_mobile_code_get_template_dir($params, $return) {
	global $target, $app_dir, $tables;

	$return = (array)$return;

	$data = cw_mobile_get_domain_data();	
	$altskin = $data['skin'];

	if (!$altskin) return $return;

	if (!in_array($app_dir . $altskin, $return, true)) {
		array_unshift($return, $app_dir . $altskin);
	}

	return $return;
}
