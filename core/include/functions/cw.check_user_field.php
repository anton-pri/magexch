<?php

function cw_check_user_field_customer_type($customer_id, $value, $section, &$full) {
    return false;
}

function cw_check_user_field_get_sex($value) {
    $day = substr($value, 9, 2);
    if ($day > 40) return 2;
    return 1;
}

function cw_check_user_field_get_birthday($value) {
    global $config;

    $sub = substr($value, 6, 5);
    $year = substr($sub, 0, 2);
    $months = array('A' => 1, 'B' => 2, 'C' => 3, 'D' => 4, 'E' => 5, 'H' => 6, 'L' => 7, 'M' => 8, 'P' => 9, 'R' => 10, 'S' => 11, 'T' => 12);
    $month = $months[substr($sub, 2, 1)];
    $day = substr($sub, 3, 2);
    if ($day > 40) $day -= 40;
    return mktime(0, 0, 1, $month, $day, $year);
}

function cw_check_user_field_extract_to_ssn($str, $letters = 3) {
    $consonants = array('B', 'C', 'D', 'F', 'G', 'H', 'J', 'K', 'L', 'M', 'N', 'P', 'Q', 'R', 'S', 'T', 'V', 'X', 'Z', 'W', 'Y');
    $str = strtoupper($str);
    $ext = '';
    for($i=0; $i<strlen($str); $i++) {
        if (in_array($str[$i], $consonants)) $ext .= $str[$i];
        if (strlen($ext) == $letters) break;
    }
    if (strlen($ext) > 3) $ext = substr($ext, 0, 1).substr($ext, strlen($ext)-2, 2);
    if (strlen($ext) < 3) {
        for($i=0; $i<strlen($str); $i++) {
            if (!in_array($str[$i], $consonants)) $ext .= $str[$i];
            if (strlen($ext) == 3) break;
        }
    }

    if (strlen($ext) < 3) {
        $ret[] = sprintf("%3sX", $ext);
        $ret[] = sprintf("%3sS", $ext);
    }
    else $ret[] = $ext;
    return $ret;
}

function cw_check_user_field_ssn_names($value, $section, &$full) {
    $last = substr($value, 0, 3);
    $fist = substr($value, 3, 3);

    $ch_last = cw_check_user_field_extract_to_ssn($full[$pft]['lastname']);
    $ch_first = cw_check_user_field_extract_to_ssn($full[$pft]['firstname'], 4);
    if (!in_array($last, $ch_last) || !in_array($fist, $ch_first)) return 2;
    return 0;
}

//ABCDEF12B23P432P
function cw_check_user_field_ssn($customer_id, $value, $section, &$full) {
return false;

    $value = $full['basic']['ssn'] = strtoupper($value);

    if(empty($value) || strlen($value)!=16 || !ereg("^[A-Z0-9]+$", $value)) return 1;
    $value = strtoupper($value);

    $s = 0;
    for( $i = 1; $i <= 13; $i += 2 ){
        $c = $value[$i];
        if( '0' <= $c && $c <= '9' )
            $s += ord($c) - ord('0');
        else
            $s += ord($c) - ord('A');
    }
    for( $i = 0; $i <= 14; $i += 2 ){
        $c = $value[$i];
        switch( $c ){
        case '0':  $s += 1;  break;
        case '1':  $s += 0;  break;
        case '2':  $s += 5;  break;
        case '3':  $s += 7;  break;
        case '4':  $s += 9;  break;
        case '5':  $s += 13;  break;
        case '6':  $s += 15;  break;
        case '7':  $s += 17;  break;
        case '8':  $s += 19;  break;
        case '9':  $s += 21;  break;
        case 'A':  $s += 1;  break;
        case 'B':  $s += 0;  break;
        case 'C':  $s += 5;  break;
        case 'D':  $s += 7;  break;
        case 'E':  $s += 9;  break;
        case 'F':  $s += 13;  break;
        case 'G':  $s += 15;  break;
        case 'H':  $s += 17;  break;
        case 'I':  $s += 19;  break;
        case 'J':  $s += 21;  break;
        case 'K':  $s += 2;  break;
        case 'L':  $s += 4;  break;
        case 'M':  $s += 18;  break;
        case 'N':  $s += 20;  break;
        case 'O':  $s += 11;  break;
        case 'P':  $s += 3;  break;
        case 'Q':  $s += 6;  break;
        case 'R':  $s += 8;  break;
        case 'S':  $s += 12;  break;
        case 'T':  $s += 14;  break;
        case 'U':  $s += 16;  break;
        case 'V':  $s += 10;  break;
        case 'W':  $s += 22;  break;
        case 'X':  $s += 25;  break;
        case 'Y':  $s += 24;  break;
        case 'Z':  $s += 23;  break;
        }
    }
    if(chr($s%26 + ord('A')) != $value[15])
        return 1;

    $full['customer_info']['sex'] = cw_check_user_field_get_sex($value);
    $full['customer_info']['birthday'] = cw_check_user_field_get_birthday($value);

    return cw_check_user_field_ssn_names($value, $section, $full);
}

