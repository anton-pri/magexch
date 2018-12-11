<?php
cw_load('files');

$required_mode = array("create", "update", "modify", "import", "messages", "delete", "archive");
if (AREA_TYPE != 'B') $required_mode[] = "subscribers";

if (!in_array($mode, $required_mode)) $mode = '';

$salesman_condition = '';
if (AREA_TYPE == 'B')
    $salesman_condition = " and salesman_customer_id='$customer_id'";

if ($mode == 'subscribers') {
    if ($action == "add" && !empty($new_email)) {
        $count = cw_query_first_cell("SELECT COUNT(*) FROM $tables[newslist_subscription] WHERE list_id='$list_id' AND email='$new_email'");
		if ($count<1) {
		    db_query("INSERT INTO $tables[newslist_subscription] (list_id, email, since_date) VALUES ('$list_id','$new_email','".time()."')");
			$top_message['content'] = cw_get_langvar_by_name("msg_adm_news_subscriber_add");
        }
		else {
		    $top_message['content'] = cw_get_langvar_by_name("msg_adm_err_news_subscriber_add");
			$top_message['type'] = "E";
        }
    }
	elseif ($action == "delete" && is_array($to_delete)) {
	    foreach ($to_delete as $email=>$flag)
		    db_query("DELETE FROM $tables[newslist_subscription] WHERE list_id='$list_id' AND email='$email'");

        $top_message['content'] = cw_get_langvar_by_name("msg_adm_news_subscribers_del");
    }
	elseif ($action == "import" && !empty($userfile)) {
	    cw_load("mail");
		$userfile = cw_move_uploaded_file("userfile");
		$fp = cw_fopen($userfile, "r", true);
		if ($fp) {
		    while ($line = fgets($fp, 255)) {
			    $new_email = trim($line);
				if (!cw_check_email($new_email))
				    continue;

                $new_email = addslashes($new_email);
				$count = cw_query_first_cell("SELECT COUNT(*) FROM $tables[newslist_subscription] WHERE list_id='$list_id' AND email='$new_email'");
				if ($count < 1)
				    db_query("INSERT INTO $tables[newslist_subscription] (list_id, email, since_date) VALUES ('$list_id','$new_email','".time()."')");
            }

            fclose($fp);
			$top_message['content'] = cw_get_langvar_by_name("msg_adm_news_subscribers_imp");
        }

		@unlink($userfile);
    }
	elseif ($action == "export" && !empty($to_delete)) {
	    header("Content-type: application/force-download");
	    header("Content-disposition: attachment; filename=subscribers.txt");

        $subscribers = cw_query("SELECT * FROM $tables[newslist_subscription] WHERE list_id='$list_id' AND email IN ('".implode("','", array_keys($to_delete))."')");
		if (is_array($subscribers))
		    foreach ($subscribers as $value)
			    echo $value['email']."\n";
        exit;
    }

    cw_header_location("index.php?target=$target&js_tab=subscriptions&list_id=".$list_id);
}

