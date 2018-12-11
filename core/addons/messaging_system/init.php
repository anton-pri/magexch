<?php
/*
 * Vendor: CW
 * addon: messaging system
 */

$tables['messages'] = 'cw_messages';

const messaging_addon_name = 'messaging_system';

cw_include('addons/' . messaging_addon_name . '/include/cw.messages.php');

if (APP_AREA == 'customer') {
    cw_addons_set_controllers(
        array('post', 'customer/auth.php', 'addons/' . messaging_addon_name . '/customer/init_messaging_counter.php'),
        array('replace', APP_AREA . '/message_box.php', 'addons/' . messaging_addon_name . '/' . APP_AREA . '/message_box.php')
    );

	cw_addons_set_template(
		array('post', 'customer/menu/addon_section.tpl', 'addons/' . messaging_addon_name . '/customer/section.tpl'),
        array('replace', APP_AREA . '/message_box/message_box.tpl', 'addons/' . messaging_addon_name . '/' . APP_AREA . '/message_box.tpl')
	);

    cw_addons_add_css('addons/' . messaging_addon_name . '/' . APP_AREA . '/style.css');
} else {
    cw_addons_set_controllers(
        array('replace', APP_AREA . '/message_box.php', 'addons/' . messaging_addon_name . '/admin/message_box.php')
    );

    cw_addons_set_template(
        array('post', 'admin/users/service_data.tpl', 'addons/' . messaging_addon_name . '/admin/contact_link.tpl'),
        array('replace', APP_AREA . '/message_box/message_box.tpl', 'addons/' . messaging_addon_name . '/admin/message_box.tpl')
    );

    // Old-fashioned hook for dashboard
    cw_addons_set_hooks(
        array('post', 'dashboard_build_sections', 'cw_messages_dashboard')
    );

    cw_addons_add_css('addons/' . messaging_addon_name . '/admin/style.css');
}
