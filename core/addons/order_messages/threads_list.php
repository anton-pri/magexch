<?php

if ($REQUEST_METHOD=='POST' && $mode == "order_messages" ) {

    $top_message = &cw_session_register('top_message');

    if ($action == "new_thread") {
        //start new thread and place new message

        if (!empty($new_thread['standard_email'])) {
            $status = $new_thread['standard_email'];
            $doc_data = cw_call('cw_doc_get', array($doc_id, 8192));
//trigger email and register it in messages
            cw_load('web');
            if ($doc_data['info']['layout_id'])
                $layout = cw_web_get_layout_by_id($doc_data['info']['layout_id']);
            else
                $layout = cw_web_get_layout('docs_'.$doc_data['type']);

            $smarty->assign('layout_data', $layout);
            $smarty->assign('info', $doc_data['info']);
            $smarty->assign('products', $doc_data['products']);
            $smarty->assign('order', $doc_data);
            $smarty->assign('doc', $doc_data);
              

            $to_customer = cw_query_first_cell ("SELECT language FROM $tables[customers] WHERE customer_id='$doc_data[userinfo][customer_id]'");
            if (empty($to_customer))
                $to_customer = $config['default_customer_language'];
            $doc_data['products'] = cw_doc_translate_products($doc_data['products'], $to_customer);
            $smarty->assign('order', $doc_data);
            cw_call('cw_send_mail', array($config['order_messages']['contact_email_address'], 
                                    $doc_data['userinfo']['email'], 
                                    'mail/docs/customer_subj.tpl', 'mail/docs/customer.tpl', $to_customer, false, true));

        } else {
            $new_thread_id = cw_array2insert('order_messages_threads', array('doc_id'=>$doc_id, 'type'=>((!empty($new_thread['standard_email'])?'A':'M'))));

            if (!empty($config['order_messages']['default_recepient_admin_email']))
                $default_recepient_admin_email =
                  $config['order_messages']['default_recepient_admin_email'];

            $new_message_sender_id = cw_query_first_cell("select customer_id from $tables[customers] where usertype='A' and email='$default_recepient_admin_email'");

            if ($new_thread_id) {
                if ($new_thread['on_behalf']) {
                    $sender_id = $new_thread['recepient_id'];
                    $recepient_id = $new_message_sender_id;
                } else {
                    $recepient_id = $new_thread['recepient_id'];
                    $sender_id = $new_message_sender_id;
                }
/* 
                $new_message_id = cw_array2insert('order_messages_messages', array('thread_id'=>$new_thread_id, 'sender_id'=>$sender_id, 'recepient_id'=>$recepient_id, 'author_id'=>$sender_id, 'date'=>time(), 'subject'=>($new_thread['subject']), 'body'=>($new_thread['body']), 'read_status'=>1));

                if ($recepient_id != $new_message_sender_id && $new_message_id) {
                    cw_order_messages_notify_other_respondent ($new_message_id, $recepient_id, $doc_id); 
                }
*/

                if ($recepient_id != $new_message_sender_id && $recepient_id != $customer_id) {

                    $recepient_email = cw_query_first_cell("select dui.email from $tables[docs_user_info] dui, $tables[docs] d where d.doc_info_id=dui.doc_info_id and d.doc_id = '$doc_id'");

                    $smarty->assign('message',
                      array('subject' => $new_thread['subject'],
                            'thread_id' => $new_thread_id,
                            'body' => $new_thread['body']));

                    $smarty->assign('doc_id', $doc_id);

                    $to_recipient_lang = cw_query_first_cell ("SELECT language FROM $tables[customers] WHERE customer_id='$recepient_id'");
                    if (empty($to_recipient_lang))
                        $to_recipient_lang = $config['default_customer_language'];

                    cw_call('cw_send_mail', array($config['order_messages']['contact_email_address'], $recepient_email, 'addons/order_messages/mail/customer_subj.tpl', 'addons/order_messages/mail/customer.tpl', $to_recipient_lang, false, true));
                } else {
                    $new_message_id = cw_array2insert('order_messages_messages', array('thread_id'=>$new_thread_id, 'sender_id'=>$sender_id, 'recepient_id'=>$recepient_id, 'author_id'=>$sender_id, 'date'=>time(), 'subject'=>($new_thread['subject']), 'body'=>($new_thread['body']), 'read_status'=>1));
                }
            }
        }
    }
    if ($new_message_id) { 
        $recepient_info = cw_user_get_info($new_thread['recepient_id'], 1);
        $top_message = array('content' => "New message has been sent to $recepient_info[firstname] $recepient_info[lastname] ($recepient_info[email])", 'type' => 'I');
    }
    cw_header_location("index.php?target=$target&mode=$mode&doc_id=$doc_id&js_tab=order_messages");
}

$contact_suppliers = array();

foreach ($doc_data['products'] as $doc_product) {
    if ($doc_product['supplier_customer_id']) {
        $contact_suppliers[] = cw_user_get_info($doc_product['supplier_customer_id'], 1);
    }
}
$smarty->assign('contact_suppliers', $contact_suppliers);

$doc_messages_threads = cw_query("select $tables[order_messages_threads].* from $tables[order_messages_threads] where doc_id='$doc_id'");
foreach ($doc_messages_threads as $dmt_key => $dmt_value) {
    $doc_messages_threads[$dmt_key]['messages'] = 
        cw_query("select * from $tables[order_messages_messages] where thread_id = '$dmt_value[thread_id]' order by date desc");
    if (!empty($doc_messages_threads[$dmt_key]['messages'])) {
        $doc_messages_threads[$dmt_key]['messages_count'] = count($doc_messages_threads[$dmt_key]['messages']);
        $doc_messages_threads[$dmt_key]['messages_unread'] = cw_query_first_cell("select count(*) from $tables[order_messages_messages] where thread_id = '$dmt_value[thread_id]' and read_status!=1");
        $doc_messages_threads[$dmt_key]['start_message'] = end($doc_messages_threads[$dmt_key]['messages']);
        $doc_messages_threads[$dmt_key]['start_message']['sender'] = cw_user_get_info($doc_messages_threads[$dmt_key]['start_message']['sender_id'], 1);
        $doc_messages_threads[$dmt_key]['start_message']['recepient'] = cw_user_get_info($doc_messages_threads[$dmt_key]['start_message']['recepient_id'], 1);  

        $doc_messages_threads[$dmt_key]['last_message'] = reset($doc_messages_threads[$dmt_key]['messages']);
        $doc_messages_threads[$dmt_key]['last_message']['sender'] = cw_user_get_info($doc_messages_threads[$dmt_key]['last_message']['sender_id'], 1);
        $doc_messages_threads[$dmt_key]['last_message']['recepient'] = cw_user_get_info($doc_messages_threads[$dmt_key]['last_message']['recepient_id'], 1);
    }
    
}

usort($doc_messages_threads, 'cw_order_messages_threads_sort');

$smarty->assign('doc_messages_threads', $doc_messages_threads);
