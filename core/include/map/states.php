<?php
$objects_per_page = 25;

if ($action == "delete" && is_array($selected)) {
	foreach ($selected as $state_id=>$v) 
        cw_map_delete_state($state_id);
    $top_message['content'] = cw_get_langvar_by_name("msg_adm_states_del");

    cw_header_location("index.php?target=$target&mode=states&country=$country".(!empty($page)?"&page=$page":""));
}

if ($action == "update" && is_array($posted_data)) {
    $top_message['content'] = cw_get_langvar_by_name("msg_adm_states_upd");

    foreach ($posted_data as $k => $v) {
        if ($k == 0) {
            $is_code_exists = cw_query_first_cell("SELECT COUNT(*) FROM $tables[map_states] WHERE code = '$v[code]' AND country_code = '$country'") > 0;
            if (!$is_code_exists) {
                if ($v['code'] && $v['state']) {
                    $query_data = array(
                    'state' => $v['state'],
                    'code' => $v['code'],
                    'country_code' => $country,
                    );
                    cw_array2insert('map_states', $query_data);
                }
            }
            else {
                $top_message = array(
                    "content" => cw_get_langvar_by_name("msg_adm_warn_states_duplicate"),
                    "type" => "W"
                );
                break;
            }
        }
        else {
            $is_code_exists = cw_query_first_cell("SELECT COUNT(*) FROM $tables[map_states] WHERE code = '$v[code]' AND country_code = '$country'") > 0;
            if ($is_code_exists)
                cw_unset($v, "code");
            cw_array2update("map_states", $v, "state_id = '$k'");
        }
    }

    cw_header_location("index.php?target=$target&mode=states&country=$country".(!empty($page)?"&page=$page":""));
}

$search_query = "FROM $tables[map_states], $tables[map_countries] LEFT JOIN $tables[languages] as lng1 ON lng1.name = CONCAT('country_', $tables[map_countries].code) AND lng1.code = '$current_language' LEFT JOIN $tables[languages] as lng2 ON lng2.name = CONCAT('country_', $tables[map_countries].code) AND lng2.code = '$config[default_admin_language]' WHERE $tables[map_states].country_code=$tables[map_countries].code AND $tables[map_states].country_code='$country'";

$total_items_in_search = cw_query_first_cell("SELECT COUNT(*) $search_query");

if ($total_items_in_search > 0) {
    $navigation = cw_core_get_navigation($target, $total_items_in_search, $page);
    $navigation['script'] = "index.php?target=$target&mode=states&country=$country";
    $smarty->assign('navigation', $navigation);

	$states = cw_query ("SELECT $tables[map_states].*, IFNULL(lng1.value, lng2.value) as country $search_query ORDER BY country_code, state LIMIT $navigation[first_page], $navigation[objects_per_page]");

	$smarty->assign('states', $states);
}

$smarty->assign('country', $country);
$smarty->assign('country_info', cw_map_get_country($country));
?>
