<?php
cw_load('product', 'warehouse', 'ean');

$smarty->assign('el_product', $el_product);
$product = cw_product_get_product_by_ean(trim($ean));
$smarty->assign('product', $product);
$smarty->assign('avails_summ', cw_warehouse_get_avails($product['product_id']));
$smarty->assign('time', cw_core_get_time());

cw_display('main/ajax/product_by_ean.tpl', $smarty);
exit(0);
