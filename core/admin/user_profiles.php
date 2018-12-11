<?php
if (!$user_type) $user_type = 'C';

cw_load( 'user', 'profile_fields');

if ($action == 'update_status' && is_array($upd_flag)) {
    foreach ($upd_flag as $field_id => $v) {
        db_query("delete from $tables[register_fields_avails] where field_id='$field_id' and (area like '".$user_type."_%' or area like '#".$user_type."_%' or area = '#$user_type')");
        if ($upd[$field_id])
        foreach($upd[$field_id] as $area=>$state) {
            $ins = array(
                'is_avail' => $state['a'],
                'is_required' => $state['r'],
                'field_id' => $field_id,
                'area' => $area,
            );
            cw_array2insert('register_fields_avails', $ins, true);
        }
    }
    cw_header_location("index.php?target=$target&user_type=$user_type&js_tab=profile_options");
}

if ($action == 'update_fields' && is_array($update)) {
    foreach ($update as $k => $v) {
        if ($k == 0) {
            if ($v['field']) cw_profile_fields_add_field($v);
        }   
        else cw_profile_fields_update_field($k, $v);
    }
    cw_header_location("index.php?target=$target&mode=fields&js_tab=addition_fields");
}

if ($action == 'delete_fields' && is_array($update)) {
    foreach($update as $field_id=>$v)
        if ($v['del'] == 'Y') cw_profile_fields_delete_field($field_id);
    cw_header_location("index.php?target=$target&mode=fields&js_tab=addition_fields");
}

if ($action == 'update_sections' && is_array($update)) {
    foreach ($update as $k => $v) {
        if ($k == 0) {
            if ($v['name']) cw_profile_fields_add_section($v);
        }
        else cw_profile_fields_update_section($k, $v);
    }
    cw_header_location("index.php?target=$target&mode=fields&js_tab=addition_section");
}

if ($action == 'delete_sections') {
    foreach($update as $k=>$v)
        if ($v['del'] == 'Y') cw_profile_fields_delete_section($k);
    cw_header_location("index.php?target=$target&mode=fields&js_tab=addition_section");
}

$membership_titles = array();
$usertypes_array = array();
$usertypes_array[$user_type] = '';
if (in_array($user_type, array('C'))) {
    $customer_memberships = cw_user_get_memberships($user_type);
    if (is_array($customer_memberships))
    foreach($customer_memberships as $val) {
        $usertypes_array[$user_type.'_'.$val['membership_id']] = '';
        $membership_titles[$user_type.'_'.$val['membership_id']] = $val['membership'];
    }
}
$smarty->assign('membership_titles', $membership_titles);
$smarty->assign('customer_memberships', $customer_memberships);
$smarty->assign('customer_memberships_count', count($customer_memberships));

# kornev, sort and add the sections if required.
if ($mode == 'fields')
    list($sections, $fields, $additional_fields) = cw_profile_fields_get_sections('U', true, '', false, $edited_language);
else
    list($sections, $fields, $additional_fields) = cw_profile_fields_get_sections('U');

$smarty->assign('user_type', $user_type);

$smarty->assign('profile_sections', $sections);
$smarty->assign('profile_fields', $fields);
$smarty->assign('additional_fields', $additional_fields);

$smarty->assign('usertypes_array', $usertypes_array);
$smarty->assign('usertypes_array_count', count($usertypes_array));

$types = array(
	'T' => 'Text',
	'C' => 'Checkbox',
    'M' => 'Multiple Checkbox',
	'S' => 'Select box',
);

$smarty->assign('types', $types);

$smarty->assign('js_tab', $js_tab);

$location[] = array(cw_get_langvar_by_name('lbl_profile_fields'), '');
if ($mode == 'fields') {
    $location[] = array(cw_get_langvar_by_name('lbl_additional_fields'), '');
    $smarty->assign('main', 'user_profiles');
}
else {
    $location[] = array(cw_get_langvar_by_name('lbl_user_type_'.$user_type), '');
    $smarty->assign('main', 'options');
}
