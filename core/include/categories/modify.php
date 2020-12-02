<?php
cw_load('category', 'image', 'user', 'group_edit', 'attributes');
$saved_category = &cw_session_register('saved_category');
$file_upload_data = &cw_session_register('file_upload_data');
$top_message = &cw_session_register('top_message', array());

cw_image_clear(array('categories_images_thumb'));

if ($ge_id && cw_group_edit_count($ge_id) == 0)
    $ge_id = false;

if ($action == 'update') {
    $rules = array(
        'category' => '',
    );
    $category_update['attributes'] = $attributes;
    $fillerror = cw_error_check($category_update, $rules, 'C');

    if ($fillerror) {
        $top_message = array('content' => $fillerror, 'type' => 'E');
        $saved_category = $category_update;
		if ($file_upload_data['categories_images_thumb']) {
			$file_upload_data['categories_images_thumb']['is_redirect'] = false;
			$saved_category['image'] = $file_upload_data['categories_images_thumb'];
		}
        cw_header_location("index.php?target=$target&mode=$mode&cat=$cat&ge_id=$ge_id");
    }
    if ($mode == 'add') {
        $cat = cw_array2insert('categories', array('parent_id' => $cat));
        cw_category_update_path($cat);
    }
 
    $update_fields = array('category', 'description', 'featured', 'order_by', 'short_list'); 
	array_push($update_fields, 'meta_descr', 'meta_keywords');
	
    if ($edited_language != $config['default_admin_language'])
        cw_unset($update_fields, 'category', 'description');

    cw_array2update('categories', $category_update, "category_id='$cat'", $update_fields);
    cw_category_update_status($cat, $category_update['status']);
    cw_category_update_path($cat);
    cw_membership_update('categories', $cat, $category_update['membership_ids'], 'category_id');

    $category_lng = array();
    $category_lng['code'] = $edited_language;
    $category_lng['category_id'] = $cat;
    $category_lng['category'] = $category_update['category'];
    $category_lng['description'] = $category_update['description'];
    cw_array2insert('categories_lng', $category_lng, true, array('code', 'category_id', 'category', 'description'));

    if (cw_image_check_posted($file_upload_data['categories_images_thumb']))
        cw_image_save($file_upload_data['categories_images_thumb']);

    $parent_categories = cw_category_get_path($cat);
	if (is_array($parent_categories))
	    cw_recalc_subcat_count($parent_categories);


    cw_func_call('cw_items_attribute_classes_save',
            array('item_id' => $cat, 'attribute_class_ids' => $category_update['attribute_class_ids'], 'item_type' => 'C'));

    if ($replicate_attribute_classes == "Y") {
        $child_subcategories = cw_func_call('cw_category_get_subcategory_ids', array('cat' => $cat));
        if (is_array($child_subcategories))  
            foreach ($child_subcategories as $subcatid) 
                cw_func_call('cw_items_attribute_classes_save',
                array('item_id' => $subcatid, 'attribute_class_ids' => $category_update['attribute_class_ids'], 'item_type' => 'C'));
    }

    cw_call('cw_attributes_save', array('item_id' => $cat, 'item_type' => 'C', 'attributes' => $attributes, 'language' => $edited_language));

    cw_group_edit_update_category($ge_id, $cat, $fields, $category_update);

    if ($mode == 'add')
        $top_message = array('content' => cw_get_langvar_by_name('msg_adm_category_add'), 'type' => 'I');
    else
        $top_message = array('content' => cw_get_langvar_by_name('msg_adm_category_upd'), 'type' => 'I');
    cw_header_location("index.php?target=$target&mode=edit&cat=$cat&ge_id=$ge_id");
}

