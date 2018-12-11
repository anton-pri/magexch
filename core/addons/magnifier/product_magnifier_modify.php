<?php
$file_upload_data = &cw_session_register('file_upload_data');
cw_image_clear(array('magnifier_images'));

if ($action == 'product_zoomer') {
    cw_load('magnifier');

    foreach($file_upload_data['magnifier_images'] as $image) {
        $image_posted = cw_image_check_posted($image);
        if ($image_posted) {
            $image_id = cw_image_save($image, array('id' => $product_id));
            $image = cw_image_get('magnifier_images', $image_id);
            $dir_name = cw_magnifier_create($image['image_path'], $image_id);
            db_query("update $tables[magnifier_images] set image_path='$dir_name/TileGroup0/0-0-0.jpg' where image_id='$image_id'");
        }
    }

	$top_message['content'] = cw_get_langvar_by_name('msg_adm_images_added_4zoomer');
    cw_refresh($product_id, 'zoomer');
}

if ($action == "zoomer_update_availability" && !empty($zoomer_image)) {

	# Update images
	foreach ($zoomer_image as $key => $value) {
		db_query("UPDATE $tables[magnifier_images] SET orderby='".$value['orderby']."', avail='".$value['avail']."' WHERE image_id='$key'");
	}
	
	$top_message['content'] = cw_get_langvar_by_name("msg_adm_images_updated_4zoomer");
	cw_refresh($product_id, 'zoomer');

} 

if ($action == "product_zoomer_delete") {
    if (!empty($iids))
    foreach($iids as $image_id => $tmp)
        cw_image_delete($image_id, 'magnifier_images');

    $top_message['content'] = cw_get_langvar_by_name("msg_adm_images_deleted_4zoomer");
	cw_refresh($product_id, 'zoomer');
}

$smarty->assign('zoomer_images', cw_image_get_list('magnifier_images', $product_id));
?>
