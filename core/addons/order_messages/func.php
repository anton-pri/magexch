<?php

function cw_order_messages_tabs_js_abstract($params, $return) {
    if ($return['name'] == 'doc_O_info') {
        if (AREA_TYPE != 'A') return $return;

        $return['js_tabs']['order_messages'] = array(
            'title' => cw_get_langvar_by_name('lbl_messages'),
            'template' => 'addons/order_messages/doc_O_info.tpl',
        );
    }

    return $return;
}


function cw_order_messages_send_mail($from, $to, $subject_template, $body_template, $language, $crypted, $is_pdf, $files) {
    global $tables;

    $last_message = cw_query_first("select * from $tables[mail_spool] order by mail_id desc limit 1"); 

    cw_load('doc');
    $extra_admin_emails = cw_call('cw_doc_order_status_extra_admin_email', array(''));
    if (in_array($to, $extra_admin_emails) || strpos(implode(",",$extra_admin_emails), $to) !== false) {
        return;
    }

    $om_debug = true;

    if ($om_debug) cw_log_add("order_messages_send_mail", array($from, $to, $subject_template, $body_template, $language, $crypted, $is_pdf, $file));
    if ($om_debug) cw_log_add("order_messages_send_mail", array('last_message'=>$last_message));

    global $smarty;
    global $current_language;
    $rnd_key = time();
    $smarty->assign('order', array('display_id'=>$rnd_key));

    $language = $language ? $language : $current_language;
    $order_subject_template = 'mail/docs/customer_subj.tpl';
    $test_subject = chop(cw_display($order_subject_template, $smarty, false, $language));
    if ($om_debug) cw_log_add("order_messages_send_mail", array('test_subject'=>$test_subject)); 
    $subject_parts = explode($rnd_key, $test_subject);
    $extracted_display_id = str_replace($subject_parts,"",$last_message['subject']);
    $extracted_doc_id = cw_query_first_cell("select doc_id from $tables[docs] where display_id='".addslashes($extracted_display_id)."'");

    if (!$extracted_doc_id && strpos($last_message['subject'], "#") !== false) {
        $_parts = explode("#", cw_order_messages_remove_doc_prefix($last_message['subject'])); 
        $extracted_doc_id = intval(trim($_parts[1])); 
    } 

    if (!$extracted_doc_id) {
        if ($om_debug) cw_log_add("order_messages_send_mail", '!$extracted_doc_id'); 
        return;
    }

    if ($om_debug) cw_log_add("order_messages_send_mail",array("extracted_doc_id"=>$extracted_doc_id));

    cw_load('doc');

    $doc_data = cw_call('cw_doc_get', array($extracted_doc_id, 8192)); 
    if (empty($doc_data)) {
       if ($om_debug) cw_log_add("order_messages_send_mail",'empty($doc_data)'); 
       return;
    }

    if ($last_message['mail_to'] != $doc_data['userinfo']['email']) {
        if ($om_debug) cw_log_add("order_messages_send_mail", array('$last_message[mail_to] != $doc_data[userinfo][email]', '$last_message[mail_to]'=>$last_message['mail_to'], '$doc_data[userinfo][email]'=>$doc_data['userinfo']['email'])); 
        return;
    }

    global $customer_id;
    $thread_exists = cw_query_first_cell("select $tables[order_messages_threads].thread_id from $tables[order_messages_messages], $tables[order_messages_threads] where $tables[order_messages_messages].subject='".addslashes($last_message['subject'])."' and $tables[order_messages_messages].thread_id=$tables[order_messages_threads].thread_id and $tables[order_messages_threads].doc_id='$extracted_doc_id'");

    if ($thread_exists) {
        $thread_id = $thread_exists;
        if ($om_debug) cw_log_add("order_messages_send_mail", array('thread_exists'=>$thread_exists));

    } else {

        cw_log_add("order_messages_send_mail", "detect thread by {thread_id}");

        $email_thread_id = intval(cw_order_messages_take_out_from_tags($last_message['subject'], "{", "}", false));

        $related_thread = cw_query_first("select $tables[order_messages_threads].* from $tables[order_messages_threads] where $tables[order_messages_threads].thread_id='$email_thread_id' and $tables[order_messages_threads].doc_id = '$extracted_doc_id'");
        if (!empty($related_thread)) {
            $thread_id = $email_thread_id;
            if ($om_debug) cw_log_add("order_messages_send_mail", array('thread_detected_by_subj' => $thread_id)); 
        }

        if (!$thread_id) 
            $thread_id = cw_array2insert('order_messages_threads', array('doc_id'=>$extracted_doc_id, 'type'=>'A'));

        if ($om_debug) cw_log_add("order_messages_send_mail", array('thread_new'=>$thread_id));
    }

    global $current_area, $config;
    if ($thread_id) {
        $recepient_id = $doc_data['userinfo']['customer_id'];
        if ($current_area == 'C') {

            if (!empty($config['order_messages']['default_recepient_admin_email']))
                $default_recepient_admin_email = 
                  $config['order_messages']['default_recepient_admin_email'];

            $sender_id = cw_query_first_cell("select customer_id from $tables[customers] where usertype='A' and email='$default_recepient_admin_email'");
            
        } else {  
//            $sender_id = $customer_id;

            if (!empty($config['order_messages']['default_recepient_admin_email']))
                $default_recepient_admin_email =
                  $config['order_messages']['default_recepient_admin_email'];

            $sender_id = cw_query_first_cell("select customer_id from $tables[customers] where usertype='A' and email='$default_recepient_admin_email'");

        }
        $last_message['body'] = 
              str_replace(array("--------- please reply above this line ----------"), "",trim($last_message['body']));

//            str_replace("<br>","",trim(cw_order_messages_take_out_from_tags($last_message['body'], "<!--start_doc_layout-->", "<!--end_doc_layout-->", false)));
 
        $new_message_id = cw_array2insert('order_messages_messages', array('thread_id'=>$thread_id, 'sender_id'=>$sender_id, 'recepient_id'=>$recepient_id, 'author_id'=>$sender_id, 'date'=>time(), 'subject'=>addslashes($last_message['subject']), 'body'=>addslashes($last_message['body']), 'read_status'=>1));

    }
    if ($om_debug) cw_log_add("order_messages_send_mail",array('created new message'=>$new_message_id));
    return $return;
}

