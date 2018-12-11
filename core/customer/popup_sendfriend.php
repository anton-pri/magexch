<?php
# [TOFIX]
# kornev, move to addon
$product_info = cw_func_call('cw_product_get', array('id' => $product_id, 'user_account' => $user_account, 'info_type' => 136));
$smarty->assign('product', $product_info);

include $app_main_dir.'/include/products/send_to_friend.php';

$smarty->assign('main', 'popup_send2friend');
$smarty->assign('home_style', 'popup');

if (defined('IS_AJAX') && constant('IS_AJAX')) {
cw_ajax_add_block(array(
    'id' => 'send_to_friend_dialog',
    'template' => 'customer/main/send_to_friend.tpl',
));
cw_ajax_add_block(array(
    'id' => 'script',
    'content' => 'sm("send_to_friend_dialog", send_to_friend_dialog_width, send_to_friend_dialog_height, false, send_to_friend_dialog_title)',
));
}
