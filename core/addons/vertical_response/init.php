<?php

const vertical_response_addon_name = 'vertical_response';
const vertical_response_addon_version = '1.0';

const vertical_response_wsdl = "https://api.verticalresponse.com/wsdl/1.0/VRAPI.wsdl"; //location of the wsdl
const vertical_response_ses_time = 10;  // duration of session in minutes

$tables['newsletter_products'] = 'cw_newsletter_products';
$tables['recurring_list_update'] = 'cw_recurring_list_update';

cw_include('addons/' . vertical_response_addon_name . '/include/func.vertical_response.php');

if (class_exists("SoapClient")) {
	cw_set_hook('cw\news\get_newslists_by_customer', 'cw_vertical_response_get_newslists_by_customer', EVENT_POST);
	cw_set_hook('cw\news\get_available_newslists', 'cw_vertical_response_get_newslists', EVENT_POST);

	cw_addons_set_controllers(
		array('post', 'admin/recurring_vr_list_update.php', 'addons/' . vertical_response_addon_name . '/admin/recurring_list_update.php')
	);

	cw_addons_set_template(
		array('post', 'admin/main/recurring_vr_list_update.tpl', 'addons/' . vertical_response_addon_name . '/admin/recurring_list_update.tpl')
	);

	cw_event_listen('on_profile_modify', 'cw_vertical_response_on_profile_modify');
	cw_event_listen('on_cron_daily', 'cw_vertical_response_daily_list_update');
	cw_event_listen('on_cron_daily', 'cw_vertical_response_emails_update');

	if (APP_AREA == 'admin') {
		cw_addons_set_controllers(
			array('replace', 'admin/news.php', 'addons/' . vertical_response_addon_name . '/admin/news.php')
		);

		cw_addons_set_template(
			array('replace', 'admin/news/news.tpl', 'addons/' . vertical_response_addon_name . '/admin/news.tpl'),
			array('replace', 'admin/news/management.tpl', 'addons/' . vertical_response_addon_name . '/admin/management.tpl'),
			array('replace', 'admin/news/details.tpl', 'addons/' . vertical_response_addon_name . '/admin/details.tpl'),
			array('replace', 'admin/news/subscribers.tpl', 'addons/' . vertical_response_addon_name . '/admin/subscribers.tpl'),
			array('replace', 'admin/news/message.tpl', 'addons/' . vertical_response_addon_name . '/admin/message.tpl')
		);
	}
}
