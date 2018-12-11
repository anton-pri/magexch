<?php
global $action, $mode, $current_area;

cw_load('image');

$file_upload_data = &cw_session_register('file_upload_data');
cw_image_clear(array('customers_images'));

if ($action == 'delete_photos' && !empty($user_image_id)) {
    cw_image_delete($user_image_id, 'customers_images');
}

if ($action == 'customer_images') {
    if (cw_image_check_posted($file_upload_data['customers_images'])) {
        $images = cw_image_get_list('customers_images', $user);

        if (is_array($images)) {
            foreach ($images as $image) {
                cw_image_delete($image['image_id'], 'customers_images');
            }
        }
        cw_image_save($file_upload_data['customers_images'], array('id' => $user));
    }
}

if ($current_area == 'C') {
    cw_header_location("index.php?target=$target");
}
else {
    cw_header_location("index.php?target=$target&mode=modify&user=$user");
}
