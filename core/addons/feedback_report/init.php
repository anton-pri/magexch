<?php
/*
 * Vendor: CW
 * addon: Feedback report
 */

const feedback_addon_name 			= 'feedback_report';
const feedback_our_email_to_send	= 'support@cartworks.com';
const feedback_files_folder_name	= 'feedback';
const feedback_image_type	        = 'jpeg'; // png/jpeg (also need change type in feedback.min.js and html2canvas.min.js)

cw_include('addons/' . feedback_addon_name . '/include/func.feedback.php');

cw_event_listen('on_build_var_dirs', 'cw_fbr_build_var_dirs');
cw_event_listen('on_log_add', 'cw_fbr_log_add');

cw_set_controller(APP_AREA . '/save_feedback_data.php', 'addons/' . feedback_addon_name . '/common/save_feedback_data.php', EVENT_REPLACE);
cw_set_controller('init/numbers.php', 'addons/' . feedback_addon_name . '/common/error_handler.php', EVENT_POST);
cw_set_controller('customer/feedback.php', 'addons/' . feedback_addon_name . '/common/feedback.php', EVENT_REPLACE);

cw_event_listen('on_cron_hourly', 'cw_fbr_prepare_and_send_feedbacks');

cw_addons_set_template(
	array('post', APP_AREA . '/elements/bottom_links.tpl', 'addons/' . feedback_addon_name . '/bottom_links.tpl'),
	array('post', 'customer/main/feedback_image.tpl', 'addons/' . feedback_addon_name . '/feedback_image.tpl')
);

cw_addons_add_css('addons/' . feedback_addon_name . '/css/feedback.css');

cw_addons_add_js('addons/' . feedback_addon_name . '/js/feedback.min.js');
cw_addons_add_js('addons/' . feedback_addon_name . '/js/html2canvas.min.js');