function cw_order_messages_decode_qprint($str) {
    return imap_qprint($str);
}

function cw_order_messages_take_out_from_tags($src_string, $tag1, $tag2, $remove_middle=true) {
        $result = $src_string; 
        $body_start_pos = strrpos($src_string, $tag1);
        $body_end_pos = strrpos($src_string, $tag2);
        if ($remove_middle) {
            $excluded = array();
            if ($body_start_pos !== false) {
                $excluded[] = substr($src_string, 0, $body_start_pos);
            }
            if ($body_end_pos !== false) {
                $excluded[] = substr($src_string, $body_end_pos+strlen($tag2));
            }
            if (!empty($excluded))
                $result = implode("",$excluded);
        } else {
            if ($body_end_pos !== false)
                $result = substr($result, 0, $body_end_pos); 

            if ($body_start_pos !== false) 
                $result = substr($result, $body_start_pos+strlen($tag1)); 
             
        } 
        return $result;  
}

function cw_order_messages_get_emails($time) {

    global $tables, $config;
    global $take_messages_debug;
/*
$config['order_messages']['contact_email_access_info'] = '{pop.gmail.com:995/novalidate-cert/pop3/ssl}INBOX';
*/
    $mail = imap_open($config['order_messages']['contact_email_access_info'], 
                      $config['order_messages']['contact_email_address'], 
                      $config['order_messages']['contact_email_password']);

    /*cw_log_add("order_messages_get_emails",array('contact_email_access_info'=>$config['order_messages']['contact_email_access_info'],
                      'contact_email_address'=>$config['order_messages']['contact_email_address'],
                      'contact_email_password'=>'--hidden--'));*/

    if(!$mail) {
        //cw_log_add("order_messages_get_emails", array("imap last error: "=>imap_last_error()));
        if ($take_messages_debug == 'Y') {
            print_r(array("order_messages_get_emails", array("imap last error: "=>imap_last_error()))); 
            print("<br>"); 
        } 
    } else {

        if ($take_messages_debug == 'Y') {
            print_r(array("order_messages_get_emails", array("imap last error: "=>imap_last_error(), "mail_obj"=>$mail)));
            print("<br>");  
        }  
    }

    $last_mail_id = cw_query_first_cell("select max(mail_id) from $tables[mail_rpool]");

    if ($mail) {
        $headers = imap_headers($mail);
        if (!empty($headers)) {
            $curr_message_id = imap_num_msg($mail);
            while ($curr_message_id > 0) {
                $header = imap_header($mail, $curr_message_id);

                //do not save emails from anyone else but users
                $from = $header->from;
                foreach ($from as $id => $object) {
                    $emailfrom = $object->mailbox . "@" . $object->host;
                }

if ($take_messages_debug == 'Y') {
print_r(array($curr_message_id, $header->date , strtotime($header->date), $header->subject, $from, $emailfrom, "<br>"));
}
                $is_users_email = cw_query_first_cell("select count(*) from $tables[customers] where email='".addslashes($emailfrom)."'");
                $is_orders_email = cw_query_first_cell("select count(*) from $tables[docs_user_info] where email='".addslashes($emailfrom)."'");

                if (!$is_users_email && !$is_orders_email) {

if ($take_messages_debug == 'Y') {
    print_r(array("not_users/orders_emails","<br>"));
}
//cw_log_add("mail_rpool_refused", array("emailfrom" => $emailfrom, "subj" => $header->subject));

//                    cw_log_add('mail_rpool', 
//                        array('not_users/orders_emails', 'emailfrom'=>$emailfrom, 'subject'=>$header->subject, 'date'=>$header->date));

                    $curr_message_id--; 
                    continue;
                }  

                // pull the plain text for message $n 
                $st = imap_fetchstructure($mail, $curr_message_id);
                if (!empty($st->parts)) {
                    for ($i = 0, $j = count($st->parts); $i < $j; $i++) {
                        $part = $st->parts[$i];
                        if ($part->subtype == 'PLAIN') {
                            $body = imap_fetchbody($mail, $curr_message_id, $i+1);
                        }
                    }
                } else {
                    $body = imap_body($mail, $curr_message_id);
                }

                cw_log_add('mail_rpool',
                    array('header' => $header,
                       'subject' => $header->subject,
                       'mail_from' => $emailfrom,
                       'mail_to' => $header->toaddress,
                       'body' => $body
                    )
                );

                $body = preg_replace('#(^\w.+:\n)?(^>.*(\n|$))+#mi', "", cw_order_messages_all_decodes($body));
                cw_array2insert('mail_rpool', 
                array('header' => serialize($header), 
                       'body' => addslashes($body), 
                       'subject' => $header->subject, 
                       'mail_from' => addslashes($emailfrom), 
                       'mail_to' => $header->toaddress)
                );

                $curr_message_id--;
            }
        }
        imap_close($mail);
    }   

    if ($last_mail_id)
        $last_mail_id_condition = " where mail_id > '$last_mail_id' "; 

    if ($take_messages_debug != 'Y')
        cw_order_messages_process_new_emails($last_mail_id_condition);   
 
    return;
}

