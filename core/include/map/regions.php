<?php
if ($action == "delete" && is_array($selected)) {
    foreach ($selected as $region_id=>$v) 
        cw_map_delete_region($region_id);
    $top_message['content'] = cw_get_langvar_by_name("msg_adm_regions_del");
    cw_header_location("index.php?target=countries&mode=regions&country=$country".(!empty($page)?"&page=$page":""));
}

if ($action == "update" && is_array($posted_data)) {
    foreach ($posted_data as $region_id=>$v) {
        if (!$region_id) {
            if ($v['region'])
                db_query ("insert into $tables[map_regions](region, country) values('$v[region]', '$country')");
        }
        else
    	    db_query ("update $tables[map_regions] SET region='$v[region]' WHERE region_id='$region_id'");
    }

    $top_message['content'] = cw_get_langvar_by_name("msg_adm_regions_upd");
    cw_header_location("index.php?target=countries&mode=regions&country=$country".(!empty($page)?"&page=$page":""));
}
	
$total_items_in_search = cw_query_first_cell("select count(*) from $tables[map_regions] where country='$country'");

if ($total_items_in_search > 0) {
    $navigation = cw_core_get_navigation($target, $total_items_in_search, $page);
    $navigation['script'] = "index.php?target=countries&mode=regions&country=$country";
    $smarty->assign('navigation', $navigation);

	$regions = cw_query("select * from $tables[map_regions] where country='$country' order by region limit $navigation[first_page], $navigation[objects_per_page]");
	$smarty->assign('regions', $regions);
}

$smarty->assign('country', $country);
$smarty->assign('country_info', cw_map_get_country($country));
