<?php
function smarty_function_price_filter_url($params, &$smarty) {
    static $pf_count = null;
    global $app_web_dir,$app_main_dir;

    if ($params['link']) {
        $link = $params['link'];
        if (!empty($app_web_dir) && strpos($link,$app_web_dir)===false) $link = $app_web_dir.with_leading_slash($link);
        return $link;
    }
    
    if (is_null($pf_count)) {
        $pf_count = count($smarty->get_template_vars('product_filter'), COUNT_RECURSIVE);
    }

    // Build dynamic URL
    $build_params = array(
        'url' => $params['ns'],
        "att[{$params['att_id']}][{$params['value_id']}]" => 
            ($params['value_selected']==$params['value_id'] || $params['is_selected'])?'':$params['value_id'],
    );


    include_once $app_main_dir.'/include/templater/plugins/function.build_url.php';    
    $url = smarty_function_build_url($build_params, $smarty);


    if ($pf_count<800 || defined('IS_CRON') || $params['force_seo']) {
        // Transform to SEO URL
        return cw_call('cw_clean_url_get_seo_url',array($url));
    } else {
        // Return as is
        return $url; 
    }
}

