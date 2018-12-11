<?php
cw_load('category','export','mail','user');

if ($action == 'delete') {
    $users_to_delete = &cw_session_register("users_to_delete", array());

    if ($confirmed == "Y") {
        if (is_array($users_to_delete['user'])) {
    		foreach ($users_to_delete['user'] as $user=>$v)
                cw_func_call('cw_user_delete', array('customer_id' => $user, 'send_mail' => true));
            $top_message = array('content' => cw_get_langvar_by_name('msg_adm_users_del'), 'type' => 'I');
        }
        else
            $top_message = array('content' => cw_get_langvar_by_name('msg_adm_warn_users_sel'), 'type' => 'W');
            
		cw_session_unregister('users_to_delete');
		cw_header_location('index.php?target='.$target.'&mode=search');
            
    }

    $users_to_delete['user'] = $user;
    $mode = 'delete';
	cw_header_location("index.php?target=$target&mode=delete");

}

if ($mode == 'delete') {
	$users_to_delete = &cw_session_register("users_to_delete", array());

	if (is_array($users_to_delete['user'])) {
        $users = array();
		foreach ($users_to_delete['user'] as $k=>$v)
			$users[] = cw_call('cw_user_get_info', array($k, 1));

		$smarty->assign('users', $users);
        $smarty->assign('current_section_dir', 'users');
		$smarty->assign('main', 'delete_confirmation');

	}
	else
		$top_message = array('content' => cw_get_langvar_by_name('msg_adm_warn_users_sel'), 'type' => 'W');
}

if (defined('IS_AJAX') && constant('IS_AJAX')) {
	cw_ajax_add_block(array(
		'id' => 'delete_confirm',
        'action' => 'popup',
		'template' => 'admin/users/delete_confirmation.tpl',
        'title' => cw_get_langvar_by_name('txt_delete_users_top_text'),
	));
}

?>
