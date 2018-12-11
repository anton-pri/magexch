<?php
cw_load('in_images');
$file_upload_data = &cw_session_register('file_upload_data', array());
cw_image_clear(array('webmaster_images'));

if ($action) 
    list($action, $id) = explode('_', $action, 2);

if ($action == 'delete') {
    cw_image_delete($id, 'webmaster_images');
    cw_header_location('index.php?target='.$target);
}

if ($action == 'update') {
    if (is_array($file_upload_data['webmaster_images']))
    foreach($file_upload_data['webmaster_images'] as $image) {
        if (cw_image_check_posted($image))
            cw_image_save($image);
    }
    
    if (is_array($image_data))
    foreach($image_data as $key => $val)
        cw_array2update('webmaster_images', $val, "id='$key'");
    cw_header_location('index.php?target='.$target);
}

$images_to_upload = cw_in_images_get_list();
foreach($images_to_upload as $key=>$val) {
    $image_id = cw_in_images_get_id($key);
    $in_images[$val['name']] = cw_in_images_get_image_info($image_id, $val['name']);
}

$smarty->assign('in_images', $in_images);

$location[] = array(cw_get_langvar_by_name('lbl_special_images'), '');
$smarty->assign('current_section_dir','webmaster');
$smarty->assign('main', 'special_images');
