<?php
$search_data = &cw_session_register('search_data', array());
cw_load('product', 'warehouse','image');

if ($action == 'print_barcode' && $addons['barcode']) {
    if (!$print['template_id'])
        cw_header_location("index.php?target=$target&mode=details&product_id=$product_id");
    cw_load('web', 'product', 'barcode', 'pdf');
    cw_barcode_print_product($product_id, $print);
}

if ($mode == 'add') {
    cw_include('include/products/modify.php');
}
elseif(($mode == 'details' && $product_id) || $mode == 'add') {
    cw_include('include/products/modify.php');
    $smarty->assign('read_only', false);
}
elseif(in_array($mode, array('process', 'delete'))) {
    cw_include('include/products/process.php');
}
else {
    cw_include('include/products/search.php');
    $smarty->assign('products', $products);

    $location[] = array(cw_get_langvar_by_name('lbl_products_management'), 'index.php?target='.$target);
    $smarty->assign('main', 'search');
}
