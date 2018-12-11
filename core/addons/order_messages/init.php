<?php

$tables['order_messages_threads'] = 'cw_order_messages_threads';
$tables['order_messages_messages'] = 'cw_order_messages_messages';
$tables['mail_rpool'] = 'cw_mail_rpool';

$cw_allowed_tunnels[] = 'cw_order_messages_decode_qprint';
$cw_allowed_tunnels[] = 'cw_order_messages_all_decodes';

const order_messages_addon_name = 'order_messages';

cw_include('addons/'.order_messages_addon_name.'/func.php');

if (APP_AREA == 'admin') {

    cw_addons_set_hooks(
        array('post', 'cw_tabs_js_abstract', 'cw_order_messages_tabs_js_abstract'),
        array('post', 'cw_send_mail', 'cw_order_messages_send_mail') 
    );

    cw_addons_set_controllers(
        array('post', 'include/orders/order.php', 'addons/'.order_messages_addon_name.'/threads_list.php'),
        array('replace', 'admin/thread_messages.php', 'addons/'.order_messages_addon_name.'/thread_messages.php'),
        array('replace', 'admin/take_messages.php', 'addons/'.order_messages_addon_name.'/take_messages.php')
    );
    cw_addons_set_template(array('replace','admin/main/thread_messages.tpl', 'addons/'.order_messages_addon_name.'/thread_messages.tpl'));

    cw_addons_set_template(array('post', 'mail/mail_header.tpl', 'addons/'.order_messages_addon_name.'/doc_layout_pre.tpl'));
    cw_addons_set_template(array('pre', 'main/docs/doc_layout.tpl', 'addons/'.order_messages_addon_name.'/doc_layout_post.tpl'));
    cw_addons_set_template(array('post', 'main/docs/additional_search_field.tpl', 'addons/'.order_messages_addon_name.'/additional_doc_search_field.tpl'));

    cw_event_listen('on_prepare_search_orders', 'cw_order_messages_prepare_search_orders');

    if ($target == "thread_messages") {
        cw_addons_set_template(array('replace','common/head.tpl', 'addons/'.order_messages_addon_name.'/admin_head_order_messages.tpl'));
    }
} elseif (APP_AREA == 'customer') {
    cw_addons_set_hooks(
        array('post', 'cw_send_mail', 'cw_order_messages_send_mail')
    );
}

cw_event_listen('on_cron_regular', 'cw_order_messages_get_emails');
