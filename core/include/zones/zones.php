<?php

cw_load('map');
function sort_zone_elements ($a, $b) {
    static $sort_order;

    $sort_order = array_flip(array("C","S","G","T","Z","A"));

    if ($sort_order[$a['element_type']] > $sort_order[$b['element_type']])
        return 1;
    else
        return 0;
}

$zones[] = array("zone" => "ALL", "title" => cw_get_langvar_by_name("lbl_all_regions"));
$zones[] = array("zone" => "NA", "title" => cw_get_langvar_by_name("lbl_na"));
$zones[] = array("zone" => "EU", "title" => cw_get_langvar_by_name("lbl_eu"));
$zones[] = array("zone" => "AU", "title" => cw_get_langvar_by_name("lbl_au"));
$zones[] = array("zone" => "LA", "title" => cw_get_langvar_by_name("lbl_la"));
$zones[] = array("zone" => "SU", "title" => cw_get_langvar_by_name("lbl_su"));
$zones[] = array("zone" => "AS", "title" => cw_get_langvar_by_name("lbl_asia"));
$zones[] = array("zone" => "AF", "title" => cw_get_langvar_by_name("lbl_af"));
$zones[] = array("zone" => "AN", "title" => cw_get_langvar_by_name("lbl_an"));

if (!in_array($mode, array("add", "details", "delete", "rename", "clone")))
    $mode = '';

# kornev, for admins we always use the same account
# !! zones in admin are used only for tax calculation

if ($action == "details") {
    $zone_name = trim($zone_name);
    if (!empty($zone_name)) {
        if (empty($zone_id)) {
            db_query("INSERT INTO $tables[zones] (zone_name, warehouse_customer_id, is_shipping) VALUES ('$zone_name', '$user_account[warehouse_customer_id]', '$is_shipping')");
            $zone_id = db_insert_id();
            $top_message['content'] = cw_get_langvar_by_name('msg_zone_add');
        }
        else {
            db_query("UPDATE $tables[zones] SET zone_name='$zone_name' WHERE zone_id='$zone_id'");
            $top_message['content'] = cw_get_langvar_by_name("msg_zone_renamed");
        }
    }
    else {
        $top_message = array('content' => cw_get_langvar_by_name('msg_err_zone_rename'), 'type' => 'W');
        cw_header_location('index.php?target='.$target.'&mode=add');
    }
    
    #
    # Update zone elements
    #

    if (!$error && $zone_id > 0) {
        
            $zone_countries = explode(";", $zone_countries);
            $zone_states = explode(";", $zone_states);
            $zone_counties = explode(";", $zone_counties);

            if ($zone_states) {
                foreach($zone_states as $v) {
                    if (preg_match('!^(.+)_!S',$v, $m))
                        $zone_countries[] = $m[1];
                }
            }
            if ($zone_counties) {
                foreach($zone_counties as $county_id) {
                    if (empty($county_id))
                        continue;
                    $state_id = cw_query_first_cell("SELECT state_id FROM $tables[map_counties] WHERE county_id = '$county_id'");
                    $state_info = cw_query_first("SELECT code, country_code FROM $tables[map_states] WHERE state_id = '$state_id'");
                    $zone_countries[] = $state_info['country_code'];
                    $zone_states[] = $state_info['country_code'] . "_" . $state_info['code'];
                }
            }

            # Update zone countries...
            cw_insert_zone_element($zone_id, "C", $zone_countries);
            # Update zone states...
            cw_insert_zone_element($zone_id, "S", $zone_states);
            # Update zone counties...
            cw_insert_zone_element($zone_id, "G", $zone_counties);
            # Update zone city masks...
            cw_insert_zone_element($zone_id, "T", explode("\n", $zone_cities));
            # Update zone zip code masks...
            cw_insert_zone_element($zone_id, "Z", explode("\n", $zone_zipcodes));
            # Update zone address masks...
            cw_insert_zone_element($zone_id, "A", explode("\n", $zone_addresses));

            cw_call('cw_zone_cache_update', array($zone_id));

            $top_message['content'] = cw_get_langvar_by_name("msg_zone_upd");


    }

    cw_header_location('index.php?target='.$target.'&zone_id='.$zone_id);
}

