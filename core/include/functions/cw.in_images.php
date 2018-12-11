<?php
cw_load('image');

function cw_in_images_get_list() {
    global $addons;

# kornev, type = 1 means multilanguage image
    $images_to_upload = array(
/*
    '4' => array(
        'name' => 'favicon',
        'type' => 0,
    ),
*/
    '4' => array(
        'name' => 'logo_admin',
        'type' => 0,
    ),
    '5' => array(
        'name' => 'logo',
        'type' => 0,
    ),
    '6' => array(
        'name' => 'logo_invoice',
        'type' => 0,
    ),
    '10' => array(
        'name' => 'main_top_image',
        'type' => 1,
        ),
    '20' => array(
        'name' => 'main_center_image',
        'type' => 1,
        ),
    '40' => array(
        'name' => 'super_deals',
        'type' => 0,
        ),
    );

    return $images_to_upload;
}

function cw_in_images_get_id($index) {
    global $edited_language;
    $images = cw_in_images_get_list();
    $image = $images[$index];
    $key = $index;
    if ($image['type']) $key .= '_'.$edited_language;
    return $key;
}

function cw_in_images_get_image_info($image_id, $title) {
    global $tables;

    $tmp = cw_query_first("select * from $tables[webmaster_images] where id='$image_id'");
    $tmp = cw_image_info('webmaster_images', $tmp);
    $tmp['id'] = $image_id;
    $tmp['title'] = cw_get_langvar_by_name('image_'.$title);

    return $tmp;
}

function cw_in_images_assign($image) {
    global $edited_language, $config;

    $images_to_upload = cw_in_images_get_list();
    $key = 0;
    foreach($images_to_upload as $k=>$val)
        if ($val['name'] == $image) {$key = $k; break;}
    if ($key) {
        $image_id = cw_in_images_get_id($key);
        $tmp = cw_in_images_get_image_info($image_id, $images_to_upload[$key]['name']);
        if (!$tmp['tmbn_url']) $tmp = cw_in_images_get_image_info($key.'_'.$config['default_customer_language'], $images_to_upload[$key]['name']);
    }
    return $tmp;
}
?>
