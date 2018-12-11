<?php
define('AREA_TYPE', seller_area_letter);
$current_area = AREA_TYPE;

$customer_id = &cw_session_register('customer_id');

cw_include('include/check_useraccount.php');
cw_include('init/lng.php');

cw_include('include/settings.php');

if ($target != 'ajax') {
    cw_include('include/area_sections.php');
    $smarty->assign('current_target', $target);
}

$location = array();
$location[] = array(cw_get_langvar_by_name('lbl_seller', null, false, true), 'index.php');

cw_call('cw_auth_security');
