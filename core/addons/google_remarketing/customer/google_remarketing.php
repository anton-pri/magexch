<?php

cw_load('category');

$page_type = $smarty->get_template_vars('main');
$ptype = $prod_id = $prod_name = $prod_cat = $prod_value = "''";

if($page_type == 'welcome'){
    $ptype = "'home'";

}elseif($page_type == 'subcategories'&& isset($cat)){
    $ptype = "'category'";
    $prod_category = cw_func_call('cw_category_get', array('cat' => $data['category_id']));
    $prod_cat      =  "'".$prod_category['category']."'";

}elseif ($page_type == 'product'){
    $category = cw_func_call('cw_category_get', array('cat' => $cat));

    $ptype      = "'product'";
    $prod_id    = $product_info['product_id'];
    $prod_name  = "'".$product_info['product']."'";
    $prod_cat   = "'".$category['category']."'";
    $prod_value = $product_info['price'];;

}elseif(($page_type == 'cart') || ($page_type == 'index' && $mode == 'checkout')){
    $cart        = &cw_session_register('cart');
    $customer_id = &cw_session_register('customer_id');
    $products    = cw_call('cw_products_in_cart',array($cart, $user_account));

   if($products){
       $prod_id  = $prod_name = $prod_cat = "[";
        foreach($products as $data){
            $prod_id         .="'".$data['product_id']."', ";
            $prod_name       .="'".addslashes($data['product'])."', ";
            $current_category = cw_func_call('cw_category_get', array('cat' => $data['category_id']));
            $prod_cat        .= "'".$current_category['category']."', ";
         }
        $prod_id   = substr($prod_id, 0, -2).']';
        $prod_name = substr($prod_name, 0, -2).']';
        $prod_cat  = substr($prod_cat, 0, -2).']';
   }

    $ptype = ((isset($mode)&& $mode=='checkout')? "'purchase'" : "'cart'" );
    $prod_value = "'".$cart['info']['total']."'";

}
$smarty->assign('google_remarketing',array('ptype'=>$ptype, 'prod_id'=> $prod_id, 'prod_name' => $prod_name, 'prod_cat' => $prod_cat, 'prod_value'=>$prod_value));

