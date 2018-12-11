<?php
define('CHECK_UNIQ_ID', true);	// Check or not the uniqueness of gift certificate ID

$tables['wishlist'] 			= 'cw_wishlist';
$tables['giftreg_events'] 		= 'cw_giftreg_events';
$tables['giftreg_maillist'] 	= 'cw_giftreg_maillist';
$tables['giftreg_guestbooks'] 	= 'cw_giftreg_guestbooks';

cw_include('addons/estore_gift/include/func.gift.php');

cw_addons_set_controllers(
    array('replace', 'admin/giftcerts.php', 'addons/estore_gift/admin/giftcerts.php'),
    array('replace', 'customer/gifts.php', 'addons/estore_gift/customer/gifts.php'),
    array('pre', 'customer/cart.php', 'addons/estore_gift/customer/cart.php')
);

cw_addons_set_template(
	array('post', 'customer/cart/content.tpl', 'addons/estore_gift/content.tpl')
);

cw_addons_set_hooks(
    array('post', 'cw_payment_get_methods', 'cw_gift_payment_get_methods'),
    array('post', 'cw_payment_run_processor', 'cw_gift_payment_run_processor')
);

cw_set_hook('cw_delete_product', 'cw_gift_delete_product', EVENT_POST);
cw_set_hook('cw_doc_update_item', 'cw_gift_doc_update_item', EVENT_PRE);
cw_set_hook('cw_doc_update', 'cw_gift_doc_update', EVENT_POST);

if (APP_AREA == 'customer') {
	cw_event_listen('on_cart_menu_build', 'cw_gift_get_menu_list');
	cw_event_listen('on_login', 'cw_gift_on_login');
}

if (APP_AREA == 'admin') {
    cw_set_controller('admin/giftcert_user_data.php', 'addons/estore_gift/admin/giftcert_user_data.php', EVENT_REPLACE);

}
