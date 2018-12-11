<?php
cw_load('product');

$cat = intval(@$cat);
if ($cat > 0 && preg_match("/^[\d,]*$/", $no_ids)) {
    $data = array();
    $data['category_id'] = $cat;
    $data['features'] = array($fclass_id => array());
    $data['where'] = "$tables[products].product_id NOT IN ('".str_replace(",","','",$no_ids)."')";
    list($products, $navigation) = cw_func_call('cw_product_search', array('data' => $data, 'user_account' => $user_account, 'current_area' => $current_area));
    $smarty->assign('products', $products);
}

$smarty->assign('no_ids', $no_ids);
$smarty->assign('fclass_id', $fclass_id);
$smarty->assign('no_bookmarks', 'Y');
?>
