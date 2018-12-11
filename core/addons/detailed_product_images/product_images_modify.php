<?php
require_once $app_main_dir . '/addons/detailed_product_images/func.php';

cw_image_clear(array('products_detailed_images'));
global $file_upload_data, $product_id, $top_message, $image, $iids, $action, $image_id, $ge_id, $fields;

if ($action == 'product_images' && is_array($file_upload_data['products_detailed_images'])) {
    foreach($file_upload_data['products_detailed_images'] as $image) {
    	$image_posted = cw_image_check_posted($image);
	    if ($image_posted) {
		    $image_id = cw_image_save($image, array('alt' => $alt, 'id' => $product_id));

    		if ($ge_id && $fields['new_d_image']) {
                $data = cw_query_first("select * from $tables[products_detailed_images] where id = '$product_id' AND image_id = '$image_id'");
    			unset($data['image_id']);
    			$data = cw_array_map("addslashes", $data);
	    		while($pid = cw_group_edit_each($ge_id, 1, $product_id)) {
		    		$id = cw_query_first_cell("select image_id FROM $tables[products_detailed_images] WHERE id = '$pid' AND md5 = '$data[md5]'");
    				if (!empty($id))
	    				cw_image_delete($id, 'products_detailed_images', true);
    				$data['id'] = $pid;
	    			cw_array2insert("products_detailed_images", $data);
		    	}
    		}
        }
    }
    $top_message = array('content' => cw_get_langvar_by_name('msg_adm_product_images_add'), 'type' => 'I');
	cw_dpi_refresh($product_id, 'dpi');
} 

$image = $_POST['image'];

if ($action == 'update_availability' && !empty($image)) {
	foreach ($image as $key => $value) {
		cw_array2update("products_detailed_images", $value, "image_id = '$key'");
		if($ge_id && $fields['d_image'][$key]) {
			$data = cw_query_first("select * from $tables[products_detailed_images] where image_id = '$key'");
			unset($data['image_id']);
			$data = cw_array_map("addslashes", $data);
			while($pid = cw_group_edit_each($ge_id, 1, $product_id)) {
				$id = cw_query_first_cell("SELECT image_id FROM $tables[products_detailed_images] WHERE id = '$pid' AND md5 = '$data[md5]'");
				if (!empty($id))
					cw_image_delete($id, "D", true);
				$data['id'] = $pid;
				cw_array2insert("products_detailed_images", $data);
			}
		}
	}
	$top_message = array('content' => cw_get_langvar_by_name('msg_adm_product_images_upd'), 'type' => 'I');
	cw_dpi_refresh($product_id, 'dpi');
}

if ($action == 'product_images_delete' && is_array($iids)) {
    foreach($iids as $image_id => $tmp) {
        $md5 = cw_query_first_cell("SELECT md5 FROM $tables[products_detailed_images] WHERE image_id = '$image_id'");
        cw_image_delete($image_id, 'products_detailed_images', true);
        if ($ge_id && $fields['d_image'][$image_id])
        while($pid = cw_group_edit_each($ge_id, 1, $product_id)) {
                $id = cw_query_first_cell("SELECT image_id FROM $tables[products_detailed_images] WHERE id = '$pid' AND md5 = '$md5'");
				if (!empty($id)) cw_image_delete($id, "D", true);
        }
    }

    $top_message = array('content' => cw_get_langvar_by_name('msg_adm_product_images_del'), 'type' => 'I');
	cw_dpi_refresh($product_id, 'dpi');
}
?>
