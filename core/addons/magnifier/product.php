<?php
cw_load('image');
//$zoomer_images = cw_image_get_list('magnifier_images', $product_id, ($current_area == 'C'?1:0));
$smarty->assign('zoomer_images_count', cw_image_get_list_count('magnifier_images', $product_id, ($current_area == 'C'?1:0)));
?>
