<?php
$salesman = &cw_session_register ("salesman");
$salesman_click_id = &cw_session_register ("salesman_click_id");

$salesman_saleman = &cw_session_register ("salesman_saleman");
$salesman_membership = &cw_session_register ("salesman_membership");
if ($_GET['saleman']) $salesman_saleman = $saleman;
if ($_GET['level'] && $_GET['saleman']) $salesman_membership = $level;

$_tmp_current_host = $app_http_host;
$_tmp = parse_url($current_location);
if (!empty($_tmp['host']))
	$_tmp_current_host = $_tmp['host'];

if (empty($salesman) && (!empty($_GET['salesman']) || !empty($_POST['salesman_customer_id']))) {
	#
	# Assign current salesman value
	#
	if (isset($_POST['salesman_customer_id']) && (!empty($_POST['salesman_customer_id']))) {
		$salesman = $_POST['salesman_customer_id'];
	} else {
		$salesman = $_GET['salesman'];
	}
	#
	# Users has clicked onto banner
	#
	db_query ("INSERT INTO $tables[salesman_clicks] (customer_id, add_date, class, product_id, banner_id, referer) VALUES ('$salesman', '".time()."', '$cl', '$product_id', '$bid', '$HTTP_REFERER')");
	$salesman_click_id = db_insert_id();
	#
	# Set cookies
	#
	$salesman_cookie_length = ($config['Salesman']['salesman_cookie_length'] ? $config['Salesman']['salesman_cookie_length']*3600*24 : 0);

	if ($salesman_cookie_length) {
		$expiry = mktime(0,0,0,date("m"),date("d"),date("Y")+1);
		cw_set_cookie("salesman_click_id", $salesman_click_id, $expiry, "/", $_tmp_current_host, 0);
		cw_set_cookie("salesman", $salesman, $expiry, "/", $_tmp_current_host, 0);
		cw_set_cookie("salesman_time", time()+$salesman_cookie_length, $expiry, "/", $_tmp_current_host, 0);
	}
}
elseif (empty($salesman) && !empty($_COOKIE['salesman']) && !empty($_COOKIE['salesman_time'])) {
	if ($_COOKIE['salesman_time'] >= time()) {
		#
		# Assign current salesman value
		#
		$salesman = $_COOKIE['salesman'];
		$salesman_click_id = $_COOKIE['salesman_click_id'];
	} else {
		#
		# Remove cookies if $salesman_cookie_length is expired
		#
		cw_set_cookie("salesman", "", 0, "/", $_tmp_current_host, 0);
		cw_set_cookie("salesman_click_id", "", 0, "/", $_tmp_current_host, 0);
		cw_set_cookie("salesman_time", "", 0, "/", $_tmp_current_host, 0);
	}
}
$smarty->assign('salesman', $salesman);
$smarty->assign('salesman_saleman', $salesman_saleman);
?>
