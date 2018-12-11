<?php
function cw_profile_fields_get_area($user_id, $possible_membership = 0, $self = 0, $area = null, $force_mem_id = false) {
    global $tables, $customer_id;

    if ($user_id) $ret = cw_query_first_cell("select usertype from $tables[customers] where customer_id='$user_id'"); // TODO: \Customer\get($user_id)
    elseif($area) $ret = $area;
    else $ret = AREA_TYPE;

    if (in_array($ret, array('C', 'R'))) {
        if (!$possible_membership && !$force_mem_id)
            $possible_membership = cw_query_first_cell("select membership_id from $tables[memberships] where default_membership='Y' and area='$ret'");

        if (!$user_id && $possible_membership)
            $memid = cw_query_first_cell("select membership_id from $tables[memberships] where membership_id='$possible_membership' and area='$ret'");
        elseif($user_id)
            $memid = cw_query_first_cell("select m.membership_id from $tables[customers] as c, $tables[memberships] as m where m.membership_id=c.membership_id and m.area='$ret' and c.customer_id='$user_id'");

        if ($memid) $ret .= "_".$memid;
    }
    if ($customer_id == $user_id) $self = 1;
    if ($self) $ret = '#'.$ret;

    return $ret;
}

function cw_profile_fields_get_sections($type = 'U', $fields = true, $avail_for = '', $with_values_for_customer_id = false, $language = '') {
    global $tables, $current_language;

    $language = $language?$language:$current_language;

    $sections = cw_query("select fs.*, IFNULL(fsl.name,fs.name) as name 
    from $tables[register_fields_sections] as fs 
    left join $tables[register_fields_sections_lng] as fsl on fs.section_id=fsl.section_id and fsl.code='$language' 
    left join $tables[addons] as m on m.addon=fs.name 
    where (m.addon is null or m.active=1) and type='$type' order by orderby");

    $return = array();
    $fields = array();
    $add_fields = array();
    if ($sections) {
        if ($avail_for) {
            $is_avail = cw_query_hash("select rf.section_id, count(*) as count 
            from $tables[register_fields] as rf, $tables[register_fields_avails] as rfa 
            where rfa.area='$avail_for' and rfa.is_avail=1 and rfa.field_id=rf.field_id 
            group by rf.section_id", 'section_id', false);
        }

        $sections_ids = array();
        foreach($sections as $k=>$val) {
            $val['section_title'] = $val['is_default']?cw_get_langvar_by_name('lbl_register_field_sections_'.$val['name'], null, $language):$val['name'];
            $val['id'] = $val['is_default']?$val['name']:'custom_section_'.$val['section_id'];
            $val['is_avail'] = (bool) $is_avail[$val['section_id']]['count'];
            $return[$val['id']] = $val;

            $sections_ids[] = $val['section_id'];
        }

        $section_ids_cond = '"'.implode('","', $sections_ids).'"';
        if ($with_values_for_customer_id !== false) {
# kornev, only custom fields
            $_tmp = cw_query("select rf.field_id, rf.type, rf.variants, rfv.value, rfa.is_avail, rfa.is_required, 
				ifnull(rfl.field, rf.field) as title, if(fs.is_default,fs.name,concat('custom_section_',fs.section_id)) as name 
            from $tables[register_fields_sections] as fs, $tables[register_fields] as rf 
            left join $tables[register_fields_lng] as rfl on rf.field_id=rfl.field_id and rfl.code='$language' 
            left join $tables[register_fields_values] as rfv on rfv.field_id=rf.field_id and customer_id = '$with_values_for_customer_id'
            left join $tables[register_fields_avails] as rfa on rfa.field_id=rf.field_id 
            where rfa.area='$avail_for' and rfa.is_avail=1 and fs.section_id=rf.section_id 
				and rf.section_id in ($section_ids_cond) and rf.type != 'D' order by rf.orderby");
            if ($_tmp)
            foreach($_tmp as $index=>$field) {
                $field['title'] = $field['type']=='D'?cw_get_langvar_by_name('lbl_'.$field['field'], null, $language):$field['title'];
                if (($field['type'] == 'S' || $field['type'] == 'M') && $field['variants']) {
					$field['variants_str'] = $field['variants'];
                    $field['variants'] = explode(';', $field['variants']);
				}
                if ($field['type'] == 'M')
                    $field['values'] = unserialize($field['value']);
                $fields[$field['name']][] = $field;
            }
        }
        elseif ($avail_for) {
            $_tmp = cw_query("select rf.*, rfa.is_avail, rfa.is_required, rf.section_id, fs.is_default, 
				ifnull(rfl.field, rf.field) as title, if(fs.is_default,fs.name,concat('custom_section_',fs.section_id)) as name 
            from $tables[register_fields_sections] as fs, $tables[register_fields] as rf 
            left join $tables[register_fields_lng] as rfl on rf.field_id=rfl.field_id and rfl.code='$language' 
            left join $tables[register_fields_avails] as rfa on rfa.field_id=rf.field_id and rfa.area='$avail_for' 
            where rfa.is_avail=1 and fs.section_id=rf.section_id and rf.section_id in ($section_ids_cond) order by rf.orderby");
            if ($_tmp)
            foreach($_tmp as $field) {
                $field['title'] = $field['type']=='D'?cw_get_langvar_by_name('lbl_'.$field['field'], null, $language):$field['title'];
                if (($field['type'] == 'S' || $field['type'] == 'M') && $field['variants']) {
					$field['variants_str'] = $field['variants'];
                    $field['variants'] = explode(';', $field['variants']);
				}
                $fields[$field['name']][$field['type'] == 'D'?$field['field']:$field['field_id']] = $field;
            }
        }
        else {
            $_tmp = cw_query($sql="select rf.*, ifnull(rfl.field, rf.field) as title, if(fs.is_default,fs.name,concat('custom_section_',fs.section_id)) as name 
            from $tables[register_fields] as rf 
            left join $tables[register_fields_lng] as rfl on rf.field_id=rfl.field_id and rfl.code='$language', 
            $tables[register_fields_sections] as fs 
            where fs.section_id=rf.section_id and rf.section_id in ($section_ids_cond) 
            order by rf.orderby");
            $additional_fields = array();
            if ($_tmp)
            foreach($_tmp as $index=>$field) {
                $field['title'] = $field['type']=='D'?cw_get_langvar_by_name('lbl_'.$field['field'], null, $language):$field['title'];
                if (($field['type'] == 'S' || $field['type'] == 'M') && $field['variants']) {
                 	$field['variants_str'] = $field['variants'];
					$field['variants'] = explode(';', $field['variants']);
				}
                $field['areas'] = cw_query_hash("select fa.area, IFNULL(ft.is_avail, fa.is_avail) as is_avail, 
					IFNULL(ft.is_required, fa.is_required) as is_required, IF(ft1.field_id is not null, IF(ft.field_id is not null, 0, 1), 0) as is_disabled 
				from $tables[register_fields_avails] as fa 
				left join $tables[register_fields_by_types] as ft on ft.field_id=fa.field_id and ft.area = LEFT(fa.area, 1) 
				left join $tables[register_fields_by_types] as ft1 on ft1.field_id=fa.field_id 
				where fa.field_id='$field[field_id]'", 'area', false);
                $fields[$field['name']][] = $field;
                if ($field['type'] != 'D')
                    $add_fields[$field['name']][] = $field;
            }
        }
    }

    return array($return, $fields, $add_fields);
}

function cw_profile_fields_get_additional($customer_id) {
    global $tables, $current_language;

    $area = cw_profile_fields_get_area($customer_id);

    return cw_profile_fields_get_sections('U', true, $area);
}

function cw_profile_fields_delete_section($section_id) {
    global $tables;

    $is_default = cw_query_first_cell("select is_default from $tables[register_fields_sections] where section_id='$section_id'");
    if (!$is_default) {
        db_query("delete from $tables[register_fields] WHERE section_id='$section_id'");
        db_query("delete from $tables[register_fields_sections] WHERE section_id='$section_id'");
        db_query("delete from $tables[register_fields_sections_lng] WHERE section_id='$section_id'");
    }
}

function cw_profile_fields_update_section($section_id, $values) {
    global $tables, $edited_language;

    $is_default = cw_query_first_cell("select is_default from $tables[register_fields_sections] where section_id='$section_id'");
    if ($is_default)
        $to_update = array(
            'orderby' => $values['orderby'],
        );
    else {
        $to_update = array(
            'orderby' => $values['orderby'],
            'is_default' => 0,
            'type' => 'U',
        );
        if ($config['default_admin_language'] != $edited_language) $to_update['name'] = $values['name'];

        cw_array2insert('register_fields_sections_lng', array('section_id' => $section_id, 'code' => $edited_language, 'name' => $values['name']), true);
    }

    cw_array2update('register_fields_sections', $to_update, "section_id='$section_id'");
}

function cw_profile_fields_add_section($values) {
    global $tables, $edited_language, $config;

    $to_insert = array(
        'orderby' => $values['orderby'],
        'name' => $values['name'],
        'is_default' => 0,
        'type' => 'U',
    );
    $section_id = cw_array2insert('register_fields_sections', $to_insert);
    cw_array2insert('register_fields_sections_lng', array('section_id' => $section_id, 'code' => $edited_language, 'name' => $values['name']), true);
}

function cw_profile_fields_update_field($field_id, $values) {
    global $tables, $edited_language, $config;

    $to_update = array(
        'section_id' => $values['section_id'],
        'orderby' => $values['orderby'],
        'type' => $values['type'],
        'variants' => $values['variants'],
        'def' => $values['def'],
        'field' => $values['field'],
    );

    cw_array2insert('register_fields_lng', array('field_id' => $field_id, 'code' => $edited_language, 'field' => $values['title']), true);
    cw_array2update('register_fields', $to_update, "field_id='$field_id'");
}

function cw_profile_fields_add_field($values) {
    global $tables, $edited_language;

    $to_insert = array(
        'section_id' => $values['section_id'],
        'orderby' => $values['orderby'],
        'type' => $values['type'],
        'variants' => ($values['type'] == 'S' || $values['type'] == 'M')?implode(";", array_filter(explode(";", $values['variants']), 'cw_func_callback_empty')):'',
        'def' => $values['def'],
        'field' => $values['field'],
    );

    $field_id = cw_array2insert('register_fields', $to_insert);
    cw_array2insert('register_fields_lng', array('field_id' => $field_id, 'code' => $edited_language, 'field' => $values['field']), true);
}

function cw_profile_fields_delete_field($field_id) {
    global $tables;

    db_query("delete from $tables[register_fields] where field_id = '$field_id'");
    db_query("delete from $tables[register_fields_lng] where field_id = '$field_id'");
    db_query("delete from $tables[register_fields_values] where field_id = '$field_id'");
}

function cw_profile_fields_get_section($section_id) {
    global $tables, $current_language;

    return cw_query_first("select fs.*, IFNULL(fsl.name,fs.name) as name from $tables[register_fields_sections] as fs left join $tables[register_fields_sections_lng] as fsl on fs.section_id=fsl.section_id and fsl.code='$current_language' where fs.section_id='$section_id'");
}

function cw_profile_fields_get_field($field_id) {
    global $tables, $current_language;

    return cw_query_first("select rf.*, ifnull(rfl.field, rf.field) as title from $tables[register_fields] as rf left join $tables[register_fields_lng] as rfl on rf.field_id=rfl.field_id and rfl.code='$current_language' where rf.field_id = '$field_id'");
}

function cw_profile_fields_get_field_by_name($field) {
    global $tables, $current_language;

    return cw_query_first("select rf.*, ifnull(rfl.field, rf.field) as title from $tables[register_fields] as rf left join $tables[register_fields_lng] as rfl on rf.field_id=rfl.field_id and rfl.code='$current_language' where rf.field = '$field'");
}

function cw_profile_fields_update_type($customer_id, $key_id, $key_type, $values) {
    global $tables;

    if (is_array($values))
    foreach($values as $field_id=>$val) {
        db_query("delete from $tables[register_fields_values] where key_id='$key_id' and key_type='$key_type' and field_id = '$field_id' and customer_id = '$customer_id'");
        cw_array2insert('register_fields_values', array('field_id' => $field_id, 'customer_id' => $customer_id, 'key_id' => $key_id, 'key_type' => $key_type, 'value' => $val));
    }
}

function cw_profile_field_updated_suspend_account($customer_id, $old_value, $new_value) {
    global $config, $smarty;

    if ($new_value == 'Y' && $new_value!=$old_value) { 

        $userinfo = cw_user_get_info($customer_id, 65535);
        $fields_area = cw_profile_fields_get_area($customer_id);
        list($profile_sections, $profile_fields, $additional_fields) = cw_profile_fields_get_sections('U', true, $fields_area);
        $smarty->assign('userinfo', $userinfo);
        $smarty->assign('profile_sections', $profile_sections);
        $smarty->assign('profile_fields', $profile_fields);
        $smarty->assign('additional_fields', $additional_fields);

        $smarty->assign('user_page_link', "index.php?target=user_C&mode=modify&user=$customer_id");

        cw_call('cw_send_mail', array($userinfo['email'], $config['Company']['users_department'], 'mail/users/customer_suspended_account_subj.tpl', 'mail/users/customer_suspended_account_admin.tpl', $config['default_admin_language']));
    }
}

?>