if ($action == 'clone' && !empty($cat)) {
    $master_category = $cat;

    $master_category_data = cw_func_call('cw_category_get', array('cat' => $master_category));

    $cloned_category = [];
    if ($master_category_data) {

        $category_update = $master_category_data;
        $category_update['category'] = $cloned_name;

        $edited_language = $config['default_admin_language'];

        $cat = cw_array2insert('categories', array('parent_id' => $category_update['parent_id']));
        $category_update['category_id'] = $cat;
        cw_category_update_path($cat);

        $update_fields = array('category', 'description', 'featured', 'order_by', 'short_list'); 
        array_push($update_fields, 'meta_descr', 'meta_keywords');

        cw_array2update('categories', $category_update, "category_id='$cat'", $update_fields);        
        cw_category_update_status($cat, $category_update['status']);
        cw_category_update_path($cat);

        cw_membership_update('categories', $cat, [0 => 0], 'category_id');

        $category_lng = array();
        $category_lng['code'] = $edited_language;
        $category_lng['category_id'] = $cat;
        $category_lng['category'] = $category_update['category'];
        $category_lng['description'] = $category_update['description'];
        cw_array2insert('categories_lng', $category_lng, true, array('code', 'category_id', 'category', 'description'));

        cw_func_call(
            'cw_items_attribute_classes_save', 
            array(
                'item_id' => $cat, 
                'attribute_class_ids' => $category_update['attribute_class_ids'], 
                'item_type' => 'C'
            )
        );

        db_query(
            "REPLACE 
                INTO $tables[attributes_values] (item_id, attribute_id, value, code, item_type) 
            SELECT 
                $cat, av.attribute_id, av.value, av.code, av.item_type 
            FROM 
                $tables[attributes_values] av 
            INNER JOIN 
                $tables[attributes] a ON a.attribute_id = av.attribute_id AND (a.addon!='clean_urls' OR a.field!='clean_url')    
            WHERE av.item_id = '$master_category' AND av.item_type='C'");

        db_query(
            "REPLACE 
                INTO $tables[attributes_values] (item_id, attribute_id, value, code, item_type) 
            SELECT 
                $cat, av.attribute_id, CONCAT(av.value,'-1'), av.code, av.item_type 
            FROM 
                $tables[attributes_values] av 
            INNER JOIN 
                $tables[attributes] a ON a.attribute_id = av.attribute_id AND (a.addon='clean_urls' AND a.field='clean_url')    
            WHERE av.item_id = '$master_category' AND av.item_type='C'");

        $attributes = cw_func_call('cw_attributes_get', array('item_id' => $cat, 'item_type' => 'C', 'prefilled' => $attributes, 'language' => $edited_language));

        $cloned_category = cw_func_call('cw_category_get', array('cat' => $cat));
    }

    $top_message = array('content' => cw_get_langvar_by_name('msg_adm_category_clone'), 'type' => 'I');

    cw_header_location("index.php?target=$target&mode=edit&cat=$cat&ge_id=$ge_id");

}

if ($action == 'move' && !empty($cat) && $cat_location != $cat) {


    $cat_ids = array();
    array_push($cat_ids , $cat);
    if($fields['category_location']){
        while($id = cw_group_edit_each($ge_id, 1, $cat))
            array_push($cat_ids , $id);
    }
    if(!in_array($cat_location, $cat_ids)){
        foreach($cat_ids as $id){
            // Get all affected categories - parent and children
            $parent_id = cw_query_first_cell("select parent_id from $tables[categories] where category_id='$id'");
            $subcats = cw_category_get_subcategory_ids($id);

            db_query("update $tables[categories] set parent_id='$cat_location' where category_id='$id'");
            cw_category_update_path($id);
            if (count($subcats)) {
                foreach($subcats as $scat_id)
                    cw_category_update_path($scat_id);
            }
            $path = array($parent_id, $cat_location);

            cw_recalc_subcat_count($path);
        }
        $top_message = array('content' => cw_get_langvar_by_name('msg_adm_category_move'), 'type' => 'I');
        cw_header_location("index.php?target=$target&mode=edit&cat=$cat&ge_id=$ge_id");
    }
}

if ($action == "delete_icon" && !empty($cat)) {
	cw_image_delete($cat, 'categories_images_thumb');
	if ($ge_id && $fields['image'])
	while ($id = cw_group_edit_each($ge_id, 100, $cat))
	    cw_image_delete($id, 'categories_images_thumb');

	$top_message = array('content' => cw_get_langvar_by_name('msg_adm_category_icon_del'), 'type' => 'I');
	cw_header_location("index.php?target=$target&mode=$mode&cat=$cat&ge_id=$ge_id");
}

$smarty->assign('memberships', cw_user_get_memberships(array('C', 'R')));
$current_category = cw_func_call('cw_category_get', array('cat' => $cat, 'from_category' => 0, 'location_target' => '', 'lang' => $edited_language));

if ($mode == 'add') {
    $current_category['category'] = '';
    $current_category['image'] = array();
}


if ($saved_category) {
    if (!is_array($current_category)) $current_category = array();
    $current_category = array_merge($current_category, $saved_category);
    cw_session_unregister('saved_category');
}
if ($mode == 'add' && !$current_category['memberships']) $current_category['membership_ids'] = unserialize($config['category_settings']['default_category_memberships']);

if (!empty($ge_id)) {
    $total_items = cw_group_edit_count($ge_id);

    $navigation = cw_core_get_navigation($target, $total_items, $page);
    $navigation['script'] = 'index.php?target='.$target.'&mode=edit&cat_id='.$cat_id.$redirect_ge_id;
    $smarty->assign('navigation', $navigation);

	$smarty->assign('categories', cw_query("select $tables[group_editing].obj_id, $tables[categories].category, $tables[categories].category_id from $tables[categories], $tables[group_editing] WHERE $tables[categories].category_id = $tables[group_editing].obj_id AND $tables[group_editing].ge_id = '$ge_id' LIMIT $navigation[first_page], $navigation[objects_per_page]"));
    $smarty->assign('ge_id', $ge_id);
}

$location[] = array(cw_get_langvar_by_name('lbl_categories'), 'index.php?target='.$target);
if ($mode == 'add')
    $location[] = array(cw_get_langvar_by_name('lbl_add_category'), '');
else {
    $location[] = array(cw_get_langvar_by_name('lbl_modify_category'), '');
    $location[] = array($current_category['category'], '');
}

if ($mode == 'add') {
    $attributes = cw_func_call('cw_attributes_get', array('item_id' => 0, 'item_type' => 'C', 'prefilled' => $attributes, 'language' => $edited_language));
} else {
    $attributes = cw_func_call('cw_attributes_get', array('item_id' => $cat, 'item_type' => 'C', 'prefilled' => $attributes, 'language' => $edited_language));
}

$smarty->assign('cw_group_edit_count', cw_group_edit_count($ge_id));

$smarty->assign('attributes', $attributes);

$smarty->assign('current_category', $current_category);
$smarty->assign('category_location', cw_category_get_location($cat, 'categories', 1));

$smarty->assign('cat', $cat);
$smarty->assign('js_tab', $js_tab);

$smarty->assign('main', 'category_modify');
$smarty->assign('mode', $mode);
