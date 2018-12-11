<?php
function smarty_function_select_country($params, &$smarty) {
	extract($params);

	if (empty($assign)) {
		$smarty->trigger_error("assign: missing 'assign' parameter");
		return;
	}

    cw_load('map');
    $smarty->assign($assign, cw_call('cw_map_get_countries'));
}

?>
