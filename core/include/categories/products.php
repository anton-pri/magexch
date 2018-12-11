<?php
if (cw_query_first_cell("SELECT COUNT(*) FROM $tables[categories] WHERE category_id='$cat'") == 0)
    cw_header_location('index.php?target=categories');

$category_page = &cw_session_register('category_page', array());

$data = array();
$data['flat_search'] = 1;
$data['category_id'] = $cat;
$data['category_main'] = "Y";
$data['category_extra'] = "Y";
$data['page'] = $page;

if (empty($sort) && $current_area == 'A') {
    $sort = 'orderby';
    $sort_direction = 0;
}

$items_per_page_targets[$target] = 50; 
$data['sort_field'] = $sort;
$data['sort_direction'] = $sort_direction;

list($products, $navigation) = cw_func_call('cw_product_search', array('data' => $data, 'user_account' => $user_account, 'current_area' => $current_area, 'info_type' => 0));
$navigation['script'] = 'index.php?target='.$target.'&mode='.$mode.'&cat='.$cat;
$smarty->assign('navigation', $navigation);

$smarty->assign('products', $products);

$products_orderbys = cw_query_hash("select * from $tables[products_categories] where category_id='$cat'", 'product_id', false);
$smarty->assign('products_orderbys', $products_orderbys);

$smarty->assign('current_category', cw_func_call('cw_category_get', array('cat' => $cat)));
$smarty->assign('search_prefilled', $data);
$smarty->assign('main', 'category_products');
