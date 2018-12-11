<?php
/*
 * Vendor: CW
 * addon: ebay
 */
namespace CW\ebay;

const addon_name 		= 'ebay';
const addon_target 	= 'ebay_export';
const addon_version 	= '0.1';

const addon_files_location_path 		= 'files/ebay/';
const addon_conditions_data_file_name 	= 'ConditionIDs_by_Category.csv';

if (
	APP_AREA == 'admin' 
	&& $target == addon_target 
	&& !empty($addons[addon_name])
) {
    $ebay_config = array(
    	'ebay_action'					=> 'Add', 	// Determines the purpose of the row: add item, relist item, revise item,
													// end listing, mark an item's status, verify an added item, and add item description information.
    												// Possible vaules: Add, VerifyAdd, Revise
    												// All possible values: Add, Revise, Relist, End, Status, VerifyAdd, AddToItemDescription
        'ebay_category'  				=> 1, 		// Numeric ID of the Category where the item is to be listed.
    												// Category list http://listings.ebay.co.uk/_W0QQloctZShowCatIdsQQsacatZQ2d1QQsalocationZatsQQsocmdZListingCategoryList
        'ebay_condition_id'   			=> 1000,	// Describes the appearance and state of the product.
        'ebay_duration'  				=> 1, 		// How long would you like your listing to be posted on eBay?
        'ebay_format'   				=> 'Auction (default)',	// Listing format for the item.
    												// Possible values: Auction (default), FixedPrice, ClassifiedAd, RealEstateAd
    	'ebay_immediate_pay_required'	=> 0,		// Indicates that immediate payment is required from the buyer.
        'ebay_location'   				=> '', 		// Location of the item. Use the Postal Code, or City, State, Country. Default get from company info.
        'ebay_paypal_accepted'   		=> 0, 		// Do you allow buyers to use PayPal to pay for your items?
        'ebay_paypal_email_address'   	=> '', 		// When you allow buyers to pay for items with PayPal,
    												// you must provide the email address associated with your PayPal account.
        'ebay_dispatch_time_max' 		=> 1, 		// Specifies the handling time, defined as the maximum number of business days you usually take to
    												// prepare an item for dispatching to domestic buyers after receiving a cleared payment.
    												// Possible vaules: 1, 2, 3, 4, 5, 10, 15, 20, 30
        'ebay_returns_accepted_option'	=> 'ReturnsAccepted', 	// Indicates that a buyer can return an item.
    												// Possible values: ReturnsAccepted, ReturnsNotAccepted
    );


    cw_addons_set_controllers(
        array('replace', 'admin/' . addon_target . '.php', 'addons/' . addon_name . '/' . addon_target . '.php')
    );

    cw_include('addons/' . addon_name . '/func.php');

    cw_addons_set_template(
        array('replace', 'admin/main/ebay_export.tpl', 'addons/ebay/ebay_export.tpl'),
        array('replace', 'admin/import_export/ebay_export.tpl', 'addons/ebay/ebay_export.tpl')
    );
}
if(APP_AREA =='admin'){
    cw_set_controller('admin/ajax_ebay_category_select.php', 'addons/ebay/ebay_attributes_modify.php', EVENT_REPLACE);
    cw_addons_set_template( array('post', 'admin/attributes/default_types.tpl', 'addons/ebay/types/ebay_category_selector.tpl'));
    cw_addons_add_js('jquery/dynatree-1.2.4/jquery.dynatree.min.js');
    cw_addons_add_css('jquery/dynatree-1.2.4/ui.dynatree.css');

}
