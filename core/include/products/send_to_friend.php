<?php
$top_message = &cw_session_register('top_message', array());

if (!$product_id)
	cw_header_location("index.php?target=error_message&error=access_denied&id=48");

$antibot_err = &cw_session_register("antibot_err");
$antibot_page = "on_send_to_friend";
if (!empty($addons['image_verification']) && $show_antibot_arr[$antibot_page] == 'Y') {
	if (isset($antibot_input_str) && !empty($antibot_input_str))
		$antibot_err = cw_validate_image($antibot_validation_val[$antibot_page], $antibot_input_str);
	else
		$antibot_err = true;

    if (defined('IS_AJAX') && constant('IS_AJAX') && $spambot_ajax_check == 'Y') {
        $antibot_validation_val[$antibot_page]['used'] = 'N';
        $smarty->assign('antibot_err', $antibot_err?'Y':'N');
        cw_ajax_add_block(array(
            'id' => 'spambot_ajax_check_res',
            'template' => 'addons/image_verification/spambot_ajax_check.tpl',
        ));
    }
}
$send_to_friend_info = &cw_session_register("send_to_friend_info");
if ($action == 'send') {
	if ($email && $from && $name && !$antibot_err) {
		cw_load('mail');

		$smarty->assign ("product", $product_info);
		$smarty->assign ("name", $name);
        $smarty->assign ("message", $from);
		cw_call('cw_send_mail', array($config['Company']['support_department'], $email, "mail/wishlist/send2friend_subj.tpl", "mail/wishlist/send2friend.tpl"));
        cw_add_top_message(cw_get_langvar_by_name('txt_send2friend_send'));
        $send_to_friend_info['fill_err'] = $send_to_friend_info['antibot_err'] = false;
	}
	else {
		$top_message['content'] = cw_get_langvar_by_name("err_filling_form");
		if ($antibot_err)
			$top_message['content'] .= "<br />".cw_get_langvar_by_name("msg_err_antibot");
		$top_message['type'] = "E";
		$send_to_friend_info['name'] = $name;
		$send_to_friend_info['email'] = $email;
		$send_to_friend_info['from'] = $from;
		$send_to_friend_info['antibot_err'] = $antibot_err;
		$send_to_friend_info['fill_err'] = true;
	}

	cw_header_location(cw_call('cw_core_get_html_page_url', array(array('var' => 'product', 'product_id'=>$product_id))));
}
if ($mode == 'sends') {
    $top_message['content'] = cw_get_langvar_by_name("txt_recommendation_sent");
}
$smarty->assign('mode', $mode);
$smarty->assign('send_to_friend_info', $send_to_friend_info);
