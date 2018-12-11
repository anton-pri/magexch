<?php
$seller_custom_fields = cw_user_get_custom_fields($customer_id,0,'','field');

$smarty->assign('alt_product_search_title', cw_get_langvar_by_name('lbl_digital_products'));

$smarty->assign('current_section_dir', 'products');

if ($seller_custom_fields['allow_digital_sales'] == 'Y') 
    cw_include("addons/seller/core/products.php");
else {
    $smarty->assign("main",'digital_products_disabled');
}
