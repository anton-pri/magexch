<?php
function smarty_function_select_categories($params, &$smarty) {
	extract($params);
    
	if (empty($assign)) {
		$smarty->trigger_error("assign: missing 'assign' parameter");
		return;
	}

    if ($remove_root && !$current_category_id) return false;
    cw_load('category','image');

    if ($remove_root) {
        $path = cw_category_get_path($current_category_id);
        $category_id = array_shift($path);
    }

    $categories = cw_category_get_subcategories($category_id, $current_category_id);
    if ($params['images']){
    foreach ($categories as $k=>$v) {
     $categories[$k]['image'] = cw_image_get('categories_images_thumb', $v['category_id']);
    }
    }
    $smarty->assign($assign, $categories);
}
?>
