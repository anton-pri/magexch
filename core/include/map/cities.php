<?php
if ($action == "delete" && is_array($selected)) {
    foreach ($selected as $city_id=>$v)
        cw_map_delete_city($city_id);
    $top_message['content'] = cw_get_langvar_by_name('msg_adm_cities_del');
    cw_header_location("index.php?target=$target&mode=cities&country=$country".(!empty($page)?"&page=$page":""));
}

if ($action == "update" && is_array($posted_data)) {
    foreach ($posted_data as $city_id=>$v) {
        if (empty($v['state_id']) || empty($v['city'])) continue;
        $to_update = array(
            'state_id' => $v['state_id'],
            'county_id' => $v['county_id'],
            'city' => $v['city'],
            'country_code' => $country,
        );
        if ($city_id) $to_update['city_id'] = $city_id;
        cw_array2insert('map_cities', $to_update, true);
    }
    $top_message['content'] = cw_get_langvar_by_name('msg_adm_cities_upd');
    cw_header_location("index.php?target=$target&mode=cities&country=$country".(!empty($page)?"&page=$page":""));
}

$total_items_in_search = cw_query_first_cell("select count(*) from $tables[map_cities] where country_code='$country'");

if ($total_items_in_search > 0) {
    $navigation = cw_core_get_navigation($target, $total_items_in_search, $page);
    $navigation['script'] = "index.php?target=$target&mode=cities&country=$country";
    $smarty->assign('navigation', $navigation);

	$cities = cw_query("select * from $tables[map_cities] where country_code='$country' order by city limit $navigation[first_page], $navigation[objects_per_page]");
	$smarty->assign('cities', $cities);
}

$smarty->assign('country', $country);
$smarty->assign('country_info', cw_map_get_country($country));
?>
