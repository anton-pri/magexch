<?php
function smarty_function_select_date($params, &$smarty) {
	extract($params);

	if (empty($assign)) {
		$smarty->trigger_error("assign: missing 'assign' parameter");
		return;
	}

    $date_formats = array(
        "%d-%m-%Y",
        "%d/%m/%Y",
        "%d.%m.%Y",
        "%m-%d-%Y",
        "%m/%d/%Y",
        "%Y-%m-%d",
        "%b %e, %Y",
        "%A, %B %e, %Y");

    $smarty->assign($assign, $date_formats);
}
?>
