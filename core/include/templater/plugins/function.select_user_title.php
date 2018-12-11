<?php
function smarty_function_select_user_title($params, &$smarty) {
	extract($params);

	if (empty($assign)) {
		$smarty->trigger_error("assign: missing 'assign' parameter");
		return;
	}

    cw_load('user');
    $smarty->assign($assign, cw_user_get_titles());
}
?>
