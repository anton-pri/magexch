<?php
$adv_campaign_id = &cw_session_register('adv_campaign_id');

if ((empty($adv_campaign_id) && !empty($_COOKIE['adv_campaign_id']) && !empty($_COOKIE['adv_campaign_id_time']))) {
	if ($_COOKIE['adv_campaign_id_time'] >= cw_core_get_time()) {
		$adv_campaign_id = 'Y';
	} else {
		cw_set_cookie('adv_campaign_id', '', 0, '/', $app_config_file['web']['http_host'], 0);
		cw_set_cookie('adv_campaign_id_time', '', 0, '/', $app_config_file['web']['http_host'], 0);
	}
}

#
# For type 'G' (use GET parameter(s))
#
if ($REQUEST_METHOD == 'GET' && empty($adv_campaign_id)) {
	$gets = cw_query("SELECT campaign_id, data, type FROM $tables[salesman_adv_campaigns] WHERE type = 'G'");
	$_campaign_id = 0;
	if ($gets) {
		foreach ($gets as $v) {	
			$tmp = cw_parse_str($v['data']);
			if (!empty($tmp)) {
				$cnt = 0;
				foreach ($tmp as $key => $value) {
					if ($_GET[$key] == $value && isset($_GET[$key]))
						$cnt++;
				}

				if ($cnt == count($tmp)) {
					$QUERY_STRING = implode("&", array_diff(explode("&", $QUERY_STRING), explode("&", $v['data'])));
					$_campaign_id = $v['campaign_id'];
					$_type = $v['type'];
					break;
				}
			}
		}
	}
}

#
# For type 'R' (use HTTP referer parameter)
#
if ($HTTP_REFERER && $REQUEST_METHOD == 'GET' && !$_campaign_id && empty($adv_campaign_id)) {
	$refs = cw_query("SELECT campaign_id, data FROM $tables[salesman_adv_campaigns] WHERE type IN ('R','L')");
	if ($refs) {
		foreach ($refs as $v) {
			if ($HTTP_REFERER == $v['data']) {
				$_campaign_id = $v['campaign_id'];
				$_type = 'R';
				break;
			}
		}
	}
}

#
# Save campaign_id if not empty
#
if ($_campaign_id) {
	if ($_type != 'L')
		db_query("REPLACE INTO $tables[salesman_adv_clicks] VALUES ('$_campaign_id', '".time()."')");
	$adv_campaign_id = $_campaign_id;
	$salesman_cookie_length = ($config['Salesman']['salesman_cookie_length'] ? $config['Salesman']['salesman_cookie_length']*3600*24 : 0);

	if ($salesman_cookie_length) {
		$expiry = mktime(0,0,0,date("m"),date("d"),date("Y")+1);
		cw_set_cookie("adv_campaign_id", $adv_campaign_id, $expiry, "/", $app_http_host, 0);
		cw_set_cookie("adv_campaign_id_time", time()+$salesman_cookie_length, $expiry, "/", $app_http_host, 0);
	}
	cw_header_location(basename($PHP_SELF)."?".$QUERY_STRING);
}

?>
