<?php
cw_load('map');
if ($mode == 'states') {
    $smarty->assign('state_name', $state_name);
    $smarty->assign('selected', $selected);
    $smarty->assign('show_code', $show_code == 1);

    if ($region > 0) $country = cw_query_first_cell("select country from $tables[map_regions] where region_id='$region'");
    $country_info = cw_map_get_country($country);

    if (!$country_info['display_states']) {
        $smarty->assign('disabled', 1);
        $smarty->assign('states', array());
    }
    if ($region > 0) $smarty->assign('states', cw_map_get_states('', $region));
    elseif ($country && (!$country_info['display_regions'] || $region == -1)) $smarty->assign('states', cw_map_get_states($country));
    else $smarty->assign('states', array());

    cw_display('main/map/states_ajax_js.tpl', $smarty);
    exit(0);
}

if ($mode == 'counties') { 
    $smarty->assign('county_name', $county_name);
    $smarty->assign('selected', $selected);
    $country = cw_query_first_cell($sql="select country_code from $tables[map_states] where state_id='$state'");
    $country_info = cw_map_get_country($country);
    if (!$country_info['display_counties']) {
        $smarty->assign('disabled', 1);
        $smarty->assign('states', array());
    }
    elseif ($state) $smarty->assign('counties', cw_map_get_counties_fast($state));
    else $smarty->assign('counties', array());

    if ($country_info['display_cities'] && !$country_info['display_counties']) {
        $smarty->assign('city_name', $city_name);
        $smarty->assign('city_value', $city_value);
        $smarty->assign('cities', cw_map_get_cities('', $state));
    }

    cw_display('main/map/counties_ajax_js.tpl', $smarty);
    exit(0);
}

if ($mode == 'cities') {
    $smarty->assign('city_name', $city_name);
    $smarty->assign('city_value', $city_value);
    $smarty->assign('cities', cw_map_get_cities('', '', $county));

    cw_display('main/map/cities_ajax_js.tpl', $smarty);
    exit(0);
}

if ($mode == 'regions') {
    $smarty->assign('region_name', $region_name);
    $smarty->assign('selected', $selected);
    if ($country) $smarty->assign('regions', cw_map_get_regions($country));
    else $smarty->assign('regions', array());

    $smarty->assign('states', array());
    $smarty->assign('state_name', $state_name);
    
    $country_info = cw_map_get_country($country);
    if (!$country_info['display_regions']) {
        $smarty->assign('disabled', 1);
        $smarty->assign('regions', array());
        $smarty->assign('state_name', $state_name);
        $smarty->assign('state_selected', $selected);
        if ($country) $smarty->assign('states', cw_map_get_states($country));
        else $smarty->assign('states', array());
    }

    cw_display('main/map/regions_ajax_js.tpl', $smarty);
    exit(0);
}

?>
