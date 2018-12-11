<?php
function smarty_function_select_time($params, &$smarty) {
	extract($params);

	if (empty($assign)) {
		$smarty->trigger_error("assign: missing 'assign' parameter");
		return;
	}

    $time_formats = array(
        "",
        "%H:%M:%S",
        "%H.%M.%S",
        "%I:%M:%S %p");

    $smarty->assign($assign, $time_formats);
}

?>
