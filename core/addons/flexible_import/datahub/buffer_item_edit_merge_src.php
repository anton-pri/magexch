<?php
$smarty->assign('table_id', $table_id);
$smarty->assign('merge_src_ID', $mID);

$bmerge_config = cw_query_hash("select * from $tables[datahub_buffer_merge_config]", 'bfield', false);

$bmerge_config['image'] = array('mfield'=>'image');

$smarty->assign('bmerge_config', $bmerge_config);

$merge_item_data = cw_query_first("select * from $tables[datahub_main_data] where ID = '$mID'");

if (!empty($merge_item_data))
    $merge_item_data_image = cw_query_first("select * from $tables[datahub_main_data_images] where id='$merge_item_data[cimageurl]' limit 1");

if (!empty($merge_item_data_image)) {

    foreach (array('filename', 'web_path', 'system_path') as $img_fld) { 
        $merge_item_data_image[$img_fld] = "/".ltrim($merge_item_data_image[$img_fld], "/");

        $img_filename_full_path = $app_dir.$merge_item_data_image[$img_fld];

        if (!file_exists($img_filename_full_path)) 
            $merge_item_data_image[$img_fld] = '/images'.$merge_item_data_image[$img_fld];
    }

    $merge_item_data['image'] = $merge_item_data_image;
} else {
    $merge_item_data['image'] = array('filename' => '/images/no_image.jpg', 'web_path'=>'/images/no_image.jpg', 'system_path'=>'/images/no_image.jpg');
}

$smarty->assign('merge_item_data', $merge_item_data);

cw_add_ajax_block(array(
    'id'        => 'dh_buffer_edit_merge_src',
    'action'    => 'update',
    'template'  => 'addons/flexible_import/datahub/edit_buffer_item_merge_src.tpl'
));
