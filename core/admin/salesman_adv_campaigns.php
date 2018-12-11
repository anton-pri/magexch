<?php
# krnev, TOFIX
define("NUMBER_VARS", "add[per_visit],add[per_period]");

if (!$addons['Salesman'])
    cw_header_location('index.php');

if($action == 'add' && !empty($add['campaign']) && ($add['per_visit'] > 0 || $add['per_period'] > 0)) {
	if($StartDay)
		$add['start_period'] = mktime(0, 0, 0, $StartMonth, $StartDay, $StartYear);
	if($EndDay)
		$add['end_period'] = mktime(23, 59, 59, $EndMonth, $EndDay, $EndYear);
	if($campaign_id) {
		db_query("UPDATE $tables[salesman_adv_campaigns] SET campaign = '$add[campaign]', type = '$add[type]', data = '$add[data]', per_visit = '$add[per_visit]', per_period = '$add[per_period]', start_period = '$add[start_period]', end_period = '$add[end_period]' WHERE campaign_id = '$campaign_id'");
	} else {
		db_query("INSERT INTO $tables[salesman_adv_campaigns] (campaign, type, data, per_visit, per_period, start_period, end_period) VALUES ('$add[campaign]', '$add[type]', '$add[data]', '$add[per_visit]', '$add[per_period]', '$add[start_period]', '$add[end_period]')");
		if($add['type'] == 'L')
			cw_header_location("index.php?target=salesman_adv_campaigns&campaign_id=".db_insert_id());
	}
} 
elseif($action == 'delete' && $campaign_id) {
	db_query("DELETE FROM $tables[salesman_adv_campaigns] WHERE campaign_id = '$campaign_id'");
}

if($action)
	cw_header_location("index.php?target=salesman_adv_campaigns");

if($campaign_id) {
	$campaign = cw_query_first("SELECT * FROM $tables[salesman_adv_campaigns] WHERE campaign_id = '$campaign_id'");
	$smarty->assign('campaign', $campaign);
}

$campaigns = cw_query("SELECT * FROM $tables[salesman_adv_campaigns]");
$smarty->assign('campaigns', $campaigns);

$smarty->assign('main', 'adv_campaigns');

$smarty->assign ("month_begin", mktime(0,0,0,date('m'),1,date('Y')));
