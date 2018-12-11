<?php
function cw_map_get_regions($country) {
    global $tables;

    return cw_query("select * from $tables[map_regions] where country='$country' order by region");

}

function cw_map_delete_region($region_id) {
    global $tables;

    db_query("delete from $tables[map_regions] where region_id='$region_id'");
    db_query("update $tables[map_states] set region_id=0 where region_id='$region_id'");
}

function cw_map_get_countries() {
    global $current_language, $tables;

    return cw_query("select l.value as country, c.code as country_code, c.display_states, c.region from $tables[map_countries] as c left join $tables[languages] as l on l.name = CONCAT('country_', c.code) and l.code='$current_language' where c.active = 1");
}

function cw_map_get_country($country) {
    global $current_language, $tables;

    return cw_query_first("select l.value as country, c.code as country_code, c.display_regions, c.display_states, c.display_states, c.display_counties, c.display_cities, c.region from $tables[map_countries] as c left join $tables[languages] as l on l.name = CONCAT('country_', c.code) and l.code='$current_language' where c.code = '$country'");
}

function cw_map_get_states($country = '', $region_id = 0) {
    global $tables;

    return cw_query($sql="SELECT $tables[map_states].state_id, $tables[map_states].state, $tables[map_states].code AS state_code, $tables[map_states].country_code FROM $tables[map_states], $tables[map_countries] WHERE $tables[map_states].country_code=$tables[map_countries].code AND $tables[map_countries].active=1".($country?" and $tables[map_countries].code='$country'":'').($region_id?" and $tables[map_states].region_id='$region_id'":"")." ORDER BY $tables[map_states].country_code, $tables[map_states].state, $tables[map_states].code");
}

/**
 * Location lookup by zip
 * Table is loaded from csv
 **/
function cw_map_get_location_by_zip($zip) {
    global $tables;

    if (empty($zip)) return '';

    for ($i = strlen($zip); $i>=2; $i--) {
        $location = cw_query_first("SELECT * FROM $tables[map_zip] WHERE zipcode like '".substr($zip,0,$i)."%'");
        if (!empty($location)) return $location;
    }

    return array();
}
function cw_map_get_state_by_zip($zip) {
    $l = cw_map_get_location_by_zip($zip);
    return $l['state'];
}
function cw_map_get_county_by_zip($zip) {
    $l = cw_map_get_location_by_zip($zip);
    return $l['county'];
}

function cw_map_get_cities($country='', $state_id='', $county_id='') {
    global $tables;

    $where = array('1');
    if ($country) $where[] = "country='$country'";
    if ($state_id) $where[] = "state_id='$state_id'";
    if ($country_id) $where[] = "county_id='$county_id'";
    return cw_query("select * from $tables[map_cities] where ".implode(" and ", $where)." order by city");
}

function cw_map_get_counties_fast($state_id = 0) {
    global $tables;

    return cw_query("SELECT $tables[map_counties].* FROM $tables[map_counties] WHERE $tables[map_counties].state_id='$state_id' ORDER BY $tables[map_counties].county");
}

function cw_map_get_counties($state_id = 0) {
    global $tables;

    cw_query($sql="SELECT $tables[map_counties].*, $tables[map_states].state, $tables[map_states].country_code FROM $tables[map_counties], $tables[map_states] WHERE $tables[map_counties].state_id=$tables[map_states].state_id".($state_id?" and $tables[map_states].state_id='$state_id'":'')." ORDER BY $tables[map_states].state, $tables[map_counties].county");
}

function cw_map_delete_city($city_id) {
    global $tables;

    db_query("delete from $tables[map_cities] where city_id='$city_id'");
}

function cw_map_delete_county($county_id) {
    global $tables;

    db_query("delete from $tables[map_counties] where county_id='$county_id'");
    db_query("delete from $tables[map_cities] where county_id='$county_id'");
}

function cw_map_delete_state($state_id) {
    global $tables;

    $state_data = cw_query_first("select code, country_code from $tables[map_states] WHERE state_id='$state_id'");
    if (!empty($state_data)) {
        $state_code = $state_data['code'];
        $country_code = $state_data['country_code'];
        db_query("delete from $tables[map_states] where state_id='$state_id'");
        db_query("delete from $tables[map_counties] where state_id='$state_id'");
        db_query("delete from $tables[map_cities] where state_id='$state_id'");

        db_query("delete from $tables[zone_element] WHERE field_type = 'S' AND field = '".$country_code."_".$state_code."' ");
    }
}

function cw_map_get_states_smarty($params) {
    return cw_map_get_states($params['country']);
}

function cw_map_get_regions_smarty($params) {
    return cw_map_get_regions($params['country']);
}

function cw_map_get_counties_smarty($params) {
    global $tables;
    return cw_query("select * from $tables[map_counties] where country_code='$params[country]' order by county");
}

function cw_update_country_states ($country, $all_countries=false) {
	global $tables;

	$countries = array();

	if (empty($country) && !$all_countries) {
		return;
	}
	elseif (!$all_countries) {

		if (is_array($country))
			$countries = $country;
		elseif (!empty($country))
			$countries[] = $country;

	}

	$countries_with_states = cw_query_column("SELECT DISTINCT(country_code) FROM $tables[map_states] WHERE 1 " . (!empty($countries) ? " AND country_code IN ('".implode("','", $countries)."')" : ""));

	db_query("UPDATE $tables[map_countries] SET display_states='N' WHERE 1" . (!empty($countries) ? " AND code IN ('".implode("','", $countries)."')" : ""));

	if (!empty($countries_with_states))
		db_query("UPDATE $tables[map_countries] SET display_states='Y' WHERE code IN ('" . implode("','", $countries_with_states) . "')");

}

#
# This function inserts the zone elements
# country (C), state (S), county (G), city (T), zip code (Z), address (A)
#
function cw_insert_zone_element($zone_id, $field_type, $zone_elements) {
	global $tables;

	db_query("DELETE FROM $tables[zone_element] WHERE zone_id='$zone_id' AND field_type='$field_type'");
	if (!empty($zone_elements) && is_array($zone_elements)) {
		foreach ($zone_elements as $k=>$v) {
			$v = trim($v);
			if (empty($v)) continue;

			db_query("REPLACE INTO $tables[zone_element] (zone_id, field, field_type) VALUES ('$zone_id', '$v', '$field_type')");
		}
	}
}
#
# This function updates the cache of zone elements
# Format: C2-S3-G4-T1-Z5-A2
#
function cw_zone_cache_update ($zone_id) {
	global $tables;

	$result = "";

	$data = cw_query("SELECT field_type, count(*) as count FROM $tables[zone_element] WHERE zone_id='$zone_id' GROUP BY field_type");

	if (!empty($data)) {
		$result_array = array();
		for ($i = 0; $i < count($data); $i++) {
			$result_array[] = $data[$i]['field_type'].$data[$i]['count'];
		}
		$result = implode("-", $result_array);
    }
	db_query("UPDATE $tables[zones] SET zone_cache='$result' WHERE zone_id='$zone_id'");
}
