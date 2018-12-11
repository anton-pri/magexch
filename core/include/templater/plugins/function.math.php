<?php

function smarty_function_math($params, &$smarty) {
	static $reserved_params = array ('assign', 'equation', 'format');
	static $allowed_funcs = array (
		'ceil','floor','round',
		'int','float','base_convert',
		'abs','max','min','pi','rand','lcg_value',
		'cos','sin','tan','acos','asin','atan',
		'log','log10','exp','pow','sqrt');

	$error_prefix = 'math ``<b>'.htmlspecialchars($params['equation']).'</b>\'\' in ``'.$smarty->current_resource_name.'\'\': ';

	if (!isset($params['equation'])) {
		$smarty->trigger_error($error_prefix.'missing equation');
		return;
	}

	$equation = $params['equation'];
	$result = null;
	if (empty($equation)) {
		$result = $equation;
	}
	else {
		if (substr_count($equation,"(") != substr_count($equation,")")) {
			$smarty->trigger_error($error_prefix.'unbalanced parenthesis');
			return;
		}

		# match all vars in equation, make sure all are passed
		preg_match_all("!(?:0x[a-fA-F0-9]+)|([a-zA-Z][a-zA-Z0-9_]+)!S",$equation, $match);
    
		foreach($match[1] as $curr_var) {
			if ($curr_var && !in_array($curr_var, array_keys($params)) && !in_array($curr_var, $allowed_funcs)) {
				$smarty->trigger_error($error_prefix."function call $curr_var is not allowed");
				return;
			}
		}

		$keys_empty = array();
		$keys_not_numeric = array();
		$error = false;

		# substitute parameters in equation
		foreach($params as $key => $val) {
			if (in_array($key, $reserved_params)) continue;

			if (strlen($val)==0) {
				$keys_empty[] = $key;
				$error = true;
				continue;
			}
			if (!is_numeric($val)) {
				$keys_not_numeric[] = $key;
				$error = true;
				continue;
			}

			if (!$error) {
				$equation = preg_replace("!\b$key\b!S",$val, $equation);
			}
		}

		if ($error) {
			$err_arr = array();
			$err_def = array (
				'parameter%s ``<b>%s</b>\'\' %s empty' => $keys_empty,
				'parameter%s ``<b>%s</b>\'\' %s not numeric' => $keys_not_numeric
			);
			foreach ($err_def as $fmt => $keys_arr) {
				$cnt = count($keys_arr);
				if ($cnt < 1) continue;
				$err_arr[] = sprintf( $fmt,
					($cnt>1?'s':''),
					implode('</b>\'\', ``<b>', $keys_arr),
					($cnt>1?'are':'is')
				);
			}

			$smarty->trigger_error($error_prefix.implode('; ', $err_arr));
			return;
		}

		@eval("\$result = ".$equation.";");
	}

	if (!empty($params['format']))
		$result = sprintf($params['format'], $result);

	if (!empty($params['assign'])) {
		$smarty->assign($params['assign'], $result);
		return '';
	}

	return $result;
}

?>
