<?php
function smarty_function_select_config($params, &$smarty) {
	extract($params);

	if (empty($assign)) {
		$smarty->trigger_error("assign: missing 'assign' parameter");
		return;
	}

    if (empty($category)) {
        $smarty->trigger_error("assign: missing 'category' parameter");
        return;
    }

    cw_load('config');
    $smarty->assign($assign, cw_config_get_category($category));
}
?>
