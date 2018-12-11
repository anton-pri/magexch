<?php

if (empty($customer_id)) {
    cw_header_location('index.php?target=help&section=login_customer');
}

cw_load('user', 'messages');

// check right to message for user
if (
    !empty($message_id)
    && !cw_messages_user_has_right_to_message($customer_id, $message_id)
) {
    cw_header_location('index.php?target=message_box');
}

$top_message = &cw_session_register('top_message', array());
$new_message_data = &cw_session_register('new_message_data', array());

// ajax email check
if (isset($check_recipient_value) && !empty($test_value)) {
    $recipient_id = cw_messages_get_recipient_id($test_value);

    if (!empty($recipient_id)) {
        exit("avail");
    }
    else {
        exit("not avail");
    }
}

// can mark messages as read or unread
if (
    ($action == 'mark_read' || $action == 'mark_unread')
    && (!empty($m_item) || !empty($message_id))
) {
    $status = ($action == 'mark_read' ? 1 : 0);

    if (empty($m_item)) {
        $m_item[$message_id] = '';
    }

    foreach ($m_item as $message_id => $_v) {

        if (cw_messages_user_has_right_to_message($customer_id, $message_id)) {
            cw_messages_mark_message($message_id, $status);
        }
    }

    if ($mode == 'new') {
        if (cw_query_first_cell("
            SELECT type
            FROM $tables[messages]
            WHERE message_id = '$message_id'
        ") == 2) {
            cw_header_location('index.php?target=message_box&mode=sent');
        } else {
            cw_header_location('index.php?target=message_box');
        }
    }
}

// sent to archive
if (
    $action == 'archive'
    && (!empty($m_item) || !empty($message_id))
) {
    if (empty($m_item)) {
        $m_item[$message_id] = '';
    }

    foreach ($m_item as $message_id => $_v) {

        if (cw_messages_user_has_right_to_message($customer_id, $message_id)) {
            cw_messages_add_message_to_archive($message_id);
        }
    }

    if ($mode == 'new') {
        cw_header_location('index.php?target=message_box&mode=archive');
    }
}

// delete message
if (
    $action == 'delete'
    && (!empty($m_item) || !empty($message_id))
) {
    if (empty($m_item)) {
        $m_item[$message_id] = '';
    }

    foreach ($m_item as $message_id => $_v) {

        if (cw_messages_user_has_right_to_message($customer_id, $message_id)) {
            cw_messages_delete_message($message_id);
        }
    }

    if ($mode == 'new') {
        cw_header_location('index.php?target=message_box');
    }
}

// create new message action
if ($action == 'new_message' && !empty($new_message)) {

    if (
        empty($new_message['recipient_id'])
        || filter_var($new_message['recipient_name'], FILTER_VALIDATE_EMAIL)
    ) {
        $recipient_mail = cw_messages_clean_text($new_message['recipient_name']);
        $recipient_id = cw_messages_get_recipient_id($recipient_mail);
    }
    elseif (is_numeric($new_message['recipient_name']) && $new_message['recipient_name'] > 0) {
        $recipient_id = $new_message['recipient_name'];
    }
    else {
        $recipient_id = $new_message['recipient_id'];
    }

    if (empty($recipient_id)) {
        $top_message = array(
            'content' => cw_get_langvar_by_name('txt_recipient_not_found'),
            'type' => 'E'
        );

        $new_message_data = $new_message;

        cw_header_location('index.php?target=message_box&mode=new');
    }

    $recipient_info = cw_call('cw_user_get_info', array($recipient_id));
    $recipient_email = $recipient_info['email'];

    $sender_name = cw_messages_clean_text($new_message['sender_name']);
    $subject = cw_messages_clean_text($new_message['subject']);
    $body = cw_messages_clean_text($new_message['body']);
    $conversation_id = $new_message['conversation_id'];

    cw_call('cw_messages_create_new_message',
    array(
        $customer_id,
        $sender_name,
        $recipient_id,
        $recipient_email,
        $subject,
        $body,
        $conversation_id
    ));

    $top_message = array(
        'content' => cw_get_langvar_by_name('lbl_message_has_been_sent'),
        'type' => 'I'
    );

    $new_message_data = array();

    cw_header_location('index.php?target=message_box&mode=sent');
}

if (empty($mode)) {
    $mode = 'incoming';
}

// new message mode
if ($mode == 'new') {
    // get sender name
    $sender_name = 'Unknown';

    if (!empty($customer_id) && !empty($user_account)) {

        if (!empty($user_account['firstname']) || !empty($user_account['lastname'])) {
            $sender_name = trim($user_account['firstname'] . ' ' . $user_account['lastname']);
        }
    }
    $smarty->assign('sender_name', $sender_name);

    // get recipient name
    $recipient_name = '';
    $recipient_id = 0;

    if (is_numeric($contact_id) && $contact_id > 0) {
        $recipient_name = 'Unknown';
        $recipient_id = $contact_id;
        $recipient_info = cw_call('cw_user_get_info', array($contact_id, 1));
        $address = isset($recipient_info['main_address']) ? $recipient_info['main_address'] : $recipient_info['current_address'];

        if (!empty($address['firstname']) || !empty($address['lastname'])) {
            $recipient_name = trim($address['firstname'] . ' ' . $address['lastname']);
        }
    }
    $smarty->assign('recipient_name', $recipient_name);
    $smarty->assign('recipient_id', $recipient_id);

    // get body
    $subject = isset($new_message_data['subject']) ? $new_message_data['subject'] : $subject;
    $smarty->assign('subject', $subject);

    // get body
    $body = isset($new_message_data['body']) ? $new_message_data['body'] : '';
    $smarty->assign('body', $body);
    $smarty->assign('conversation_id', $conversation_id);

    $new_message_data = array();
}
elseif ($action == 'show' && !empty($message_id)) {
    $mode = 'show';
    cw_messages_mark_message($message_id, 1);
    $message = cw_messages_get_message($message_id);

    if (empty($message)) {
        cw_header_location('index.php?target=message_box');
    }
    $messages_counter = cw_messages_get_messages_counters($customer_id);
    $conversation_messages = cw_messages_get_conversation_messages($message_id, $customer_id);
    $smarty->assign('message', $message);
    $smarty->assign('messages_counter', $messages_counter);
    $smarty->assign('conversation_messages', $conversation_messages);
}
else {
    $page = (!empty($page) ? $page : 1);

    switch ($mode) {
        case 'sent':    // sent
            $where = "WHERE m.sender_id = '$customer_id' AND m.type = 2 AND m.is_archive = 0";
            break;
        case 'archive': // archive
            $where = "WHERE (m.sender_id = '$customer_id' AND m.type = 2 AND m.is_archive = 1)
                OR (m.recipient_id = '$customer_id' AND m.type = 1 AND m.is_archive = 1)";
            break;
        default:        // incoming
            $where = "WHERE m.recipient_id = '$customer_id' AND m.type = 1 AND m.is_archive = 0";
            break;
    }

    $get_count = TRUE;
    $total_items = cw_messages_get_messages_list($customer_id, $mode, $get_count, $where);

    $navigation = cw_core_get_navigation($target, $total_items, $page);
    $navigation['script'] = 'index.php?target=message_box&mode=' . $mode;

    $smarty->assign('sort_field', "");
    $smarty->assign('sort_direction', 0);

    $orderby = "ORDER BY sending_date DESC";
    if (!empty($sort_field)) {
        $orderby = "ORDER BY " . $sort_field;
        $navigation['script'] .= "&sort_field=" . $sort_field;
        $smarty->assign('sort_field', $sort_field);
    }
    if ($sort_direction != "") {
        if (!empty($orderby)) {
            $orderby .= $sort_direction ? " ASC" : " DESC";
        }
        $navigation['script'] .= "&sort_direction=" . $sort_direction;
        $smarty->assign('sort_direction', abs($sort_direction - 1));
    }
    $smarty->assign('navigation', $navigation);

    $get_count = FALSE;
    $limit = " LIMIT $navigation[first_page], $navigation[objects_per_page]";
    $messages_list = cw_call('cw_messages_get_messages_list', array($customer_id, $mode, $get_count, $where, $orderby, $limit));

    $smarty->assign('messages_list', $messages_list);
}
$smarty->assign('mode', $mode);

if (defined('IS_AJAX')) {
    cw_load('ajax');

    $addon_app_area = (APP_AREA == 'customer' ? APP_AREA : 'admin');

    cw_add_ajax_block(array(
        'id' => 'contents_messages_list',
        'action' => 'update',
        'template' => 'addons/' . messaging_addon_name . '/' . $addon_app_area . '/messages.tpl'
    ));
}
else {
    $avail_langvar_modes = array(
        'show'      => 'lbl_message',
        'new'       => 'lbl_new_message',
        'incoming'  => 'lbl_avail_type_incoming',
        'sent'      => 'lbl_sent',
        'archive'   => 'lbl_archive'
    );

    $location[] = array(cw_get_langvar_by_name($avail_langvar_modes[$mode]), '');
    $smarty->assign('main', 'message_box');
    $smarty->assign('current_section_dir', 'message_box');
}
