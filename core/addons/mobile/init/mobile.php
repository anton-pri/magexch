<?php
global $tables, $smarty, $HTTPS, $mobile_attributes;
global $app_dir, $app_config_file;

$mobile_device_type = &cw_session_register('mobile_device_type', '');		// user device type ('mobile' : 'computer')
$mobile_select_type = &cw_session_register('mobile_select_type', 2);		// selected version (1 - mobile, 2 - computer)
$mobile_mobile_flag = &cw_session_register('mobile_mobile_flag');			// if user select mobile version by link (used flag)
$mobile_was_redirect = &cw_session_register('mobile_was_redirect', 0);	// is was sistem redirect to mobile version, then 1

if ($_GET['mwr'] == 'Y')
    $mobile_was_redirect = 1;

// Detect device if empty
if (empty($mobile_device_type)) {
	$detect = new Mobile_Detect;
	$mobile_device_type = ($detect->isMobile() && !($detect->isTablet())) ? 'mobile' : 'computer';
    cw_event('on_detect_mobile_device', array($mobile_device_type));
}

// if user select mobile version by link (used flag mobile_version)
if (isset($_GET['mobile_version'])) {
	$mobile_mobile_flag = $_GET['mobile_version'];
}

$domain = cw_mobile_get_domain_data();

$domain_mobile_host = ($HTTPS ? 'https://' : 'http://');
$domain_mobile_host .= (!empty($domain['mobile_host']) ? $domain['mobile_host'] : $domain['http_host']);
$domain_full_host 	= $HTTPS ? $domain['https_host'] : $domain['http_host'];

// if user select mobile version
if (
	$mobile_mobile_flag == 'on'
	||
	cw_mobile_get_host() == $domain['mobile_host']
	&& $mobile_mobile_flag != 'on'
	&& $mobile_select_type == 2
) {
	$mobile_select_type = 1;
}
// if user select full version
else if (
	$mobile_mobile_flag == 'off'
	||
	cw_mobile_get_host() == $domain_full_host
	&& $mobile_mobile_flag != 'off'
	&& $mobile_select_type == 1
) {
	$mobile_select_type = 2;
}
// or redirect to mobile skin if the device is phone
else if ($mobile_device_type == 'mobile' && !$mobile_was_redirect) {

	if (
		!empty($domain)
		&& is_dir($app_dir . $domain['skin'] . mobile_addon_skin_prefix)
	) {
		$param = '';
		// if mobile host not set or have equal host, but have mobile skin folder, then mobile_mobile_flag set to on
		if (
			empty($domain['mobile_host'])
			|| (!empty($domain['mobile_host']) && $domain['mobile_host'] == cw_mobile_get_host())
		) {
			$mobile_mobile_flag = 'on';
			$param = '/index.php?mobile_version=on&mwr=Y';
		}

		$mobile_select_type = 1;
        $mobile_was_redirect = 1;

        if (!empty($REQUEST_URI))  { 

            if (strpos('?', $REQUEST_URI) !== false) 
                $concat_char = '&';
            else 
                $concat_char = '?';

            $redirect = $domain_mobile_host . $REQUEST_URI . $concat_char . 'mobile_version=on&mwr=Y';
        } else
            $redirect = $domain_mobile_host . $domain['web_dir'] . $param;

        // Googlebot refuses to follow redirect when we go to the same URL, so we have to add some param
        if (defined('IS_ROBOT') && constant('IS_ROBOT')!='') {
            if (strpos($redirect,'?')===false) $redirect .= '?mobile_version=on';
            else $redirect .= '&mobile_version=on';
        }
                //cw_log_add('mobile_redirect', array($REQUEST_URI, $redirect));
		cw_header_location($redirect);
	}
}

$smarty->assign('mobile_device_type', $mobile_device_type);
$smarty->assign('mobile_select_type', $mobile_select_type);

// get mobile and full host location
if (is_dir($app_dir . str_replace(mobile_addon_skin_prefix, "", $domain['skin']) . mobile_addon_skin_prefix)) {
	$param = cw_mobile_check_mobile_host_is_specified() ?
			'' :
			($mobile_select_type == 2 ? '/index.php?mobile_version=on' : '/index.php?mobile_version=off');
	$smarty->assign('mobile_host', $domain_mobile_host . $domain['web_dir'] . $param);
	$full_host = $HTTPS ? 'https://' . $app_config_file['web']['https_host'] : 'http://' . $app_config_file['web']['http_host'];
	$smarty->assign('full_host', $full_host . $domain['web_dir'] . $param);
}

// get attributes data
cw_load('attributes');
$values = array();
$mobile_attributes = cw_func_call('cw_attributes_get_attributes_by_field', array('field' => 'display_mode'));

if (!empty($mobile_attributes)) {
	$result = cw_call('cw_attributes_get_attribute_default_value', array('attribute_id' => $mobile_attributes['AB']));
	// get values
	if (is_array($result)) {

		foreach ($result as $value) {

			if (
				$value['value_key'] == 0	// Both
				|| $value['value_key'] == $mobile_select_type
			) {
				$values[] = $value['attribute_value_id'];
			}
		}
	}
}

$mobile_attributes['AB'] = array(
	'attribute_id' 	=> $mobile_attributes['AB'],
	'values' 		=> $values
);

// Identify mobile skin
$is_mobile = 0;
if ($mobile_select_type == 1) {
    $is_mobile = 1;
}

$smarty->assign('is_mobile', $is_mobile);