function cw_order_messages_remove_re($text) {
    return preg_replace("/([\[\(] *)?(RE?S?|FYI|RIF|I|FS|FWD?) *([-:;)\]][ :;\])-]*|$)|\]+ *$/im", '', $text);
}

function cw_order_messages_process_new_emails($condition = "") {
    global $tables, $config;
    global $take_messages_debug;
    cw_load('doc', 'user');
    $new_emails = cw_query("select * from $tables[mail_rpool] $condition");
    if (empty($new_emails)) return;
    $processed_mail_ids = array();

    if (!empty($config['order_messages']['default_recepient_admin_email'])) {
        $default_recepient_admin_email = $config['order_messages']['default_recepient_admin_email'];
        $default_recepient_admin_id = cw_query_first_cell("select customer_id from $tables[customers] where usertype='A' and email='$default_recepient_admin_email'");
    }

    foreach ($new_emails as $email) {

        if (strpos($email['body'], '--------- please reply above this line ----------') !== false) {
            $bodyparts = explode("--------- please reply above this line ----------", $email['body']);
            if (!empty($bodyparts[0])) 
                $email['body'] = $bodyparts[0];
        } 

        //remove RE, FWD etc from subject
        $clean_subject = cw_order_messages_remove_re($email['subject']);

        cw_log_add("order_messages_process_new_emails",array('clean_subject'=>$clean_subject));  
        if ($take_messages_debug == 'Y') {print_r(array('clean_subject'=>$clean_subject));print("<br>");}    

        $related_threads = cw_query($s = "select $tables[order_messages_messages].*, $tables[order_messages_threads].doc_id from $tables[order_messages_messages] left join $tables[order_messages_threads] on $tables[order_messages_threads].thread_id=$tables[order_messages_messages].thread_id where $tables[order_messages_messages].subject = '$clean_subject'");

        cw_log_add("order_messages_process_new_emails", array('related_threads'=>$related_threads, 'sql'=>$s));  
        if ($take_messages_debug == 'Y') {print_r(array('related_threads'=>$related_threads, 'sql'=>$s));print("<br>");}

        if (!empty($related_threads)) {
            foreach ($related_threads as $doc_thread) {
                $doc_data = cw_call('cw_doc_get', array($doc_thread['doc_id'], 8192));
                if (strcasecmp($email['mail_from'], $doc_data['userinfo']['email']) == 0) {
                    $new_message_id = cw_array2insert('order_messages_messages', array('thread_id'=>$doc_thread['thread_id'], 'sender_id'=>$doc_data['userinfo']['customer_id'], 'recepient_id'=>$doc_thread['author_id'], 'author_id'=>$doc_data['userinfo']['customer_id'], 'date'=>time(), 'subject'=>addslashes($email['subject']), 'body'=>addslashes($email['body']), 'read_status'=>0));                      

                    //notify recipient over email when reply is processed
                    cw_order_messages_notify_other_respondent ($new_message_id, $doc_thread['author_id'], $doc_thread['doc_id']);  

                    $processed_mail_ids[] = $email['mail_id']; 
                    break;

                } elseif (cw_query_first_cell("select count(*) from $tables[customers] where usertype='A' and email='$email[mail_from]'")) {
                    $admin_id = cw_query_first_cell("select customer_id from $tables[customers] where usertype='A' and email='$email[mail_from]'");
                    $recepient_ids = array();
                    if ($doc_thread['sender_id'] != $admin_id && $doc_thread['sender_id'] != $default_recepient_admin_id) 
                        $recepient_ids[] = $doc_thread['sender_id'];
                    if ($doc_thread['recepient_id'] != $admin_id && $doc_thread['recepient_id'] != $default_recepient_admin_id) 
                        $recepient_ids[] = $doc_thread['recepient_id'];
 
                    if ($default_recepient_admin_id) 
                        $admin_id = $default_recepient_admin_id;  
 
                    $new_message_id = cw_array2insert('order_messages_messages', array('thread_id'=>$doc_thread['thread_id'], 'sender_id'=>$admin_id, 'recepient_id'=>$recepient_ids[0], 'author_id'=>$admin_id, 'date'=>time(), 'subject'=>addslashes($email['subject']), 'body'=>addslashes($email['body']), 'read_status'=>0));

                    //notify recipient over email when reply is processed
                    foreach ($recepient_ids as $recepient_id) {  
                        cw_order_messages_notify_other_respondent ($new_message_id, $recepient_id, $doc_thread['doc_id']);
                    }

                    $processed_mail_ids[] = $email['mail_id'];
                    break;
                } else {
                    $supplier_info = array();
                    foreach ($doc_data['products'] as $doc_product) {
                        if ($doc_product['supplier_customer_id']) {
                            $supplier_info = cw_user_get_info($doc_product['supplier_customer_id'], 1);
                            if (strcasecmp($supplier_info['email'], $email['mail_from']) == 0) 
                                break;

                            $supplier_info = array();
                        }
                    }
                    if (!empty($supplier_info)) {
                        $new_message_id = cw_array2insert('order_messages_messages', array('thread_id'=>$doc_thread['thread_id'], 'sender_id'=>$supplier_info['customer_id'], 'recepient_id'=>$doc_thread['author_id'], 'author_id'=>$supplier_info['customer_id'], 'date'=>time(), 'subject'=>addslashes($email['subject']), 'body'=>addslashes($email['body']), 'read_status'=>0));

                        //notify recipient over email when reply is processed
                        cw_order_messages_notify_other_respondent ($new_message_id, $doc_thread['author_id'], $doc_thread['doc_id']);

                        $processed_mail_ids[] = $email['mail_id']; 
                        break;
                    }
                }    
            } 
        } else {
            //detect thread by {thread_id} or create new thread by #SW [doc_id]

            cw_log_add("order_messages_process_new_emails", "detect thread by {thread_id}");
            if ($take_messages_debug == 'Y') {print_r(array("detect thread by {thread_id}"));print("<br>");}

            $email_thread_id = intval(cw_order_messages_take_out_from_tags($email['subject'], "{", "}", false)); 

            $related_thread = cw_query_first("select $tables[order_messages_threads].* from $tables[order_messages_threads] where $tables[order_messages_threads].thread_id='$email_thread_id'");
            if (empty($related_thread)) {
                $email_thread_id = 0;
            } 

            cw_log_add("order_messages_process_new_emails",array("email_thread_id"=>$email_thread_id));
            if ($take_messages_debug == 'Y') {print_r(array("email_thread_id"=>$email_thread_id));print("<br>");}

            if (empty($email_thread_id) && strpos($email['subject'], "#") !== false) {
                $_parts = explode("#", cw_order_messages_remove_doc_prefix($email['subject']));      
                $extracted_doc_id = intval(trim($_parts[1]));

               cw_log_add("order_messages_process_new_emails", array("extracted_doc_id"=>$extracted_doc_id)); 
                if ($take_messages_debug == 'Y') {print_r(array("extracted_doc_id"=>$extracted_doc_id));print("<br>");}

                if ($extracted_doc_id) {
                    $doc_data = cw_call('cw_doc_get', array($extracted_doc_id, 8192));
                    if (!empty($doc_data)) {
                        $email_thread_id = cw_array2insert('order_messages_threads', array('doc_id'=>$extracted_doc_id, 'type'=>'A'));
                    }
                } 

                cw_log_add("order_messages_process_new_emails", array("created new thread"=>$email_thread_id)); 
                if ($take_messages_debug == 'Y') {print_r(array("created new thread"=>$email_thread_id));print("<br>");}

                if (!empty($email_thread_id)) 
                    $related_thread = $email_thread_id;

            } 

            if (empty($related_thread)) { 
                $processed_mail_ids[] = $email['mail_id'];
                continue;  
            }

            $email_sender_id = cw_query_first_cell("select customer_id from $tables[customers] where email='$email[mail_from]'"); 

            cw_log_add("order_messages_process_new_emails", array("$email[mail_from] email_sender_id $email_sender_id"));
            if ($take_messages_debug == 'Y') {print_r(array("$email[mail_from] email_sender_id $email_sender_id"));print("<br>");}

            if (empty($email_sender_id)) {
                $processed_mail_ids[] = $email['mail_id'];   
                continue;        
            }   

            //is sender email related to detected thread
            $is_email_allowed = cw_query_first_cell("select count(*) from $tables[order_messages_messages] where $tables[order_messages_messages].thread_id='$email_thread_id' and ($tables[order_messages_messages].sender_id='$email_sender_id' or $tables[order_messages_messages].recepient_id='$email_sender_id' or $tables[order_messages_messages].author_id='$email_sender_id')");

            if (!$is_email_allowed)  {
                $is_email_allowed = cw_query_first_cell("select count(*) from $tables[docs_user_info] dui, $tables[docs] d, $tables[order_messages_threads] omt where omt.thread_id='$email_thread_id' and omt.doc_id=d.doc_id and d.doc_info_id=dui.doc_info_id and dui.email='$email[mail_from]'");
            } 

            if (!$is_email_allowed) {
                $is_email_allowed = cw_query_first_cell("select count(*) from $tables[customers] where email='$email[mail_from]' and usertype='A'");   
            }
              
            cw_log_add("order_messages_process_new_emails", "<br>is_email_allowed $is_email_allowed<br>");
            if ($take_messages_debug == 'Y') {print_r(array("<br>is_email_allowed $is_email_allowed<br>"));print("<br>");}

            $other_respondent_id = 0;

            if ($is_email_allowed) {
                //get other respondends in thread
                //if email is sent by admin then select non-admin user as respondent
                if (cw_query_first_cell("select count(*) from $tables[customers] where email='$email[mail_from]' and usertype='A'")) {
                    $other_respondent_id = cw_query_first_cell("select $tables[order_messages_messages].sender_id from $tables[order_messages_messages], $tables[customers] where $tables[customers].usertype!='A' and $tables[customers].customer_id=$tables[order_messages_messages].sender_id and $tables[order_messages_messages].thread_id='$email_thread_id'");
                    if (empty($other_respondent_id))
                        $other_respondent_id = cw_query_first_cell("select $tables[order_messages_messages].recepient_id from $tables[order_messages_messages], $tables[customers] where $tables[customers].usertype!='A' and $tables[customers].customer_id=$tables[order_messages_messages].recepient_id and $tables[order_messages_messages].thread_id='$email_thread_id'");    
                }
                
                if (empty($other_respondent_id))
                    $other_respondent_id = cw_query_first_cell("select sender_id from $tables[order_messages_messages] where thread_id='$email_thread_id' and sender_id != '$email_sender_id'"); 

                cw_log_add("order_messages_process_new_emails","<br>other_respondent_id $other_respondent_id<br>");
                if ($take_messages_debug == 'Y') {print_r(array("<br>other_respondent_id $other_respondent_id<br>"));print("<br>");}  

                if (empty($other_respondent_id)) 
                    $other_respondent_id = cw_query_first_cell("select recepient_id from $tables[order_messages_messages] where thread_id='$email_thread_id' and recepient_id != '$email_sender_id'");

                if (empty($other_respondent_id) || 
                    cw_query_first_cell("select count(*) from $tables[customers] where usertype='A' and customer_id='$other_respondent_id'")) {
                    //use default id to sent email
                    if (!empty($config['order_messages']['default_recepient_admin_email']))   
                        $default_recepient_admin_email = $config['order_messages']['default_recepient_admin_email'];

                    $other_respondent_id = cw_query_first_cell("select customer_id from $tables[customers] where usertype='A' and email='$default_recepient_admin_email'");

                    cw_log_add("order_messages_process_new_emails","<br>selected default recepient ($default_recepient_admin_email): customer_id = $other_respondent_id<br>");
                    if ($take_messages_debug == 'Y') {print_r(array("<br>selected default recepient ($default_recepient_admin_email): customer_id = $other_respondent_id<br>"));print("<br>");}

                } 

                if (!empty($other_respondent_id))  {
                    global $smarty, $current_language;
                    $rnd_key = time();
                    $smarty->assign('message', array('subject'=>$rnd_key, 'thread_id'=>$email_thread_id));
                    $smarty->assign('doc_id', $related_thread['doc_id']); 

                    $language = $language ? $language : $current_language;
                    $order_subject_template = 'addons/order_messages/mail/customer_subj.tpl';
                    $test_subject = chop(cw_display($order_subject_template, $smarty, false, $language));

                    cw_log_add("order_messages_process_new_emails","<br>test subject: $test_subject<br>");
                    if ($take_messages_debug == 'Y') {print_r(array("<br>test subject: $test_subject<br>"));print("<br>");}                    
 
                    $subject_parts = explode($rnd_key, $test_subject);
                    $extracted_replied_subject = str_replace($subject_parts,"",$email['subject']);

                    cw_log_add("order_messages_process_new_emails","<br>extracted_replied_subject $extracted_replied_subject<br>");
                    if ($take_messages_debug == 'Y') {print_r(array("<br>extracted_replied_subject $extracted_replied_subject<br>"));print("<br>");}                

                    if (cw_query_first_cell("select count(*) from $tables[customers] where customer_id = '$email_sender_id' and usertype='A'") && 
                        $default_recepient_admin_id)
                        $email_sender_id = $default_recepient_admin_id;  

                    $new_message_id = cw_array2insert('order_messages_messages', array('thread_id'=>$email_thread_id, 'sender_id'=>$email_sender_id, 'recepient_id'=>$other_respondent_id, 'author_id'=>$email_sender_id, 'date'=>time(), 'subject'=>addslashes($extracted_replied_subject), 'body'=>addslashes($email['body']), 'read_status'=>0));
                    cw_order_messages_notify_other_respondent ($new_message_id, $other_respondent_id, $related_thread['doc_id']);

                    $processed_mail_ids[] = $email['mail_id'];
                }
            }
        } 
    }

    //if ($take_messages_debug != "Y")
    if (!empty($processed_mail_ids)) 
        db_query("delete from $tables[mail_rpool] where mail_id in ('".implode("','", $processed_mail_ids)."')");

    return $processed_mail_ids; 
}