if ($mode == 'messages') {
    if (is_array($message)) {
        $message['subject'] = @trim($message['subject']);

		$smarty->assign('action', 'modify');
        $smarty->assign('message', $message);
		$fill_error = array();
        foreach (array('subject', 'body') as $key)
            if (empty($message[$key])) $fill_error[$key] = true;

        if (!count($fill_error)) {
            $mode = '';

			if (!empty($message['news_id'])) {
                db_query("UPDATE $tables[newsletter] SET subject='$message[subject]', updated_date='".time()."', body='$message[body]', allow_html='$message[allow_html]'".(AREA_TYPE == 'B'?"":", show_as_news='$message[show_as_news]'").", email1='$message[email1]', email2='$message[email2]', email3='$message[email3]' WHERE news_id='$message[news_id]'");
                $top_message['content'] = cw_get_langvar_by_name("msg_adm_news_message_upd");
            }
			else {
                if (AREA_TYPE == 'B') $message['show_as_news'] = 0;
				db_query("INSERT INTO $tables[newsletter] (list_id, send_date, updated_date, created_date, subject, body, allow_html, show_as_news, email1, email2, email3) VALUES ('$list_id','".time()."','".time()."','".time()."','$message[subject]', '$message[body]', '$message[allow_html]', '$message[show_as_news]', '$message[email1]', '$message[email2]', '$message[email3]')");
				$message['news_id'] = db_insert_id();
				$top_message['content'] = cw_get_langvar_by_name("msg_adm_news_message_add");
            }
            db_query("delete from $tables[newsletter_countries] where news_id='".$message['news_id']."'");
            if (is_array($countries))
                foreach($countries as $val)
                    db_query("replace into $tables[newsletter_countries] (code, news_id, list_id) values('$val', '".$message['news_id']."', '$list_id')");

            cw_include('include/news/send.php');

            cw_header_location("index.php?target=$target&list_id=".$list_id."&js_tab=message&messageid=".$message['news_id']);
        }
		else {
            $nwslt_object = &cw_session_register('nwslt_object');
		    $nwslt_object['fill_error'] = 'error';
            $nwslt_object['message'] = $message;
            cw_header_location("index.php?target=$target&list_id=".$list_id."&js_tab=add_message&messageid=".$message['news_id']);
        }
    }
    elseif ($action == "send" || $action == "send_continue") {
        if (AREA_TYPE == 'B') {
            cw_load('mail');
            $salesman_email = cw_query_first_cell("select email from $tables[customers] where customer_id='$customer_id'");
            $smarty->assign('list_id', $list_id);
            cw_call('cw_send_mail', array($salesman_email, $config['Company']['site_administrator'], "mail/salesman_news_notification_subj.tpl", "mail/salesman_news_notification.tpl"));
            db_query("update $tables[newsletter] set status='A' where news_id='$messageid'");
        }
        else {
    	    cw_include('include/news/send.php');
        }
	
    	$top_message['content'] = cw_get_langvar_by_name("msg_adm_news_message_sent");
    }
	elseif ($action == "delete" && is_array($to_delete)) {
        foreach ($to_delete as $k=>$v)
        db_query("DELETE FROM $tables[newsletter] WHERE news_id='$k'");
        $top_message['content'] = cw_get_langvar_by_name("msg_adm_news_message_del");
	}

    cw_header_location("index.php?target=$target&js_tab=messages&list_id=".$list_id);
}

if ($action == 'delete' && is_array($to_delete)) {
    foreach ($to_delete as $k=>$v) {
        if (AREA_TYPE == 'B') {
            $is_list_count = cw_query_first_cell("select count(*) from $tables[newslists] where list_id='$k' $salesman_condition");
            if (!$is_list_count) continue;
        }
        db_query("DELETE FROM $tables[newslist_subscription] WHERE list_id='$k'");
        db_query("DELETE FROM $tables[newslists] WHERE list_id='$k'");
        db_query("DELETE FROM $tables[newsletter] WHERE list_id='$k'");
        db_query("DELETE FROM $tables[newslists_memberships] WHERE list_id='$k'");
        db_query("DELETE FROM $tables[newsletter_countries] WHERE list_id='$k'");
    }
    $top_message['content'] = cw_get_langvar_by_name('msg_adm_newslists_del');
    cw_header_location("index.php?target=$target");
}

// Update newslists on list page
if ($action == 'update' && AREA_TYPE != 'B' && is_array($posted_data)) {

    foreach ($posted_data as $list_id=>$v)
        cw_array2update('newslists', array('subscribe' => $v['subscribe'], 'show_as_news' => $v['show_as_news'], 'avail' => $v['avail']), "list_id='$list_id' $salesman_condition");
    $top_message['content'] = cw_get_langvar_by_name('msg_adm_newslists_upd');
    cw_header_location("index.php?target=$target");
}

// Modify certain newslist by list_id
if ($action == 'modify') {
    $list = cw_array_map('trim', $list);

    $fill_error = array();
    foreach (array('name', 'descr') as $key)
        if (empty($list[$key])) $fill_error[$key] = true;

    if (!count($fill_error)) {
        $list_values = $list;

        $list_values['salesman_customer_id'] = '';
        if (AREA_TYPE == 'B') {
            $list['salesman_customer_id'] = $customer_id;
            if ($list['list_id']) {
                $def_val = cw_query_first("select * from $tables[newslists] where list_id='".$list['list_id']."'");
                $list_values['avail'] = $def_val['avail'];
            }
            else
                $list_values['avail'] = 0;
        }

        cw_unset($list_values, 'list_id');

        if (!empty($list['list_id'])) {
            cw_array2update('newslists', $list_values, "list_id='$list[list_id]'");
            $top_message['content'] = cw_get_langvar_by_name("msg_adm_newslist_upd");
        }
        else {
            $list_values['lngcode'] = empty($edit_lng) ? $current_language : $edit_lng;
            cw_array2insert('newslists', $list_values);
            $list['list_id'] = db_insert_id();
            $top_message['content'] = cw_get_langvar_by_name("msg_adm_newslists_add");
        }

        db_query("delete from $tables[newslists_memberships] where list_id='$list[list_id]'");
        if (is_array($memberships)) {
            $arr_to_insert = array();
            $arr_to_insert['list_id'] = $list['list_id'];
            foreach($memberships as $membership_id) {
                $arr_to_insert['membership_id'] = $membership_id;
                cw_array2insert('newslists_memberships', $arr_to_insert, true);
            }
        }
    }
    else {
        $top_message = array('content' => cw_get_langvar_by_name('err_filling_form'), 'type' => 'E');
        $nwslt_object = &cw_session_register('nwslt_object');
        $nwslt_object['fill_error'] = $fill_error;
        $nwslt_object['list'] = $list;

        cw_header_location("index.php?target=$target&list_id=".$list['list_id']);
    }

    cw_header_location("index.php?target=$target&list_id=".$list['list_id']);
}

