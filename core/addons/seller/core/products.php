<?php
cw_load('web', 'product', 'barcode', 'cart', 'warehouse', 'image', 'category', 'user');

global $search_data, $products, $navigation;

$search_data = &cw_session_register('search_data', array());

if ($action == 'print_barcode' && $addons['barcode']) {
    if (!$print['template_id'])
        cw_header_location("index.php?target=$target&mode=details&product_id=$product_id");
    cw_load('pdf');
    cw_barcode_print_product($product_id, $print);
}

if ($action == 'preview' && is_array($product_ids))  {
	global $product_id;
	$product_id = intval(key($product_ids));
	cw_include('include/products/preview.php');
}
elseif ($action == 'links' && is_array($product_ids))  {
	global $product_id;
	$product_id = intval(key($product_ids));
    cw_include('include/products/product_links.php');
}
elseif ($mode == 'add') {
    cw_include('include/products/modify.php');
}
elseif (($mode == 'details' && $product_id) || $mode == 'add') {
    cw_include('include/products/modify.php');
    $smarty->assign('read_only', false);
}
elseif ($mode == 'suppliers_list' && $product_id && $accl['120301'])  {
    cw_include('include/products/modify_avails.php');
}
elseif (in_array($mode, array('process', 'delete', 'clone'))) {
    cw_include('include/products/process.php');
}
else {
    cw_include('include/products/search.php');
    $smarty->assign('products', $products);

    $navigation['script'] = "index.php?target=$target&mode=search";
    $smarty->assign('navigation', $navigation);

    $location[] = array(cw_get_langvar_by_name('lbl_products_management'), 'index.php?target='.$target);
    $smarty->assign('main', 'search');
}
$smarty->assign('page_acl', '');

$smarty->assign('mode', $mode);