//12345678903
function cw_check_user_field_tax_number($customer_id, $value, $section, &$full) {
return false;
    if(empty($value) || strlen($value)!=11 || !ereg("^[0-9]+$", $value)) return true;

    $s = 0;
    for( $i = 0; $i <= 9; $i += 2 )
        $s += ord($value[$i]) - ord('0');
    for( $i = 1; $i <= 9; $i += 2 ){
        $c = 2*( ord($value[$i]) - ord('0') );
        if( $c > 9 ) $c = $c - 9;
        $s += $c;
    }
    if( ( 10 - $s%10 )%10 != ord($value[10]) - ord('0') ) return true;

    return false;
}

function cw_check_user_field_state($customer_id, $state, $section, &$full) {
    global $tables;

    $display_states = cw_query_first_cell("select display_states from $tables[map_countries] where code = '$section[country]'") == 'Y';
    if (!$display_states) return false;

    if (empty($state) || !cw_user_check_state($state, $section['country'])) return true;

    return false;
}

function cw_check_user_field_region($customer_id, $region, $section, &$full) {
    global $tables;

    $display_regions = cw_query_first_cell("select display_regions from $tables[map_countries] where code = '$section[country]'") == 'Y';
    if (!$display_regions) return false;

    if (empty($region) || !cw_user_check_region($region, $section['country'], $section['state'])) return true;

    return false;
}

function cw_check_user_field_county($customer_id, $county, $section, &$full) {
    global $tables, $config;

    $display_county = cw_query_first_cell("select display_counties from $tables[map_countries] where code = '$section[country]'") == 'Y';
    if (!$display_county) return false;

    if (empty($county) || !cw_user_check_county($county, $section['state'], $section['country'])) return true;

    return false;
}

function cw_check_user_field_sector($customer_id, $sector, $section, &$full) {
    global $tables, $config;

    $display_states = cw_query_first_cell("select count(*) from $tables[map_sectors] where county_id = '$section[county]'");
    if (!$display_states) return false;

    if (empty($sector) || !cw_user_check_sector($sector, $section['county'], $section['state'], $section['country'])) return true;

    return false;
}

function cw_check_user_field_email($customer_id, $email, $sections, $full) {
    return !cw_check_email($email);
}

function cw_check_user_field_password($customer_id, $password, $section, $full) {

    if ($section['password'] != $section['password2']) return true;

    return false;
}

function cw_check_user_get_error($fill_error) {

    $str = '';
    if (is_array($fill_error))
    foreach($fill_error as $section=>$fields) {
        if (preg_match('/custom_section_(.*)/', $section, $out)) {
            $name = 'err_section_custom';
            $section = cw_profile_fields_get_section($out[1]);
            $value = cw_get_langvar_by_name($name, array('section' => $section['name']), false, true);
        }
        else {
            $name = 'err_section_'.$section;
            $value = cw_get_langvar_by_name($name, array(), false, true);
        }
        $str .= ($value?$value:$name).'<br/>';
        if (is_array($fields))
        foreach($fields as $field=>$error) {
            if (intval($field)) {
                $field = cw_profile_fields_get_field($field);
                if ($error === true)
                    $value = cw_get_langvar_by_name('err_field_custom', array('field' => $field['title']), false, true);
                else
                    $value = $error;
            }
            else {
                $name = 'err_field_'.$field;
                if ($error > 1) $name = 'err_field_'.$field.'_ind_'.$error;
                $value = cw_get_langvar_by_name($name, array(), false, true);
            }
            $str .= ($value?$value:$name).'<br/>';
        }
    }
    return $str;
}

