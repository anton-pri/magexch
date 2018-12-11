<?php
# kornev, we need to modify the attributes sometimes - it's related with the date/integer type usually
function cw_error_check(&$array_to_check, $rules, $attributes_type = '') {

    $error = array();
    $index = 1;
 
    foreach($rules as $k=>$v) {
        $func = (is_array($v) && isset($v['func']))?$v['func']:$v;
        if ($func && function_exists($func)) $is_error = $func($array_to_check[$k], $k, $array_to_check);
        else $is_error = empty($array_to_check[$k]) || !$array_to_check[$k];
        if ($is_error) {
            $var = 'err_field_'.($v['lng']?$v['lng']:$k);
            $lng = cw_get_langvar_by_name($var, '', false, true);
            $error[] = $index++.'. '.($lng?$lng:$var);
        }
    }

    if ($attributes_type) {
	    $error = array_merge($error, cw_call('cw_attributes_check', array($array_to_check['attribute_class_id'], &$array_to_check['attributes'], $attributes_type, $index)));
	}

    if (!count($error)) return false;
    return implode("<br/>\n", $error);
}

function cw_error_sku_exists($value, $field, $arr) {
    global $tables;

    return (!$value || cw_query_first_cell("select count(*) from $tables[products] where productcode='$value' AND product_id!='$arr[product_id]'") ? true : false);
}

function cw_error_manufacturer_code_exists($value, $field, $arr) {
    global $tables;
    
    return ($value && cw_query_first_cell("select count(*) from $tables[products] where manufacturer_code='$value' AND product_id!='$arr[product_id]'") ? true : false);
}

function cw_error_check_state($state, $section, &$full) {
    global $tables;

    $display_states = cw_query_first_cell("select display_states from $tables[map_countries] where code = '$section[country]'") == 'Y';
    if (!$display_states) return false;

    if (empty($state) || !cw_user_check_state($state, $section['country'])) return true;

    return false;
}
