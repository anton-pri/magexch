<?php
# [TOFIX]
# kornev, move to addon
if (!$addons['magnifier'])
	cw_close_window();

cw_load('image');
if ($image_id)
    $image = cw_image_get('magnifier_images', $image_id);
$product = cw_func_call('cw_product_get', array('id' => $product_id, 'user_account' => $user_account));
$zoomer_images = cw_image_get_list('magnifier_images', $product_id, ($current_area == 'C'?1:0));
if (!$image_id) $image = cw_image_get('magnifier_images', $zoomer_images[0]['image_id']);

if (!$product || !$image)
    cw_close_window();

$location[] = array($product['product'], '');
$location[] = array(cw_get_langvar_by_name('lbl_magnifier_image'), '');

$smarty->assign('images_count', count($zoomer_images));
if (is_array($zoomer_images))
foreach($zoomer_images as $k=>$v)
    $zoomer_images[$k]['image_path'] = dirname(dirname($v['tmbn_url']));
$smarty->assign('zoomer_images', $zoomer_images);
$smarty->assign('image_path', dirname(dirname($image['tmbn_url'])));
$smarty->assign('product_id', $product_id);

$smarty->assign('home_style', 'iframe');
$smarty->assign('current_main_dir', 'addons');
$smarty->assign('current_section_dir', 'magnifier');
$smarty->assign('main', 'product_magnifier');
?>
