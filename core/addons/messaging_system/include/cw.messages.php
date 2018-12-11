<?php
// get recipient_id by recipient mail
function cw_messages_get_recipient_id($recipient_value) {
    global $tables;

    return cw_query_first_cell("
        SELECT customer_id
        FROM $tables[customers]
        WHERE email = '$recipient_value' OR customer_id='$recipient_value'
    ");
}

// clean text value
function cw_messages_clean_text($value) {
    $value = trim($value);
    $value = strip_tags($value);

    return $value;
}

// create new message
function cw_messages_create_new_message(
    $customer_id,
    $sender_name,
    $recipient_id,
    $recipient_email,
    $subject,
    $body,
    $conversation_id
) {
    global $config, $current_location;

    cw_load('email');

    // sent message (incoming folder)
    $new_message_id = cw_array2insert(
        'messages',
        array(
            'subject' => $subject,
            'body' => $body,
            'sender_id' => $customer_id,
            'recipient_id' => $recipient_id,
            'sending_date' => cw_core_get_time(),
            'conversation_id' => !empty($conversation_id) ? $conversation_id : 0,
            'conversation_customer_id' => $recipient_id
        )
    );

    // duplicate for sent folder
    $current_conversation_id = !empty($conversation_id) ? $conversation_id : $new_message_id;
    $duplicate_message_id = cw_array2insert(
        'messages',
        array(
            'subject' => $subject,
            'body' => $body,
            'sender_id' => $customer_id,
            'recipient_id' => $recipient_id,
            'sending_date' => cw_core_get_time(),
            'read_status' => 1,
            'conversation_id' => $current_conversation_id,
            'conversation_customer_id' => $customer_id,
            'type' => 2,
            'link_id' => $new_message_id
        )
    );

    // unite message if they have not been united
    $data = array(
        'link_id' => $duplicate_message_id,
    );

    if (empty($conversation_id)) {
        $data['conversation_id'] = $new_message_id;
    }

    cw_array2update(
        'messages',
        $data,
        "message_id = '$new_message_id'"
    );

    // send notification email to recipient
    // notification is sent from system email and says about new received message from Sender at <sitename>

    $from = $config['Company']['site_administrator'];
/*
    $mail_subject = "The notification of a new message";
    $mail_body = '<b>You have received a new message from "' . $sender_name . '" at <a href="' . $current_location . '">';
    $mail_body .= $config['Company']['company_name'] . '</a></b><br />';
    $mail_body .= '<b>Subject:</b> ' . $subject . '<br />';
    $mail_body .= '<b>Body:</b> ' . nl2br($body) . '<br />';
    $mail_body .= '<a href="' . $current_location . '/index.php?target=message_box&mode=new';
    $mail_body .= '&contact_id=' . $customer_id . '&conversation_id=' . $current_conversation_id . '">Link to reply</a><br />';
    cw_send_simple_mail($from, $recipient_email, $mail_subject, $mail_body);
*/
    global $smarty;
    $smarty->assign('sender_name', $sender_name);
    $smarty->assign('current_location', $current_location);
    $smarty->assign('config', $config);
    $smarty->assign('subject', $subject);
    $smarty->assign('body', $body);
    $smarty->assign('customer_id', $customer_id);
    $smarty->assign('recipient_id', $recipient_id);
    $smarty->assign('current_conversation_id', $current_conversation_id);
    $smarty->assign('new_message_id', $new_message_id);

    cw_call('cw_send_mail', array($from, $recipient_email, 'addons/messaging_system/mail/new_message_subj.tpl', 'addons/messaging_system/mail/new_message_body.tpl'));

    return $new_message_id;  
}

// get new messages counter by customer
function cw_messages_get_new_messages_counter($customer_id) {
    global $tables;

    if ($customer_id) {
        return cw_query_first_cell("
            SELECT count(message_id)
            FROM $tables[messages]
            WHERE recipient_id = '$customer_id' AND read_status = 0
                AND type = 1 AND is_archive = 0
        ");
    }
    else {
        return 0;
    }
}

// get incoming messages counter by customer
function cw_messages_get_incoming_messages_counter($customer_id) {
    global $tables;

    if ($customer_id) {
        return cw_query_first_cell("
            SELECT count(message_id)
            FROM $tables[messages]
            WHERE recipient_id = '$customer_id' AND type = 1 AND is_archive = 0
        ");
    }
    else {
        return 0;
    }
}

// get sent messages counter by customer
function cw_messages_get_sent_messages_counter($customer_id) {
    global $tables;

    if ($customer_id) {
        return cw_query_first_cell("
            SELECT count(message_id)
            FROM $tables[messages]
            WHERE sender_id = '$customer_id' AND type = 2 AND is_archive = 0
        ");
    }
    else {
        return 0;
    }
}

// get archive messages counter by customer
function cw_messages_get_archive_messages_counter($customer_id) {
    global $tables;

    if ($customer_id) {
        return cw_query_first_cell("
            SELECT count(message_id)
            FROM $tables[messages]
            WHERE (sender_id = '$customer_id' AND type = 2 AND is_archive = 1)
                OR (recipient_id = '$customer_id' AND type = 1 AND is_archive = 1)
        ");
    }
    else {
        return 0;
    }
}

// get messages counter for each folders by customer
function cw_messages_get_messages_counters($customer_id) {
    $result = array(
        'new'       => cw_messages_get_new_messages_counter($customer_id),
        'incoming'  => cw_messages_get_incoming_messages_counter($customer_id),
        'sent'      => cw_messages_get_sent_messages_counter($customer_id),
        'archive'   => cw_messages_get_archive_messages_counter($customer_id)
    );

    return $result;
}

// get messages list by conditions
function cw_messages_get_messages_list($customer_id, $mode, $get_count=FALSE, $where="", $orderby="", $limit="") {
    global $tables, $config;

    if ($customer_id) {
        $result = cw_query("
            SELECT " . ($get_count ? "count(*) as cnt" : "m.*, ca.firstname, ca.lastname") . "
            FROM $tables[messages] m
            LEFT JOIN $tables[customers] c ON m.". ($mode == "sent" ? "recipient_id" : "sender_id") . " = c.customer_id
            LEFT JOIN $tables[customers_addresses] ca ON ca.customer_id = c.customer_id
                AND ca.customer_id <> 0 AND ca.main = 1
            $where
            $orderby
            $limit
        ");

        if ($get_count) {
            return $result[0]["cnt"];
        }

        if (!empty($result) && is_array($result)) {
            $date_format = (!empty($config['Appearance']['date_format']) ? $config['Appearance']['date_format'] : '%Y-%m-%d');
            $time_format = (!empty($config['Appearance']['time_format']) ? $config['Appearance']['time_format'] : '%H:%M:%S');
// @TODO truncate subject field
            foreach ($result as $k => $v) {
                // date
                $result[$k]['sending_date'] = strftime(
                    $date_format . ' ' . $time_format,
                    $v['sending_date'] + $config['Appearance']['timezone_offset'] * SECONDS_PER_HOUR
                );

                // sender name
                if (!empty($v['firstname']) || !empty($v['lastname'])) {
                    $name = trim($v['firstname'] . ' ' . $v['lastname']);
                }
                else {
                    $name = 'Unknown';
                }
                $result[$k]['sender_name'] = $name;

                // admin can see if corresponding message in recipient box is read or deleted
                if (APP_AREA == 'admin' && $mode == 'sent') {
                    $status_result = cw_query_first_cell("
                        SELECT read_status
                        FROM $tables[messages]
                        WHERE message_id = '" . $result[$k]['link_id'] . "'
                    ");

                    if ($status_result == "") {
                        $result[$k]['status'] = 2;
                    }
                    else {
                        $result[$k]['status'] = $status_result;
                    }
                }
            }
        }

        return $result;
    }
    else {
        return array();
    }
}

// get message data
function cw_messages_get_message($message_id) {
    global $tables;

    if (empty($message_id)) {
        return array();
    }

    $result = cw_query_first("
        SELECT m.*, ca1.firstname as s_firstname, ca1.lastname as s_lastname,
            ca2.firstname as r_firstname, ca2.lastname as r_lastname
        FROM $tables[messages] m
        LEFT JOIN $tables[customers] c1 ON m.sender_id = c1.customer_id
        LEFT JOIN $tables[customers_addresses] ca1 ON ca1.customer_id = c1.customer_id
            AND ca1.customer_id <> 0 AND ca1.main = 1
        LEFT JOIN $tables[customers] c2 ON m.recipient_id = c2.customer_id
        LEFT JOIN $tables[customers_addresses] ca2 ON ca2.customer_id = c2.customer_id
            AND ca2.customer_id <> 0 AND ca2.main = 1
        WHERE m.message_id = '$message_id'
    ");

    if (!empty($result) && is_array($result)) {
        $date_format = (!empty($config['Appearance']['date_format']) ? $config['Appearance']['date_format'] : '%Y-%m-%d');
        $time_format = (!empty($config['Appearance']['time_format']) ? $config['Appearance']['time_format'] : '%H:%M:%S');
        $result['sending_date'] = strftime(
            $date_format . ' ' . $time_format,
            $result['sending_date'] + $config['Appearance']['timezone_offset'] * SECONDS_PER_HOUR
        );

        // sender name
        if (!empty($result['s_firstname']) || !empty($result['s_lastname'])) {
            $name = trim($result['s_firstname'] . ' ' . $result['s_lastname']);
        }
        else {
            $name = 'Unknown';
        }
        $result['sender_name'] = $name;

        // recipient name
        if (!empty($result['r_firstname']) || !empty($result['r_lastname'])) {
            $name = trim($result['r_firstname'] . ' ' . $result['r_lastname']);
        }
        else {
            $name = 'Unknown';
        }
        $result['recipient_name'] = $name;

        $result['body'] = nl2br($result['body']);

        return $result;
    }
    else {
        return array();
    }
}

// get conversation messages
function cw_messages_get_conversation_messages($message_id, $customer_id) {
    global $tables;

    if (empty($message_id) || empty($customer_id)) {
        return array();
    }

    $conversation_id = cw_query_first_cell("
        SELECT conversation_id
        FROM $tables[messages]
        WHERE message_id = '$message_id'
    ");

    if (empty($conversation_id)) {
        return array();
    }

    $result = cw_query("
        SELECT m.*, ca.firstname as s_firstname, ca.lastname as s_lastname
        FROM $tables[messages] m
        LEFT JOIN $tables[customers] c ON m.sender_id = c.customer_id
        LEFT JOIN $tables[customers_addresses] ca ON ca.customer_id = c.customer_id
            AND ca.customer_id <> 0 AND ca.main = 1
        WHERE m.conversation_id = '$conversation_id' AND m.conversation_customer_id = '$customer_id'
        ORDER BY sending_date DESC
    ");

    if (!empty($result) && is_array($result)) {
        $date_format = (!empty($config['Appearance']['date_format']) ? $config['Appearance']['date_format'] : '%Y-%m-%d');
        $time_format = (!empty($config['Appearance']['time_format']) ? $config['Appearance']['time_format'] : '%H:%M:%S');

        foreach ($result as $k => $v) {
            $result[$k]['sending_date'] = strftime(
                $date_format . ' ' . $time_format,
                $v['sending_date'] + $config['Appearance']['timezone_offset'] * SECONDS_PER_HOUR
            );

            // sender name
            if (
                !empty($result[$k]['s_firstname'])
                || !empty($result[$k]['s_lastname'])
            ) {
                $name = trim($result[$k]['s_firstname'] . ' ' . $result[$k]['s_lastname']);
            }
            else {
                $name = 'Unknown';
            }
            $result[$k]['user_name'] = $name;

            $result[$k]['body'] = nl2br($result[$k]['body']);
        }

        return $result;
    }
    else {
        return array();
    }
}

// mark message as read / unread
function cw_messages_mark_message($message_id, $status) {

    if (empty($message_id)) {
        return;
    }

    cw_array2update(
        'messages',
        array(
            'read_status' => $status
        ),
        "message_id = '$message_id'"
    );
}

// add message to archive
function cw_messages_add_message_to_archive($message_id) {

    if (empty($message_id)) {
        return;
    }

    cw_array2update(
        'messages',
        array(
            'is_archive' => 1
        ),
        "message_id = '$message_id'"
    );
}

// delete message
function cw_messages_delete_message($message_id) {
    global $tables;

    if (empty($message_id)) {
        return;
    }

    cw_query("
        DELETE FROM $tables[messages]
        WHERE message_id = '$message_id'
    ");
}

// check right to message for user
function cw_messages_user_has_right_to_message($customer_id, $message_id) {
    global $tables;

    if (empty($customer_id) || empty($message_id)) {
        return FALSE;
    }

    return cw_query_first_cell("
        SELECT count(message_id)
        FROM $tables[messages]
        WHERE (sender_id = '$customer_id' OR recipient_id = '$customer_id') AND message_id = '$message_id'
    ");
}

/*
 * Mailbox for dashboard section
 * use function for registration in dashboard as follwing:
 * cw_addons_set_hooks(array('post','dashboard_build_sections','cw_messages_dashboard'));
 *
 * Input:
 * $parms['mode'] is 'setting' or 'dashboard'. You should not prepare full content in 'setting' mode.
 * $parms['sections'] is list of current dashboard settings
 */
function cw_messages_dashboard($params, $return=null) {
    global $smarty, $customer_id;

    // Set the dashboard code name here
    $name = 'messages_dashboard';

    // If the section is disabled then skip it on dashboard
    if ($params['mode'] == 'dashboard' && $params['sections'][$name]['active']==='0') return $return;

    // Define basic data for configuration
    $return[$name] =  array(
        'title'         => 'Mailbox',
        'description'   => 'This section shows number of incoming and also sent and archived messages',
        'active'        => 1,       // Default status: 0 or 1; optional
        'pos'           => 10,       // Default position; optional
        'size'          => 'small',   // Section size: 'small' (25%), 'medium' (50%) or 'big' (100%); optional
        'frame'         => 0,       // Show frame: 0 or 1; 1 by default; optional
        'header'        => 0,       // Show header: 0 or 1; 1 by default; optional; igmored if frame is 0
    );

    if ($params['mode']=='setting') return $return;

    // get counter messages in folders for message_box
    $messages_counter = cw_messages_get_messages_counters($customer_id);
    $smarty->assign('messages_counter', $messages_counter);

    if (array_sum($messages_counter)==0) {
        $return[$name]['active'] = 0;
        return $return;
    }

    // Add content for dashboard in 'dashboard' mode
    // Define either content or template name or both    
    $return[$name]['template'] = 'addons/messaging_system/admin/dashboard/mailbox_dashboard.tpl';

    return $return;
}

