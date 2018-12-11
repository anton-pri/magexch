/*
INSERT INTO `cw_languages` (`code`, `name`, `value`, `tooltip`, `topic`) VALUES
('EN', 'lbl_cc_gcheckout_mid', 'Merchant id', '', 'Labels'),
('EN', 'lbl_cc_gcheckout_mkey', 'Merchant key', '', 'Labels'),
('EN', 'lbl_gcheckout_add_tracking_data', 'Add tracking data', '', 'Labels'),
('EN', 'lbl_gcheckout_archive_order', 'Archive order', '', 'Labels'),
('EN', 'lbl_gcheckout_archiving_commands', 'Archiving commands', '', 'Labels'),
('EN', 'lbl_gcheckout_cancel_order', 'Cancel order', '', 'Labels'),
('EN', 'lbl_gcheckout_charge_order', 'Charge order', '', 'Labels'),
('EN', 'lbl_gcheckout_comment', 'Comment', '', 'Labels'),
('EN', 'lbl_gcheckout_coupon_applied', 'Coupon "{{coupon_code}}" has been successfully applied', '', 'Labels'),
('EN', 'lbl_gcheckout_coupon_freeship', 'The shipping cost has been reset for you by the coupon "{{coupon_code}}"', '', 'Labels'),
('EN', 'lbl_gcheckout_current_order_state', 'Current state', '', 'Labels'),
('EN', 'lbl_gcheckout_deliver_order', 'Deliver order', '', 'Labels'),
('EN', 'lbl_gcheckout_financial_state', 'Financial state', '', 'Labels'),
('EN', 'lbl_gcheckout_fulfillment_commands', 'Fulfillment commands', '', 'Labels'),
('EN', 'lbl_gcheckout_fulfillment_state', 'Fulfillment state', '', 'Labels'),
('EN', 'lbl_gcheckout_giftcert_applied', 'You have used a Gift Certificate', '', 'Labels'),
('EN', 'lbl_gcheckout_issues_found', 'Issues found', '', 'Labels'),
('EN', 'lbl_gcheckout_item_discount', 'Discount', '', 'Labels'),
('EN', 'lbl_gcheckout_linked_orders', 'This Google Checkout order is linked with the following orders', '', 'Labels'),
('EN', 'lbl_gcheckout_order', 'Google Checkout Order', '', 'Labels'),
('EN', 'lbl_gcheckout_order_processing', 'Google Checkout: Order processing', '', 'Labels'),
('EN', 'lbl_gcheckout_or_use', 'or use', '', 'Labels'),
('EN', 'lbl_gcheckout_process_order', 'Process order', '', 'Labels'),
('EN', 'lbl_gcheckout_product_disabled', 'Sorry, this product is not available through Google Checkout', '', 'Labels'),
('EN', 'lbl_gcheckout_product_valid', 'Enable Google Checkout for this product', '', 'Labels'),
('EN', 'lbl_gcheckout_reason', 'Reason', '', 'Labels'),
('EN', 'lbl_gcheckout_refund_amount', 'Refund amount', '', 'Labels'),
('EN', 'lbl_gcheckout_refund_order', 'Refund order', '', 'Labels'),
('EN', 'lbl_gcheckout_req_coupon_codes', 'The same code is being used for a discount coupon and a gift certificate. It is recommended that you use different codes, because, if the code of some gift certificate and some discount coupon happen to be the same, the system will identify the code of the gift certificate as a discount coupon', '', 'Labels'),
('EN', 'lbl_gcheckout_req_display_taxed_order_totals', 'The option ''Display cart/order totals including tax'' is enabled (must be disabled).', '', 'Labels'),
('EN', 'lbl_gcheckout_req_mid', 'Merchant ID is empty', '', 'Labels'),
('EN', 'lbl_gcheckout_req_mkey', 'Merchant key is empty', '', 'Labels'),
('EN', 'lbl_gcheckout_req_realtime_shipping_enabled', 'Real-time shipping rates calculation is enabled. Using real-time shipping rates calculation in Eshop increases the risk of Eshop''s not being able to provide a merchant-calculation-results response to Google''s merchant-calculation-callback within the allowed period of three seconds. If Google does not receive a response within three seconds, it will use the backup tax and shipping values it received in the Checkout API request. If this represents a problem, disable real-time shipping rates calculation at your store.', '', 'Labels'),
('EN', 'lbl_gcheckout_req_tax_included_into_price', 'One or more taxes have the option ''Included into the product price'' enabled (must be disabled).', '', 'Labels'),
('EN', 'lbl_gcheckout_req_zone_masks', 'Google cannot discriminate between destination zones based on addresses and counties, so address and county masks should not be used when defining destination zones.', '', 'Labels'),
('EN', 'lbl_gcheckout_send_email', 'Send e-mail to the customer', '', 'Labels'),
('EN', 'lbl_gcheckout_send_message', 'Send message', '', 'Labels'),
('EN', 'lbl_gcheckout_test_callback_url', 'Test Callback URL', '', 'Labels'),
('EN', 'lbl_gcheckout_test_gc', 'Test Google Checkout accessibility', '', 'Labels'),
('EN', 'lbl_gcheckout_unarchive_order', 'Unarchive order', '', 'Labels'),
('EN', 'lbl_google_checkout', 'Google Checkout', '', 'Labels'),
('EN', 'addon_descr_Google_Checkout', 'This addon enables Google checkout for your store.', '', 'Addons'),
('en', 'addon_name_google_checkout', 'Google Checkout', '', 'Addons'),
('EN', 'option_title_Google_Checkout', 'Google Checkout Options', '', 'Options'),
('EN', 'txt_gcheckout_add_coupon_note', '<b>Note:</b> If you are going to use Google Checkout and wish to redeem a discount coupon, please enter its code on  Google Checkout''s Place Order page.', '', 'Text'),
('EN', 'txt_gcheckout_archiving_commands_note', 'Archiving commands enable you to manage the list of orders in your Merchant Center inbox: the ''Archive order'' command moves the order from the Merchant Center inbox to the Merchant Center archive; the ''Unarchive order'' command restores the order from the archive back to the inbox. Archiving commands do not have any impact on the order''s state or on the information that is communicated to the customer in connection with the order. It is recommended that you only archive orders after they have been delivered or canceled.', '', 'Text'),
('EN', 'txt_gcheckout_callback_test_failure', 'Your Callback URL is not accessible!<br /><br />Please, make sure you have a valid SSL certificate installed. Pay attention to the fact that Google Checkout does not accept SSL certicates from certain issuers. Contact Google Checkout support to make sure your SSL certificate will be accepted.', '', 'Text'),
('EN', 'txt_gcheckout_callback_test_success', 'The test has been passed successfully. Your Callback URL is properly configured and should be accessible by Google Checkout.<br /><br /><b>Warning!</b> Please, pay attention to the fact that Google Checkout does not accept SSL certicates from certain issuers. Contact Google Checkout support to make sure your SSL certificate will be accepted.', '', 'Text'),
('EN', 'txt_gcheckout_cancel_order_note', 'This command instructs Google Checkout to cancel the order. Use the fields below to specify a reason why you canceled the order and provide a comment.', '', 'Text'),
('EN', 'txt_gcheckout_charge_order_note', 'This command instructs Google Checkout to charge the buyer for the order. After the order reaches the CHARGEABLE order state, you have 72 hours to capture funds by issuing the ''Charge order'' command.', '', 'Text'),
('EN', 'txt_gcheckout_deliver_order_note', 'The ''Deliver'' command instructs Google Checkout to update the order''s fulfillment state from either NEW or PROCESSING to DELIVERED. You may want to send this command after the order has been charged and shipped. The ''Add tracking data'' command instructs Google Checkout to associate a shipper''s tracking number with the order.', '', 'Text'),
('EN', 'txt_gcheckout_disabled', 'Google Checkout is disabled. Please, check the account settings on the <a href="index.php?target=configuration&option=Google_Checkout">General settings/Google Checkout options</a> page.', '', 'Text'),
('EN', 'txt_gcheckout_error_redirect', 'Cannot redirect to Google Checkout server. Please try again later.', '', 'Text'),
('EN', 'txt_gcheckout_impossible_error', 'Error: Cannot start Google checkout because a unique key for transaction could not be created.', '', 'Text'),
('EN', 'txt_gcheckout_order_delivered', 'The order is in the DELIVERED status', '', 'Text'),
('EN', 'txt_gcheckout_order_list_status_note', '<b>Note:</b> Statuses of the orders created using Google Checkout are displayed in disabled drop-down boxes to disallow their manual updating without updating the Google Checkout statuses of the respective orders. To update the order statuses, use the controls of the ''Google Checkout: Order processing'' section of the order''s details page or the controls of Google Checkout Merchant Center.', '', 'Text'),
('EN', 'txt_gcheckout_order_status_note', '<b>Note:</b> This order has been created using Google Checkout. Please, use Google Checkout commands to update the order status.', '', 'Text'),
('EN', 'txt_gcheckout_process_order_note', 'The ''Process order'' command instructs Google Checkout to update the order''s fulfillment state from NEW to PROCESSING.', '', 'Text'),
('EN', 'txt_gcheckout_refund_order_note', 'This command instructs Google Checkout to refund the buyer for the order. You may issue the ''Refund order'' command after the order has been charged and is in the CHARGED financial order state.\r\n<br />\r\n<br />\r\n<b>Note:</b> The ''Refund order'' command will not affect the current state of the order in your store. If you wish the order state change caused by the refund to take effect on the side of the store, you will need to edit the order manually using X-AOM (Advanced Order Management) add-on module.', '', 'Text'),
('EN', 'txt_gcheckout_requirements_failed_note', 'To ensure seamless integration of Google Checkout with your store, Eshop''s Google Checkout module imposes some restrictions on the store configuration. The system has checked the store configuration for compliance with these restrictions and detected some problems. Please resolve the problems listed below to ensure that the module works correctly.', '', 'Text'),
('EN', 'txt_gcheckout_send_email_note', 'This command instructs Google Checkout to place a message in the customer''s Google Checkout account. It may also include an optional argument instructing Google Checkout to also send the message to the customer by email. This command does not impact the order''s fulfillment state.', '', 'Text'),
('EN', 'txt_gcheckout_setup_note', 'To set up your Google Checkout module, please adjust the fields below. You should obtain your Merchant ID and Merchant key values from your Google Checkout account. Choose ''Test mode'' if you are going to use a Sandbox account. Choose ''Live mode'' if you are going to use your production account.<br /><br />\r\nThis URL should be used as an ''API callback URL'' in your Google Checkout account:<br />\r\n<b>{{callback_url}}</b><br /><br />\r\n(Log in to your Merchant Center account, click on the ''Settings'' tab, then click on the ''Integration'' link in the menu on the left side of the page. Enter this URL into the field ''API callback URL'')<br /><br />\r\n<b>Note:</b> Make sure this callback URL is secured by <a href="http://code.google.com/apis/checkout/developer/index.html#security_precautions" target="_new">HTTP Basic Authentication</a>.<br /><br />Please note that, in Live mode, Google Checkout only communicates with servers that have SSL certificates installed. Make sure your server has a valid SSL certificate, otherwise the module will not be able to function correctly, as your store will not be able to receive any messages or notifications from Google Checkout.<br /><br />\r\nIn Test mode, an http connection can be used.<br /><br />\r\nPlease be aware that Google Checkout cannot be used for certain kinds of products (see <a href="http://checkout.google.com/seller/content_policies.html" target="_new">Google Checkout: Content policies</a> for details). If your store sells products that do not comply with Google Checkout content policies, you should disable Google Checkout for these products by deselecting the check box ''Enable Google Checkout for this product'' on their details pages.<br /><br />\r\nVisit <a href="http://code.google.com/apis/checkout/" target="_new">this page</a> to learn more about Google Checkout API.', '', 'Text'),
('EN', 'txt_gcheckout_status_update_note', '<b>Note:</b> Please be aware that Google Checkout takes some time to update an order status after you issue a status update request, so you should expect a delay before the updated status appears in the order details or in the order state log.', '', 'Text'),
('EN', 'txt_gcheckout_test_failure', 'Google Checkout accessibility test failed!<br /><br />Please, make sure you specified correct ''Merchant ID'' and ''Merchant key'' values.', '', 'Text'),
('EN', 'txt_gcheckout_test_success', 'Google Checkout accessibility test has been passed successfully.', '', 'Text'),
('EN', 'txt_gcheckout_valid_carriers', '(Note: Allowed values for carrier are DHL, FedEx, UPS, USPS and Other)', '', 'Text'),
('EN', 'config_google_checkout', 'Google_Checkout', '', 'config');
*/