function cw_order_messages_remove_doc_prefix($str) {
    global $tables;
    $result = $str;
    $doc_prefixes = cw_query_column("select value from $tables[config] where name='prefix' and value != ''");
    if (!empty($doc_prefixes))
        $result = str_replace($doc_prefixes, "", $result);

    return $result;
}

function cw_order_messages_notify_other_respondent ($message_id, $recepient_id, $doc_id) {
    global $tables, $config;
    global $smarty;

    $recepient_usertype = cw_query_first_cell("select usertype from $tables[customers] where customer_id='$recepient_id'");

    if ($recepient_usertype == 'C') {
        $recepient_email = cw_query_first_cell("select $tables[docs_user_info].email from $tables[docs_user_info], $tables[docs] where $tables[docs_user_info].doc_info_id=$tables[docs].doc_info_id and $tables[docs].doc_id='$doc_id'");
    } else {

        if ($recepient_usertype == "A") {
            if (!empty($config['order_messages']['default_recepient_admin_email'])) {
                $default_recepient_admin_email = $config['order_messages']['default_recepient_admin_email'];
                $respondent_id = cw_query_first_cell("select customer_id from $tables[customers] where usertype='A' and email='$default_recepient_admin_email'");
            } 
        }
 
        $recepient_email = cw_query_first_cell("select email from $tables[customers] where customer_id='$recepient_id'");
        cw_log_add("order_messages_process_new_emails", array('recepient_usertype'=>$recepient_usertype, 'default_recepient_admin_email'=>$default_recepient_admin_email, 'recepient_id'=>$recepient_id));
    }

    if (empty($recepient_email)) return;

    $message_info = cw_query_first("select * from $tables[order_messages_messages] where message_id='$message_id'");  
 
    $smarty->assign('message', 
                    array('subject'=>$message_info['subject'], 
                          'thread_id'=>$message_info['thread_id'], 
                          'body'=>$message_info['body']));
    $smarty->assign('doc_id', $doc_id);

    $to_recipient_lang = cw_query_first_cell ("SELECT language FROM $tables[customers] WHERE customer_id='$recepient_id'");
    if (empty($to_recipient_lang))
        $to_recipient_lang = $config['default_customer_language'];

    cw_call('cw_send_mail', array($config['order_messages']['contact_email_address'], $recepient_email, 'addons/order_messages/mail/customer_subj.tpl', 'addons/order_messages/mail/customer.tpl', $to_recipient_lang, false, true));
    $subj = cw_display('addons/order_messages/mail/customer_subj.tpl', $smarty, false, $to_recipient_lang);
    $body = cw_display('addons/order_messages/mail/customer.tpl', $smarty, false, $to_recipient_lang);
    cw_log_add("order_messages_process_new_emails", array($config['order_messages']['contact_email_address'], $recepient_email, $subj, $body, $to_recipient_lang, false, true));
}


