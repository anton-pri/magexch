<?php

cw_load('user');

if ($REQUEST_METHOD == "POST" && $mode == "thread_messages") {
    if ($action == "add_message") {
        if ($thread_id && $doc_id) {

            //get other respondends in thread
/*
            $new_message_recepient_id = cw_query_first_cell("select sender_id from $tables[order_messages_messages] where thread_id='$thread_id' and sender_id != '$customer_id'");
            if (empty($new_message_recepient_id))
                $new_message_recepient_id = cw_query_first_cell("select recepient_id from $tables[order_messages_messages] where thread_id='$thread_id' and recepient_id != '$customer_id'");
*/
               
            if (!empty($config['order_messages']['default_recepient_admin_email']))
                $default_recepient_admin_email =
                  $config['order_messages']['default_recepient_admin_email'];

            $default_recepient_admin_id = cw_query_first_cell("select customer_id from $tables[customers] where usertype='A' and email='$default_recepient_admin_email'");

            $new_message_sender_id = $default_recepient_admin_id;

            $new_message_recepient_id = cw_query_first_cell("select dui.customer_id from $tables[docs_user_info] dui, $tables[docs] d where d.doc_info_id=dui.doc_info_id and d.doc_id = '$doc_id'");

            if ($new_message['on_behalf']) {
                    $sender_id = $new_message_recepient_id;
                    $recepient_id = $new_message_sender_id;
            } else {
                    $recepient_id = $new_message_recepient_id;
                    $sender_id = $new_message_sender_id;
            }
/*
            $new_message_id = cw_array2insert('order_messages_messages', array('thread_id'=>$thread_id, 'sender_id'=>$sender_id, 'recepient_id'=>$recepient_id, 'author_id'=>$customer_id, 'date'=>time(), 'subject'=>addslashes($new_message['subject']), 'body'=>addslashes($new_message['body']), 'read_status'=>1));

            if ($recepient_id != $new_message_sender_id && $new_message_id) {
                    cw_order_messages_notify_other_respondent ($new_message_id, $recepient_id, $doc_id);
            }
*/
            if ($recepient_id != $new_message_sender_id && $recepient_id != $customer_id) {

                $recepient_email = cw_query_first_cell("select dui.email from $tables[docs_user_info] dui, $tables[docs] d where d.doc_info_id=dui.doc_info_id and d.doc_id = '$doc_id'");

                $smarty->assign('message',
                      array('subject' => $new_message['subject'],
                            'thread_id' => $thread_id,
                            'body' => $new_message['body']));

                $smarty->assign('doc_id', $doc_id);

                $to_recipient_lang = cw_query_first_cell ("SELECT language FROM $tables[customers] WHERE customer_id='$recepient_id'");
                if (empty($to_recipient_lang))
                    $to_recipient_lang = $config['default_customer_language'];

                cw_call('cw_send_mail', array($config['order_messages']['contact_email_address'], $recepient_email, 'addons/order_messages/mail/customer_subj.tpl', 'addons/order_messages/mail/customer.tpl', $to_recipient_lang, false, true));
            } else {
                $new_message_id = cw_array2insert('order_messages_messages', array('thread_id'=>$thread_id, 'sender_id'=>$sender_id, 'recepient_id'=>$recepient_id, 'author_id'=>$customer_id, 'date'=>time(), 'subject'=>addslashes($new_message['subject']), 'body'=>addslashes($new_message['body']), 'read_status'=>1));
            }
        }
    }
    cw_header_location("index.php?target=$target&mode=$mode&doc_id=$doc_id&thread_id=$thread_id");
} 

$thread_messages = cw_query("select * from $tables[order_messages_messages] where thread_id = '$thread_id' order by date desc");
cw_array2update('order_messages_messages', array('read_status'=>1), "thread_id = '$thread_id'");

//global $config;
$messages_users = array();
$start_message = array();
foreach ($thread_messages as $msg_k => $msg) {

    if (!$msg['sender_id']) { 
       if (!empty($config['order_messages']['default_recepient_admin_email']))
          $default_recepient_admin_email =
          $config['order_messages']['default_recepient_admin_email'];

        $msg['sender_id'] = cw_query_first_cell("select customer_id from $tables[customers] where usertype='A' and email='$default_recepient_admin_email'");
        $thread_messages[$msg_k]['sender_id'] = $msg['sender_id'];

    }

    if (!isset($messages_users[$msg['sender_id']])) 
        $messages_users[$msg['sender_id']] = cw_user_get_info($msg['sender_id'], 1);

    if (!isset($messages_users[$msg['recepient_id']]))
        $messages_users[$msg['recepient_id']] = cw_user_get_info($msg['recepient_id'], 1);


    if (!$msg['author_id']) { 
       if (!empty($config['order_messages']['default_recepient_admin_email']))
          $default_recepient_admin_email =
          $config['order_messages']['default_recepient_admin_email'];
        
        $msg['author_id'] = cw_query_first_cell("select customer_id from $tables[customers] where usertype='A' and email='$default_recepient_admin_email'");
        $thread_messages[$msg_k]['author_id'] = $msg['author_id'];

    }

    if (!isset($messages_users[$msg['author_id']]))
        $messages_users[$msg['author_id']] = cw_user_get_info($msg['author_id'], 1);
  
    $start_message = $msg;

}

$doc_id = cw_query_first_cell("select doc_id from $tables[order_messages_threads] where thread_id = '$thread_id'");

$smarty->assign('thread_messages', $thread_messages);
$smarty->assign('messages_users', $messages_users);
$smarty->assign('start_message', $start_message);

$smarty->assign('thread_id', $thread_id);
$smarty->assign('doc_id', $doc_id);

$smarty->assign('home_style', 'popup');
$smarty->assign('main', 'thread_messages');
