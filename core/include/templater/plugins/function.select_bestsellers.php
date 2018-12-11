<?php
function smarty_function_select_bestsellers($params, &$smarty) {
	extract($params);

	if (empty($assign)) {
		$smarty->trigger_error("assign: missing 'assign' parameter");
		return;
	}

    cw_load('bestseller');
    $smarty->assign($assign, cw_bestseller_get_menu($category_id));
}
?>
