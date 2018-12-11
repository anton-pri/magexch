<?php
/* TODO, TOFIX
 * + apply free product bonus
 * + fix bug: cart with free product split into two orders
 * 
 * + apply discount bonus
 * 	+ to cart
 * 	- to condition
 * 	+ to selected
 *  - to selected qty only
 * 
 * + apply free shipping bonus
 * 	+ to cart
 * 	+ to condition
 * 	+ to selected
 * 
 * + apply coupon bonus
 * - describe coupon details in order
 * 
 * + fix coupons desc in PS admin
 * + add times to repeat in admin
 * - mdm
 * + discount bundles
 * 		+ admin
 * 		+ customer
 * 
 * + add shiping method choice for free shipping bonus
 * + add weight condition
 * + add membership condition
 * + subtotal and weight conditions must be a range
 * - pagination on bundles list in admin
 * - fix wording in admin/bonuses/free shipping
 * + remove bonus "free category"
 * - fix bug when switch discount bonus radio button back from "selected products"
 * + hook product/category/manufacturer/zones/coupon deletion
 * - function to cleanup cart from free products
 * - offer log (condition, bonuses, summary) in human readable format saved with order
 * - remove Serg's unused function
 */
// condition object
define('PS_OBJ_TYPE_PRODS',     1);
define('PS_OBJ_TYPE_CATS',      2);
define('PS_OBJ_TYPE_MANS',      3);
define('PS_OBJ_TYPE_ZONES',     4);
define('PS_OBJ_TYPE_FROM',      5);
define('PS_OBJ_TYPE_TILL',      6);
define('PS_OBJ_TYPE_MEMBERSHIP',7);
define('PS_OBJ_TYPE_COUPON',    8);
define('PS_OBJ_TYPE_ATTR',      9);
define('PS_OBJ_TYPE_SHIPPING', 10);
define('PS_OBJ_TYPE_COOKIE',   11);

// apply a discount to
define('PS_APPLY_CART',     1);
define('PS_APPLY_COND',     2);
define('PS_APPLY_PRODS',    3);

// discount type
define('PS_DISCOUNT_TYPE_ABSOLUTE', 1);
define('PS_DISCOUNT_TYPE_PERCENT',  2);

// bonus
define('PS_DISCOUNT',   'D');
define('PS_FREE_PRODS', 'F');
define('PS_FREE_SHIP',  'S');
define('PS_COUPON',     'C');

// condition
define('PS_TOTAL',          'T');
define('PS_SHIP_ADDRESS',   'A');
define('PS_SPEC_PRODUCTS',  'P');
define('PS_WEIGHT',         'W');
define('PS_MEMBERSHIP',     'E');
define('PS_USE_COUPON',     'B');
define('PS_COOKIE',         'K');


define('PS_ATTR_ITEM_TYPE', 'PS');
define('PS_IMG_TYPE', 'ps_offer_images');

define('PS_COND_LOGIC','OR'); // [AND|OR] conjunction of multiple categories, products, manufacturers conditions - shall be found all or any one

define('PS_VERSION', 'v.2.4.3.xc 2012-10-22 -> 2013.05.16 -> 2014.11.20');

global $bonus_names, $cond_names;

$bonus_names = array(
    PS_COUPON     => 'lbl_ps_bonus_coupon',
    PS_DISCOUNT   => 'lbl_ps_bonus_discount',
    PS_FREE_PRODS => 'lbl_ps_bonus_forfree',
    PS_FREE_SHIP  => 'lbl_ps_bonus_freeship'
);

$cond_names = array(
    PS_TOTAL         => 'lbl_ps_cond_subtotal',
    PS_SHIP_ADDRESS  => 'lbl_ps_cond_shipping',
    PS_SPEC_PRODUCTS => 'lbl_ps_cond_products',
    PS_WEIGHT        => 'lbl_ps_cond_weight'
);

cw_include('addons/promotion_suite/include/func.hooks.php');
cw_include('addons/promotion_suite/include/func.ps.php');
cw_include('addons/promotion_suite/include/func.php');

// Hooks for data consistency
cw_set_hook('cw_category_delete',             'cw_ps_category_delete',          EVENT_PRE);
cw_set_hook('cw_delete_product',              'cw_ps_delete_product',           EVENT_POST);
cw_set_hook('cw_salesman_delete_discount',    'cw_ps_salesman_delete_discount', EVENT_POST);
cw_set_hook('cw_manufacturer_delete',         'cw_ps_manufacturer_delete',      EVENT_POST);
cw_set_hook('cw_shipping_delete_zone',        'cw_ps_shipping_delete_zone',     EVENT_POST);
cw_set_hook('cw_warehouse_delete_division',   'cw_ps_warehouse_delete_division',EVENT_PRE);

/* 
 * Customer area 
 */
cw_addons_set_controllers(
	array('replace', 'customer/promosuite.php', 'addons/promotion_suite/customer/promosuite.php'),
	array('post', 'customer/index.php', 'addons/promotion_suite/customer/index.php'),
    array('pre', 'customer/product.php', 'addons/promotion_suite/customer/product_bundle.php')
	
);

// Registration of shipping_rate hook must be after all shipping addons - in post_init.php
cw_set_controller('init/post_init.php', 'addons/promotion_suite/post_init.php', EVENT_POST);
cw_addons_set_hooks(array('post','cw_shipping_cart_calc','cw_ps_shipping_cart_calc'));
cw_addons_set_hooks(array('post','cw_shipping_cart_summarize','cw_ps_shipping_cart_summarize'));

