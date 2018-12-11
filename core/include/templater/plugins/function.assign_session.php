<?php
function smarty_function_assign_session($params, &$smarty) {
	extract($params);

	if (empty($assign)) {
		$smarty->trigger_error("assign: missing 'assign' parameter");
		return;
	}

    global $$var;
    $var = cw_session_register($var, array());
    $smarty->assign($assign, $var);
}
?>
