<?php
/** ATTENTION TODO
* This controler is called in addons/salesman/news.php ONLY
* It mostly duplicates actions in include/news/news.php
* TODO: Compare these two controllers, remove one, move another into addon folder
*/



cw_load('files');

$required_mode = array("create", "update", "modify", "import", "messages", "delete", "archive");
if (AREA_TYPE != 'B') $required_mode[] = "subscribers";
if (!in_array($mode, $required_mode))
	$mode = "";

$salesman_condition = "";
if (AREA_TYPE == 'B')
    $salesman_condition = " and salesman_customer_id='$customer_id'";

if ($flag_show_as_news) {
    $salesman_condition .= " and show_as_news='Y'";
    $current_script = 'index.php?target=news_c';
}
else {
    $salesman_condition .= " and show_as_news='N'";
    $current_script = 'index.php?target=news';
}

if ($REQUEST_METHOD == "POST" || ($action == "messages" && $action == "send_continue")) {

	if ($action == "delete" && !empty($to_delete)) {
		if (is_array($to_delete)) {
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

			$top_message['content'] = cw_get_langvar_by_name("msg_adm_newslists_del");
		}
	}

	if ($action == "update" && AREA_TYPE != 'B') {
		#
		# Update news lists
		#
		if (is_array($posted_data)) {
			foreach ($posted_data as $list_id=>$v) {
				$show_as_news = $flag_show_as_news ? "Y" : "N";
				$avail = (empty($v['avail']) ? "N" : "Y");
				db_query("UPDATE $tables[newslists] SET show_as_news='$show_as_news', avail='$avail' WHERE list_id='$list_id' $salesman_condition");
			}

			$top_message['content'] = cw_get_langvar_by_name("msg_adm_newslists_upd");
		}
	}

	if ($action == "modify" || $action == "create") {
		if (is_array($list)) {
			$list['name'] = @trim($list['name']);
			$list['descr'] = @trim($list['descr']);
			$list = cw_array_map('stripslashes', $list);

			$error = array();
			$err = false;
			foreach (array("name", "descr") as $key) {
				$err = $err || ($error[$key] = empty($list[$key]));
			}

			if (!$err) {
				$list = cw_array_map('addslashes', $list);
				$mode = "";
				$list_values = $list;

                if (AREA_TYPE == 'B') {
                    $list_values['salesman'] = $customer_id;
                    if ($list['list_id']) {
                        $def_val = cw_query_first("select * from $tables[newslists] where list_id='".$list['list_id']."'");
                        $list_values['avail'] = $def_val['avail'];
                    }
                    else
                        $list_values['avail'] = 'N';
                }
                else
                    $list_values['salesman'] = '';

                $list_values['subscribe'] = 'N';
                if ($flag_show_as_news) {
                    if (is_array($memberships)) {
                        $count = cw_query("select count(*) from $tables[memberships] where membership_id in (".implode(', ', $memberships).") and area='C'");
                        if ($count || in_array(0, $memberships))
                            $list_values['subscribe'] = 'Y';
                    }
                }
                $list_values['show_as_news'] = $flag_show_as_news?'Y':'N';

				cw_unset($list_values,'list_id');

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
				$top_message['content'] = cw_get_langvar_by_name("err_filling_form");
				$top_message['type'] = "E";
				$nwslt_object = &cw_session_register("nwslt_object");
				$nwslt_object['error'] = $error;
				$nwslt_object['list'] = $list;

				cw_header_location($current_script."&mode=$mode&targetlist=".$list['list_id']);
			}
		}

		cw_header_location($current_script."&mode=modify&targetlist=".$list['list_id']);
	}
	elseif ($action == "subscribers") {
		#
		# Modify subscriptions for the selected newslist
		#
		if ($action == "add" && !empty($new_email)) {
			$count = cw_query_first_cell("SELECT COUNT(*) FROM $tables[newslist_subscription] WHERE list_id='$targetlist' AND email='$new_email'");
			if ($count<1) {
				db_query("INSERT INTO $tables[newslist_subscription] (list_id, email, since_date) VALUES ('$targetlist','$new_email','".time()."')");
				$top_message['content'] = cw_get_langvar_by_name("msg_adm_news_subscriber_add");
			}
			else {
				$top_message['content'] = cw_get_langvar_by_name("msg_adm_err_news_subscriber_add");
				$top_message['type'] = "E";
			}
		}
		elseif ($action == "delete" && is_array($to_delete)) {
			foreach ($to_delete as $email=>$flag) {
				db_query("DELETE FROM $tables[newslist_subscription] WHERE list_id='$targetlist' AND email='$email'");
			}

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
					$count = cw_query_first_cell("SELECT COUNT(*) FROM $tables[newslist_subscription] WHERE list_id='$targetlist' AND email='$new_email'");
					if ($count < 1) {
						db_query("INSERT INTO $tables[newslist_subscription] (list_id, email, since_date) VALUES ('$targetlist','$new_email','".time()."')");
					}
				}

				fclose($fp);
				$top_message['content'] = cw_get_langvar_by_name("msg_adm_news_subscribers_imp");
			}

			@unlink($userfile);
		}
		elseif ($action == "export" && !empty($to_delete)) {
			header("Content-type: application/force-download");
			header("Content-disposition: attachment; filename=subscribers.txt");

			$subscribers = cw_query("SELECT * FROM $tables[newslist_subscription] WHERE list_id='$targetlist' AND email IN ('".implode("','", array_keys($to_delete))."')");
			if (is_array($subscribers)) {
				foreach ($subscribers as $value) {
					echo $value['email']."\n";
				}
			}

			exit;
		}

		cw_header_location($current_script."&mode=subscribers&targetlist=".$targetlist);
	}
	elseif ($action == "messages") {
		#
		# Manage messages of newslist
		#
		if (is_array($message)) {
			$message['subject'] = @trim($message['subject']);
			$message = cw_array_map('stripslashes',$message);

			$smarty->assign('action', 'modify');
			$smarty->assign('message', $message);
			$error = array(); $err = false;
			foreach (array("subject", "body") as $key) {
				$err = $err || ($error[$key] = empty($message[$key]));
			}

			if (!$err) {
				$message = cw_array_map('addslashes',$message);

				$mode = "";
                $message['show_as_news'] = $flag_show_as_news?'Y':'N';
				if (!empty($message['newsid'])) {
					db_query("UPDATE $tables[newsletter] SET subject='$message[subject]', body='$message[body]', allow_html='$message[allow_html]'".(AREA_TYPE == 'B'?"":", show_as_news='$message[show_as_news]'").", email1='$message[email1]', email2='$message[email2]', email3='$message[email3]' WHERE newsid='$message[newsid]'");
					$top_message['content'] = cw_get_langvar_by_name("msg_adm_news_message_upd");
				}
				else {
                    if (AREA_TYPE == 'B') $message['show_as_news'] = 'N';
					db_query("INSERT INTO $tables[newsletter] (list_id, send_date, subject, body, allow_html, show_as_news, email1, email2, email3) VALUES ('$targetlist','".time()."','$message[subject]', '$message[body]', '$message[allow_html]', '$message[show_as_news]', '$message[email1]', '$message[email2]', '$message[email3]')");
					$message['newsid'] = db_insert_id();
					$top_message['content'] = cw_get_langvar_by_name("msg_adm_news_message_add");
				}
                db_query("delete from $tables[newsletter_countries] where newsid='".$message['newsid']."'");
                if (is_array($countries))
                    foreach($countries as $val)
                        db_query("replace into $tables[newsletter_countries] (code, newsid, list_id) values('$val', '".$message['newsid']."', '$targetlist')");

				if (!$admin_safe_mode) {
					include $app_main_dir."/addons/news/news_send.php";
				}
			}
			else {
				$nwslt_object = &cw_session_register("nwslt_object");
				$nwslt_object['error'] = "error";
				$nwslt_object['message'] = $message;
			}

			cw_header_location($current_script."&mode=messages&targetlist=".$targetlist."&messageid=".$message['newsid']."&action=modify");
		}
		elseif ($action == "send" || $action == "send_continue") {
            if (AREA_TYPE == 'B') {
                cw_load('mail');
                $salesman_email = cw_query_first_cell("select email from $tables[customers] where customer_id='$customer_id'");
                $smarty->assign('targetlist', $targetlist);
                cw_call('cw_send_mail', array($config['Company']['site_administrator'], "mail/salesman_news_notification_subj.tpl", "mail/salesman_news_notification.tpl", $salesman_email, false));
                db_query("update $tables[newsletter] set status='A' where newsid='$messageid'");

            }
            else
    			include $app_main_dir."/addons/news/news_send.php";
			$top_message['content'] = cw_get_langvar_by_name("msg_adm_news_message_sent");
		}
		elseif ($action == "delete" && !empty($to_delete)) {
			if (is_array($to_delete)) {
				foreach ($to_delete as $k=>$v) {
					db_query("DELETE FROM $tables[newsletter] WHERE newsid='$k'");
				}

				$top_message['content'] = cw_get_langvar_by_name("msg_adm_news_message_del");
			}
		}

		cw_header_location($current_script."&mode=messages&targetlist=".$targetlist);

	}

	cw_header_location($current_script);
}

