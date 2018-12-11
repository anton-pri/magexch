<?php
function smarty_function_select_webmaster_image($params, &$smarty) {
	extract($params);

	if (empty($assign)) {
		$smarty->trigger_error("assign: missing 'assign' parameter");
		return;
	}

    if (empty($image)) {
        $smarty->trigger_error("assign: missing 'image' parameter");
        return;
    }

    cw_load('in_images');
    $smarty->assign($assign, cw_in_images_assign($image));
}
?>