function cw_order_messages_threads_sort($x, $y) {
    $a = $x['last_message']['date'];
    $b = $y['last_message']['date'];
    if ($a == $b) {
        return 0;
    }
    return ($a < $b) ? 1 : -1;
}

// change search query params for order search
function cw_order_messages_prepare_search_orders($data, $docs_type, &$fields, &$query_joins, &$where, &$groupbys, &$having, &$orderbys) {
    global $tables;

    if (
        $data['search_sections']['tab_search_orders_advanced']
        && $docs_type == 'O'
        && $data['order_messages']['unread_messages']
    ) {
        $query_joins['order_messages_threads'] = array(
            'on' => "$tables[order_messages_threads].doc_id = $tables[docs].doc_id",
            'is_inner' => 1
        );
        $query_joins['order_messages_messages'] = array(
            'on' => "$tables[order_messages_messages].thread_id = $tables[order_messages_threads].thread_id and $tables[order_messages_messages].read_status = 0", 
            'is_inner' => 1
        ); 
    }
}

function cw_order_messages_is_base64($s) {
    // Check if there are valid base64 characters
    if (!preg_match('/^[a-zA-Z0-9\/\r\n+]*={0,2}$/', $s)) return 2;

    // Decode the string in strict mode and check the results
    $decoded = base64_decode($s, true);
    if(false === $decoded) return 3;

    // Encode the string again
    //if(base64_encode($decoded) != $s) return 4;

    return true;
}

function cw_order_messages_all_decodes($s) {

    $s = cw_order_messages_decode_qprint($s);
    $is_base64 = cw_order_messages_is_base64($s);  
    if ($is_base64 == 1) 
        $s = base64_decode($s);

    return $s; 
} 
