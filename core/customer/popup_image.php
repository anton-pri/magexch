<?php
cw_load('files', 'image');

if(!isset($available_images[$type])) cw_close_window();

$images = cw_query($sql="SELECT ".(($available_images[$type] == "U") ? "id" : "image_id")." as id, image_path, image_x, image_y, image_size, alt FROM ".$tables[$type]." WHERE id = '$id' AND avail = 1 ORDER BY orderby");

if (empty($images)) cw_close_window();

$navigation = cw_core_get_navigation($target, count($total_items), $page);
$navigation['script'] = "index.php?target=popup_image&type=$type&id=$id&title=".urlencode($title);
$smarty->assign('navigation', $navigation);

$max_x = 0;
// get max height
foreach ($images as $k => $v) {
    $images[$k] = cw_image_info($type, $v);

	if ($images[$k]['image_x'] > $max_x) {
		$max_x = $images[$k]['image_x'];
	}
}

if (!empty($title))
	$smarty->assign('title', $title);

$smarty->assign('max_x', $max_x);
$smarty->assign('images_count', count($images));
$smarty->assign('images', $images);
$smarty->assign('id', $id);
$smarty->assign('type', $type);
$smarty->assign('area', $area);

$smarty->assign('body_onload', 'changeImg(0);');

$location = array();
$smarty->assign('home_style', 'popup');
$smarty->assign('current_section_dir', 'images');
$smarty->assign('main', 'popup_image');
?>
