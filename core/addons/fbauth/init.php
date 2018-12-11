<?php
/*
 * Vendor: CW
 * addon: Facebook auth/login
 */

const fbauth_addon_name 		= 'fbauth';
const fbauth_addon_target 		= 'fb_auth';
const fbauth_addon_version 	= '0.1';

if (!empty($addons[fbauth_addon_name]) && APP_AREA == 'customer') {
	cw_include('addons/' . fbauth_addon_name . '/include/func.fbauth.php');

	cw_event_listen('on_logout', 'cw_fbauth_user_logout');

	cw_addons_set_controllers(
        array('replace', 'customer/fb_auth.php', 'addons/' . fbauth_addon_name . '/customer/fb_auth.php'),
        array('replace', 'customer/fb_auth_get_email.php', 'addons/' . fbauth_addon_name . '/customer/fb_auth_get_email.php')
    );

	cw_addons_set_template(
		array('post', 'buttons/social_media_panel.tpl', 'addons/' . fbauth_addon_name . '/customer/auth-button.tpl'),
		array('pre', 'customer/head.tpl', 'addons/' . fbauth_addon_name . '/customer/init.tpl'),
		array('replace', 'customer/main/fb_auth_get_email.tpl', 'addons/' . fbauth_addon_name . '/customer/email_request.tpl')
	);
}
