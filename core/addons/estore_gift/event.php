<?php
cw_load('cart', 'files', 'mail', 'user');

if ($action == 'update') {
    $date_fields = array (
        '' =>array('event_date' => 0),
    );
    cw_core_process_date_fields($event_details, $date_fields);

    if (!$event_details['title']) {
        $top_message = array('type' => 'E', 'content' => cw_get_langvar_by_name('err_giftreg_required_fields_msg'));
        $event_details_sess = &cw_session_register('event_details_sess', $event_details);
        cw_header_location("index.php?target=giftreg_manage&event_id=$event_id");
    }

    $event_id = cw_query_first_cell("select event_id from $tables[giftreg_events] where event_id='$event_id' and customer_id='$customer_id'");
    if (!$event_id) {
        $count = cw_query_first_cell("select count(*) FROM $tables[giftreg_events] WHERE customer_id='$customer_id'");
        if ($count < $config['estore_gift']['events_lists_limit']) $event_id = cw_array2insert('giftreg_events', array('customer_id' => $customer_id));
        else $error = 'events_limit_exceeded';
    }
    if (empty($error))
        cw_array2update('giftreg_events', $event_details, "event_id='$event_id'", array('status', 'title', 'event_date', 'description', 'html_content', 'guestbook'));
    else 
        $top_message = array('type' => 'E', 'content' => cw_get_langvar_by_name('err_giftreg_events_lists_limit'));

    cw_header_location("index.php?target=gifts&mode=events&event_id=$event_id");
}

if ($action == 'guestbook') {
    $events_creator = cw_query_first_cell("select customer_id from $tables[giftreg_events] where event_id='$event_id' and guestbook=1");
    if (!$events_creator)
        cw_header_location("index.php?target=$target&mode=events");

    $gb_details['moderator'] = $events_creator == $customer_id ? "Y" : "N";
    $gb_details['message'] = str_replace("\n", "<br />", $gb_details['message']);
    $gb_details['date'] = cw_core_get_time();
    $gb_details['event_id'] = $event_id;
    cw_array2insert('giftreg_guestbooks', $gb_details, true);

    cw_header_location("index.php?target=$target&mode=events&event_id=$event_id&js_tab=guestbook");
}

if ($action == 'delete_gbm' && $message_id) {
    $events_creator = cw_query_first_cell("select customer_id from $tables[giftreg_events] where event_id='$event_id' and guestbook=1");
    if ($events_creator == $customer_id)
        db_query("delete from $tables[giftreg_guestbooks] where message_id='$message_id' AND event_id='$event_id'");
    cw_header_location("index.php?target=gifts&mode=events&event_id=$event_id&js_tab=guestbook");
}   

if ($event_id  && $action == 'send') {
    if (empty($mail_data['message']) || empty($mail_data['subj'])) {
        $top_message = array('type' => 'E', 'content' => cw_get_langvar_by_name('err_giftreg_required_fields_msg'));
        cw_header_location("index.php?target=$target&mode=events&event_id=$event_id&js_tab=send");
    }

    $mailing_list = cw_query("select * from $tables[giftreg_maillist] where event_id='$event_id' and status=1");
    foreach ($mailing_list as $k=>$v) {
        cw_send_simple_mail($user_account['email'], $v['recipient_email'], $mail_data['subj'], $mail_data['message']);
        $recipients_sent[] = $v;
    }

    db_query("update $tables[giftreg_events] set sent_date='".cw_core_get_time()."' where event_id='$event_id' and customer_id='$customer_id'");

	cw_header_location("index.php?target=$target&mode=events&event_id=$event_id&js_tab=send");
}

if ($action == 'maillist_delete' && is_array($del)) {
    db_query("delete from $tables[giftreg_maillist] where reg_id in ('".implode("', '", array_keys($del))."') AND event_id='$event_id'");
    cw_header_location("index.php?target=$target&mode=events&event_id=$event_id&js_tab=recipients");
}
if ($action == 'send_conf' && is_array($del)) {
    $event_data = cw_query_first("select * from $tables[giftreg_events] where customer_id='$customer_id' and event_id='$event_id'");
    $smarty->assign('event_data', $event_data);
    $time = cw_core_get_time();

    foreach($del as $k=>$v) {
        $recipient_data = cw_query_first("SELECT * FROM $tables[giftreg_maillist] WHERE reg_id='$k' AND event_id='$event_id'");
        $smarty->assign('recipient_data', $recipient_data);
        $smarty->assign('confirmation_code', md5($recipient_data['confirmation_code']."_confirmed"));
        $smarty->assign('decline_code', md5($recipient_data['confirmation_code']."_declined"));
        $smarty->assign('http_customer_location', $http_location);
        cw_call('cw_send_mail', array($user_account['email'], $recipient_data['recipient_email'], 'mail/giftreg_confirmation_subj.tpl', 'mail/giftreg_confirmation.tpl'));
        db_query("UPDATE $tables[giftreg_maillist] SET status='S', status_date='$time' WHERE reg_id='$k' AND event_id='$event_id'");
    }
    cw_header_location("index.php?target=$target&mode=events&event_id=$event_id&js_tab=recipients");
}

