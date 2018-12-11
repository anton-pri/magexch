<?php
function cw_config_get_categories($with_addons = false, $with_local = false) {
    global $tables, $current_language;

    $categories = cw_query("
        SELECT c.category, IFNULL(lng.value, c.category) AS title, m.addon, c.is_local
        FROM $tables[config_categories] AS c
        LEFT JOIN $tables[addons] AS m ON m.addon = c.category
        LEFT JOIN $tables[languages] AS lng ON lng.code = '$current_language' AND lng.name = CONCAT('option_title_', c.category)
        WHERE " . (!$with_addons ? "m.addon IS NULL" : "1") . " AND ". (!$with_local?"c.is_local=0":"1") ." 
            AND c.category NOT IN ('main')
        GROUP BY title
    ");
    return $categories;
}

function cw_config_get_all_categories() {
    global $tables, $current_language;

    $categories = cw_query("
        SELECT c.category, IFNULL(lng.value, c.category) AS title
        FROM $tables[config_categories] AS c
        LEFT JOIN $tables[languages] AS lng ON lng.code = '$current_language' AND lng.name = CONCAT('option_title_', c.category)
        WHERE c.category NOT IN ('main')
        GROUP BY category
    ");
    return $categories;
}

function cw_config_get_category($category) {
    global $tables, $current_language;

    $options = cw_query("
        SELECT c.*, IFNULL(lng.value, c.comment) AS title, lng.value as title_lng
        FROM $tables[config_categories] AS cc, $tables[config] AS c
        LEFT JOIN $tables[languages] AS lng ON lng.code = '$current_language' AND lng.name = CONCAT('opt_', c.name)
        WHERE c.config_category_id = cc.config_category_id AND cc.category = '$category'
        ORDER BY orderby
    ");
    return cw_config_process_options($options);
}

function cw_config_process_options($options) {
    global $tables;

    if ($options)
    foreach($options as $k=>$v) {

        if (in_array($v['type'], array("selector","multiselector"))) {
            if (empty($v['variants'])) {
                unset($options[$k]);
                continue;
            }
            $vars = cw_parse_str(trim($v['variants']), "\n", ":");
            $vars = cw_array_map("trim", $vars);

            if ($v['type'] == "multiselector") {
                $v['value'] = explode(";", $v['value']);
                foreach ($v['value'] as $vk => $vv) {
                    if (!isset($vars[$vv]))
                        unset($v['value'][$vk]);
                }
                $options[$k]['value'] = $v['value'] = array_values($v['value']);
            }

            $options[$k]['variants'] = array();
            foreach ($vars as $vk => $vv) {
                $options[$k]['variants'][$vk] = array("name" => $vv);
                if (strpos($vv, " ") === false) {
                    $name = cw_get_langvar_by_name($vv, NULL, false, true);
                    if (!empty($name))
                        $options[$k]['variants'][$vk] = array("name" => $name);
                }

                if ($v['type'] == "selector")
                    $options[$k]['variants'][$vk]['selected'] = ($v['value'] == $vk);
                else
                    $options[$k]['variants'][$vk]['selected'] = (in_array($vk, $v['value']));
            }
        }
        if (in_array($v['type'], array('shipping', 'memberships', 'doc_status'))) {
            $options[$k]['value'] = unserialize($v['value']);
        }
    }
    return $options;
}

function cw_config_update($category, $options) {
    global $tables;

    $config_category_id = cw_query_first_cell("SELECT config_category_id FROM $tables[config_categories] WHERE category='$category'");

    $var_properties = cw_query_hash(
        "SELECT name, type FROM $tables[config] WHERE config_category_id='$config_category_id'",
        "name",
        false,
        true
    );

    $section_data = array();
    foreach ($options as $key => $val) {

        if (isset($var_properties[$key])) {

            if ($var_properties[$key] == "numeric") {
                $val = doubleval(cw_convert_numeric($val));
            }
            elseif ($var_properties[$key] == "textarea") {
                $val = strtr($val, array("\r" => ''));
            }
            elseif ($var_properties[$key] == "multiselector") {
                $val = implode(";", $val);
            }
            elseif ($var_properties[$key] == "checkbox") {
                $val = in_array($val,array('on','1','Y'))?'Y':'N';
            }
            elseif (in_array($var_properties[$key], array('shipping', 'memberships', 'doc_status'))) {
                $ret = array();
                if (is_array($val))
                foreach($val as $k=>$v) $ret[$v] = 1;
                $val = serialize($ret);
            } elseif (is_array($val)) {
                $val = serialize($val);
            }

            cw_array2update(
                "config",
                array("value" => $val),
                "name='" . $key . "' AND config_category_id='" . $config_category_id . "'"
            );
            $section_data[stripslashes($key)] = stripslashes($val);
        }
    }
}


function cw_config_advanced_search_attributes($custom_area = true) {
    global $tables, $config, $current_language;

    $allowed_types = array('selectbox','text', 'textarea', 'decimal', 'multiple_selectbox', 'integer');

    $adv_search_attributes_config = $config['adv_search_attributes_config'];
    if (!empty($adv_search_attributes_config)) {
        $adv_search_attributes_config = unserialize($adv_search_attributes_config);
    } 
    $adv_search_attributes = cw_query_hash("select at.*, lng.value as addon_name, 0 as orderby_adv_search, ad.active as addon_active from $tables[attributes] at left join $tables[languages] lng on lng.name=concat('addon_name_',at.addon)  and lng.code='".(empty($current_language)?'EN':$current_language)."' left join $tables[addons] ad on ad.addon=at.addon where at.item_type='P' and at.type in ('".implode("','", $allowed_types)."') order by at.addon, at.orderby, at.field",'attribute_id',false); 
    if (!empty($adv_search_attributes_config)) {
        foreach ($adv_search_attributes_config as $attr_id => $attr_data) { 
            if (empty($adv_search_attributes[$attr_id])) continue;
            $adv_search_attributes[$attr_id]['enabled_adv_search'] = $attr_data['enabled']; 
            $adv_search_attributes[$attr_id]['orderby_adv_search'] = $attr_data['orderby'];  
            $adv_search_attributes[$attr_id]['enabled_adv_search_more'] = $attr_data['enabled_more'];
        } 
    }

    $count_numeric = 0;
    $count_text = 0;
    $count_multiselect = 0;
    if ($custom_area) {
        uasort($adv_search_attributes, 'cw_config_advanced_search_sort');
        $_adv_search_attributes = array();
        foreach ($adv_search_attributes as $attr_id=>$attr_data) {
            if ($attr_data['enabled_adv_search'] || $attr_data['enabled_adv_search_more']) {
                $_adv_search_attributes[$attr_id] = $attr_data;
            }

            if (in_array($attr_data['type'], array('decimal', 'integer')) && $attr_data['enabled_adv_search_more'])     
                $count_numeric++;

            if (in_array($attr_data['type'], array('selectbox','text', 'textarea', 'multiple_selectbox')) && $attr_data['enabled_adv_search'])   
                $count_text++;

            if (in_array($attr_data['type'], array('selectbox', 'multiple_selectbox')) && $attr_data['enabled_adv_search_more']) {
                $count_multiselect++;
                $_adv_search_attributes[$attr_id]['options'] = cw_query("select * from $tables[attributes_default] where attribute_id='$attr_id' and active=1 order by value");
            }  

        } 
        $adv_search_attributes = $_adv_search_attributes;
    }

    return array('attributes'=>$adv_search_attributes, 'count_numeric'=>$count_numeric, 'count_text'=>$count_text, 'count_multiselect'=>$count_multiselect);
}

function cw_config_advanced_search_sort($a, $b) {
    if ($a['orderby_adv_search'] == $b['orderby_adv_search']) {
        return 0;
    }
    return ($a['orderby_adv_search'] < $b['orderby_adv_search']) ? -1 : 1;
}
