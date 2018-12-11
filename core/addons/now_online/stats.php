<?php
if (!defined('APP_START')) die("Access denied");

$sesses = cw_query("SELECT sess_id, data FROM $tables[sessions_data] WHERE usertype IN ('C', 'R')");
$statistics = array();
if ($sesses) {
	foreach($sesses as $s) {
		$data = array();
		$rec = array("last_date" => $s['expiry']);
        $data = unserialize($data['data']);

		if (!empty($data['customer_id']) && !in_array($data['usertype'],  array('C', 'R')))
			continue;

		if (!empty($data['customer_id']))
			$rec['userinfo'] = cw_user_get_info($data['customer_id']);

		if (!empty($data['cart']['products']))
			$rec['products'] = $data['cart']['products'];

		$rec['current_date'] = $data['current_date'];
		$rec['current_url_page'] = $data['current_url_page'];
		if (strstr($data['current_url_page'], $https_location)) {
			$rec['display_url_page'] = str_replace($https_location, "...", $data['current_url_page']);
		}
		else {
			$rec['display_url_page'] = str_replace($http_location, "...", $data['current_url_page']);
		}

		$statistics[] = $rec;
	}
}

?>