$location[] = array(cw_get_langvar_by_name('lbl_news_management'), 'index.php?target='.$target);

if (isset($_GET['list_id'])) {
    if (!empty($list_id)) {
    	$list = cw_query_first("select * from $tables[newslists] WHERE list_id='$list_id'");

        if (empty($list['list_id'])) {
            $top_message['content'] = cw_get_langvar_by_name('msg_adm_err_newslist_not_exists');
            cw_header_location('index.php?target='.$target);
        }
        else {
            if ($list['lngcode'] != $current_language && is_array($d_langs) && !in_array($list['lngcode'], $d_langs))
                cw_header_location("index.php?target=$target&mode=modify&list_id=$list_id&edit_lng=$list[lngcode]&old_lng=$current_language");
        }

        $memberships = cw_query("select membership_id from $tables[newslists_memberships] where list_id='$list[list_id]'");
        if ($memberships)
        foreach($memberships as $val)
            $list['memberships'][$val['membership_id']] = true;

        $list_idname = cw_query_first_cell("SELECT name FROM $tables[newslists] WHERE list_id='$list_id'");
        $smarty->assign('list_idname', $list_idname);
        $smarty->assign('list_id', $list_id);
        $smarty->assign('list', $list);
# subscribers tabs


        $total_items = count($subscribers = cw_call('cw\news\get_subscribers',array($list_id)));

        if (!empty($total_items)) {
            $navigation = cw_core_get_navigation($target, $total_items, $page);
            $navigation['script'] = "index.php?target=$target&js_tab=subscriptions&list_id=".$list_id;
            $smarty->assign('navigation', $navigation);

            $subscribers = array_slice($subscribers, $navigation['first_page'], $navigation['objects_per_page']);
        }
        $smarty->assign('subscribers', $subscribers);
# messages tab
        cw_load('map');
        $countries = cw_map_get_countries();
        $smarty->assign('countries', $countries);

        if (isset($messageid)) {
            $message = cw_query_first("SELECT * FROM $tables[newsletter] WHERE news_id='$messageid'");
            $countries = cw_query("select * from $tables[newsletter_countries] where news_id='$messageid'");
            if(is_array($countries))
            foreach($countries as $val)
                $message['countries'][$val['code']] = true;
            $smarty->assign('message', $message);
            $smarty->assign('messageid', $messageid);
        }
        $messages = cw_query("SELECT * FROM $tables[newsletter] WHERE list_id='$list_id'");
        $smarty->assign('messages', $messages);

        $smarty->assign('main', 'management');
    }
    else {
        $smarty->assign('main', 'details');
        $list['usertype']='C';
        $smarty->assign('list',$list); // default values of new list
    }

    $smarty->assign('memberships', cw_user_get_memberships(array('C', 'R')));
    $location[] = array($list['name'], '');
}
else {
    $lists = cw_query("SELECT * FROM $tables[newslists] WHERE lngcode='$current_language' $salesman_condition");
    $smarty->assign('lists', $lists);
    $smarty->assign('main', 'lists_select');
}

if (cw_session_is_registered('nwslt_object')) {
	$nwslt_object = &cw_session_register('nwslt_object');
	if (is_array($nwslt_object))
		foreach ($nwslt_object as $k=>$v)
			$smarty->assign($k, $v);

	cw_session_unregister("nwslt_object");
}

$smarty->assign('action', $action);
$smarty->assign('mode', $mode);
$smarty->assign('js_tab', $js_tab);
?>
