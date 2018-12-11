<?php
namespace CW\Ajax_Add2Cart;

function on_add_cart(&$cart, $added_product) {
    global $smarty;

    cw_load('ajax','product');

    foreach ($cart['products'] as $cart_product) {
        if ($cart_product['cartid'] == $added_product['cartid']) {
            $product_id = $cart_product['product_id']; 
            break;
        }
    } 

    $product = cw_func_call('cw_product_get',array('id'=>$product_id,'info_type'=>0|128));
    $product = cw_array_merge($product, $added_product);

    $smarty->assign('product',$product);

    cw_add_ajax_block(array(
    'id' => 'add2cart_popup',
    'action' => 'update',
    'template' => 'addons/ajax_add2cart/add2cart_popup.tpl',
    ),'add2cart_popup');
    cw_add_ajax_block(array(
    'id' => 'script',
    'content' => 'sm("add2cart_popup",add2cart_popup_width,add2cart_popup_height, true, "'.$added_product['added_amount'].($added_product['added_amount']>1?' items':' item').' added to cart")',
    ),'add2cart_popup_script');
}

function cw_smarty_replace_href(&$param) {
	$param['href'] = str_replace('cw_submit_form','cw_submit_form_add2cart',$param['href']);
	return false; // prevent real template replacement because href is already replaced
}