// Init cart. Check all offers and prepare service array with collected applicable bonuses
cw_set_controller('customer/cart.php', 'addons/promotion_suite/customer/cart_init.php', EVENT_PRE);

// AOM support
cw_set_hook('cw_aom_recalculate_totals', 'cw_ps_aom_recalculate_totals', EVENT_PRE);
cw_set_hook('cw_aom_recalculate_totals', 'cw_ps_aom_recalculate_totals_extra', EVENT_POST);

cw_addons_set_template(
    array('replace', 'customer/main/promosuite.tpl', 'addons/promotion_suite/customer/main.tpl'),
    array('post', 'customer/menu/special_product_links_inner.tpl', 'addons/promotion_suite/customer/menu/special_product_links.tpl'),
    array('pre', 'customer/products/subcategories.tpl', 'addons/promotion_suite/customer/products/subcategories.tpl')
);

cw_addons_set_template(
	array('replace', 'customer/cart/item_price.tpl', 'addons/promotion_suite/customer/cart/item_price.tpl', 'cw_is_product_free'),
	array('post', 'customer/cart/cart.tpl', 'addons/promotion_suite/customer/cart/cart_init.tpl')
);

cw_addons_set_template(
	array('post', 'main/docs/layout/bottom.tpl','addons/promotion_suite/main/coupon.tpl'),
        array('post', 'admin/docs/layout/bottom.tpl','addons/promotion_suite/main/coupon.tpl')
);

/* SERG
cw_addons_set_hooks(
    array('pre', 'cw_cart_actions', 'cw_ps_cart_actions'),
// // ATTENTION 
    array('post', 'cw_cart_calc_single', 'cw_ps_cart_calc_single', 300)// this hook critically must be called after all standard hooks (e.g. shipping)
    //array('post', 'cw_cart_calc_discounts', 'cw_ps_cart_calc_discounts'),
    //array('post', 'cw_cart_calc', 'cw_ps_cart_calc')
);
*/

/* Hooks for free product bonus */
cw_set_hook('cw_products_in_cart',            'cw_ps_products_in_cart_pre',		EVENT_PRE);
//cw_set_hook('cw_products_in_cart',            'cw_ps_products_in_cart_post',		EVENT_POST);
cw_event_listen('on_product_from_scratch', 'cw_apply_special_offer_free');

/* Hooks for discount bonus */
// Function adds offer discounts applied to whole cart
cw_event_listen('on_collect_discounts', 'cw_ps_on_collect_discounts');
// Function adds offer discounts applied to a product in cart
cw_event_listen('on_product_from_scratch', 'cw_apply_special_offer_discount');


/* Hooks for coupon bonus */
cw_event_listen('on_place_order_extra', 'cw_ps_on_place_order_extra');

// CMS
cw_event_listen('on_cms_check_restrictions','cw_ps_on_cms_check_restrictions_PS');

// cw_delete_from_cart
// $product_id = cw_delete_from_cart($cart, $productindex);


if (APP_AREA == 'admin') {
    cw_addons_set_controllers(
        array('replace', 'admin/promosuite.php', 'addons/promotion_suite/admin/promosuite.php'),
        array('replace', 'admin/discount_bundles.php', 'addons/promotion_suite/admin/discount_bundles.php'),
        array('post', 'include/auth.php', 'addons/promotion_suite/include/auth.php')
    );

    cw_set_controller('include/products/modify.php','addons/promotion_suite/admin/product_modify.php', EVENT_PRE);

    cw_addons_set_hooks(
        array('post', 'cw_tabs_js_abstract', 'cw_ps_tabs_js_abstract')
    );

    cw_addons_set_template(
        array('replace', 'admin/main/promosuite.tpl', 'addons/promotion_suite/admin/main.tpl'),
        array('replace', 'admin/main/discount_bundles.tpl', 'addons/promotion_suite/admin/discount_bundles.tpl')
    );
    
	if ($target=='cms') {
		cw_addons_set_template(
			array('pre','admin/attributes/object_modify.tpl','addons/promotion_suite/addons/cms/cms_details.tpl')
		);
		cw_event_listen('on_cms_update','cw_ps_on_cms_update');
		cw_set_controller('addons/cms/cs_banner.php','addons/promotion_suite/addons/cms/cs_banner.php', EVENT_POST);
		cw_set_controller('addons/cms/cs_banners.php','addons/promotion_suite/addons/cms/cs_banner.php', EVENT_POST);
	}

    // Search order by offer
    $cw_allowed_tunnels[] = 'cw_ps_get_offers';
    cw_event_listen('on_prepare_search_orders', 'cw_ps_prepare_search_orders');
    cw_addons_set_template(
        array('post', 'main/docs/additional_search_field.tpl', 'addons/promotion_suite/admin/additional_doc_search_field.tpl')
    );
	
	cw_addons_add_css('addons/promotion_suite/admin/promosuite.css');
}

if (APP_AREA == 'customer') {
	cw_addons_add_css('addons/promotion_suite/customer/promosuite.css');
    cw_addons_set_hooks(
        array('post', 'cw_tabs_js_abstract', 'cw_ps_tabs_js_abstract')
    );

}

/* PS tables */
$_addon_tables = array('ps_offers', 'ps_conditions', 'ps_bonuses', 'ps_offer_images', 'ps_bonus_details', 'ps_cond_details');
foreach ($_addon_tables as $_table) {
    $tables[$_table] = 'cw_' . $_table;
}


