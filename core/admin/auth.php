<?php
define('AREA_TYPE', 'A');
$current_area = AREA_TYPE;

$customer_id = &cw_session_register('customer_id');

# kornev, with ajax we are not need these data
cw_include('include/check_useraccount.php');
cw_include('init/lng.php');

cw_include('include/settings.php');

if ($target != 'ajax') {
    cw_include('include/area_sections.php');
    $smarty->assign('current_target', $target);
}

$location = array();
$location[] = array(cw_get_langvar_by_name('lbl_area_admin',null,false,true), 'index.php');

cw_set_hook('cw_auth_security', 'cw_auth_updates', EVENT_PRE);

cw_call('cw_auth_security');

//cw_event_listen('on_login','cw_license_check');
cw_event_listen('on_login','cw_on_login_crontab');

//logging code
cw_include('include/logging_data.php');