if ($action == 'maillist' && is_array($recipient_details)) {
    foreach ($recipient_details as $k=>$v) {
        if (!$k && (!$v['recipient_name'] || !$v['recipient_email'])) continue;
        if (!$k) {
            $recipients_count = cw_query_first_cell("select count(*) from $tables[giftreg_maillist] where event_id='$event_id'");
            $is_exists = cw_query_first_cell("select count(*) from $tables[giftreg_maillist] where event_id='$event_id' and recipient_email='$v[recipient_email]'");
            if ($is_exists || $recipients_count > $config['estore_gift']['recipients_limit']) continue;
            $k = cw_array2insert('giftreg_maillist', array('event_id' => $event_id, 'date' => cw_core_get_time(), 'confirmation_code' => cw_gift_get_confirmation_code(), 'recipient_email' => $v['recipient_email']));
        }
        cw_array2update('giftreg_maillist', $v, "reg_id='$k'", array('recipient_name', 'recipient_email'));
    }

    cw_header_location("index.php?target=$target&mode=events&event_id=$event_id&js_tab=recipients");
}

if ($action == 'delete') {
    $event_id = cw_query_first_cell("select event_id from $tables[giftreg_events] where event_id='$event_id' and customer_id='$customer_id'");
    db_query("delete from $tables[giftreg_events] where event_id='$event_id' AND customer_id='$customer_id'");
    db_query("delete from $tables[giftreg_maillist] where event_id='$event_id'");
    db_query("delete from $tables[giftreg_guestbooks] where event_id='$event_id'");
    cw_header_location("index.php?target=$target&mode=events");
}
$location[] = array(cw_get_langvar_by_name('lbl_giftreg_events_list', 'index.php?target=gifts&mode=events'));

if ($event_id) {
    $event_data = cw_query_first("select * from $tables[giftreg_events] where customer_id='$customer_id' AND event_id='$event_id'");
    $event_data['allow_to_send'] = cw_query_first_cell("select count(*) from $tables[giftreg_maillist] where event_id='$event_id' AND status=1");

    $smarty->assign('event_id', $event_id);

    $search_condition = "event_id='$event_id'";
    $total_items_in_search = cw_query_first_cell("select count(*) from $tables[giftreg_guestbooks] where $search_condition");
    $navigation = cw_core_get_navigation($target, $total_items_in_search, $page);
    $navigation['script'] = "index.php?target=gifts&mode=events&event_id=$event_id&js_tab=guestbook";
    $smarty->assign('navigation', $navigation);

    $guestbook = cw_query("select * from $tables[giftreg_guestbooks] where $search_condition order by date DESC LIMIT $navigation[first_page], $navigation[objects_per_page]");
    $smarty->assign('guestbook', $guestbook);

    $mailing_list = cw_query("select * from $tables[giftreg_maillist] where event_id='$event_id' order by recipient_name, recipient_email");
    $recipients_count = cw_query_first_cell("select count(*) from $tables[giftreg_maillist] where event_id='$event_id'");
    if ($recipients_count >= $config['estore_gift']['recipients_limit']) $smarty->assign('recipients_limit_reached', 1);
    $smarty->assign('mailing_list', $mailing_list);

    $wl_products = cw_gift_get_giftreg_wishlist($customer_id, $event_id);
	$smarty->assign('wl_products', $wl_products);

    $location[] = array($event_data['title'], '');
}
else
    $location[] = array(cw_get_langvar_by_name('lbl_giftreg_new_event', ''));


$event_details_sess = &cw_session_register('event_details_sess');
if ($event_details_sess) {
	$event_data = cw_array_map('stripslashes', $event_details_sess);
	cw_session_unregister('event_details_sess');
}
$smarty->assign('event_data', $event_data);
$smarty->assign('allow_edit', 1);

$smarty->assign('js_tab', $js_tab);
$smarty->assign('current_main_dir', 'addons/estore_gift');
$smarty->assign('current_section_dir','');
$smarty->assign('main', 'event');
?>
