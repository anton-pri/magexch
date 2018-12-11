<?php

if (isset($app_config_file['interface']['product_filter_home_class']) && empty($product_filter)) {
    $smarty->assign('show_left_bar', true);
   
    $cache_key = array($current_language,$user_account['membership_id']);

    list($product_filter, $navigation) = cw_cache_get($cache_key, 'PF_home');
    
    if (empty($product_filter)) {
# cached {
        $data = array();
        $data['flat_search'] = true;
        $info_type = 0|1024;    # add product filter
        list($products, $navigation, $product_filter) = cw_func_call('cw_product_search', array('data' => $data, 'user_account' => $user_account, 'current_area' => $current_area, 'info_type' => $info_type));
        $products = array();
        $navigation['script'] = cw_call(
                                    'cw_core_get_html_page_url', 
                                    array(
                                    array(
                                        'var'               => 'search', 
                                        'mode'              => 'search',
                                        )
                                    )
                                    );
        $home_attribute_ids = cw_query_column("SELECT aca.attribute_id 
            FROM $tables[attributes_classes_assignement] aca, $tables[attributes_classes] ac
            WHERE ac.name = '".addslashes($app_config_file['interface']['product_filter_home_class'])."' AND
            ac.attribute_class_id = aca.attribute_class_id");
        $home_attribute_ids[] = 'price';
        if (!empty($home_attribute_ids)) {
            foreach ($product_filter as $k=>$v) {
                if (!in_array($v['attribute_id'],$home_attribute_ids)) {
                    unset($product_filter[$k]);
                }
            }
        }
        cw_cache_save(array($product_filter, $navigation), $cache_key, 'PF_home');
# } cached
    }
                            
    $smarty->assign('product_filter', $product_filter);

    if ($target != 'docs_O') 
        $smarty->assign('navigation', $navigation);
    
    unset($config['product']['pf_is_ajax']);
}