#
# Process the GET request
#

//if (!empty($mode))
//	$location[count($location)-1][1] = $current_script;

if (!empty($targetlist)) {
	$list = cw_query_first("SELECT * FROM $tables[newslists] WHERE list_id='$targetlist'");

    $javascript_tabs = array();
    $javascript_tabs['modify'] = array (
        'title' => cw_get_langvar_by_name('lbl_details'),
        'template' => 'addons/news/news_details.tpl',
    );
    $javascript_tabs['subscribers'] = array (
        'title' => cw_get_langvar_by_name('lbl_subscriptions'),
        'template' => 'addons/news/news_subscribers.tpl',
    );
    $javascript_tabs['messages'] = array (
        'title' => cw_get_langvar_by_name('lbl_news_list_messages'),
        'template' => 'addons/news/news_messages_list.tpl',
    );
    $javascript_tabs['message_modify'] = array (
        'title' => cw_get_langvar_by_name('lbl_news_list_add_message'),
        'template' => 'addons/news/news_messages_modify.tpl',
    );

    $js_tab = in_array($mode, array_keys($javascript_tabs))?$mode:'modify';
    $smarty->assign('js_tab', $js_tab);
    $smarty->assign('javascript_tabs', $javascript_tabs);
}

if (!empty($list['list_id'])) {
	if ($list['lngcode'] != $current_language && is_array($d_langs) && !in_array($list['lngcode'], $d_langs)) {
		cw_header_location($current_script."&mode=modify&targetlist=$targetlist&edit_lng=$list[lngcode]&old_lng=$current_language");
	}
}

