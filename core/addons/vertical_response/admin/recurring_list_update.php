<?php
$top_message = &cw_session_register('top_message');

if ($action == 'add') {
	if (!empty($vr_data['email_list']) && !empty($vr_data['saved_search'])) {
		$result = cw_query_first_cell("
			SELECT id FROM $tables[recurring_list_update]
			WHERE list_id = '" . $vr_data['email_list'] . "' AND saved_search_id = '" . $vr_data['saved_search'] . "'
		");

		if ($result) {
			$top_message = array(
				'content' => cw_get_langvar_by_name("err_profile_already_exists", null, false, true),
				'type' => 'E'
			);
		} else {
			cw_array2insert(
				"recurring_list_update",
				array(
					'list_id' => $vr_data['email_list'],
					'list_name' => $vr_data['email_list_name'],
					'saved_search_id' => $vr_data['saved_search'],
					'active' => 1
				)
			);
			$top_message = array(
				'content' => cw_get_langvar_by_name("txt_recurring_list_profile_added", null, false, true),
				'type' => 'I'
			);
		}
	} else {
		$top_message = array(
			'content' => cw_get_langvar_by_name("err_filling_form", null, false, true),
			'type' => 'E'
		);
	}
	cw_header_location('index.php?target=recurring_vr_list_update');
}

if ($action == 'update' && !empty($active)) {
	db_query("UPDATE $tables[recurring_list_update] SET active = 0");
	foreach ($active as $profile_id => $v) {
		db_query("UPDATE $tables[recurring_list_update] SET active = 1 WHERE id = '$profile_id'");
	}
	$top_message = array(
		'content' => cw_get_langvar_by_name("txt_recurring_list_profile_updated", null, false, true),
		'type' => 'I'
	);
	cw_header_location('index.php?target=recurring_vr_list_update');
}

if ($action == 'delete' && !empty($to_delete)) {
	foreach ($to_delete as $profile_id => $v) {
		cw_vertical_response_profile_delete($profile_id);
	}
	$top_message = array(
		'content' => cw_get_langvar_by_name("txt_selected_recurring_list_profiles_deleted", null, false, true),
		'type' => 'I'
	);
	cw_header_location('index.php?target=recurring_vr_list_update');
}

$smarty->assign('recurring_list', cw_vertical_response_get_recurring_list_profiles());
$smarty->assign('email_lists', cw_vertical_response_get_lists());
$smarty->assign('saved_searches', cw_vertical_response_get_saved_search());
$smarty->assign('current_target', 'recurring_vr_list_update');
$smarty->assign('main', 'recurring_vr_list_update');