if ($action == 'delete' && is_array($to_delete)) {
    foreach ($to_delete as $zone_id=>$v)
        cw_call('cw_shipping_delete_zone', array($zone_id));
    $top_message['content'] = cw_get_langvar_by_name('msg_zone_del');
    cw_header_location('index.php?target='.$target);
}

if ($action == "clone") {
        $zone_data = cw_query_first("SELECT * FROM $tables[zones] WHERE zone_id='$zone_id'");
        if (!empty($zone_data)) {
            # Duplicate main zone data
            $zone_data['zone_id'] = "";
            $zone_data['warehouse_customer_id'] = $user_account['warehouse_customer_id'];
            $zone_data['zone_name'] = $zone_data['zone_name']." (clone)";
            foreach ($zone_data as $k=>$v) {
                $zone_data[$k] = "'".addslashes($v)."'";
            }
            db_query("INSERT INTO $tables[zones] (".implode(",",array_keys($zone_data)).") VALUES (".implode(",",$zone_data).")");
            $new_zone_id = db_insert_id();

            $zone_elements = cw_query("SELECT * FROM $tables[zone_element] WHERE zone_id='$zone_id'");
            if (is_array($zone_elements)) {
                foreach ($zone_elements as $k=>$zone_element) {
                    db_query("INSERT INTO $tables[zone_element] (zone_id, field, field_type) VALUES ('$new_zone_id', '".addslashes($zone_element['field'])."', '$zone_element[field_type]')");
                }
            }
            $top_message['content'] = cw_get_langvar_by_name("msg_zone_cloned");
        }

        cw_header_location('index.php?target='.$target.'&zone_id='.$new_zone_id);
}

$location[] = array(cw_get_langvar_by_name("lbl_destination_zones"), "");

