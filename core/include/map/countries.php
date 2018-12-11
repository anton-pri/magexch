<?php
$zones[] = array("zone" => "ALL", "title" => cw_get_langvar_by_name("lbl_all_regions"));
$zones[] = array("zone" => "NA", "title" => cw_get_langvar_by_name("lbl_na"));
$zones[] = array("zone" => "EU", "title" => cw_get_langvar_by_name("lbl_eu"));
$zones[] = array("zone" => "AU", "title" => cw_get_langvar_by_name("lbl_au"));
$zones[] = array("zone" => "LA", "title" => cw_get_langvar_by_name("lbl_la"));
$zones[] = array("zone" => "SU", "title" => cw_get_langvar_by_name("lbl_su"));
$zones[] = array("zone" => "AS", "title" => cw_get_langvar_by_name("lbl_asia"));
$zones[] = array("zone" => "AF", "title" => cw_get_langvar_by_name("lbl_af"));
$zones[] = array("zone" => "AN", "title" => cw_get_langvar_by_name("lbl_an"));

$zone_is_valid = false;
if (!empty($zone)) {
	foreach ($zones as $k=>$v) {
		if ($zone == $v['zone']) {
			$zone_is_valid = true;
			break;
		}
	}
	if ($zone == "ALL")
		$zone = "";
}
else {
	if ($REQUEST_METHOD != "POST")
		$zone = cw_query_first_cell("SELECT region FROM $tables[map_countries] WHERE code='".$config['Company']['country']."'");
	else
		$zone = "ALL";
}

#
# Countries per page
#
$objects_per_page = 40;

if ($action == "deactivate_all") {
    db_query("UPDATE $tables[map_countries] SET active=0");
	$top_message['content'] = cw_get_langvar_by_name("msg_adm_countries_disabled");
}

if ($action == "activate_all") {
    db_query("UPDATE $tables[map_countries] SET active=1");
	$top_message['content'] = cw_get_langvar_by_name("msg_adm_countries_enabled");
}

if ($action == 'update' && is_array($posted_data)) {
    foreach ($posted_data as $k=>$v) {
            $to_update = array(
                'active' => $v['active'],
                'display_regions' => $v['display_regions'],
                'display_states' => $v['display_states'],
                'display_counties' => $v['display_counties'],
                'display_cities' => $v['display_cities'],
                'lang' => $v['lang'],
            );
            cw_array2update('map_countries', $to_update, "code='$k'");
            db_query("UPDATE $tables[languages] SET value = '$v[country]' WHERE name = 'country_$k' AND code = '$current_language'");
    }
    $top_message['content'] = cw_get_langvar_by_name("msg_adm_countries_upd");
       
    cw_header_location("index.php?target=countries&zone=$zone&page=$page");
}

if ($action)
    cw_header_location("index.php?target=countries&zone=$zone&page=$page");


$condition = "";
if (!empty($zone)) {
	if ($zone == "SU")
		$condition = " WHERE $tables[map_countries].code IN ('AM','AZ','BY','EE','GE','KZ','KG','LV','LT','MD','RU','TJ','TM','UA','UZ')";
	else
		$condition = " WHERE $tables[map_countries].region='$zone'";
}

$total_items_in_search = cw_query_first_cell("SELECT COUNT(*) FROM $tables[map_countries] $condition");

$navigation = cw_core_get_navigation($target, $total_items_in_search, $page);
$navigation['script'] = "index.php?target=countries&zone=".(empty($zone)?"ALL":$zone);
$smarty->assign('navigation', $navigation);

$countries = cw_query ("SELECT $tables[map_countries].*, IFNULL(lng1c.value, lng2c.value) as country, IFNULL(lng1l.value, lng2l.value) as language FROM $tables[map_countries] LEFT JOIN $tables[languages] as lng1c ON lng1c.name = CONCAT('country_', $tables[map_countries].code) AND lng1c.code = '$current_language' LEFT JOIN $tables[languages] as lng2c ON lng2c.name = CONCAT('country_', $tables[map_countries].code) AND lng2c.code = '$config[default_admin_language]' LEFT JOIN $tables[languages] as lng1l ON lng1l.name = CONCAT('language_', $tables[map_countries].code) AND lng1l.code = '$current_language' LEFT JOIN $tables[languages] as lng2l ON lng2l.name = CONCAT('language_', $tables[map_countries].code) AND lng2l.code = '$config[default_admin_language]' $condition ORDER BY country LIMIT $navigation[first_page], $navigation[objects_per_page]");

$smarty->assign('countries', $countries);
$smarty->assign('zones', $zones);
$smarty->assign('zone', $zone);
