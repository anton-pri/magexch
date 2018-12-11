<?php
// {product_image product_id=<product_id> [src_url=<image_path> image=<product.image>]}
function smarty_function_product_image($params, &$smarty) {

    global $config;

    require_once $smarty->_get_plugin_filepath('function','xcm_thumb');

    cw_load('image');

    if (!empty($params['image'])) $params['image_type'] = $params['image']['in_type'];

    if (!in_array($params['image_type'],array('products_images_det','products_images_thumb'),true))
        $params['image_type'] = 'products_images_det';

    // Get image by product ID only
    if (empty($params['src_url']) && empty($params['image']) && !empty($params['product_id'])) {
        if (empty($params['image_type'])) $params['image_type'] = 'products_images_thumb';
        $params['image'] = cw_image_get($params['image_type'],$params['product_id']);
    }

    // Try to find main image instead of empty thumbnail
    if ($params['image_type'] == 'products_images_thumb' && (empty($params['image']) || $params['image']['is_default']) && !empty($params['product_id'])) {
        $image = cw_image_get('products_images_det',$params['product_id']);
        if (!empty($image) && empty($image['is_default']))
            $params['image'] = $image;
    }

    if (!empty($params['image'])) $params['src_url'] = $params['image']['image_path'];

    if (empty($params['width'])) $params['width'] = $config['Appearance'][$params['image_type'].'_width'];

    $params['keep_file_h2w'] = 'Y';


    if (empty($params['alt']) && !empty($params['image']['alt'])) 
        $params['alt'] = $params['image']['alt'];

    $is_email_invoice = $smarty->_tpl_vars['is_email_invoice']; 

    if ($params['no_xcm_thumb_cache'] == 'Y' || $is_email_invoice=='Y') {
        $result = "<!--extra params: $params[extra] --><img src=\"".$params['image']['tmbn_url']."\" $params[extra] alt=\"$params[alt]\"/>";
        return $result;
    }

    $orig_params = $params;

    $params['assign_x'] = "result_thumb_width";
    $params['assign_y'] = "result_thumb_height";
    $params['assign_url'] = "result_thumb_url";
    $params['just_url'] = "Y";

    smarty_function_xcm_thumb($params, $smarty);

    $thumb_image_url = $smarty->get_template_vars($params['assign_url']);
    $result_thumb_width = $smarty->get_template_vars($params['assign_x']);
    $result_thumb_height = $smarty->get_template_vars($params['assign_y']);

    $extra_img_code = "";

    if (!empty($params['extra'])) {
        $extra_img_code = $params['extra'];
    }
    $extra_html_params = array("title", "class", "style", "id", "alt");

    foreach ($extra_html_params as $p_name) {
        if (!empty($params[$p_name]))
            $extra_img_code .= " $p_name=\"".$params[$p_name]."\"";
    }
    
    $html_width = (!empty($params['html_width']))?$params['html_width']:$result_thumb_width;
    if (!empty($params['html_height'])) $html_heigth = $params['html_heigth'];
    elseif (!empty($params['html_width'])) $html_heigth='';
    else $html_heigth=$result_thumb_height;
    

    if ($orig_params['just_url'] != 'Y') 
        $img_tag_code = '<img src="'.$thumb_image_url.'" width="'.$html_width.'" '.(!empty($html_heigth)?'height="'.$html_heigth.'" ':' ').$extra_img_code.' />';
    else
        $img_tag_code = $thumb_image_url;

    return $img_tag_code; 
}
