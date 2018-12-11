<?php
cw_load('attributes', 'image');

$attribute_modified_data = &cw_session_register('attribute_modified_data');
$attribute_class_modified_data = &cw_session_register('attribute_class_modified_data');
$top_message = &cw_session_register('top_message');
$file_upload_data = &cw_session_register('file_upload_data');
$search_data = &cw_session_register('search_data', array());

cw_image_clear(array('attributes_images'));

if ($action == 'modify_att') {

    $attribute = null;
    if ($posted_data['attribute_id'])
        $attribute = cw_func_call('cw_attributes_get_attribute', array('attribute_id' => $posted_data['attribute_id'], 'language' => $edited_language));

    foreach($attribute as $field => $value)
       if (!is_array($value) && !isset($posted_data[$field])) $posted_data[$field] = addslashes($value);

    if ($attribute['protection'] & ATTR_PROTECTION_FIELD) {
		$posted_data['field'] = $attribute['field'];
    }
    if ($attribute['protection'] & ATTR_PROTECTION_VALUES) {
		if ($attribute['default_value'])
			foreach($attribute['default_value'] as $k=>$v) {
				$posted_data['default_value'][$k] = $v;
			}		
		// Protection of values means protection of type too
		$attribute['protection'] = $attribute['protection'] | ATTR_PROTECTION_TYPE;
    }
     
    if ($attribute['protection'] & ATTR_PROTECTION_TYPE) {
		$posted_data['type'] = $attribute['type'];
    }
 
	if (empty($posted_data['item_type'])) {
		$posted_data['item_type'] = 'P';
	}
	
    $rules = array(
        'field' => 'cw_attributes_check_code',
        'name' => '',
    );
    $fillerror = cw_error_check($posted_data, $rules);
    if (!$fillerror) {
        $attribute_modified_data = '';
     
		if (!($attribute['protection'] & ATTR_PROTECTION_VALUES)) {
        if ($posted_data['type'] == 'selectbox') {
            if (is_array($posted_data['default_values_select']))
                foreach($posted_data['default_values_select'] as $k=>$v)
                    if (!empty($v)){
                        $value_key = ( is_numeric($posted_data['default_values_select_from'][$k]) && is_numeric($posted_data['default_values_select_to'][$k]) ? $posted_data['default_values_select_from'][$k].'-'.$posted_data['default_values_select_to'][$k] : '');
                        $posted_data['default_value'][] = array(
                            'attribute_value_id' => $posted_data['default_values_select_id'][$k],
                            'value' => $v,
                            'value_key' =>$value_key,
                            'is_default' => $k == $posted_data['default_values_select_is_default'],
                            'image_id' => $posted_data['default_select_images'][$k],
                            'pf_image_id' => $posted_data['default_select_pf_images'][$k],
                            'active' => $posted_data['default_values_select_active'][$k],
                            'orderby' => $posted_data['default_values_select_orderby'][$k],
                            'facet' => $posted_data['default_values_select_facet'][$k],
                            'description' => addslashes(html_entity_decode($posted_data['default_values_select_description'][$k], ENT_QUOTES)),
                        );
                    }
        }
        elseif ($posted_data['type'] == 'multiple_selectbox') {
            if (is_array($posted_data['default_values_multiselect']))
                foreach($posted_data['default_values_multiselect'] as $k=>$v)
                    if (!empty($v))
                        $posted_data['default_value'][] = array(
                            'attribute_value_id' => $posted_data['default_values_multiselect_id'][$k],
                            'value' => $v,
                            'is_default' => $posted_data['default_values_multiselect_is_default'][$k],
                            'image_id' => $posted_data['default_multiselect_images'][$k],
                            'pf_image_id' => $posted_data['default_multiselect_pf_images'][$k],
                            'active' => $posted_data['default_values_multiselect_active'][$k],
                            'orderby' => $posted_data['default_values_multiselect_orderby'][$k],
                            'facet' => $posted_data['default_values_multiselect_facet'][$k],
                            'description' => addslashes(html_entity_decode($posted_data['default_values_multiselect_description'][$k], ENT_QUOTES)),
                        );
        }
        elseif ($posted_data['type'] == 'date') {
            $posted_data['default_value'] = mktime(0, 0, 0, $default_value_dateMonth, $default_value_dateDay, $default_value_dateYear);
			$posted_data['default_values']['facet'] = $posted_data['default_value_date_facet'];
			$posted_data['default_values']['description'] = $posted_data['default_value_date_description'];
		} elseif ($posted_data['type'] == 'decimal') {
            $posted_data['default_value'] = floatval($posted_data['default_value_text']);
			$posted_data['default_values']['facet'] = $posted_data['default_value_text_facet'];
			$posted_data['default_values']['description'] = $posted_data['default_value_text_description'];
		} elseif ($posted_data['type'] == 'integer') {
            $posted_data['default_value'] = intval($posted_data['default_value_text']);
			$posted_data['default_values']['facet'] = $posted_data['default_value_text_facet'];
			$posted_data['default_values']['description'] = $posted_data['default_value_text_description'];
		} elseif ($posted_data['type'] == 'yes_no') {
            $posted_data['default_value'] = $posted_data['default_value_yes_no'];
            $posted_data['default_values']['facet'] = $posted_data['default_value_yes_no_facet'];
            $posted_data['default_values']['description'] = $posted_data['default_value_yes_no_description'];
		} else {
            $posted_data['default_value'] = $posted_data['default_value_text'];
			$posted_data['default_values']['facet'] = $posted_data['default_value_text_facet'];
			$posted_data['default_values']['description'] = $posted_data['default_value_text_description'];
		}
		}

		$attribute_id = cw_func_call('cw_attributes_create_attribute', array('attribute_id' => $posted_data['attribute_id'], 'data'=> $posted_data, 'language' => $edited_language));
    }
    else {
        $top_message = array('content' => $fillerror, 'type' => 'E');
        $attribute_modified_data = $posted_data;
    }

    cw_header_location('index.php?target='.$target.'&mode=att&attribute_id='.$attribute_id);
}

