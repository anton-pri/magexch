<?php
cw_load('image');
# Collect product images
$images = cw_image_get_list('products_detailed_images', $product_id, ($current_area == 'C'?1:0));
if (isset($product_info['image_det']['image_id'])) {
	array_unshift($images, $product_info['image_det']);
}

// Integration with product_options addon
// DPI addon must be called after PO, pay attention to orderby of these addons
if (isset($variants) && is_array($variants)) {
	foreach ($variants as $vid => $v) {
        if (!isset($v['image']['image_id'])) continue;
		$v['image']['variant_id'] = $vid;
		$images[] = $v['image'];
	}
}

$smarty->assign('images', $images);

$max_x = 0;
$max_y = 0;
// get max size for window
if (is_array($images)) {
	
	foreach ($images as $image) {

		if ($image['image_x'] > $max_x) {
			$max_x = $image['image_x'];
		}

		if ($image['image_y'] > $max_y) {
			$max_y = $image['image_y'];
		}
	}
}
$smarty->assign('max_x', $max_x);
$smarty->assign('max_y', $max_y);

$_addon = 'detailed_product_images';
require_once $app_main_dir . "/addons/$_addon/func.php";
$smarty->assign('viewers_exist', cw_dpi_check_viewers($_addon));
$smarty->assign('available_images', $available_images);

?>
