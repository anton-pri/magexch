<?php
// This controller handles case when user has marked checkbox in profile that forces password change
cw_load('crypt', 'user');

global $customer_id;
global $top_message;

if (empty($customer_id)) cw_header_location('index.php'); // TODO: better to show access error than silent redirect. Need way to easy control pages that needs logged in customer

if ($REQUEST_METHOD=="POST") {
	$userinfo = cw_user_get_info($customer_id);
	$smarty->assign('old_password', $old_password);
	$smarty->assign('new_password', $new_password);
	$smarty->assign('confirm_password', $confirm_password);

	if (!cw_call('cw_user_is_right_password', array($old_password, $userinfo['password']))) {
		$top_message['content'] = cw_get_langvar_by_name("txt_chpass_wrong");
		$top_message['type'] = 'E';
	}
	elseif ($new_password != $confirm_password) {
		$top_message['content'] = cw_get_langvar_by_name("txt_chpass_match");
		$top_message['type'] = 'E';
	}
	elseif (cw_call('cw_user_is_right_password',array($new_password, $userinfo['password'])) || empty($new_password)) {
		$top_message['content'] = cw_get_langvar_by_name("txt_chpass_another");
		$top_message['type'] = 'E';
	}
	else {
		$count = cw_query_first_cell("SELECT COUNT(*) FROM $tables[old_passwords] WHERE customer_id='".intval($customer_id)."' AND password='".addslashes(md5($new_password))."'");
		if ($count > 0) {
			$top_message['content'] = cw_get_langvar_by_name("txt_chpass_another");
			$top_message['type'] = 'E';
		}
		else {
			$count = cw_query_first_cell("SELECT COUNT(*) FROM $tables[old_passwords] WHERE customer_id='".intval($customer_id)."' AND password='".addslashes(md5($old_password))."'");
			if ($count<1)
				db_query("INSERT INTO $tables[old_passwords] (customer_id,password) VALUES ('".addslashes($customer_id)."','".addslashes(md5($old_password))."')");

			db_query("UPDATE $tables[customers] SET password='".cw_call('cw_user_get_hashed_password', array($new_password))."', change_password='N' WHERE customer_id='".addslashes($customer_id)."'");

			$top_message['content'] = cw_get_langvar_by_name("txt_chpass_changed");
			cw_header_location('index.php');
		}
	}

	cw_header_location("index.php?target=change_password");
}

$location[] = array(cw_get_langvar_by_name("lbl_chpass"), "");

?>
