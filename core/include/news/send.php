<?php
cw_load('files','mail');

function cw_spam($message, $recipients, $send_language, $list_id) {

	global $config, $smarty;
	global $current_language, $tables;
	global $app_main_dir;

	$saved_language = $current_language;

	$current_language = $send_language;

	$email_spec = "###EMAIL###";

	$smarty->assign('email', $email_spec);
	$smarty->assign('list_id', $list_id);

	$signature_template = "mail/newsletter_signature.tpl";
	$sign_delim = "\n\n";

    $message['headers'] = array("Content-Type"=>"text/html");
	$sign_delim = "<br /><br />";

	$signature = cw_display($signature_template,$smarty,false);

    $extra = array("Content-Type" => "text/html");
	foreach($recipients as $recipient) {
	    cw_send_simple_mail($config['news']['newsletter_email'], $recipient, $message['subject'], $message['body'].$sign_delim.preg_replace("/$email_spec/S", $recipient, $signature), $extra);
    }

	$current_language = $saved_language;
}

$do_not_update_status = false;

$recipients = array();

$limit = '';

if (is_array($message)) {
	$message = cw_array_map('stripslashes',$message);

	foreach (array("email1", "email2", "email3") as $f) {
		if (!empty($message[$f]))
			$recipients[] = $message[$f];
	}
	$do_not_update_status = true;

	$list_lng = cw_query_first_cell("SELECT lngcode FROM $tables[newslists] WHERE list_id='$list_id'");
}
else {
	$list = cw_query_first("SELECT * FROM $tables[newslists] WHERE list_id='$list_id'");
	if (empty($list)) return;

	$list_lng = $list['lngcode'];

    $fields = array();
    $from_tbls = array();
    $query_joins = array();
    $where = array();
    $groupbys = array();
    $having = array();
    $orderbys = array();

    $fields[] = 'distinct(c.email)';

    $from_tbls['c'] = 'customers';

    $groupbys[] = 'c.customer_id';

    $where[] = 'c.usertype = "'.$list['usertype'].'"';
 
    // send notifications by memberships, not a customers
    $mems = cw_query_column("select membership_id from $tables[newslists_memberships] where list_id='$list[list_id]'");
    if (count($mems)) {
        $where[] = 'c.membership_id in ('.implode(', ', $mems).')';
    }

    $countries = cw_query_column("select code from $tables[newsletter_countries] where news_id='$messageid'");
    if (count($countries)) {
        $from_tbls['ca'] = 'customers_addresses';
        $where[] = 'ca.customer_id=c.customer_id';
        $where[] = "ca.country in ('".implode("', '", $countries)."')";
    }

	if ($config['news']['news_emails_per_pass'] > 0) {
		$news_send_data = &cw_session_register('news_send_data');

		if ($action == 'send') {
            $count_query = cw_db_generate_query(array('count(*)'), $from_tbls, $query_joins, $where, $groupbys, $having, array());
            $subscribers_count = cw_query_first_cell($count_query);
			if (!$subscribers_count) return;

			$news_send_data[$messageid] = array (
				'count' => $subscribers_count,
				'lastpos' => 0
			);

			echo cw_get_langvar_by_name("lbl_news_sending_messages", array ("count" => $news_send_data[$messageid]['count']), false, true);
		}
		else {
			echo cw_get_langvar_by_name("lbl_news_continue_sending_messages", array ("last" => $news_send_data[$messageid]['lastpos'], "count" => $news_send_data[$messageid]['count']), false, true);
		}
		cw_flush();

		$limit = sprintf(" LIMIT %d,%d", $news_send_data[$messageid]['lastpos'], $config['news']['news_emails_per_pass']);
	}

    $user_search_query = cw_db_generate_query($fields, $from_tbls, $query_joins, $where, $groupbys, $having, $orderbys);

    $tmp = cw_query($user_search_query.$limit);

	if (is_array($tmp))
	foreach ($tmp as $v) {
		if (!empty($v['email']))
			$recipients[] = $v['email'];
	}
	$message = cw_query_first("SELECT * FROM $tables[newsletter] WHERE news_id='$messageid'");

    if (empty($news_send_data[$messageid]['lastpos'])) {
# kornev, simple subscribers on the first step
        $tmp = cw_query("SELECT email FROM $tables[newslist_subscription] WHERE list_id='$list_id' ORDER BY email");
        if (is_array($tmp))
        foreach ($tmp as $v)
            if (!empty($v['email']))
                $recipients[] = $v['email'];
    }
}

$recipients = array_unique($recipients);

if (count($recipients)>0) {
    
	cw_call('cw_spam', array($message, $recipients, $list_lng, $list_id));

	if (!$do_not_update_status)
		db_query("UPDATE $tables[newsletter] SET status = 'S', send_date = '".time()."' WHERE news_id = '$message[news_id]'");
}

if (!empty($limit) && count($recipients) > 0) {
	$news_send_data[$messageid]['lastpos'] += count($recipients);

	if ($news_send_data[$messageid]['lastpos'] >= $news_send_data[$messageid]['count']) {
		cw_unset($news_send_data, $messageid);
		return;
	}

	cw_html_location("index.php?target=$target&mode=messages&list_id=$list_id&messageid=$messageid&action=send_continue", $config['news']['news_sleep_interval']);
}

?>
