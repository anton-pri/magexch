<?php
cw_load('attributes','image');

$attribute = array();
if ($attribute_id) {
	$attribute = cw_func_call('cw_attributes_get_attribute', array('attribute_id' => $attribute_id, 'language' => $edited_language));
}
if ($attribute_modified_data) {
    $attribute = array_merge($attribute, $attribute_modified_data);
    $attribute_modified_data = '';
}


if ($REQUEST_METHOD == "POST" && $action == "modify_att_options") {
		if ($attribute['type'] == 'multiple_selectbox') {
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
        } else {
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
        $existing = cw_query_key("select attribute_value_id from $tables[attributes_default] where attribute_id='$attribute_id'");
        if (is_array($posted_data['default_value']))
            foreach($posted_data['default_value'] as $v) {
                cw_call('cw_attributes_update_default_value', array('attribute_id' => $attribute_id, 'data' => $v, 'language' => $edited_language));
                if ($v['attribute_value_id']) unset($existing[$v['attribute_value_id']]);
            }
        if (count($existing))
            cw_call('cw_attributes_delete_values', array(array_keys($existing)));

        cw_header_location('index.php?target=attribute_options&attribute_id='.$attribute_id);
}

$images = cw_image_get_list('attributes_images', $attribute_id, 0);
$smarty->assign('images', $images);
    
$smarty->assign('attribute', $attribute);

$smarty->assign('current_section_dir', 'attributes');
$smarty->assign('main', 'attribute_options');
