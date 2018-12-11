<?php
function cw_session_start($sess_id = '') {
	global $APP_SESSION_VARS, $APP_SESS_ID;
	global $tables, $config;

	# $sess_id should contain only '0'..'9' or 'a'..'z' or 'A'..'Z'
	if (strlen($sess_id) > 32 || !empty($sess_id) && !preg_match('!^[0-9a-zA-Z]+$!S', $sess_id))
		$sess_id = '';

	$APP_SESSION_VARS = array();

	$l = 0;
	if (isset($_SERVER['REMOTE_PORT']))
		$l = $_SERVER['REMOTE_PORT'];

	list($usec, $sec) = explode(' ', microtime());
	srand((float) $sec + ((float) $usec * 1000000) + (float)$l);

    $curtime = cw_core_get_time();
    $expiry_time = $curtime + USE_SESSION_LENGTH;

	if ($sess_id) {
        $sess_data = cw_query_first("select * from $tables[sessions_data] where sess_id='$sess_id' and expiry>$curtime");
        if (!$sess_data) $sess_id = '';
	}

	if (empty($sess_id)) {
		do {
			$sess_id = md5(uniqid(rand()));
			$already_exists = false;
			$already_exists = cw_query_first_cell("select count(*) from $tables[sessions_data] where sess_id='$sess_id'") > 0;
		} while ($already_exists);
	}

	if ($sess_data)
		$APP_SESSION_VARS = unserialize($sess_data['data']);
    else {
		if (!defined("NEW_SESSION"))
			define("NEW_SESSION", true);

		db_query("REPLACE INTO $tables[sessions_data] (sess_id, start, expiry, data) VALUES('$sess_id', '$curtime', '$expiry_time', '')");
	}

	$APP_SESS_ID = $sess_id;

    global $app_config_file;
    cw_set_cookie(APP_SESSION_NAME, $APP_SESS_ID, 0, with_leading_slash_only($app_config_file['web']['web_dir'], true), $app_config_file['web']['http_host'], 0);
    if ($app_config_file['web']['http_host']!=$app_config_file['web']['https_host'])
           cw_set_cookie(APP_SESSION_NAME, $APP_SESS_ID, 0, with_leading_slash_only($app_config_file['web']['web_dir'], true), $app_config_file['web']['https_host'], 0);
}

function cw_session_read() {
    global $APP_SESS_ID;
    cw_session_id($APP_SESS_ID);
}

function cw_session_id($sess_id="") {
	global $tables, $APP_SESSION_VARS, $APP_SESS_ID, $APP_SESSION_UNPACKED_VARS;

	$APP_SESSION_VARS = array();
	if ($sess_id) {
		$sess_data = cw_query_first("SELECT * FROM $tables[sessions_data] WHERE sess_id='$sess_id'");
		$APP_SESS_ID = $sess_id;
		if ($sess_data) {
			$APP_SESSION_VARS = unserialize($sess_data['data']);
			if (!empty($APP_SESSION_UNPACKED_VARS)) {
				foreach ($APP_SESSION_UNPACKED_VARS as $var => $v) {
					if (isset($GLOBALS[$var]))
						unset($GLOBALS[$var]);

					unset($APP_SESSION_UNPACKED_VARS[$var]);
				}
			}
		}
		else {
			cw_session_start($sess_id);
		}
	}
	else {
		$sess_id = $APP_SESS_ID;
	}

	return $sess_id;
}

function cw_session_check_var($varname) {

	if (isset($_GET[$varname]) || isset($_POST[$varname]) || isset($_COOKIE[$varname]))
		return false;

	return true;
}

function &cw_session_register($varname, $default = '') {
	global $APP_SESSION_VARS, $APP_SESSION_UNPACKED_VARS;
	
	if (empty($varname))
		return false;

	if (!isset($APP_SESSION_VARS[$varname])) {
		if (isset($GLOBALS[$varname]) && cw_session_check_var($varname)) {
			$APP_SESSION_VARS[$varname] = $GLOBALS[$varname];
		}
		else {
			$APP_SESSION_VARS[$varname] = $default;
		}
	}
	else {
		if (isset($GLOBALS[$varname]) && cw_session_check_var($varname)) {
			$APP_SESSION_VARS[$varname] = $GLOBALS[$varname];
		}
	}

	$APP_SESSION_UNPACKED_VARS[$varname] = $APP_SESSION_VARS[$varname];
	$GLOBALS[$varname] = $APP_SESSION_VARS[$varname];

    return $GLOBALS[$varname];
}

function cw_session_save() {
	global $APP_SESS_ID;
	global $APP_SESSION_VARS, $APP_SESSION_UNPACKED_VARS;
	global $tables, $bench_max_session;
    global $customer_id, $config;

	$varnames = func_get_args();
	if (!empty($varnames)) {
		foreach ($varnames as $varname) {
			if (isset($GLOBALS[$varname]))
				$APP_SESSION_VARS[$varname] = $GLOBALS[$varname];
		}
	}
	elseif (is_array($APP_SESSION_UNPACKED_VARS)) {
		foreach ($APP_SESSION_UNPACKED_VARS as $varname=>$value) {
			if (isset($GLOBALS[$varname]))
				$APP_SESSION_VARS[$varname] = $GLOBALS[$varname];
		}
	}

    $curtime = cw_core_get_time();
    $expiry_time = $curtime + USE_SESSION_LENGTH;

    if (
        !defined('PREVENT_SESSION_SAVE')
    ) {
	    db_query("update $tables[sessions_data] set data='".addslashes(serialize($APP_SESSION_VARS))."', customer_id='".intval($customer_id)."', usertype='".($customer_id?AREA_TYPE:'C')."', ip='".$_SERVER['REMOTE_ADDR']."', expiry='$expiry_time' where sess_id='$APP_SESS_ID'");
    }

}

function cw_session_unregister($varname, $unset_global=false) {
	global $APP_SESSION_VARS, $APP_SESSION_UNPACKED_VARS;

	if (empty($varname))
		return false;

	cw_unset($APP_SESSION_VARS, $varname);
	cw_unset($APP_SESSION_UNPACKED_VARS, $varname);

	if ($unset_global) {
		cw_unset($GLOBALS, $varname);
	}
}

function cw_session_is_registered($varname) {
	global $APP_SESSION_VARS;

	if (empty($varname))
		return false;

	return isset($APP_SESSION_VARS[$varname]);
}

/*
function cw_session_change() {
	global $APP_SESS_ID, $tables;

	$sid = $APP_SESS_ID;
	cw_session_start();
	db_query("DELETE FROM $tables[sessions_data] WHERE sess_id = '$sid'");

	if (!defined("SESSION_ID_CHANGED"))
		define("SESSION_ID_CHANGED", true);

	return $APP_SESS_ID;
}
*/

function cw_set_cookie($name, $value = "", $expire = 0, $path = "", $domain = "", $secure = FALSE, $httponly = FALSE) {
    if ($_COOKIE['CW_Accept_Cookies'] == 'Y') {
        return setcookie ($name, $value, $expire, $path, $domain, $secure, $httponly);
    } else {
        return false;
    }
}
?>