function cw_check_user_field_build_profile($profile, $fields, $profile_fields) {
    global $customer_id;

    // Map of data trasition from section/field to profile field
    $map = array(
        'basic' => array(
            'email'=> '',
            'password'=>'',
            'ssn'=>'',
            'tax_number'=>'additional_info',
            'language'=>'',
            'company'=>'additional_info',
            'membership_id' => '',
            ),
        'customer_info' => array(
            'birthday'=>'additional_info',
            'birthday_place'=>'additional_info',
            'sex'=>'additional_info',
            'married'=>'additional_info',
            'nationality'=>'additional_info',
            ),
        'customer_company' => array(
            'employees'=>'additional_info',
            'foundation'=>'additional_info',
            'foundation_place'=>'additional_info',
            'employees'=>'additional_info',
        ),
        'mailing_list' => '',       // TODO: Ahggr, this must be in "news" addon somehow
//        'address' => 'addresses',
        'commerciale' => array(
            'company_id'=>'additional_info',
            'can_change_company_id'=>'additional_info',
            'parent_customer_id'=>'salesman_info',
            'department'=>'additional_info',
            'division_id'=>'additional_info',
            'doc_prefix'=>'additional_info',
            'order_entering_format'=>'additional_info',
        ),
        'administration' => array(
            'special_tax'=>'additional_info',
            'tax_id'=>'additional_info',
            'payment_note'=>'additional_info',
            'tax_exempt'=>'additional_info',
            'separate_invoices'=>'additional_info',
            'department'=>'additional_info',

        ),
        'shipping' => array(
            'shipping_operated'=>'additional_info',
            'shipment_paid'=>'additional_info',
            'shipping_company_to'=>'additional_info',
            'shipping_company_from'=>'additional_info',
            'cod_delivery_type'=>'additional_info',
            'special_tax'=>'additional_info',
        ),
    );


    if (AREA_TYPE == 'A') {
        $map['basic'] = array_merge($map['basic'], array(
                'status' => '',
                'change_password' => '',
                'status_note' => 'additional_info',
            ));

        $profile_fields['basic']['change_password']['is_avail'] = true;
        $profile_fields['basic']['status_note']['is_avail'] = true;
    }

    foreach ($map as $section=>$all_fields) {

        if (!isset($fields[$section])) continue;

        if (empty($all_fields)) {
            $profile[$section] = $fields[$section];
            continue;
        }
        if (is_scalar($all_fields)) {
            $profile[$all_fields] = $fields[$section];
            continue;
        }
        foreach ($all_fields as $field=>$to) {
            if ($profile_fields[$section][$field]['is_avail'])
                if (empty($to)) $profile[$field] = $fields[$section][$field];
                else $profile[$to][$field] = $fields[$section][$field];
        }
    }

    foreach (cw_user_address_array($fields['address']) as $address_id=>$address) {
        $profile['addresses'][$address_id] = cw_user_process_address($address);
    }

    if ($profile_fields['basic']['password']['is_avail']) {
        $profile['password2'] = $fields['basic']['password'];
    }

    foreach($profile_fields as $section => $pf)
        foreach($pf as $field)
            if (!empty($field['field_id']) && $field['type'] != 'D' && $field['name']!='address') {
				$profile['custom_fields'][$field['field_id']] = $fields[$section][$field['field_id']];
			}
    return $profile;
}

// Validates profile sections/fields
// Required $update_fields as posted from register page, do not mess with ready profile
// $update_fields[address] may contain one address or array of addresses
function cw_check_user_field_validate($customer_id, $update_fields, $profile_fields) {

    $fill_error = array();

    // Transform one array of addresses to multiple addresses
    // $update_fields['address']['main'] and $update_fields['address']['current'] become $update_fields['address_main'] and $update_fields['address_current']
    foreach (cw_user_address_array($update_fields['address']) as $address_id=>$address) {
       $update_fields['address_'.$address_id] = $address;
    }
    unset($update_fields['address']);
    // Check sections which presented in updated fields
    foreach ($update_fields as $section=>$fields) {

        $_section = $section;
        if (strpos($section,'address_')!==false) $_section = 'address'; // Check address_xxxxx sections according to 'address' rules
        if (!empty($profile_fields[$_section]) && is_array($profile_fields[$_section]))
        foreach ($profile_fields[$_section] as $field=>$val) {
            $cw_for_check = 'cw_check_user_field_'.$val['field'];
            if (function_exists($cw_for_check)) {
                if ($ret = cw_call($cw_for_check, array($customer_id, $update_fields[$section][$field], $update_fields[$section], &$update_fields))) {
                    $fill_error[$_section][$field] = $ret;
                }
            }
            if ($val['is_required'] && empty($update_fields[$section][$field]))  {
				$fill_error[$_section][$field] = 'a';
			}

        }
    }

    return $fill_error; // TODO: should return true or error() 

}
