<?php
namespace CW\Ajax_Add2Cart;

const addon_name = 'ajax_add2cart';
const addon_version = '0.2';


if (APP_AREA == 'customer' && !empty($addons[addon_name])) {
     cw_include('addons/ajax_add2cart/func.php');

    if ($target == 'cart') {
        cw_addons_set_controllers(array('post','customer/cart.php','addons/ajax_add2cart/minicart.php'));

        cw_event_listen('on_add_cart','CW\Ajax_Add2Cart\on_add_cart');
    }

    // Skin requirements:
    // add_to_cart.tpl is used for button
    // cw_form_submit is used for form submittion, no GET links supported
    if ($target!='gifts') {
        cw_addons_set_template(
            array('replace', 'customer/menu/minicart.tpl', 'addons/ajax_add2cart/minicart.tpl'),
            array('pre', 'buttons/add_to_cart.tpl','addons/'. addon_name.'/add_to_cart.tpl','CW\Ajax_Add2Cart\cw_smarty_replace_href'),
            array('pre', 'buttons/buy_now.tpl', 'addons/'. addon_name.'/buy_now.tpl','CW\Ajax_Add2Cart\cw_smarty_replace_href')
        );
    }

    cw_addons_set_template(
        array('pre', 'customer/menu/microcart.tpl', 'addons/ajax_add2cart/microcart.tpl')
    );

    cw_addons_add_css('addons/ajax_add2cart/minicart.css');
    cw_addons_add_js('addons/ajax_add2cart/minicart.js');

    cw_set_controller('addons/mobile/init/mobile.php','addons/'.addon_name.'/addons/mobile.php', EVENT_POST);

}