delete from `cw_languages` WHERE `name` LIKE '%google_checkout%' OR `name` LIKE '%gcheckout%';
delete from `cw_langvars_statistics` WHERE `name` LIKE '%google_checkout%' OR `name` LIKE '%gcheckout%';
delete from `cw_languages_alt` WHERE `name` LIKE '%google_checkout%' OR `name` LIKE '%gcheckout%';

select @cid:=config_category_id from cw_config_categories where category='google_checkout';

delete from cw_config where config_category_id=@cid;
delete from cw_config_categories where category='google_checkout';

/*
INSERT INTO `cw_config` (`name`, `comment`, `value`, `config_category_id`, `orderby`, `type`, `defvalue`, `variants`) VALUES
('test_mode', 'Test mode', 'Y', 83, 5, 'checkbox', 'N', ''),
('merchant_calc', 'Cancel order if merchant calculations fail', 'N', 83, 80, 'checkbox', 'N', ''),
('default_shipping_cost', 'Default shipping cost (will be used if Google Checkout does not receive a correct XML response from your store)', '10', 83, 90, 'numeric', '', ''),
('currency', 'Seller account currency', 'USD', 83, 40, 'selector', '', 'USD:USD\nGBP:GBP'),
('check_avs', 'Charge order if the result of AVS check-up is', 'N', 83, 50, 'selector', '', 'Y:Full AVS match (address and postal code)\nP:Partial AVS match (postal code only)\nA:Partial AVS match (address only)\nN:No AVS match\nU:AVS not supported by issuer'),
('check_cvn', 'Charge order if the result of CVN check-up is', 'N', 83, 60, 'selector', '', 'M:CVN match\nN:No CVN match\nU:CVN not available\nE:CVN error'),
('check_prot', 'Charge order only if it is eligible for Google Checkout''s payment guarantee policy', '', 83, 70, 'selector', '', ''),
('prefix', 'Order prefix', '', 83, 91, 'text', '', ''),
('mid', 'Merchant ID', '975226313580298', 83, 10, 'text', '', ''),
('mkey', 'Merchant key', 'GTOIFVd6TCIXMBmfCrJjzQ', 83, 20, 'text', '', '');
*/

