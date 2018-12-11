<?php

$current_domain = &cw_session_register('current_domain', -1);

if (in_array(APP_AREA, array('admin'))) {
# kornev, admin top filter

    if ($action == 'set-domains-filter') {
        $current_domain = $domain_selection;
        cw_header_location($l_redirect);
    }
}

$smarty->assign('current_domain', $current_domain);

if (is_array($smarty->template_dir) && count($smarty->template_dir)>1 && $current_domain) {
    $altskin = str_replace($app_dir,'',$smarty->template_dir[0]);
    $smarty->assign('AltImagesDir', $app_web_dir . $altskin . '/images');
    $smarty->assign('AltSkinDir', $app_web_dir . $altskin);
    if (@file_exists($app_dir . $altskin. '/altskin.css')) {
        cw_addons_add_css('altskin.css');
    }
    if (@file_exists($app_dir . $altskin. '/'.APP_AREA.'_altskin.css')) {
        cw_addons_add_css(APP_AREA.'_altskin.css');
    }
}

if (!$data && $current_domain) {
    $data = cw_func_call('cw_md_domain_get', array('domain_id' => $current_domain));
}

$host_value = cw_md_get_host();
$domain_full_host = $HTTPS ? $data['https_host'] : $data['http_host'];

// if use alias
if ($host_value != $domain_full_host) {
	global $http_location, $https_location, $current_location, $current_host_location;
	global $smarty, $app_config_file, $var_dirs_web, $app_dirs, $HTTPS;

	$http_location 			= 'http://' . $host_value . $app_config_file['web']['web_dir'];
	$https_location 		= 'https://' . $host_value . $app_config_file['web']['web_dir'];
	$current_location 		= $HTTPS ? $https_location : $http_location;
	$current_host_location 	= ($HTTPS ? 'https://' : 'http://') . $host_value;

	$smarty->assign('current_location', 		$current_location);
	$smarty->assign('current_host_location', 	$current_host_location);

	$app_catalogs = array();
	$app_catalogs_secure = array();
	foreach ($app_dirs as $k => $v) {
	    $app_catalogs[$k] = $current_location . ($v ? with_leading_slash($v) : '');
	    $app_catalogs_secure[$k] = $https_location . ($v ? with_leading_slash($v) : '');
	}
	
	$smarty->assign('catalogs', 		$app_catalogs);
	$smarty->assign('catalogs_secure', 	$app_catalogs_secure);
}

# kornev, it will work faster in comparison with the additional table join
global $domain_attributes;
cw_load('attributes');
$domain_attributes = cw_func_call('cw_attributes_get_attributes_by_field',array('field'=>'domains'));

