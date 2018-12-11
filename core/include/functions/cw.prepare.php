<?php
function cw_unset(&$array) {
    $keys = func_get_args();
    array_shift($keys);
    if (!empty($keys) && !empty($array) && is_array($array)) {
        foreach ($keys as $key) {
            if (@isset($array[$key]))
                unset($array[$key]);
        }
    }
}

function zerolen() {
    foreach (func_get_args() as $arg)
        if (strlen($arg) == 0) return true;

    return false;
}

function cw_array_map($func, $var) {
    if (!is_array($var)) return $var;

    foreach($var as $k=>$v)
        $var[$k] = call_user_func($func, $v);

    return $var;
}

function cw_array_map_hash($func, $var) {
    if (!is_array($var))
        return $var;

    $var_proc = array();
    foreach ($var as $k => $v) {
        $var_proc[call_user_func($func, $k)] = call_user_func($func, $v);
        unset($var[$k]);
    }

    return $var_proc;
}

function cw_array_merge() {
    $vars = func_get_args();

    $result = array();
    if (!is_array($vars) || empty($vars)) {
        return $result;
    }

    foreach($vars as $v) {
        if (is_array($v) && !empty($v)) {
            $result = array_merge($result, $v);
        }
    }

    return $result;
}

function cw_addslashes($var) {
    return is_array($var) ? cw_array_map_hash('cw_addslashes', $var) : addslashes($var);
}

function cw_addslashes_keys($var) {
    if (!is_array($var))
        return addslashes($var);

    $var_proc = array();
    foreach ($var as $k => $v) {
        unset($var[$k]);
        $var_proc[cw_addslashes_keys($k)] = $v;
    }

    return $var_proc;
}

function cw_stripslashes($var) {
    return is_array($var) ? cw_array_map_hash('cw_stripslashes', $var) : stripslashes($var);
}

function cw_array_key_exists($key, $search) {
    if (function_exists("array_key_exists")) {
        return array_key_exists($key, $search);

    } elseif (!isset($search[$key])) {
        foreach ($search as $k => $v) {
            if ($k === $key)
                return true;
        }

        return false;
    }

    return true;
}

function cw_strip_tags($var) {
    return is_array($var) ? cw_array_map_hash('cw_strip_tags', $var) : strip_tags($var);
}

function cw_have_script_tag($var) {
    if (!is_array($var)) {
        return (stristr($var, '<script') !== false);
    }
    foreach ($var as $item) {
        if (!is_array($var)) {
            if (stristr($var, '<script') !== false) return true;
        }
        elseif (cw_have_script_tag($item)) return true;
    }
    return false;
}

function cw_allowed_var($name) {
    global $reject;
    if (in_array($name,$reject) && !defined('ADMIN_UNALLOWED_VAR_FLAG')) {
        define('ADMIN_UNALLOWED_VAR_FLAG',1);
    }
    return !in_array($name,$reject);
}

function cw_stripslashes_sybase($data) {
    return is_array($data) ? cw_array_map_hash("cw_stripslashes_sybase", $data) : str_replace("''", "'", $data);
}

/*
$vars[$name] = array(
	'path' => $path,
	'mode' => 0777,
	'files' => array (
		'.htaccess' => array(
			'mode' => 0666,
			'content' => "Order Deny,Allow\n<Files \"*\">\n Deny from all\n</Files>\n"
		)
	),
	'criticality' => 0	// 0 - not criticality, 1 - criticality
);
*/
function on_build_var_dirs(&$vars) {
	global $app_dir;

	if (is_array($vars)) {

		unset($vars['var'], $vars['file'], $vars['clear_cache']);

		// Add var dirs data
		$var_dir = array();
		$var_dir['var'] = array(
			'path' => $app_dir . '/var',
			'mode' => 0777,
			'files' => array (
				'.htaccess' => array(
					'mode' => 0666,
					'content' => "Order Deny,Allow\nDeny from all"
				)
			),
			'criticality' => 1
		);
		$vars = $var_dir + $vars;

		// Add file dirs data
		$var_dir = array();
		$var_dir['file'] = array(
			'path' => $app_dir . '/files',
			'mode' => 0777,
			'files' => array (
				'.htaccess' => array(
					'mode' => 0666,
					'content' => "Deny from all"
				)
			),
			'criticality' => 1
		);
		$vars = $var_dir + $vars;

		// Add clear cache dirs data
		$var_dir = array();
		$var_dir['clear_cache'] = array(
			'path' => $app_dir . '/var/cache',
			'mode' => 0777,
			'files' => array (
				'.htaccess' => array(
					'mode' => 0666,
					'content' => "Order Deny,Allow\n<Files \"*\">\n Deny from all\n</Files>\n\n<FilesMatch \"\\.(css|js|png)$\">\n Allow from all\n</FilesMatch>\n"
				)
			),
			'criticality' => 1
		);
		$vars = $var_dir + $vars;

		foreach ($vars as $name => $path) {
			switch ($name) {
				case 'var' :
				case 'file' :
				case 'clear_cache' :
					break;
				case 'tmp' :
					$vars[$name] = array(
						'path' => $path,
						'mode' => 0777,
						'files' => array (
							'.htaccess' => array(
								'mode' => 0666,
								'content' => "Deny from all"
							)
						),
						'criticality' => 1
					);
					break;
				default :
					$vars[$name] = array(
						'path' => $path,
						'mode' => 0777,
						'files' => array (),
						'criticality' => 0
					);
					break;
			}
		} 
	}
}


function cw_array_merge_ext() {
	$vars = func_get_args();

	if (!is_array($vars) || empty($vars))
		return array();

	foreach($vars as $k => $v) {
		if (!is_array($v) || empty($v))
			unset($vars[$k]);
	}

	if (empty($vars))
		return array();

	$vars = array_values($vars);
	$orig = array_shift($vars);
	foreach ($vars as $var) {
		foreach ($var as $k => $v) {
			if (isset($orig[$k]) && is_array($orig[$k]) && is_array($v)) {
				$orig[$k] = cw_array_merge_ext($orig[$k], $v);
			}
			else {
				$orig[$k] = $v;
			}
		}
	}

	return $orig;
}

#
# Callback function: determination of empty field
#
function cw_func_callback_empty($value) {
	return strlen($value) > 0;
}