delete from cw_addons where addon='google_checkout';

drop table cw_docs_gcheckout, cw_google_checkout, cw_gcheckout_orders, cw_gcheckout_restrictions;

/*
--
-- Структура таблицы `cw_docs_gcheckout`
--

CREATE TABLE IF NOT EXISTS `cw_docs_gcheckout` (
  `doc_id` int(11) NOT NULL DEFAULT '0',
  `google_id` varchar(255) NOT NULL DEFAULT '',
  `total` decimal(12,4) NOT NULL DEFAULT '0.0000',
  `refunded_amount` decimal(12,4) NOT NULL DEFAULT '0.0000',
  `fulfillment_state` varchar(255) NOT NULL DEFAULT '',
  `financial_state` varchar(255) NOT NULL DEFAULT '',
  `state_log` mediumtext NOT NULL,
  `archived` char(1) NOT NULL DEFAULT 'N',
  PRIMARY KEY (`doc_id`),
  KEY `google_id` (`google_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `cw_gcheckout_orders`
--

CREATE TABLE IF NOT EXISTS `cw_gcheckout_orders` (
  `order_id` int(11) NOT NULL DEFAULT '0',
  `google_id` varchar(255) NOT NULL DEFAULT '',
  `total` decimal(12,4) NOT NULL DEFAULT '0.0000',
  `refunded_amount` decimal(12,4) NOT NULL DEFAULT '0.0000',
  `fulfillment_state` varchar(255) NOT NULL DEFAULT '',
  `financial_state` varchar(255) NOT NULL DEFAULT '',
  `state_log` mediumtext NOT NULL,
  `archived` char(1) NOT NULL DEFAULT 'N',
  PRIMARY KEY (`order_id`),
  KEY `google_id` (`google_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `cw_gcheckout_restrictions`
--

CREATE TABLE IF NOT EXISTS `cw_gcheckout_restrictions` (
  `product_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`product_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `cw_google_checkout`
--

CREATE TABLE IF NOT EXISTS `cw_google_checkout` (
  `doc_id` int(11) NOT NULL DEFAULT '0',
  `google_order_id` varchar(255) NOT NULL DEFAULT '',
  `total` decimal(12,2) NOT NULL DEFAULT '0.00',
  `refunded_amount` decimal(12,2) NOT NULL DEFAULT '0.00',
  `fulfillment_state` varchar(255) NOT NULL DEFAULT '',
  `financial_state` varchar(255) NOT NULL DEFAULT '',
  `state_log` text NOT NULL,
  `archived` char(1) NOT NULL DEFAULT 'N',
  `ref_id` varchar(32) NOT NULL DEFAULT '',
  PRIMARY KEY (`google_order_id`),
  KEY `google_order_id` (`google_order_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
*/
