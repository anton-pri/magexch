<?php
namespace CW\Ajax_Add2Cart;

// Remove bookmark for mobile skin
if ($mobile_select_type == 1) {

function disable_popup() {
    global $ajax_blocks;
    cw_ajax_remove_block('add2cart_popup');
    cw_ajax_remove_block('add2cart_popup_script');
}

cw_set_hook('CW\Ajax_Add2Cart\on_add_cart','CW\Ajax_Add2Cart\disable_popup',EVENT_POST);
}
