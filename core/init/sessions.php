<?php
if (defined('APP_SESSION_START')) return;

define("APP_SESSION_START", 1);

# PHP 4.3.0 and higher allow to turn off trans-sid using this command:
ini_set("url_rewriter.tags", '');
# Let's garbage collection will occurs more frequently
ini_set("session.gc_probability", 90);
ini_set("session.gc_divisor", 100); # for PHP >= 4.3.0
ini_set("session.use_cookies", false);

if (defined("SET_EXPIRE")) {
	header("Expires: ".gmdate("D, d M Y H:i:s", SET_EXPIRE)." GMT");
} else {
	header("Expires: ".gmdate("D, d M Y H:i:s")." GMT");
}
header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");

if (defined("SET_EXPIRE")) {
	header("Cache-Control: public");
}
elseif ($HTTPS) {
	header("Cache-Control: private, must-revalidate");
}
else {
	header("Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0");
	header("Pragma: no-cache");
}


if (isset($_POST[APP_SESSION_NAME]))
	$APP_SESS_ID = $_POST[APP_SESSION_NAME];
elseif (isset($_GET[APP_SESSION_NAME]))
	$APP_SESS_ID = $_GET[APP_SESSION_NAME];
elseif (isset($_COOKIE[APP_SESSION_NAME]))
	$APP_SESS_ID = $_COOKIE[APP_SESSION_NAME];
else
	$APP_SESS_ID = false;

cw_session_start($APP_SESS_ID);
register_shutdown_function("cw_session_save");
