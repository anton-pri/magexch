<?php
global $config, $current_location;

$fb_referer 	= &cw_session_register('fb_referer');
$fb_access_token= &cw_session_register('fb_access_token');
$top_message 	= &cw_session_register('top_message');

$fb_referer = !empty($fb_referer) ? $fb_referer : (!empty($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'index.php');

if (
	$_GET 
	&& !empty($_GET['accessToken']) 
	&& is_numeric($_GET['id'])
	&& $_GET['app_secret'] === $config[fbauth_addon_name]['fbauth_app_secret']
) {
	$fb_access_token = $_GET['accessToken'];

	cw_fbauth_user_login($_GET);

	cw_header_location($fb_referer, TRUE);
}
else {
	$top_message = array('type' => 'E', 'content' => "User's data wrong or empty.");
	cw_header_location($fb_referer, TRUE);
}
