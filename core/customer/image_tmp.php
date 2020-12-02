<?php

cw_load( 'files', 'image', 'user');

$file_upload_data = &cw_session_register('file_upload_data', array());

global $smarty;

cw_log_add('image_tmp', [$idtag, $in_type]);

if (defined('IS_AJAX') && !empty($in_type)) {
    cw_load('ajax');

    cw_log_add('image_tmp', [$in_type, $available_images[$in_type]['multiple'], $file_upload_data[$in_type]], $file_upload_data);

    $smarty->assign('in_type', $in_type);
    $smarty->assign('multiple', $available_images[$in_type]['multiple']);
    $smarty->assign('file_upload_data', $file_upload_data[$in_type]);

    cw_add_ajax_block(array(
        'id' => 'tmp_' . $idtag,
        'action' => 'update',
        'template' => 'admin/images/image_tmp.tpl'
    ));
}
