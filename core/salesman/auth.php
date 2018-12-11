<?php
# [TOFIX]
# kornev, fix the whole area - move to addon
define('AREA_TYPE', 'B');
$current_area = AREA_TYPE;

$customer_id = &cw_session_register('customer_id');

$top_message = &cw_session_register('top_message', array());
if (!empty($top_message)) {
    $smarty->assign('top_message', $top_message);
	$top_message = '';
}

cw_include('init/lng.php');
cw_include('include/check_useraccount.php');

cw_include('include/area_sections.php');

$smarty->assign('current_target', $target);

if (!$addons['salesman'])
    cw_header_location($app_catalogs['customer']);

$location = array();
$location[] = array(cw_get_langvar_by_name('lbl_area_salesman'), 'index.php');

cw_call('cw_auth_security');

