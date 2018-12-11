<?php
global $smarty, $customer_id;

if (!empty($customer_id)) {
	cw_header_location('index.php');
}

$fb_referer = &cw_session_register('fb_referer');

$fb_referer = !empty($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'index.php';

$smarty->assign('main', "fb_auth_get_email");
