<?php
cw_load('http');

#
# Get $merchant_password from command line
#
$merchant_password = '';

if (is_array($_SERVER['argv']) && !empty($_SERVER['argv'])) {
	foreach ($_SERVER['argv'] as $v) {
		if (preg_match("/merchant_password=(\S+)/S", $v, $preg)) {
			$merchant_password = $preg[1];
			break;
		}
	}
}

#
# Get $merchant_password from GET parametrs
#

if($_GET['merchant_password']) {
	$merchant_password = $_GET['merchant_password'];
}

#
# Get $merchant_password from hardcoded variables
#

if(!$merchant_password) {
	$merchant_password = "";
}

if($config['mpassword'] != md5($merchant_password) || !$merchant_password) {
	die(cw_get_langvar_by_name("err_mpassword_wrong"));
}

$res = cw_https_request("POST", $app_catalogs_secure['admin']."/recrypt.php", array("merchant_password=".$merchant_password));
die("Result: ".$res[1]);
?>
