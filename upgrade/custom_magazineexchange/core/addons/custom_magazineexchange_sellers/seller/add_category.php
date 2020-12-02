<?php
namespace cw\custom_magazineexchange_sellers;
cw_load('category', 'image');

if ($REQUEST_METHOD == "POST") {

	exit;
}

$master_category_data = cw_func_call('cw_category_get', array('cat' => $master_category));

if (defined('IS_AJAX') && $mode == 'add_category') {

    $cloned_category = [];
    if ($master_category_data) {

        $category_update = $master_category_data;
        $category_update['category'] = $new_category;

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

        //cw_call('cw_attributes_save', array('item_id' => $cat, 'item_type' => 'C', 'attributes' => $attributes, 'language' => $edited_language));

        $cloned_category = cw_func_call('cw_category_get', array('cat' => $cat));
    }

    //cw_log_add('clone_category', compact('master_category', 'new_category', 'master_category_data', 'cloned_category', 'attributes'));

    define('PREVENT_SESSION_SAVE', true);
    echo json_encode($cloned_category);
    exit();
}


$smarty->assign('current_main_dir',     'addons/' . addon_name);
$smarty->assign('master_category', $master_category_data);
//$smarty->assign('master_category', $master_category);

$smarty->assign('current_section_dir',  'seller');
$smarty->assign('main', 'seller_add_category');
$smarty->assign('home_style', 'iframe');

define('PREVENT_XML_OUT', true); // need simple HTML out if controller called as ajax via $.load()