if ($mode == "add" or !empty($zone_id)) {
#
# Display zone details page
#
    $location[count($location) - 1][1] = "index.php?target=zones";
    $location[] = array(cw_get_langvar_by_name("lbl_zone_details"), "");

    if (!empty($zone_id))
        $zone = cw_query_first("SELECT * FROM $tables[zones] WHERE zone_id='$zone_id' $zones_condition");

    if (empty($zone))
        $action = "add";

    #
    # Countries in this zone and rest
    #
    $zone_countries = cw_query("SELECT $tables[map_countries].code, $tables[languages].value as country FROM $tables[zone_element], $tables[map_countries], $tables[languages] WHERE $tables[zone_element].field_type='C' AND $tables[zone_element].field=$tables[map_countries].code AND $tables[languages].name = CONCAT('country_', $tables[map_countries].code) AND $tables[languages].code='$current_language' AND $tables[map_countries].active=1 AND $tables[zone_element].zone_id='$zone_id' ORDER BY country");

    $rest_countries = cw_query("SELECT $tables[map_countries].code, $tables[map_countries].region, $tables[languages].value as country, $tables[zone_element].zone_id FROM $tables[languages], $tables[map_countries] LEFT JOIN $tables[zone_element] ON $tables[zone_element].field_type='C' AND $tables[zone_element].field=$tables[map_countries].code AND $tables[zone_element].zone_id='$zone_id' WHERE $tables[map_countries].active=1 AND $tables[languages].name = CONCAT('country_', $tables[map_countries].code) AND $tables[languages].code='$current_language' AND zone_id IS NULL ORDER BY country");
    $rest_zones = array();
    if($rest_countries) {
        foreach($rest_countries as $v)
            $rest_zones[$v['region']][] = $v['code'];
        $rest_zones['SU'] = array('AM','AZ','BY','EE','GE','KZ','KG','LV','LT','MD','RU','TJ','TM','UA','UZ');
    }


    $smarty->assign('countries_box_size', min(20, max(count($rest_countries)+5, count($zone_countries)+5)));

    #
    # States in this zone and rest
    #
    $zone_states = cw_query("SELECT $tables[map_states].* FROM $tables[map_states], $tables[zone_element] WHERE $tables[zone_element].field_type='S' AND $tables[zone_element].field=CONCAT($tables[map_states].country_code,'_',$tables[map_states].code) AND $tables[zone_element].zone_id='$zone_id' ORDER BY $tables[map_states].country_code, $tables[map_states].state");

    $rest_states = cw_query("SELECT $tables[map_states].*, $tables[zone_element].zone_id FROM $tables[map_countries], $tables[map_states] LEFT JOIN $tables[zone_element] ON $tables[zone_element].field_type='S' AND $tables[zone_element].field=CONCAT($tables[map_states].country_code,'_',$tables[map_states].code) AND $tables[zone_element].zone_id='$zone_id' WHERE $tables[map_countries].code=$tables[map_states].country_code AND $tables[map_countries].active=1 AND zone_id IS NULL ORDER BY $tables[map_states].country_code, $tables[map_states].state");

    $_distinct_countries = cw_query("SELECT DISTINCT country_code, $tables[languages].value as country FROM $tables[map_states], $tables[languages] WHERE $tables[languages].name = CONCAT('country_', $tables[map_states].country_code) AND $tables[languages].code='$current_language'");

    $state_country = array();
    if (is_array($_distinct_countries)) {
        foreach ($_distinct_countries as $k=>$v)
            $state_country[$v['country_code']] = $v['country'];
    }

    if (is_array($zone_states)) {
        foreach ($zone_states as $k=>$v)
            $zone_states[$k]['country'] = $state_country[$v['country_code']];
    }
    if (is_array($rest_states)) {
        foreach ($rest_states as $k=>$v)
            $rest_states[$k]['country'] = $state_country[$v['country_code']];
    }

    $smarty->assign('states_box_size', min(20, max(count($rest_states)+5, count($zone_states)+5)));

    if ($config['General']['use_counties'] == "Y") {
    #
    # Counties in this zone and rest
    #
        if (cw_query_first_cell("SELECT county_id FROM $tables[map_counties] LIMIT 1")) {
            $zone_counties = cw_query("SELECT $tables[map_counties].*, $tables[map_states].code as state_code, $tables[map_states].state, $tables[map_countries].code as country_code FROM $tables[map_counties], $tables[zone_element], $tables[map_states], $tables[map_countries] WHERE $tables[zone_element].field_type='G' AND $tables[zone_element].field=$tables[map_counties].county_id AND $tables[zone_element].zone_id='$zone_id' AND $tables[map_states].state_id=$tables[map_counties].state_id AND $tables[map_countries].code=$tables[map_states].country_code AND $tables[map_countries].active=1");

            $rest_counties = cw_query("SELECT $tables[map_counties].*, $tables[map_states].code as state_code, $tables[map_states].state, $tables[map_countries].code as country_code, $tables[zone_element].zone_id FROM $tables[map_states], $tables[map_countries], $tables[map_counties] LEFT JOIN $tables[zone_element] ON $tables[zone_element].field_type='G' AND $tables[zone_element].field=$tables[map_counties].county_id AND $tables[zone_element].zone_id='$zone_id' WHERE $tables[map_states].state_id=$tables[map_counties].state_id AND $tables[map_countries].code=$tables[map_states].country_code AND $tables[map_countries].active=1 AND zone_id IS NULL ORDER BY $tables[map_states].country_code, $tables[map_states].state");

            if (!empty($zone_counties) && is_array($zone_counties)) {
                foreach ($zone_counties as $k => $v) {
                    $zone_counties[$k]['country'] = $state_country[$v['country_code']];
                }
            }

            if (!empty($rest_counties) && is_array($rest_counties)) {
                foreach ($rest_counties as $k => $v) {
                    $rest_counties[$k]['country'] = $state_country[$v['country_code']];
                }
            }

            $smarty->assign('zone_counties', $zone_counties);
            $smarty->assign('rest_counties', $rest_counties);
            $smarty->assign('counties_box_size', min(20, max(count($rest_counties)+5, count($zone_counties)+5)));
        }
    }

    #
    # City/Zipcode/Address masks in this zone and rest
    #
    $zone_elements = cw_query("SELECT * FROM $tables[zone_element] WHERE zone_id='$zone_id' AND field_type IN ('T','Z','A')");

    $smarty->assign('zone_elements', $zone_elements);

    $smarty->assign('cities_box_size', min(10, cw_query_first_cell("SELECT COUNT(*) FROM $tables[zone_element] WHERE zone_id='$zone_id' AND field_type='T'") + 9));

    $smarty->assign('zipcodes_box_size', min(10, cw_query_first_cell("SELECT COUNT(*) FROM $tables[zone_element] WHERE zone_id='$zone_id' AND field_type='Z'") + 9));

    $smarty->assign('addresses_box_size', min(10, cw_query_first_cell("SELECT COUNT(*) FROM $tables[zone_element] WHERE zone_id='$zone_id' AND field_type='A'") + 9));

    $smarty->assign('zone_id', $zone_id);

    $smarty->assign('zone', $zone);

    $smarty->assign('zone_countries', $zone_countries);
    $smarty->assign('rest_countries', $rest_countries);
    $smarty->assign('rest_zones', $rest_zones);

    $smarty->assign('zone_states', $zone_states);
    $smarty->assign('rest_states', $rest_states);

    $smarty->assign('zone_elements', $zone_elements);

    $smarty->assign('main', 'zone_edit');
}
else {
#
# Get the zones list
#
    $zones = cw_query("SELECT $tables[zones].* FROM $tables[zones] WHERE 1 $zones_condition ORDER BY $tables[zones].zone_name");
    
    if (!empty($zones)) {
    #
    # Gather the additional information on each zone (for notes field)
    #
    
        foreach ($zones as $k=>$zone) {
            if (!empty($zone['zone_cache'])) {
                $zone_cache_array = explode("-", $zone['zone_cache']);
                for ($i = 0; $i < count($zone_cache_array); $i++) {
                    if (preg_match("/^([\w])([0-9]+)$/", $zone_cache_array[$i], $match)) {
                        $zones[$k]['elements'][$i]['element_type'] = $match[1];
                        $zones[$k]['elements'][$i]['counter']= $match[2];
                        
                        if ($match[2] == 1) {
                        
                            $_element = cw_query_first_cell("SELECT field FROM $tables[zone_element] WHERE zone_id='$zone[zone_id]' AND field_type='$match[1]' LIMIT 1");
                            if ($match[1] == "C")
                                $element_name = cw_get_country($_element);
                            
                            elseif ($match[1] == "S")
                                $element_name = cw_get_state(substr($_element, strpos($_element, "_")+1), substr($_element, 0, strpos($_element, "_")));
                            
                            elseif ($match[1] == "G")
                                $element_name = cw_get_state(substr($_element, strpos($_element, "_")+1), substr($_element, 0, strpos($_element, "_")));
                            
                            else
                                $element_name = $_element;
                            
                            $zones[$k]['elements'][$i]['element_name'] = $element_name;
                        
                        }
                    }
                }

                usort($zones[$k]['elements'], "sort_zone_elements");

            }
        }
    }
    
    $smarty->assign('zones', $zones);
    $smarty->assign('main', 'zones');
}

$smarty->assign('mode', $mode);
$smarty->assign('zones', $zones);
