<?php
function smarty_function_select_category($params, &$smarty) {
	extract($params);

	if (empty($assign)) {
		$smarty->trigger_error("assign: missing 'assign' parameter");
		return;
	}

    cw_load('category');
    $smarty->assign($assign, cw_func_call('cw_category_get', array('cat' => $category_id)));
}
?>
