<?php

cw_load( 'files', 'image', 'user');

$file_upload_data = &cw_session_register('file_upload_data', array());

global $smarty;

$idtag = 'seller_product_images_'.$in_type;
//$in_type = 'products_detailed_images';

//cw_log_add('seller', [$idtag, $in_type]);

if (defined('IS_AJAX') && in_array($in_type, ['products_images_det', 'products_images_thumb', 'products_detailed_images'])) {
    cw_load('ajax');

    $is_multiple = intval($available_images[$in_type]['multiple']);
    cw_log_add('seller_product_images', [$in_type, $available_images[$in_type]['multiple'], $file_upload_data[$in_type], $file_upload_data]);

    if (isset($to_delete)) {
        if ($is_permanent) {
            if (in_array($in_type, ['products_images_det', 'products_images_thumb'])) {
                cw_image_delete($product_id, $in_type);                    
            } elseif ($in_type == 'products_detailed_images') {
                cw_image_delete($to_delete, $in_type);
            }
        } else {
            if ($is_multiple) {
                $to_del_idx = intval($to_delete);
                if (isset($file_upload_data[$in_type][$to_del_idx])) 
                    unset($file_upload_data[$in_type][$to_del_idx]);
            } else {
                unset($file_upload_data[$in_type]);
            }   
        }
    }
    cw_session_save();

    //cw_log_add('image_tmp', [$in_type, $available_images[$in_type]['multiple'], $file_upload_data[$in_type]], $file_upload_data);

    $smarty->assign('in_type', $in_type);
    
    $smarty->assign('multiple', $is_multiple);

    $temp_data = 
        $is_multiple 
            ? $file_upload_data[$in_type] 
            : (isset($file_upload_data[$in_type]) ? [$file_upload_data[$in_type]] : []);

    $smarty->assign('file_upload_data', $temp_data);
    $smarty->assign('file_upload_data_count', count($temp_data));

    $product_images = [];

    if (in_array($in_type, ['products_images_det', 'products_images_thumb'])) {
        $image_data = cw_image_get($in_type, $product_id);
        if ($image_data && $image_data['tmbn_url'] != '' && !$image_data['is_default'])
            $product_images = [$image_data];
    } elseif ($in_type == 'products_detailed_images') {
        $product_images = cw_image_get_list('products_detailed_images', $product_id, ($current_area == 'C'?1:0));
    }

    $smarty->assign('product_images', $product_images);
    $smarty->assign('product_images_count', count($product_images));
    

    cw_add_ajax_block(array(
        'id' => $idtag,
        'action' => 'update',
        'template' => 'addons/custom_magazineexchange_sellers/seller/seller_product_images_list.tpl'
    ));
}
