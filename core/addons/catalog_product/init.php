<?php
/*
 * Vendor:	CW
 * addon:	catalog_product
 * description:	
 *  New product type "Catalog" means that product is only listed on this site but can be bought on another site
 *  For this type of products the URL to real shop is required
 *  This product has special "buy" button in customer area, which may differ by appearance and text from normal products. For example it cat say "Buy from artist's site"
 *  Statistic about redirections to target site can be gathered as add_to_cart statistic (see "New statistic couner" spec)
 *  "Report as sold" feature is for this type of products only
 */
 
namespace CW\catalog_product;

const addon_name = 'catalog_product';
const addon_target = '';
const addon_version = '0.3';

define('PRODUCT_TYPE_CATALOG', 4);

cw_include('addons/'.addon_name.'/func.php');


if (APP_AREA == 'admin' && $target == 'products') {

// Hook product modify to require Original URL attr for catalog product type
	cw_set_hook('cw_error_check', 'CW\catalog_product\cw_error_check', EVENT_POST);

// Hook product modify to hide Original URL attr for other product types
	cw_addons_set_hooks(array('post','cw_attributes_get', 'CW\catalog_product\cw_attributes_get'));
}

if (APP_AREA == 'customer') {
// Hook special catalog_redirect controller to redirect to original_url
	cw_set_controller(
        'customer/catalog_redirect.php',
        'addons/'.addon_name.'/customer/catalog_redirect.php',
        EVENT_REPLACE
    );
    cw_set_controller('customer/product.php', 'addons/'.addon_name.'/customer/product.php', EVENT_POST);
    cw_set_controller(
        'customer/report_about_sold.php',
        'addons/' . addon_name . '/customer/report_about_sold.php',
        EVENT_REPLACE
    );

// hook for add_to_cart button to replace form, text and action
	cw_addons_set_template(
		array('pre', 'buttons/add_to_cart.tpl','addons/'. addon_name.'/add_to_cart.tpl', 'CW\catalog_product\cw_is_catalog_product'),
		array('pre', 'buttons/buy_now.tpl','addons/'.  addon_name.'/buy_now.tpl', 'CW\catalog_product\cw_is_catalog_product'),
		array('replace', 'customer/products/product-amount.tpl','','CW\catalog_product\cw_is_catalog_product'),
		array('pre', 'customer/products/product-fields.tpl', 'addons/' . addon_name . '/report_as_sold.tpl')
	);
}
