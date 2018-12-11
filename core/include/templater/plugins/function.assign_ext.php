<?php
# Templater plugin
# -------------------------------------------------------------
# Type:     function
# Name:     assign_ext
# Purpose:  assign variable as array to the inner array
# -------------------------------------------------------------
#

function smarty_function_assign_ext($params, &$smarty) {
	extract($params);

	if (empty($var)) {
		$smarty->trigger_error("assign: missing 'var' parameter");
		return;
	}

	if (!in_array('value', array_keys($params))) {
		$smarty->trigger_error("assign: missing 'value' parameter");
		return;
	}

	if (preg_match("/^([^\[]+)\[([^\]]*)\]$/S", $var, $preg)) {
		if (empty($preg[2])) {
			$smarty->_tpl_vars[$preg[1]][] = $value;
		} else {
			$smarty->_tpl_vars[$preg[1]][$preg[2]] = $value;
		}
	} else {
		$smarty->assign($var, $value);
	}
}

?>
