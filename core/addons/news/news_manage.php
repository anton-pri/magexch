<?php
cw_load('mail');

if (empty($mode)) {
	if ($REQUEST_METHOD == "GET")
		$mode = "archive";
	else
		$mode = "view";
}

if ($REQUEST_METHOD == "POST") {
	$email = trim($newsemail);
	if (!cw_check_email($email)) {
		cw_add_top_message(cw_get_langvar_by_name("err_subscribe_email_invalid"),'E');
		cw_header_location("index.php?target=news");
	}

	$c = cw_query_first_cell("
SELECT COUNT(DISTINCT($tables[newslists].list_id))
FROM $tables[newslists], $tables[newslist_subscription]
WHERE
$tables[newslists].list_id=$tables[newslist_subscription].list_id AND
$tables[newslists].lngcode='$subscribe_lng' AND
$tables[newslist_subscription].email='$email'");
	if ($c > 0) {
		cw_add_top_message(cw_get_langvar_by_name("err_subscribed_already"),'W');
		cw_header_location("index.php?target=news");
	}

	if ($action == "subscribe") {
        if ($target_newslist_id) 
            $lists = cw_query("SELECT * FROM $tables[newslists] WHERE list_id='$target_newslist_id'"); 
        else
   		    $lists = cw_query("SELECT * FROM $tables[newslists] WHERE avail=1 AND subscribe=1 AND lngcode='$subscribe_lng'");

		if (!is_array($lists) || empty($lists)) {
			$top_message['type'] = "W";
			$top_message['content'] = cw_get_langvar_by_name('lbl_no_newslists_for_sbuscription');
			cw_header_location("index.php?target=news");
		}
	}
	if ($action == "subscribe" && !empty($lists) && is_array($lists)) {
		foreach ($lists as $list) {
			db_query("REPLACE INTO $tables[newslist_subscription] (list_id, email, since_date) VALUES ('$list[list_id]', '$email', '".time()."')");
		}

		$saved_lng = $current_language;

		#
		# Send mail notification to customer
		#
		$smarty->assign('email', $email);
		if($config['news']['eml_newsletter_subscribe'] == 'Y') {
			$current_language = $subscribe_lng;
			cw_call('cw_send_mail', array($config['news']['newsletter_email'], $email, "mail/newsletter_subscribe_subj.tpl", "mail/newsletter_subscribe.tpl"));
		}
		#
		# Send mail notification to admin
		#
		if($config['news']['eml_newsletter_subscribe_admin'] == 'Y') {
			$current_language = $config['default_admin_language'];
			cw_call('cw_send_mail', array($email, $config['news']['newsletter_email'], "mail/newsletter_admin_subj.tpl", "mail/newsletter_admin.tpl", $config['default_admin_language']));
		}

		$current_language = $saved_lng;

		cw_header_location("index.php?target=news&mode=subscribed&email=".urlencode(stripslashes($email)));
	}
}

if ($mode == 'unsubscribe') {
	$subscribe_lng = $current_language;
	$listid_cond = "";
	if (!empty($listid)) {
		$listid_cond = " AND list_id='$listid'";
		$subscribe_lng = cw_query_first_cell("SELECT lngcode FROM $tables[newslists] WHERE list_id='$listid'");
	}

	$c = cw_query_first_cell("SELECT COUNT(*) FROM $tables[newslist_subscription] WHERE email='$email'".$listid_cond);
	if ($c < 1) {
		cw_add_top_message('Email is not subscribed to newslist', 'W');
		cw_header_location("index.php?target=news");
	}

	db_query("DELETE FROM $tables[newslist_subscription] WHERE email='$email'".$listid_cond);

	$saved_lng = $current_language;

	#
	# Send mail notification to customer
	#
	$smarty->assign("email",$email);
	if($config['news']['eml_newsletter_unsubscr'] == 'Y') {
		$current_language = $subscribe_lng;
		cw_call('cw_send_mail', array($config["news"]["newsletter_email"], $email, "mail/newsletter_unsubscribe_subj.tpl", "mail/newsletter_unsubscribe.tpl"));
	}

	#
	# Send mail notification to admin
	#
	if($config['news']['eml_newsletter_unsubscr_admin'] == 'Y') {
		$current_language = $config['default_admin_language']; 
		cw_call('cw_send_mail', array($email, $config["news"]["newsletter_email"], "mail/newsltr_unsubscr_admin_subj.tpl", "mail/newsltr_unsubscr_admin.tpl"));
	}

	$current_language = $saved_lng;

	cw_header_location("index.php?target=news&mode=unsubscribed&email=".urlencode(stripslashes($email)));
}

if ($REQUEST_METHOD=="POST" && $action == "view") {
	$location[] = array(cw_get_langvar_by_name("lbl_news_subscribe_to_newslists"), "");
	$smarty->assign('main', 'news_lists');
	$smarty->assign('lists', $lists);
	$smarty->assign('newsemail', $email);
}
else {
	$location[] = array(cw_get_langvar_by_name('lbl_news_archive'), '');

	$smarty->assign('current_main_dir', 'addons');
	$smarty->assign('current_section_dir', 'news');
	$smarty->assign('main', 'news_archive');
	$smarty->assign('news_messages', cw_call('\cw\news\get_messages',array($user_account['membership_id'], $current_language)));
}
$smarty->assign('mode', $mode);
$smarty->assign('email', $email);
?>
