<?php
function smarty_function_lng($params, &$smarty) {
	extract($params);

	if (empty($name)) {
		$smarty->trigger_error("lng: missing 'name' parameter");
		return;
	}
    
    if ($assign)
        $smarty->assign($assign, cw_get_langvar_by_name($name));
    else 
        return cw_get_langvar_by_name($name);
}
?>
