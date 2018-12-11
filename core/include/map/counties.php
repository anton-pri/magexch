<?php
if ($action == "delete" && is_array($selected)) {
    foreach ($selected as $county_id=>$v)
        cw_map_delete_county($county_id);
    $top_message['content'] = cw_get_langvar_by_name("msg_adm_counties_del");
}

if ($action == "update" && is_array($posted_data)) {
    foreach ($posted_data as $county_id=>$v) {
        if (empty($v['state_id']) || empty($v['county'])) continue;
        $to_update = array(
            'county' => $v['county'],
            'state_id' => $v['state_id'],
            'country_code' => $country,
        );
        if ($county_id) $to_update['county_id'] = $county_id;
        cw_array2insert('map_counties', $to_update, true);
    }

    $top_message['content'] = cw_get_langvar_by_name('msg_adm_counties_upd');
}

if ($action)
    cw_header_location("index.php?target=$target&mode=counties&country=$country".(!empty($page)?"&page=$page":""));


$total_items_in_search = cw_query_first_cell("select count(*) from $tables[map_counties] where country_code='$country'");

if ($total_items_in_search > 0) {
    $navigation = cw_core_get_navigation($target, $total_items_in_search, $page);
    $navigation['script'] = "index.php?target=$target&mode=country&country=$country";
    $smarty->assign('navigation', $navigation);

	$counties = cw_query ("select * from $tables[map_counties] where country_code='$country' order by county limit $navigation[first_page], $navigation[objects_per_page]");
	$smarty->assign('counties', $counties);
}

$smarty->assign('state_id', $state_id);
$smarty->assign('country', $country);
$smarty->assign('country_info', cw_map_get_country($country));
