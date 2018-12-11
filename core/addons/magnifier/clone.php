<?php
cw_load("image");

if (!$addons['magnifier']) return;

$zoomer_images_old = cw_query("SELECT * from $tables[magnifier_images] WHERE id='".$product_id."'");
if (empty($zoomer_images_old))
	return;

foreach ($zoomer_images_old as $image_old) {
	$folder_with_images = cw_image_dir("Z").DIRECTORY_SEPARATOR.$product_id.DIRECTORY_SEPARATOR.$image_old['imageid'].DIRECTORY_SEPARATOR;
	cw_unset($image_old, "imageid");
	$image_old['id'] = $new_product_id;
	$new_imageid = cw_array2insert("magnifier_images", $image_old);
   
	$new_folder_with_images = cw_image_dir("Z").DIRECTORY_SEPARATOR.$new_product_id.DIRECTORY_SEPARATOR.$new_imageid.DIRECTORY_SEPARATOR;

	if (!file_exists($new_folder_with_images))
		cw_mkdir($new_folder_with_images);
   
	cw_magnifier_dircpy($folder_with_images, $new_folder_with_images);
}

?>
