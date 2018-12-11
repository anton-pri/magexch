<?php
function smarty_function_select_manufacturer_menu($params, &$smarty) {
	extract($params);

	if (empty($assign)) {
		$smarty->trigger_error("assign: missing 'assign' parameter");
		return;
	}
    $smarty->assign($assign, cw_manufacturer_get_menu($params['is_image']));
}
?>
