<?php
if (ini_get("magic_quotes_sybase") && ini_get("magic_quotes_gpc"))
	define("CW_MAGIC_QUOTES_SYBASE", true);

//@set_magic_quotes_runtime(0);
ini_set('magic_quotes_sybase', 0);
ini_set('session.bug_compat_42', 1);
ini_set('session.bug_compat_warn', 0);

$__quotes_qpc = get_magic_quotes_gpc();

if (!defined('APP_EXT_ENV')) {

    global $reject;
    $reject = array_keys(get_defined_vars());

    if (isset($_COOKIE['is_robot']) && $_COOKIE['is_robot']) define('IS_ROBOT', 1);

# kornev, strip, check and make global
    foreach(array('_GET', '_POST', '_COOKIE', '_SERVER') as $__avar) {
        if (!$__quotes_qpc)
	    	$GLOBALS[$__avar] = cw_addslashes($GLOBALS[$__avar]);
        elseif (defined('CW_MAGIC_QUOTES_SYBASE')) {
	    	$GLOBALS[$__avar] = cw_stripslashes_sybase($GLOBALS[$__avar]);
		    $GLOBALS[$__avar] = cw_addslashes($GLOBALS[$__avar]);
    	}
        else
	    	$GLOBALS[$__avar] = cw_addslashes_keys($GLOBALS[$__avar]);

	    foreach ($GLOBALS[$__avar] as $__var => $__res) {
		    if (cw_allowed_var($__var)) {
                global $$__var;
                // Only admin scripts and "html_*" vars are trusted until addons init.
                // Add your html_* vars into $cw_trusted_variables list in addon init to allow HTML tags
                if (APP_AREA != 'admin' && strpos($__var,'html_')!==0) {
                    $__res = cw_strip_tags($__res);
                }
			    $GLOBALS[$__avar][$__var] = $$__var = $request_prepared[$__var] = $__res;
            }
    		else {
	    		cw_unset($GLOBALS[$__avar], $__var);
            }
        }

	    reset($GLOBALS[$__avar]);
    }

    foreach ($_FILES as $__name => $__value) {
    	if (!cw_allowed_var($__name)) continue;
	    $$__name = $__value['tmp_name'];
    	foreach($__value as $__k=>$__v) {
	    	$__varname_ = $__name."_".$__k;
		    if (!cw_allowed_var($__varname_)) continue;
    		$request_prepared[$__varname_] = $__v;
	    }
    }
    unset($reject, $__avar, $__var, $__res);
}

# OS detection
define('CW_IS_OS_WINDOWS', (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN'));

if (!defined('PATH_SEPARATOR')) {
	if (CW_IS_OS_WINDOWS)
		define('PATH_SEPARATOR', ';');
	else
		define('PATH_SEPARATOR', ':');
}

if (empty($REQUEST_URI))
	$REQUEST_URI = $PHP_SELF.(isset($QUERY_STRING)?"?$QUERY_STRING":"");

#
# HTTP_REFERER override
#
if(isset($_GET['iframe_referer']))
	$HTTP_REFERER = urldecode($_GET['iframe_referer']);

if (!empty($HTTP_REFERER) && strncasecmp($HTTP_REFERER,'http://', 7) && strncasecmp($HTTP_REFERER,'https://', 8)) {
	$HTTP_REFERER = "";
	if (!empty($_SERVER['HTTP_REFERER'])) {
		unset($_SERVER['HTTP_REFERER']);
	}
	if (!empty($_GET['iframe_referer'])) {
		unset($_GET['iframe_referer']);
	}
}

#
# Proxy IP
#
global $CLIENT_IP, $PROXY_IP;

$PROXY_IP = '';
if (!empty($HTTP_X_FORWARDED_FOR)) {
	$PROXY_IP = $HTTP_X_FORWARDED_FOR;
} elseif (!empty($HTTP_X_FORWARDED)) {
	$PROXY_IP = $HTTP_X_FORWARDED;
} elseif (!empty($HTTP_FORWARDED_FOR)) {
	$PROXY_IP = $HTTP_FORWARDED_FOR;
} elseif (!empty($HTTP_FORWARDED)) {
	$PROXY_IP = $HTTP_FORWARDED;
} elseif (!empty($HTTP_CLIENT_IP)) {
	$PROXY_IP = $HTTP_CLIENT_IP;
} elseif (!empty($HTTP_X_COMING_FROM)) {
	$PROXY_IP = $HTTP_X_COMING_FROM;
} elseif (!empty($HTTP_COMING_FROM)) {
	$PROXY_IP = $HTTP_COMING_FROM;
}

if(!empty($PROXY_IP)) {
	$CLIENT_IP = $PROXY_IP;
	$PROXY_IP = $REMOTE_ADDR;
}
elseif(isset($REMOTE_ADDR))
    $CLIENT_IP = $REMOTE_ADDR;

if(isset($_GET['benchmark']) || isset($_POST['benchmark'])) {
	define("START_TIME", cw_core_microtime());
}

/**
 * Service server for updates, news, adv
 */
define('SERVICE_SERVER', base64_decode('d3d3LmNhcnR3b3Jrcy5jb20='));
define('SERVICE_SERVER_SCRIPT','/index.php?target=api');


#
# Allow displaying content in functions, registered in register_shutdown_function()
#
$zlib_oc = ini_get("zlib.output_compression");
if (!empty($zlib_oc) || function_exists('fastcgi_finish_request'))
	define("NO_RSFUNCTION", true);

unset($zlib_oc);

// Shutdown function to call all delayed functions which were registered via cw_call_delayed()
register_shutdown_function('cw_exec_delayed'); // Must be one of the first shutdown functions