if ($action == 'product_filter') {
	$posted_data['facet'] = $posted_data['facet'];
	cw_array2update('attributes', $posted_data, "attribute_id='$attribute_id'");
    cw_header_location('index.php?target='.$target.'&mode=att&attribute_id='.$attribute_id);
}

if ($action == 'update_att' && is_array($posted_data)) {
    foreach($posted_data as $attribute_id => $data)
        cw_array2update('attributes', $data, "attribute_id='$attribute_id'");
    cw_header_location('index.php?target='.$target.'&mode=att');
}

if ($action == 'delete_att' && is_array($to_delete)) {

    foreach($to_delete as $attribute_id => $tmp)
        cw_call('cw_attributes_delete', array($attribute_id));

    cw_add_top_message('Attribute has been deleted');

    cw_header_location('index.php?target='.$target.'&mode=att');
}

if ($action == 'update_class' && is_array($posted_data)) {
    foreach($posted_data as $attribute_class_id => $data)
        cw_array2update('attributes_classes', $data, "attribute_class_id='$attribute_class_id'");
    if ($is_default) cw_func_call('cw_attributes_set_default_class', array('attribute_class_id' => $is_default));
    cw_header_location('index.php?target='.$target);
}

if ($action == 'modify_class') {

    $rules = array(
        'name' => '',
    );
    $fillerror = cw_error_check($posted_data, $rules);

    if (!$fillerror) {
        $attribute_class_modified_data = '';
        $attribute_class_id = cw_array2insert('attributes_classes', $posted_data, 1);
        if ($posted_data['is_default'])
            cw_func_call('cw_attributes_set_default_class', array('attribute_class_id' => $attribute_class_id));
        db_query("delete from $tables[attributes_classes_assignement] where attribute_class_id='$attribute_class_id'");
        if ($posted_data['attributes'])
            foreach($posted_data['attributes'] as $attribute_id)
                if ($attribute_id) cw_array2insert('attributes_classes_assignement', $arr = array('attribute_class_id' => $attribute_class_id, 'attribute_id' => $attribute_id), 1);
    }
    else {
        $top_message = array('content' => $fillerror, 'type' => 'E');
        $attribute_class_modified_data = $posted_data;
    }

    cw_header_location('index.php?target='.$target.'&attribute_class_id='.$attribute_class_id);
}

if ($action == 'delete_class' && is_array($to_delete)) {
    foreach($to_delete as $attribute_class_id => $tmp)
        cw_func_call('cw_attributes_class_delete', array('attribute_class_id' => $attribute_class_id));

    cw_header_location('index.php?target='.$target);
}

if ($action == 'images') {
    foreach($file_upload_data['attributes_images'] as $image) {
        $image_posted = cw_image_check_posted($image);
        if ($image_posted)
            cw_image_save($image, array('alt' => $alt, 'id' => $attribute_id));
    }
    $top_message = array('content' => cw_get_langvar_by_name('lbl_att_images_add'), 'type' => 'I');

    cw_header_location('index.php?target='.$target.'&mode=att&attribute_id='.$attribute_id);
}

if ($action == 'images_delete' && is_array($iids)) {
    foreach($iids as $image_id => $tmp)
        cw_image_delete($image_id, 'attributes_images', true);
    cw_header_location('index.php?target='.$target.'&mode=att&attribute_id='.$attribute_id);
}

if ($action == 'images_update' && !empty($image)) {
    foreach ($image as $key => $value)
        cw_array2update('attributes_images', $value, "image_id = '$key'");
    cw_header_location('index.php?target='.$target.'&mode=att&attribute_id='.$attribute_id);
}

