<?php
# [TOFIX]
# kornev, move to the addon
define('AREA_TYPE', 'P');
$current_area = AREA_TYPE;

$customer_id = &cw_session_register('customer_id');

$top_message = &cw_session_register('top_message');
if (!empty($top_message)) {
	$smarty->assign('top_message', $top_message);
	$top_message = '';
}

cw_include('init/lng.php');
cw_include('include/check_useraccount.php');

cw_include('include/area_sections.php');
$smarty->assign('current_target', $target);

if (!$addons['warehouse'])
    cw_header_location($app_catalogs['customer']);

$location = array();
$location[] = array(cw_get_langvar_by_name('lbl_area_warehouse'), 'index.php');

cw_call('cw_auth_security');
