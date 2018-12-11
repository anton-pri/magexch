<?php
cw_load('files');

$https_messages = array(array("target=order-message","doc_ids="), "target=error_message");
$https_scripts = array();

if (empty($REQUEST_URI) || substr($REQUEST_URI, -1) == '/')
	$_SERVER['REQUEST_URI'] = $REQUEST_URI = $PHP_SELF.($QUERY_STRING ? ("?".$QUERY_STRING) : "");

$https_scripts[] = 'secure_login';
if ($config['Security']['use_https_login'] == "Y") {
	$https_scripts[] = 'register';
	$https_scripts[] = array('cart', 'mode=checkout');
	$https_scripts[] = array('cart', 'mode=auth');
}

if ($config['Security']['use_secure_login_page'] == 'Y')
	$https_scripts[] = array('error_message', 'need_login');

function is_https_link($target, $link, $https_scripts) {
	if (empty($https_scripts))
		return false;

	$link = preg_replace('!^/+!S','', $link);

	foreach ($https_scripts as $https_script) {
		if (!is_array($https_script))
			$https_script = array($https_script);

		$tmp = true;
		foreach ($https_script as $v) {
			$p = strpos($link, $v);
			if ($p === false) {
				$tmp = false;
				break;
			}

			if ($v[strlen($v)-1] === '=') continue;

			if ($p + strlen($v) < strlen($link)) {
				$last = $link[$p+strlen($v)];
				if ($last === '?' && $p == 0) continue;

				if ($last !== '&') {
					$tmp = false;
					break;
				}
			}
		}

		if ($tmp) return true;
	}

	return false;
}

$_location = parse_url($current_location.$app_dirs['customer']);
$_location['path'] = cw_normalize_path($_location['path'],'/');
$current_script = substr(cw_normalize_path($REQUEST_URI,'/'), strlen($_location['path']));

$additional_query = ($QUERY_STRING ? "&" : "?")
    . (
        strstr($QUERY_STRING, APP_SESSION_NAME)
        ? ''
        : APP_SESSION_NAME . "=" . $APP_SESS_ID
    );

if (!preg_match("/(?:^|&)sl=/", $additional_query) && $app_http_host != $app_https_host)
	$additional_query .= ($additional_query?'&':'?')."sl=".$current_language."&is_https_redirect=Y";

if ($REQUEST_METHOD=="GET" && empty($_GET['keep_https'])) {
	$tmp_location = "";
	if (!$HTTPS && is_https_link($target, $current_script, $https_scripts)) {
		$tmp_location = $app_catalogs_secure['customer'].$current_script.$additional_query;
	}
	elseif (!$HTTPS && is_https_link($target, $current_script, $https_messages) && !strncasecmp($HTTP_REFERER, $https_location, strlen($https_location))) {
		$tmp_location = $app_catalogs_secure['customer'].$current_script.$additional_query;
	}
	elseif ($config['Security']['dont_leave_https'] != 'Y' && $HTTPS && !is_https_link($target, $current_script, $https_scripts) && !is_https_link($target, $current_script, $https_messages)) {
		$login_redirect = &cw_session_register("login_redirect");
		$do_redirect = empty($login_redirect);
		cw_session_unregister("login_redirect");
		if ($do_redirect)
			$tmp_location = $http_location.$app_dirs['customer'].$current_script.$additional_query;
	}

	if (!empty($tmp_location))
	    cw_header_location($tmp_location);
}

?>