if ($mode == 'att' && isset($attribute_id)) {
    $attribute = array();
    if ($attribute_id) $attribute = cw_func_call('cw_attributes_get_attribute', array('attribute_id' => $attribute_id, 'language' => $edited_language));
    if ($attribute_modified_data) {
        $attribute = array_merge($attribute, $attribute_modified_data);
        $attribute_modified_data = '';
    }
    $smarty->assign('attribute', $attribute);

    $images = cw_image_get_list('attributes_images', $attribute_id, 0);
    $smarty->assign('images', $images);

    $location[] = array(cw_get_langvar_by_name('lbl_attributes'), '');
    $smarty->assign('main', 'attribute');
}
elseif ($mode == 'att') {

    if ($sort)
        $search_data['attributes']['sort_field'] = $sort;
    if (isset($sort_direction))
        $search_data['attributes']['sort_direction'] = $sort_direction;
    if (!$search_data['attributes']['sort_field'])
        $search_data['attributes']['sort_field'] = 'name';

# kornev, addon attributes are hidden
# kornev, since the attributes are multi-lng now - we cannot hide it anymore
//    $search_data['attributes']['addon'] = '';

    if ($REQUEST_METHOD == "POST") {
        if ($action == 'reset' || !is_array($attribute_filter))
            unset($search_data['attributes']);
        else{
            $search_data['attributes']['item_type'] = $attribute_filter['item_type'];
            $search_data['attributes']['addon'] = $attribute_filter['addon'];
            $search_data['attributes']['class'] = $attribute_filter['class'];
            $search_data['attributes']['page'] = 1;
        }
        cw_header_location("index.php?target=$target&mode=att");
    }
    if(isset($search_data['attributes']['addon']['custom_attribute'])){
        $search_data['attributes']['addon']['custom_attribute'] = '';
    }

    $search_data['attributes']['page'] = $page;
    list($attributes, $navigation) = cw_func_call('cw_attributes_search', array('data' => $search_data['attributes'], 'language' => $edited_language));
    $navigation['script'] = 'index.php?target='.$target.'&mode=att';

    $attribute_filter['item_type'] = cw_query("SELECT DISTINCT item_type FROM $tables[attributes]");
    foreach($attribute_filter['item_type'] AS $k => $v){
        $attribute_filter['item_type'][$k]['name'] = cw_get_langvar_by_name('attribute_name_'.strtolower($v['item_type']), '', false, true);
    }
    $language = !empty($current_language) ? $current_language : $config['default_admin_language'];
    $attribute_filter['addon'] = cw_query("SELECT DISTINCT a.addon, lng1.value FROM $tables[attributes] a left join $tables[languages] as lng1 ON lng1.code = '$language' and lng1.name = CONCAT('addon_name_', a.addon)");

    foreach($attribute_filter['addon'] AS $k => $v){
        if($v['addon'] == 'core')
           $attribute_filter['addon'][$k]['value'] =  cw_get_langvar_by_name('lbl_core', '', false, true);

        if($v['addon'] == ""){
            $attribute_filter['addon'][$k]['value'] = cw_get_langvar_by_name('lbl_custom_attribute', '', false, true);
            $attribute_filter['addon'][$k]['addon'] = "custom_attribute";
        }
    }
    $attribute_filter['classes'] = cw_query("SELECT DISTINCT ac.attribute_class_id, ac.name FROM $tables[attributes_classes] ac");
    array_push($attribute_filter['classes'], array('attribute_class_id' => 0, 'name'=> cw_get_langvar_by_name('lbl_unassigned', '', false, true)));

    $smarty->assign('navigation', $navigation);
    $smarty->assign('attributes', $attributes);
    $smarty->assign('attribute_filter', $attribute_filter);
    $smarty->assign('search_prefilled', $search_data['attributes']);

    $location[] = array(cw_get_langvar_by_name('lbl_attributes'), '');

    $smarty->assign('main', 'attributes');
}
elseif (isset($attribute_class_id))  {
    $attribute_class = array();
    if ($attribute_class_id) $attribute_class = cw_func_call('cw_attributes_get_class', array('attribute_class_id' => $attribute_class_id));
    if ($attribute_class_modified_data) {
        $attribute_class = array_merge($attribute_class, $attribute_class_modified_data);
        $attribute_class_modified_data = '';
    }
    $smarty->assign('attribute_class', $attribute_class);

    $location[] = array(cw_get_langvar_by_name('lbl_attributes_classes'), '');
    $smarty->assign('main', 'class');
}
else {
    if ($sort)
        $search_data['attributes_classes']['sort_field'] = $sort;
    if (isset($sort_direction))
        $search_data['attributes_classes']['sort_direction'] = $sort_direction;
    if (!$search_data['attributes_classes']['sort_field'])
        $search_data['attributes_classes']['sort_field'] = 'name';

    $search_data['attributes_classes']['page'] = $page;
    list($attributes_classes, $navigation) = cw_func_call('cw_attributes_classes_search', array('data' => $search_data['attributes_classes']));
    $navigation['script'] = 'index.php?target='.$target;

    $smarty->assign('navigation', $navigation);
    $smarty->assign('attributes_classes', $attributes_classes);
    $smarty->assign('search_prefilled', $search_data['attributes_classes']);

    $location[] = array(cw_get_langvar_by_name('lbl_attributes_classes'), '');
    $smarty->assign('main', 'classes');
}

$smarty->assign('current_section_dir', 'attributes');
