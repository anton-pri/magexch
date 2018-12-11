<?php
cw_load('product', 'image', 'category', 'warehouse');

$search_data = &cw_session_register('search_data', array());
$product_bookmarks = &cw_session_register('product_bookmarks', array());

if (!is_array($product_bookmarks[$field_product_id])) $product_bookmarks[$field_product_id] = array();

$link = "&field_product_id=$field_product_id&field_product=$field_product&field_amount=$field_amount";

if ($action == 'update_bookmarks'&& is_array($del_bookmark)) {
    foreach($del_bookmark as $index=>$tmp)
        unset($product_bookmarks[$field_product_id][$index]);
    cw_header_location("index.php?target=popup_products&$link");
}

if ($action == 'delete_all_bookmarks') {
    $product_bookmarks[$field_product_id] = array();
    cw_header_location("index.php?target=popup_products&$link");
}

if ($action == 'bookmark' && is_array($product_ids)) {
    $product_bookmarks[$field_product_id] = array_merge($product_bookmarks[$field_product_id], array_keys($product_ids));
    cw_header_location("index.php?target=popup_products&$link");
}

$use_search_conditions = 'popup_products';
include $app_main_dir.'/include/products/search.php';

$bookmarks = array();
if ($product_bookmarks[$field_product_id]) 
foreach($product_bookmarks[$field_product_id] as $product_id)
    if ($prd = cw_func_call('cw_product_get', array('id' => $product_id, 'user_account' => $user_account, 'info_type' => 0)))
        $bookmarks[] = $prd;
$smarty->assign('bookmarks', $bookmarks);

$smarty->assign('field_product', $field_product);
$smarty->assign('field_product_id', $field_product_id);
$smarty->assign('field_amount', $field_amount);
$smarty->assign('home_style', 'iframe');

$location[] = array(cw_get_langvar_by_name('lbl_search_products'), '');
