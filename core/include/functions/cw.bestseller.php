<?php
# kornev, TOFIX
function cw_bestseller_get_menu($category_id, $limit = 0, $info_type = 288, $att = array()) {
    global $addons, $config, $tables, $user_account,$current_area;
    
    $data = array();
    $data['all'] = true;
    $data['flat_search'] = true;

    if ($config['General']['disable_outofstock_products'] == 'Y')
        $data['min_avail'] = 0;

    if ($category_id)
        $data['category_id'] = $category_id;

    $add_params = array();
    $add_params['query_joins']['products_stats'] = array(
        'on' => "$tables[products_stats].product_id = $tables[products].product_id",
        'only_select' => 1,
    );
    $data['where'] = "IFNULL($tables[products_stats].views_stats,0) > 0 and IFNULL($tables[products_stats].sales_stats,0) > 0";
    $data['sort_condition'] = "$tables[products_stats].sales_stats DESC, $tables[products_stats].views_stats DESC";
    $data['limit'] = $limit?$limit:$config['bestsellers']['number_of_bestsellers'];
    $data['attributes'] = $att;

    cw_load('product');
    $return = cw_func_call('cw_product_search', array('data' => $data, 'user_account' => $user_account, 'current_area' => $current_area, 'info_type' => $info_type),$add_params);
    if (!$limit) return $return[0];
    return $return;
}
