<?php
define("NUMBER_VARS", "posted_data[price_min],posted_data[price_max],posted_data[avail_min],posted_data[avail_max],posted_data[weight_min],posted_data[weight_max]");
cw_load('product', 'warehouse','image');
$search_data = &cw_session_register('search_data', array());

if ($mode == 'suppliers_list' && $product_id)  {
    cw_include('include/products/modify_avails.php');
}
elseif($product_id) {
    cw_include('include/products/modify.php');
    $smarty->assign('read_only', true);
}
else {
    $use_search_conditions = 'general';
    $search_data['products'][$use_search_conditions]['warehouse_customer_id'] = $user_account['warehouse_customer_id'];

    cw_include('include/products/search.php');
    $smarty->assign('products', $products);
    $smarty->assign('mode', $mode);

    $location[] = array(cw_get_langvar_by_name('lbl_products_management'), 'index.php?target='.$target);
    $smarty->assign('main', 'search');
}

$smarty->assign('page_acl', '__1100');
$smarty->assign('mode', $mode);