if ($mode == "modify") {
	#
	# Get the news list details and display it
	#
	if (empty($list)) {
		$top_message['content'] = cw_get_langvar_by_name("msg_adm_err_newslist_not_exists");
		cw_header_location($current_script);
	}

    $memberships = cw_query($sql="select membership_id from $tables[newslists_memberships] where list_id='$list[list_id]'");
    if ($memberships)
        foreach($memberships as $val)
            $list['memberships'][$val['membership_id']] = true;
	$smarty->assign('list', $list);
}
elseif ($mode == "create") {
	$location[] = array(cw_get_langvar_by_name("lbl_add_new_list"), "");
}
elseif ($mode == "subscribers") {

	$total_items = cw_query_first_cell("SELECT COUNT(*) FROM $tables[newslist_subscription] WHERE list_id='$targetlist'");

	if (!empty($total_items)) {
        $navigation = cw_core_get_navigation($target, $total_items, $page);
        $navigation['script'] = "index.php?target=$target&mode=subscribers&targetlist=".$targetlist;
        $smarty->assign('navigation', $navigation);

		$subscribers = cw_query("SELECT * FROM $tables[newslist_subscription] WHERE list_id='$targetlist' LIMIT $navigation[first_page], $navigation[objects_per_page]");
	}
	else
		$subscribers = array();

	$smarty->assign('subscribers', $subscribers);
	$location[] = array(cw_get_langvar_by_name("lbl_subscribers_title"), "");
}
elseif ($mode == "messages") {
	if ($action == "modify") {
		$message = cw_query_first("SELECT * FROM $tables[newsletter] WHERE newsid='$messageid'");
        $countries = cw_query("select * from $tables[newsletter_countries] where newsid='$messageid'");
        if(is_array($countries))
            foreach($countries as $val)
                $message['countries'][$val['code']] = true;
		$smarty->assign('message', $message);
	}
	else {
		$messages = cw_query("SELECT * FROM $tables[newsletter] WHERE list_id='$targetlist'");
		$smarty->assign('messages', $messages);
	}

	$location[] = array(cw_get_langvar_by_name("lbl_messages"), "");
}

if (cw_session_is_registered("nwslt_object")) {
	$nwslt_object = &cw_session_register("nwslt_object");
	if (is_array($nwslt_object)) {
		foreach ($nwslt_object as $k=>$v)
			$smarty->assign($k, $v);
	}

	cw_session_unregister("nwslt_object");
}

if (!empty($targetlist)) {
	$targetlistname = cw_query_first_cell("SELECT name FROM $tables[newslists] WHERE list_id='$targetlist'");
	$smarty->assign('targetlistname', $targetlistname);
	$smarty->assign('targetlist', $targetlist);
}

$lists = cw_query("SELECT * FROM $tables[newslists] WHERE lngcode='$current_language' $salesman_condition");
$smarty->assign('lists', $lists);
$smarty->assign('action', $action);
$smarty->assign('mode', $mode);

cw_load('user');
$memberships = cw_user_get_memberships(array('C', 'R'));//cw_query("select * from $tables[memberships] where area='C' order by orderby");
$memberships[] = array('membership' => cw_get_langvar_by_name("lbl_retail_level"), 'membership_id' => 0);
$smarty->assign('memberships', $memberships);

$smarty->assign('flag_show_as_news', $flag_show_as_news);
$smarty->assign('current_script', $current_script);

cw_load('map');
$smarty->assign('countries', cw_map_get_countries());
?>
