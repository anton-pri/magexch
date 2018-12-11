ALTER TABLE `cw_shipping_carriers` CHANGE `module` `addon` VARCHAR( 255 );
ALTER TABLE `cw_attributes` CHANGE `module` `addon` VARCHAR( 255 );
ALTER TABLE `cw_attributes` CHANGE `is_show_module` `is_show_addon` INT( 1 ) NOT NULL DEFAULT '1';
ALTER TABLE `cw_navigation_menu` CHANGE `module` `addon` VARCHAR( 255 ) NOT NULL DEFAULT '';
ALTER TABLE `cw_navigation_sections` CHANGE `module` `addon` VARCHAR( 255 ) NOT NULL DEFAULT '';
ALTER TABLE `cw_navigation_targets` CHANGE `module` `addon` VARCHAR( 255 ) NOT NULL DEFAULT '';
ALTER TABLE `cw_sections_pos` CHANGE `module` `addon` VARCHAR( 255 ) NOT NULL DEFAULT '';

UPDATE `cw_payment_settings` SET `processor` = replace( `processor` , '-', '_') ;

DROP TABLE IF EXISTS `cw_modules`;

--
-- Структура таблицы `cw_addons`
--

CREATE TABLE IF NOT EXISTS `cw_addons` (
  `addon` varchar(255) NOT NULL DEFAULT '',
  `descr` varchar(255) NOT NULL DEFAULT '',
  `active` int(1) NOT NULL DEFAULT '0',
  `status` int(11) NOT NULL DEFAULT '0',
  `parent` varchar(255) NOT NULL DEFAULT '',
  `version` varchar(16) NOT NULL DEFAULT '0.1',
  `orderby` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`addon`),
  KEY `active` (`active`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



UPDATE cw_attributes SET addon='accessories' WHERE addon='Accessories';
UPDATE cw_attributes SET addon='ad_banners' WHERE addon='Ad_Banners';
UPDATE cw_attributes SET addon='advanced_order_management' WHERE addon='Advanced_Order_Management';
UPDATE cw_attributes SET addon='ajax_add2cart' WHERE addon='ajax-add2cart';
UPDATE cw_attributes SET addon='amazon' WHERE addon='Amazon';
UPDATE cw_attributes SET addon='barcode' WHERE addon='BarCode';
UPDATE cw_attributes SET addon='bestsellers' WHERE addon='Bestsellers';
UPDATE cw_attributes SET addon='bookmarks' WHERE addon='bookmarks';
UPDATE cw_attributes SET addon='clean_urls' WHERE addon='clean-urls';
UPDATE cw_attributes SET addon='dashboard' WHERE addon='dashboard';
UPDATE cw_attributes SET addon='demo_module' WHERE addon='Demo_Module';
UPDATE cw_attributes SET addon='detailed_product_images' WHERE addon='Detailed_Product_Images';
UPDATE cw_attributes SET addon='discount_coupons' WHERE addon='Discount_Coupons';
UPDATE cw_attributes SET addon='ebay' WHERE addon='Ebay';
UPDATE cw_attributes SET addon='egoods' WHERE addon='Egoods';
UPDATE cw_attributes SET addon='estore_category_tree' WHERE addon='EStoreCategoryTree';
UPDATE cw_attributes SET addon='estore_gift' WHERE addon='EStoreGift';
UPDATE cw_attributes SET addon='estore_products_review' WHERE addon='EStoreProductsReview';
UPDATE cw_attributes SET addon='fancy_categories' WHERE addon='Fancy_Categories';
UPDATE cw_attributes SET addon='faq' WHERE addon='FAQ';
UPDATE cw_attributes SET addon='fbauth' WHERE addon='FBauth';
UPDATE cw_attributes SET addon='feedback_report' WHERE addon='Feedback_report';
UPDATE cw_attributes SET addon='froogle' WHERE addon='Froogle';
UPDATE cw_attributes SET addon='google_analytics' WHERE addon='GoogleAnalytics';
UPDATE cw_attributes SET addon='google_base' WHERE addon='GoogleBase';
UPDATE cw_attributes SET addon='google_checkout' WHERE addon='google-checkout';
UPDATE cw_attributes SET addon='image_verification' WHERE addon='Image_Verification';
UPDATE cw_attributes SET addon='import_3x_4x' WHERE addon='Import_3x_4x';
UPDATE cw_attributes SET addon='interneka' WHERE addon='Interneka';
UPDATE cw_attributes SET addon='magnifier' WHERE addon='Magnifier';
UPDATE cw_attributes SET addon='mailchimp_subscription' WHERE addon='Mailchimp_Subscription';
UPDATE cw_attributes SET addon='manufacturers' WHERE addon='Manufacturers';
UPDATE cw_attributes SET addon='mobile' WHERE addon='Mobile';
UPDATE cw_attributes SET addon='modules_manager' WHERE addon='Modules_Manager';
UPDATE cw_attributes SET addon='multi_domains' WHERE addon='multi-domains';
UPDATE cw_attributes SET addon='multimedia_products' WHERE addon='Multimedia_Products';
UPDATE cw_attributes SET addon='news' WHERE addon='news';
UPDATE cw_attributes SET addon='now_online' WHERE addon='NowOnLine';
UPDATE cw_attributes SET addon='order_tracking' WHERE addon='Order_Tracking';
UPDATE cw_attributes SET addon='payment_authorize_sim' WHERE addon='payment-authorize-sim';
UPDATE cw_attributes SET addon='payment_system' WHERE addon='payment-system';
UPDATE cw_attributes SET addon='paypal_express' WHERE addon='paypal-express';
UPDATE cw_attributes SET addon='pos' WHERE addon='POS';
UPDATE cw_attributes SET addon='ppd' WHERE addon='ppd';
UPDATE cw_attributes SET addon='product_options' WHERE addon='product-options';
UPDATE cw_attributes SET addon='product_tabs' WHERE addon='product_tabs';
UPDATE cw_attributes SET addon='promo' WHERE addon='promo';
UPDATE cw_attributes SET addon='quickbooks' WHERE addon='QuickBooks';
UPDATE cw_attributes SET addon='recommended_products' WHERE addon='Recommended_Products';
UPDATE cw_attributes SET addon='remember_anonymouse_carts' WHERE addon='remember_anonymouse_carts';
UPDATE cw_attributes SET addon='serial_numbers' WHERE addon='serial_numbers';
UPDATE cw_attributes SET addon='shipping_fedex' WHERE addon='shipping-fedex';
UPDATE cw_attributes SET addon='shipping_label_generator' WHERE addon='Shipping_Label_Generator';
UPDATE cw_attributes SET addon='shipping_system' WHERE addon='shipping-system';
UPDATE cw_attributes SET addon='shipping_tnt' WHERE addon='shipping-tnt';
UPDATE cw_attributes SET addon='shipping_ups' WHERE addon='shipping-ups';
UPDATE cw_attributes SET addon='sitemap_xml' WHERE addon='Sitemap_XML';
UPDATE cw_attributes SET addon='sn' WHERE addon='SN';
UPDATE cw_attributes SET addon='special_offers' WHERE addon='Special_Offers';
UPDATE cw_attributes SET addon='stop_list' WHERE addon='Stop_List';
UPDATE cw_attributes SET addon='subscriptions' WHERE addon='Subscriptions';
UPDATE cw_attributes SET addon='survey' WHERE addon='Survey';
UPDATE cw_attributes SET addon='top_menu' WHERE addon='Top_Menu';
UPDATE cw_attributes SET addon='users_online' WHERE addon='Users_online';
UPDATE cw_attributes SET addon='wholesale_trading' WHERE addon='Wholesale_Trading';

UPDATE cw_shipping_carriers SET addon='shipping_fedex' WHERE addon='shipping-fedex';
UPDATE cw_shipping_carriers SET addon='shipping_label_generator' WHERE addon='Shipping_Label_Generator';
UPDATE cw_shipping_carriers SET addon='shipping_system' WHERE addon='shipping-system';
UPDATE cw_shipping_carriers SET addon='shipping_tnt' WHERE addon='shipping-tnt';
UPDATE cw_shipping_carriers SET addon='shipping_ups' WHERE addon='shipping-ups';


UPDATE cw_languages SET name='addon_descr_accessories' WHERE name='module_descr_Accessories';
UPDATE cw_languages SET name='addon_descr_ad_banners' WHERE name='module_descr_Ad_Banners';
UPDATE cw_languages SET name='addon_descr_advanced_order_management' WHERE name='module_descr_Advanced_Order_Management';
UPDATE cw_languages SET name='addon_descr_ajax_add2cart' WHERE name='module_descr_ajax-add2cart';
UPDATE cw_languages SET name='addon_descr_amazon' WHERE name='module_descr_Amazon';
UPDATE cw_languages SET name='addon_descr_barcode' WHERE name='module_descr_BarCode';
UPDATE cw_languages SET name='addon_descr_bestsellers' WHERE name='module_descr_Bestsellers';
UPDATE cw_languages SET name='addon_descr_bookmarks' WHERE name='module_descr_bookmarks';
UPDATE cw_languages SET name='addon_descr_clean_urls' WHERE name='module_descr_clean-urls';
UPDATE cw_languages SET name='addon_descr_dashboard' WHERE name='module_descr_dashboard';
UPDATE cw_languages SET name='addon_descr_demo_module' WHERE name='module_descr_Demo_Module';
UPDATE cw_languages SET name='addon_descr_detailed_product_images' WHERE name='module_descr_Detailed_Product_Images';
UPDATE cw_languages SET name='addon_descr_discount_coupons' WHERE name='module_descr_Discount_Coupons';
UPDATE cw_languages SET name='addon_descr_ebay' WHERE name='module_descr_Ebay';
UPDATE cw_languages SET name='addon_descr_egoods' WHERE name='module_descr_Egoods';
UPDATE cw_languages SET name='addon_descr_estore_category_tree' WHERE name='module_descr_EStoreCategoryTree';
UPDATE cw_languages SET name='addon_descr_estore_gift' WHERE name='module_descr_EStoreGift';
UPDATE cw_languages SET name='addon_descr_estore_products_review' WHERE name='module_descr_EStoreProductsReview';
UPDATE cw_languages SET name='addon_descr_fancy_categories' WHERE name='module_descr_Fancy_Categories';
UPDATE cw_languages SET name='addon_descr_faq' WHERE name='module_descr_FAQ';
UPDATE cw_languages SET name='addon_descr_fbauth' WHERE name='module_descr_FBauth';
UPDATE cw_languages SET name='addon_descr_feedback_report' WHERE name='module_descr_Feedback_report';
UPDATE cw_languages SET name='addon_descr_froogle' WHERE name='module_descr_Froogle';
UPDATE cw_languages SET name='addon_descr_google_analytics' WHERE name='module_descr_GoogleAnalytics';
UPDATE cw_languages SET name='addon_descr_google_base' WHERE name='module_descr_GoogleBase';
UPDATE cw_languages SET name='addon_descr_google_checkout' WHERE name='module_descr_google-checkout';
UPDATE cw_languages SET name='addon_descr_image_verification' WHERE name='module_descr_Image_Verification';
UPDATE cw_languages SET name='addon_descr_import_3x_4x' WHERE name='module_descr_Import_3x_4x';
UPDATE cw_languages SET name='addon_descr_interneka' WHERE name='module_descr_Interneka';
UPDATE cw_languages SET name='addon_descr_magnifier' WHERE name='module_descr_Magnifier';
UPDATE cw_languages SET name='addon_descr_mailchimp_subscription' WHERE name='module_descr_Mailchimp_Subscription';
UPDATE cw_languages SET name='addon_descr_manufacturers' WHERE name='module_descr_Manufacturers';
UPDATE cw_languages SET name='addon_descr_mobile' WHERE name='module_descr_Mobile';
UPDATE cw_languages SET name='addon_descr_modules_manager' WHERE name='module_descr_Modules_Manager';
UPDATE cw_languages SET name='addon_descr_multi_domains' WHERE name='module_descr_multi-domains';
UPDATE cw_languages SET name='addon_descr_multimedia_products' WHERE name='module_descr_Multimedia_Products';
UPDATE cw_languages SET name='addon_descr_news' WHERE name='module_descr_news';
UPDATE cw_languages SET name='addon_descr_now_online' WHERE name='module_descr_NowOnLine';
UPDATE cw_languages SET name='addon_descr_order_tracking' WHERE name='module_descr_Order_Tracking';
UPDATE cw_languages SET name='addon_descr_payment_authorize_sim' WHERE name='module_descr_payment-authorize-sim';
UPDATE cw_languages SET name='addon_descr_payment_system' WHERE name='module_descr_payment-system';
UPDATE cw_languages SET name='addon_descr_paypal_express' WHERE name='module_descr_paypal-express';
UPDATE cw_languages SET name='addon_descr_pos' WHERE name='module_descr_POS';
UPDATE cw_languages SET name='addon_descr_ppd' WHERE name='module_descr_ppd';
UPDATE cw_languages SET name='addon_descr_product_options' WHERE name='module_descr_product-options';
UPDATE cw_languages SET name='addon_descr_product_tabs' WHERE name='module_descr_product_tabs';
UPDATE cw_languages SET name='addon_descr_promo' WHERE name='module_descr_promo';
UPDATE cw_languages SET name='addon_descr_quickbooks' WHERE name='module_descr_QuickBooks';
UPDATE cw_languages SET name='addon_descr_recommended_products' WHERE name='module_descr_Recommended_Products';
UPDATE cw_languages SET name='addon_descr_remember_anonymouse_carts' WHERE name='module_descr_remember_anonymouse_carts';
UPDATE cw_languages SET name='addon_descr_serial_numbers' WHERE name='module_descr_serial_numbers';
UPDATE cw_languages SET name='addon_descr_shipping_fedex' WHERE name='module_descr_shipping-fedex';
UPDATE cw_languages SET name='addon_descr_shipping_label_generator' WHERE name='module_descr_Shipping_Label_Generator';
UPDATE cw_languages SET name='addon_descr_shipping_system' WHERE name='module_descr_shipping-system';
UPDATE cw_languages SET name='addon_descr_shipping_tnt' WHERE name='module_descr_shipping-tnt';
UPDATE cw_languages SET name='addon_descr_shipping_ups' WHERE name='module_descr_shipping-ups';
UPDATE cw_languages SET name='addon_descr_sitemap_xml' WHERE name='module_descr_Sitemap_XML';
UPDATE cw_languages SET name='addon_descr_sn' WHERE name='module_descr_SN';
UPDATE cw_languages SET name='addon_descr_special_offers' WHERE name='module_descr_Special_Offers';
UPDATE cw_languages SET name='addon_descr_stop_list' WHERE name='module_descr_Stop_List';
UPDATE cw_languages SET name='addon_descr_subscriptions' WHERE name='module_descr_Subscriptions';
UPDATE cw_languages SET name='addon_descr_survey' WHERE name='module_descr_Survey';
UPDATE cw_languages SET name='addon_descr_top_menu' WHERE name='module_descr_Top_Menu';
UPDATE cw_languages SET name='addon_descr_users_online' WHERE name='module_descr_Users_online';
UPDATE cw_languages SET name='addon_descr_wholesale_trading' WHERE name='module_descr_Wholesale_Trading';


UPDATE cw_languages SET name='addon_name_accessories' WHERE name='module_name_Accessories';
UPDATE cw_languages SET name='addon_name_ad_banners' WHERE name='module_name_Ad_Banners';
UPDATE cw_languages SET name='addon_name_advanced_order_management' WHERE name='module_name_Advanced_Order_Management';
UPDATE cw_languages SET name='addon_name_ajax_add2cart' WHERE name='module_name_ajax-add2cart';
UPDATE cw_languages SET name='addon_name_amazon' WHERE name='module_name_Amazon';
UPDATE cw_languages SET name='addon_name_barcode' WHERE name='module_name_BarCode';
UPDATE cw_languages SET name='addon_name_bestsellers' WHERE name='module_name_Bestsellers';
UPDATE cw_languages SET name='addon_name_bookmarks' WHERE name='module_name_bookmarks';
UPDATE cw_languages SET name='addon_name_clean_urls' WHERE name='module_name_clean-urls';
UPDATE cw_languages SET name='addon_name_dashboard' WHERE name='module_name_dashboard';
UPDATE cw_languages SET name='addon_name_demo_module' WHERE name='module_name_Demo_Module';
UPDATE cw_languages SET name='addon_name_detailed_product_images' WHERE name='module_name_Detailed_Product_Images';
UPDATE cw_languages SET name='addon_name_discount_coupons' WHERE name='module_name_Discount_Coupons';
UPDATE cw_languages SET name='addon_name_ebay' WHERE name='module_name_Ebay';
UPDATE cw_languages SET name='addon_name_egoods' WHERE name='module_name_Egoods';
UPDATE cw_languages SET name='addon_name_estore_category_tree' WHERE name='module_name_EStoreCategoryTree';
UPDATE cw_languages SET name='addon_name_estore_gift' WHERE name='module_name_EStoreGift';
UPDATE cw_languages SET name='addon_name_estore_products_review' WHERE name='module_name_EStoreProductsReview';
UPDATE cw_languages SET name='addon_name_fancy_categories' WHERE name='module_name_Fancy_Categories';
UPDATE cw_languages SET name='addon_name_faq' WHERE name='module_name_FAQ';
UPDATE cw_languages SET name='addon_name_fbauth' WHERE name='module_name_FBauth';
UPDATE cw_languages SET name='addon_name_feedback_report' WHERE name='module_name_Feedback_report';
UPDATE cw_languages SET name='addon_name_froogle' WHERE name='module_name_Froogle';
UPDATE cw_languages SET name='addon_name_google_analytics' WHERE name='module_name_GoogleAnalytics';
UPDATE cw_languages SET name='addon_name_google_base' WHERE name='module_name_GoogleBase';
UPDATE cw_languages SET name='addon_name_google_checkout' WHERE name='module_name_google-checkout';
UPDATE cw_languages SET name='addon_name_image_verification' WHERE name='module_name_Image_Verification';
UPDATE cw_languages SET name='addon_name_import_3x_4x' WHERE name='module_name_Import_3x_4x';
UPDATE cw_languages SET name='addon_name_interneka' WHERE name='module_name_Interneka';
UPDATE cw_languages SET name='addon_name_magnifier' WHERE name='module_name_Magnifier';
UPDATE cw_languages SET name='addon_name_mailchimp_subscription' WHERE name='module_name_Mailchimp_Subscription';
UPDATE cw_languages SET name='addon_name_manufacturers' WHERE name='module_name_Manufacturers';
UPDATE cw_languages SET name='addon_name_mobile' WHERE name='module_name_Mobile';
UPDATE cw_languages SET name='addon_name_modules_manager' WHERE name='module_name_Modules_Manager';
UPDATE cw_languages SET name='addon_name_multi_domains' WHERE name='module_name_multi-domains';
UPDATE cw_languages SET name='addon_name_multimedia_products' WHERE name='module_name_Multimedia_Products';
UPDATE cw_languages SET name='addon_name_news' WHERE name='module_name_news';
UPDATE cw_languages SET name='addon_name_now_online' WHERE name='module_name_NowOnLine';
UPDATE cw_languages SET name='addon_name_order_tracking' WHERE name='module_name_Order_Tracking';
UPDATE cw_languages SET name='addon_name_payment_authorize_sim' WHERE name='module_name_payment-authorize-sim';
UPDATE cw_languages SET name='addon_name_payment_system' WHERE name='module_name_payment-system';
UPDATE cw_languages SET name='addon_name_paypal_express' WHERE name='module_name_paypal-express';
UPDATE cw_languages SET name='addon_name_pos' WHERE name='module_name_POS';
UPDATE cw_languages SET name='addon_name_ppd' WHERE name='module_name_ppd';
UPDATE cw_languages SET name='addon_name_product_options' WHERE name='module_name_product-options';
UPDATE cw_languages SET name='addon_name_product_tabs' WHERE name='module_name_product_tabs';
UPDATE cw_languages SET name='addon_name_promo' WHERE name='module_name_promo';
UPDATE cw_languages SET name='addon_name_quickbooks' WHERE name='module_name_QuickBooks';
UPDATE cw_languages SET name='addon_name_recommended_products' WHERE name='module_name_Recommended_Products';
UPDATE cw_languages SET name='addon_name_remember_anonymouse_carts' WHERE name='module_name_remember_anonymouse_carts';
UPDATE cw_languages SET name='addon_name_serial_numbers' WHERE name='module_name_serial_numbers';
UPDATE cw_languages SET name='addon_name_shipping_fedex' WHERE name='module_name_shipping-fedex';
UPDATE cw_languages SET name='addon_name_shipping_label_generator' WHERE name='module_name_Shipping_Label_Generator';
UPDATE cw_languages SET name='addon_name_shipping_system' WHERE name='module_name_shipping-system';
UPDATE cw_languages SET name='addon_name_shipping_tnt' WHERE name='module_name_shipping-tnt';
UPDATE cw_languages SET name='addon_name_shipping_ups' WHERE name='module_name_shipping-ups';
UPDATE cw_languages SET name='addon_name_sitemap_xml' WHERE name='module_name_Sitemap_XML';
UPDATE cw_languages SET name='addon_name_sn' WHERE name='module_name_SN';
UPDATE cw_languages SET name='addon_name_special_offers' WHERE name='module_name_Special_Offers';
UPDATE cw_languages SET name='addon_name_stop_list' WHERE name='module_name_Stop_List';
UPDATE cw_languages SET name='addon_name_subscriptions' WHERE name='module_name_Subscriptions';
UPDATE cw_languages SET name='addon_name_survey' WHERE name='module_name_Survey';
UPDATE cw_languages SET name='addon_name_top_menu' WHERE name='module_name_Top_Menu';
UPDATE cw_languages SET name='addon_name_users_online' WHERE name='module_name_Users_online';
UPDATE cw_languages SET name='addon_name_wholesale_trading' WHERE name='module_name_Wholesale_Trading';
UPDATE cw_languages SET name='option_title_accessories' WHERE name='option_title_Accessories';
UPDATE cw_languages SET name='option_title_ad_banners' WHERE name='option_title_Ad_Banners';
UPDATE cw_languages SET name='option_title_advanced_order_management' WHERE name='option_title_Advanced_Order_Management';
UPDATE cw_languages SET name='option_title_ajax_add2cart' WHERE name='option_title_ajax-add2cart';
UPDATE cw_languages SET name='option_title_amazon' WHERE name='option_title_Amazon';
UPDATE cw_languages SET name='option_title_barcode' WHERE name='option_title_BarCode';
UPDATE cw_languages SET name='option_title_bestsellers' WHERE name='option_title_Bestsellers';
UPDATE cw_languages SET name='option_title_bookmarks' WHERE name='option_title_bookmarks';
UPDATE cw_languages SET name='option_title_clean_urls' WHERE name='option_title_clean-urls';
UPDATE cw_languages SET name='option_title_dashboard' WHERE name='option_title_dashboard';
UPDATE cw_languages SET name='option_title_demo_module' WHERE name='option_title_Demo_Module';
UPDATE cw_languages SET name='option_title_detailed_product_images' WHERE name='option_title_Detailed_Product_Images';
UPDATE cw_languages SET name='option_title_discount_coupons' WHERE name='option_title_Discount_Coupons';
UPDATE cw_languages SET name='option_title_ebay' WHERE name='option_title_Ebay';
UPDATE cw_languages SET name='option_title_egoods' WHERE name='option_title_Egoods';
UPDATE cw_languages SET name='option_title_estore_category_tree' WHERE name='option_title_EStoreCategoryTree';
UPDATE cw_languages SET name='option_title_estore_gift' WHERE name='option_title_EStoreGift';
UPDATE cw_languages SET name='option_title_estore_products_review' WHERE name='option_title_EStoreProductsReview';
UPDATE cw_languages SET name='option_title_fancy_categories' WHERE name='option_title_Fancy_Categories';
UPDATE cw_languages SET name='option_title_faq' WHERE name='option_title_FAQ';
UPDATE cw_languages SET name='option_title_fbauth' WHERE name='option_title_FBauth';
UPDATE cw_languages SET name='option_title_feedback_report' WHERE name='option_title_Feedback_report';
UPDATE cw_languages SET name='option_title_froogle' WHERE name='option_title_Froogle';
UPDATE cw_languages SET name='option_title_google_analytics' WHERE name='option_title_GoogleAnalytics';
UPDATE cw_languages SET name='option_title_google_base' WHERE name='option_title_GoogleBase';
UPDATE cw_languages SET name='option_title_google_checkout' WHERE name='option_title_google-checkout';
UPDATE cw_languages SET name='option_title_image_verification' WHERE name='option_title_Image_Verification';
UPDATE cw_languages SET name='option_title_import_3x_4x' WHERE name='option_title_Import_3x_4x';
UPDATE cw_languages SET name='option_title_interneka' WHERE name='option_title_Interneka';
UPDATE cw_languages SET name='option_title_magnifier' WHERE name='option_title_Magnifier';
UPDATE cw_languages SET name='option_title_mailchimp_subscription' WHERE name='option_title_Mailchimp_Subscription';
UPDATE cw_languages SET name='option_title_manufacturers' WHERE name='option_title_Manufacturers';
UPDATE cw_languages SET name='option_title_mobile' WHERE name='option_title_Mobile';
UPDATE cw_languages SET name='option_title_modules_manager' WHERE name='option_title_Modules_Manager';
UPDATE cw_languages SET name='option_title_multi_domains' WHERE name='option_title_multi-domains';
UPDATE cw_languages SET name='option_title_multimedia_products' WHERE name='option_title_Multimedia_Products';
UPDATE cw_languages SET name='option_title_news' WHERE name='option_title_news';
UPDATE cw_languages SET name='option_title_now_online' WHERE name='option_title_NowOnLine';
UPDATE cw_languages SET name='option_title_order_tracking' WHERE name='option_title_Order_Tracking';
UPDATE cw_languages SET name='option_title_payment_authorize_sim' WHERE name='option_title_payment-authorize-sim';
UPDATE cw_languages SET name='option_title_payment_system' WHERE name='option_title_payment-system';
UPDATE cw_languages SET name='option_title_paypal_express' WHERE name='option_title_paypal-express';
UPDATE cw_languages SET name='option_title_pos' WHERE name='option_title_POS';
UPDATE cw_languages SET name='option_title_ppd' WHERE name='option_title_ppd';
UPDATE cw_languages SET name='option_title_product_options' WHERE name='option_title_product-options';
UPDATE cw_languages SET name='option_title_product_tabs' WHERE name='option_title_product_tabs';
UPDATE cw_languages SET name='option_title_promo' WHERE name='option_title_promo';
UPDATE cw_languages SET name='option_title_quickbooks' WHERE name='option_title_QuickBooks';
UPDATE cw_languages SET name='option_title_recommended_products' WHERE name='option_title_Recommended_Products';
UPDATE cw_languages SET name='option_title_remember_anonymouse_carts' WHERE name='option_title_remember_anonymouse_carts';
UPDATE cw_languages SET name='option_title_serial_numbers' WHERE name='option_title_serial_numbers';
UPDATE cw_languages SET name='option_title_shipping_fedex' WHERE name='option_title_shipping-fedex';
UPDATE cw_languages SET name='option_title_shipping_label_generator' WHERE name='option_title_Shipping_Label_Generator';
UPDATE cw_languages SET name='option_title_shipping_system' WHERE name='option_title_shipping-system';
UPDATE cw_languages SET name='option_title_shipping_tnt' WHERE name='option_title_shipping-tnt';
UPDATE cw_languages SET name='option_title_shipping_ups' WHERE name='option_title_shipping-ups';
UPDATE cw_languages SET name='option_title_sitemap_xml' WHERE name='option_title_Sitemap_XML';
UPDATE cw_languages SET name='option_title_sn' WHERE name='option_title_SN';
UPDATE cw_languages SET name='option_title_special_offers' WHERE name='option_title_Special_Offers';
UPDATE cw_languages SET name='option_title_stop_list' WHERE name='option_title_Stop_List';
UPDATE cw_languages SET name='option_title_subscriptions' WHERE name='option_title_Subscriptions';
UPDATE cw_languages SET name='option_title_survey' WHERE name='option_title_Survey';
UPDATE cw_languages SET name='option_title_top_menu' WHERE name='option_title_Top_Menu';
UPDATE cw_languages SET name='option_title_users_online' WHERE name='option_title_Users_online';
UPDATE cw_languages SET name='option_title_wholesale_trading' WHERE name='option_title_Wholesale_Trading';


UPDATE `cw_languages` SET name = replace( name, 'module', 'addon' ) , value = replace( value, 'module', 'addon' ) , topic = 'Addons' WHERE topic = 'Modules';

UPDATE cw_navigation_menu SET addon='accessories' WHERE addon='Accessories';
UPDATE cw_navigation_menu SET addon='ad_banners' WHERE addon='Ad_Banners';
UPDATE cw_navigation_menu SET addon='advanced_order_management' WHERE addon='Advanced_Order_Management';
UPDATE cw_navigation_menu SET addon='ajax_add2cart' WHERE addon='ajax-add2cart';
UPDATE cw_navigation_menu SET addon='amazon' WHERE addon='Amazon';
UPDATE cw_navigation_menu SET addon='barcode' WHERE addon='BarCode';
UPDATE cw_navigation_menu SET addon='bestsellers' WHERE addon='Bestsellers';
UPDATE cw_navigation_menu SET addon='bookmarks' WHERE addon='bookmarks';
UPDATE cw_navigation_menu SET addon='clean_urls' WHERE addon='clean-urls';
UPDATE cw_navigation_menu SET addon='dashboard' WHERE addon='dashboard';
UPDATE cw_navigation_menu SET addon='demo_module' WHERE addon='Demo_Module';
UPDATE cw_navigation_menu SET addon='detailed_product_images' WHERE addon='Detailed_Product_Images';
UPDATE cw_navigation_menu SET addon='discount_coupons' WHERE addon='Discount_Coupons';
UPDATE cw_navigation_menu SET addon='ebay' WHERE addon='Ebay';
UPDATE cw_navigation_menu SET addon='egoods' WHERE addon='Egoods';
UPDATE cw_navigation_menu SET addon='estore_category_tree' WHERE addon='EStoreCategoryTree';
UPDATE cw_navigation_menu SET addon='estore_gift' WHERE addon='EStoreGift';
UPDATE cw_navigation_menu SET addon='estore_products_review' WHERE addon='EStoreProductsReview';
UPDATE cw_navigation_menu SET addon='fancy_categories' WHERE addon='Fancy_Categories';
UPDATE cw_navigation_menu SET addon='faq' WHERE addon='FAQ';
UPDATE cw_navigation_menu SET addon='fbauth' WHERE addon='FBauth';
UPDATE cw_navigation_menu SET addon='feedback_report' WHERE addon='Feedback_report';
UPDATE cw_navigation_menu SET addon='froogle' WHERE addon='Froogle';
UPDATE cw_navigation_menu SET addon='google_analytics' WHERE addon='GoogleAnalytics';
UPDATE cw_navigation_menu SET addon='google_base' WHERE addon='GoogleBase';
UPDATE cw_navigation_menu SET addon='google_checkout' WHERE addon='google-checkout';
UPDATE cw_navigation_menu SET addon='image_verification' WHERE addon='Image_Verification';
UPDATE cw_navigation_menu SET addon='import_3x_4x' WHERE addon='Import_3x_4x';
UPDATE cw_navigation_menu SET addon='interneka' WHERE addon='Interneka';
UPDATE cw_navigation_menu SET addon='magnifier' WHERE addon='Magnifier';
UPDATE cw_navigation_menu SET addon='mailchimp_subscription' WHERE addon='Mailchimp_Subscription';
UPDATE cw_navigation_menu SET addon='manufacturers' WHERE addon='Manufacturers';
UPDATE cw_navigation_menu SET addon='mobile' WHERE addon='Mobile';
UPDATE cw_navigation_menu SET addon='modules_manager' WHERE addon='Modules_Manager';
UPDATE cw_navigation_menu SET addon='multi_domains' WHERE addon='multi-domains';
UPDATE cw_navigation_menu SET addon='multimedia_products' WHERE addon='Multimedia_Products';
UPDATE cw_navigation_menu SET addon='news' WHERE addon='news';
UPDATE cw_navigation_menu SET addon='now_online' WHERE addon='NowOnLine';
UPDATE cw_navigation_menu SET addon='order_tracking' WHERE addon='Order_Tracking';
UPDATE cw_navigation_menu SET addon='payment_authorize_sim' WHERE addon='payment-authorize-sim';
UPDATE cw_navigation_menu SET addon='payment_system' WHERE addon='payment-system';
UPDATE cw_navigation_menu SET addon='paypal_express' WHERE addon='paypal-express';
UPDATE cw_navigation_menu SET addon='pos' WHERE addon='POS';
UPDATE cw_navigation_menu SET addon='ppd' WHERE addon='ppd';
UPDATE cw_navigation_menu SET addon='product_options' WHERE addon='product-options';
UPDATE cw_navigation_menu SET addon='product_tabs' WHERE addon='product_tabs';
UPDATE cw_navigation_menu SET addon='promo' WHERE addon='promo';
UPDATE cw_navigation_menu SET addon='quickbooks' WHERE addon='QuickBooks';
UPDATE cw_navigation_menu SET addon='recommended_products' WHERE addon='Recommended_Products';
UPDATE cw_navigation_menu SET addon='remember_anonymouse_carts' WHERE addon='remember_anonymouse_carts';
UPDATE cw_navigation_menu SET addon='serial_numbers' WHERE addon='serial_numbers';
UPDATE cw_navigation_menu SET addon='shipping_fedex' WHERE addon='shipping-fedex';
UPDATE cw_navigation_menu SET addon='shipping_label_generator' WHERE addon='Shipping_Label_Generator';
UPDATE cw_navigation_menu SET addon='shipping_system' WHERE addon='shipping-system';
UPDATE cw_navigation_menu SET addon='shipping_tnt' WHERE addon='shipping-tnt';
UPDATE cw_navigation_menu SET addon='shipping_ups' WHERE addon='shipping-ups';
UPDATE cw_navigation_menu SET addon='sitemap_xml' WHERE addon='Sitemap_XML';
UPDATE cw_navigation_menu SET addon='sn' WHERE addon='SN';
UPDATE cw_navigation_menu SET addon='special_offers' WHERE addon='Special_Offers';
UPDATE cw_navigation_menu SET addon='stop_list' WHERE addon='Stop_List';
UPDATE cw_navigation_menu SET addon='subscriptions' WHERE addon='Subscriptions';
UPDATE cw_navigation_menu SET addon='survey' WHERE addon='Survey';
UPDATE cw_navigation_menu SET addon='top_menu' WHERE addon='Top_Menu';
UPDATE cw_navigation_menu SET addon='users_online' WHERE addon='Users_online';
UPDATE cw_navigation_menu SET addon='wholesale_trading' WHERE addon='Wholesale_Trading';


UPDATE cw_navigation_sections SET addon='accessories' WHERE addon='Accessories';
UPDATE cw_navigation_sections SET addon='ad_banners' WHERE addon='Ad_Banners';
UPDATE cw_navigation_sections SET addon='advanced_order_management' WHERE addon='Advanced_Order_Management';
UPDATE cw_navigation_sections SET addon='ajax_add2cart' WHERE addon='ajax-add2cart';
UPDATE cw_navigation_sections SET addon='amazon' WHERE addon='Amazon';
UPDATE cw_navigation_sections SET addon='barcode' WHERE addon='BarCode';
UPDATE cw_navigation_sections SET addon='bestsellers' WHERE addon='Bestsellers';
UPDATE cw_navigation_sections SET addon='bookmarks' WHERE addon='bookmarks';
UPDATE cw_navigation_sections SET addon='clean_urls' WHERE addon='clean-urls';
UPDATE cw_navigation_sections SET addon='dashboard' WHERE addon='dashboard';
UPDATE cw_navigation_sections SET addon='demo_module' WHERE addon='Demo_Module';
UPDATE cw_navigation_sections SET addon='detailed_product_images' WHERE addon='Detailed_Product_Images';
UPDATE cw_navigation_sections SET addon='discount_coupons' WHERE addon='Discount_Coupons';
UPDATE cw_navigation_sections SET addon='ebay' WHERE addon='Ebay';
UPDATE cw_navigation_sections SET addon='egoods' WHERE addon='Egoods';
UPDATE cw_navigation_sections SET addon='estore_category_tree' WHERE addon='EStoreCategoryTree';
UPDATE cw_navigation_sections SET addon='estore_gift' WHERE addon='EStoreGift';
UPDATE cw_navigation_sections SET addon='estore_products_review' WHERE addon='EStoreProductsReview';
UPDATE cw_navigation_sections SET addon='fancy_categories' WHERE addon='Fancy_Categories';
UPDATE cw_navigation_sections SET addon='faq' WHERE addon='FAQ';
UPDATE cw_navigation_sections SET addon='fbauth' WHERE addon='FBauth';
UPDATE cw_navigation_sections SET addon='feedback_report' WHERE addon='Feedback_report';
UPDATE cw_navigation_sections SET addon='froogle' WHERE addon='Froogle';
UPDATE cw_navigation_sections SET addon='google_analytics' WHERE addon='GoogleAnalytics';
UPDATE cw_navigation_sections SET addon='google_base' WHERE addon='GoogleBase';
UPDATE cw_navigation_sections SET addon='google_checkout' WHERE addon='google-checkout';
UPDATE cw_navigation_sections SET addon='image_verification' WHERE addon='Image_Verification';
UPDATE cw_navigation_sections SET addon='import_3x_4x' WHERE addon='Import_3x_4x';
UPDATE cw_navigation_sections SET addon='interneka' WHERE addon='Interneka';
UPDATE cw_navigation_sections SET addon='magnifier' WHERE addon='Magnifier';
UPDATE cw_navigation_sections SET addon='mailchimp_subscription' WHERE addon='Mailchimp_Subscription';
UPDATE cw_navigation_sections SET addon='manufacturers' WHERE addon='Manufacturers';
UPDATE cw_navigation_sections SET addon='mobile' WHERE addon='Mobile';
UPDATE cw_navigation_sections SET addon='modules_manager' WHERE addon='Modules_Manager';
UPDATE cw_navigation_sections SET addon='multi_domains' WHERE addon='multi-domains';
UPDATE cw_navigation_sections SET addon='multimedia_products' WHERE addon='Multimedia_Products';
UPDATE cw_navigation_sections SET addon='news' WHERE addon='news';
UPDATE cw_navigation_sections SET addon='now_online' WHERE addon='NowOnLine';
UPDATE cw_navigation_sections SET addon='order_tracking' WHERE addon='Order_Tracking';
UPDATE cw_navigation_sections SET addon='payment_authorize_sim' WHERE addon='payment-authorize-sim';
UPDATE cw_navigation_sections SET addon='payment_system' WHERE addon='payment-system';
UPDATE cw_navigation_sections SET addon='paypal_express' WHERE addon='paypal-express';
UPDATE cw_navigation_sections SET addon='pos' WHERE addon='POS';
UPDATE cw_navigation_sections SET addon='ppd' WHERE addon='ppd';
UPDATE cw_navigation_sections SET addon='product_options' WHERE addon='product-options';
UPDATE cw_navigation_sections SET addon='product_tabs' WHERE addon='product_tabs';
UPDATE cw_navigation_sections SET addon='promo' WHERE addon='promo';
UPDATE cw_navigation_sections SET addon='quickbooks' WHERE addon='QuickBooks';
UPDATE cw_navigation_sections SET addon='recommended_products' WHERE addon='Recommended_Products';
UPDATE cw_navigation_sections SET addon='remember_anonymouse_carts' WHERE addon='remember_anonymouse_carts';
UPDATE cw_navigation_sections SET addon='serial_numbers' WHERE addon='serial_numbers';
UPDATE cw_navigation_sections SET addon='shipping_fedex' WHERE addon='shipping-fedex';
UPDATE cw_navigation_sections SET addon='shipping_label_generator' WHERE addon='Shipping_Label_Generator';
UPDATE cw_navigation_sections SET addon='shipping_system' WHERE addon='shipping-system';
UPDATE cw_navigation_sections SET addon='shipping_tnt' WHERE addon='shipping-tnt';
UPDATE cw_navigation_sections SET addon='shipping_ups' WHERE addon='shipping-ups';
UPDATE cw_navigation_sections SET addon='sitemap_xml' WHERE addon='Sitemap_XML';
UPDATE cw_navigation_sections SET addon='sn' WHERE addon='SN';
UPDATE cw_navigation_sections SET addon='special_offers' WHERE addon='Special_Offers';
UPDATE cw_navigation_sections SET addon='stop_list' WHERE addon='Stop_List';
UPDATE cw_navigation_sections SET addon='subscriptions' WHERE addon='Subscriptions';
UPDATE cw_navigation_sections SET addon='survey' WHERE addon='Survey';
UPDATE cw_navigation_sections SET addon='top_menu' WHERE addon='Top_Menu';
UPDATE cw_navigation_sections SET addon='users_online' WHERE addon='Users_online';
UPDATE cw_navigation_sections SET addon='wholesale_trading' WHERE addon='Wholesale_Trading';
UPDATE cw_navigation_targets SET addon='accessories' WHERE addon='Accessories';
UPDATE cw_navigation_targets SET addon='ad_banners' WHERE addon='Ad_Banners';
UPDATE cw_navigation_targets SET addon='advanced_order_management' WHERE addon='Advanced_Order_Management';
UPDATE cw_navigation_targets SET addon='ajax_add2cart' WHERE addon='ajax-add2cart';
UPDATE cw_navigation_targets SET addon='amazon' WHERE addon='Amazon';
UPDATE cw_navigation_targets SET addon='barcode' WHERE addon='BarCode';
UPDATE cw_navigation_targets SET addon='bestsellers' WHERE addon='Bestsellers';
UPDATE cw_navigation_targets SET addon='bookmarks' WHERE addon='bookmarks';
UPDATE cw_navigation_targets SET addon='clean_urls' WHERE addon='clean-urls';
UPDATE cw_navigation_targets SET addon='dashboard' WHERE addon='dashboard';
UPDATE cw_navigation_targets SET addon='demo_module' WHERE addon='Demo_Module';
UPDATE cw_navigation_targets SET addon='detailed_product_images' WHERE addon='Detailed_Product_Images';
UPDATE cw_navigation_targets SET addon='discount_coupons' WHERE addon='Discount_Coupons';
UPDATE cw_navigation_targets SET addon='ebay' WHERE addon='Ebay';
UPDATE cw_navigation_targets SET addon='egoods' WHERE addon='Egoods';
UPDATE cw_navigation_targets SET addon='estore_category_tree' WHERE addon='EStoreCategoryTree';
UPDATE cw_navigation_targets SET addon='estore_gift' WHERE addon='EStoreGift';
UPDATE cw_navigation_targets SET addon='estore_products_review' WHERE addon='EStoreProductsReview';
UPDATE cw_navigation_targets SET addon='fancy_categories' WHERE addon='Fancy_Categories';
UPDATE cw_navigation_targets SET addon='faq' WHERE addon='FAQ';
UPDATE cw_navigation_targets SET addon='fbauth' WHERE addon='FBauth';
UPDATE cw_navigation_targets SET addon='feedback_report' WHERE addon='Feedback_report';
UPDATE cw_navigation_targets SET addon='froogle' WHERE addon='Froogle';
UPDATE cw_navigation_targets SET addon='google_analytics' WHERE addon='GoogleAnalytics';
UPDATE cw_navigation_targets SET addon='google_base' WHERE addon='GoogleBase';
UPDATE cw_navigation_targets SET addon='google_checkout' WHERE addon='google-checkout';
UPDATE cw_navigation_targets SET addon='image_verification' WHERE addon='Image_Verification';
UPDATE cw_navigation_targets SET addon='import_3x_4x' WHERE addon='Import_3x_4x';
UPDATE cw_navigation_targets SET addon='interneka' WHERE addon='Interneka';
UPDATE cw_navigation_targets SET addon='magnifier' WHERE addon='Magnifier';
UPDATE cw_navigation_targets SET addon='mailchimp_subscription' WHERE addon='Mailchimp_Subscription';
UPDATE cw_navigation_targets SET addon='manufacturers' WHERE addon='Manufacturers';
UPDATE cw_navigation_targets SET addon='mobile' WHERE addon='Mobile';
UPDATE cw_navigation_targets SET addon='modules_manager' WHERE addon='Modules_Manager';
UPDATE cw_navigation_targets SET addon='multi_domains' WHERE addon='multi-domains';
UPDATE cw_navigation_targets SET addon='multimedia_products' WHERE addon='Multimedia_Products';
UPDATE cw_navigation_targets SET addon='news' WHERE addon='news';
UPDATE cw_navigation_targets SET addon='now_online' WHERE addon='NowOnLine';
UPDATE cw_navigation_targets SET addon='order_tracking' WHERE addon='Order_Tracking';
UPDATE cw_navigation_targets SET addon='payment_authorize_sim' WHERE addon='payment-authorize-sim';
UPDATE cw_navigation_targets SET addon='payment_system' WHERE addon='payment-system';
UPDATE cw_navigation_targets SET addon='paypal_express' WHERE addon='paypal-express';
UPDATE cw_navigation_targets SET addon='pos' WHERE addon='POS';
UPDATE cw_navigation_targets SET addon='ppd' WHERE addon='ppd';
UPDATE cw_navigation_targets SET addon='product_options' WHERE addon='product-options';
UPDATE cw_navigation_targets SET addon='product_tabs' WHERE addon='product_tabs';
UPDATE cw_navigation_targets SET addon='promo' WHERE addon='promo';
UPDATE cw_navigation_targets SET addon='quickbooks' WHERE addon='QuickBooks';
UPDATE cw_navigation_targets SET addon='recommended_products' WHERE addon='Recommended_Products';
UPDATE cw_navigation_targets SET addon='remember_anonymouse_carts' WHERE addon='remember_anonymouse_carts';
UPDATE cw_navigation_targets SET addon='serial_numbers' WHERE addon='serial_numbers';
UPDATE cw_navigation_targets SET addon='shipping_fedex' WHERE addon='shipping-fedex';
UPDATE cw_navigation_targets SET addon='shipping_label_generator' WHERE addon='Shipping_Label_Generator';
UPDATE cw_navigation_targets SET addon='shipping_system' WHERE addon='shipping-system';
UPDATE cw_navigation_targets SET addon='shipping_tnt' WHERE addon='shipping-tnt';
UPDATE cw_navigation_targets SET addon='shipping_ups' WHERE addon='shipping-ups';
UPDATE cw_navigation_targets SET addon='sitemap_xml' WHERE addon='Sitemap_XML';
UPDATE cw_navigation_targets SET addon='sn' WHERE addon='SN';
UPDATE cw_navigation_targets SET addon='special_offers' WHERE addon='Special_Offers';
UPDATE cw_navigation_targets SET addon='stop_list' WHERE addon='Stop_List';
UPDATE cw_navigation_targets SET addon='subscriptions' WHERE addon='Subscriptions';
UPDATE cw_navigation_targets SET addon='survey' WHERE addon='Survey';
UPDATE cw_navigation_targets SET addon='top_menu' WHERE addon='Top_Menu';
UPDATE cw_navigation_targets SET addon='users_online' WHERE addon='Users_online';
UPDATE cw_navigation_targets SET addon='wholesale_trading' WHERE addon='Wholesale_Trading';
UPDATE cw_config_categories SET category='accessories' WHERE category='Accessories';
UPDATE cw_config_categories SET category='ad_banners' WHERE category='Ad_Banners';
UPDATE cw_config_categories SET category='advanced_order_management' WHERE category='Advanced_Order_Management';
UPDATE cw_config_categories SET category='ajax_add2cart' WHERE category='ajax-add2cart';
UPDATE cw_config_categories SET category='amazon' WHERE category='Amazon';
UPDATE cw_config_categories SET category='barcode' WHERE category='BarCode';
UPDATE cw_config_categories SET category='bestsellers' WHERE category='Bestsellers';
UPDATE cw_config_categories SET category='bookmarks' WHERE category='bookmarks';
UPDATE cw_config_categories SET category='clean_urls' WHERE category='clean-urls';
UPDATE cw_config_categories SET category='dashboard' WHERE category='dashboard';
UPDATE cw_config_categories SET category='demo_module' WHERE category='Demo_Module';
UPDATE cw_config_categories SET category='detailed_product_images' WHERE category='Detailed_Product_Images';
UPDATE cw_config_categories SET category='discount_coupons' WHERE category='Discount_Coupons';
UPDATE cw_config_categories SET category='ebay' WHERE category='Ebay';
UPDATE cw_config_categories SET category='egoods' WHERE category='Egoods';
UPDATE cw_config_categories SET category='estore_category_tree' WHERE category='EStoreCategoryTree';
UPDATE cw_config_categories SET category='estore_gift' WHERE category='EStoreGift';
UPDATE cw_config_categories SET category='estore_products_review' WHERE category='EStoreProductsReview';
UPDATE cw_config_categories SET category='fancy_categories' WHERE category='Fancy_Categories';
UPDATE cw_config_categories SET category='faq' WHERE category='FAQ';
UPDATE cw_config_categories SET category='fbauth' WHERE category='FBauth';
UPDATE cw_config_categories SET category='feedback_report' WHERE category='Feedback_report';
UPDATE cw_config_categories SET category='froogle' WHERE category='Froogle';
UPDATE cw_config_categories SET category='google_analytics' WHERE category='GoogleAnalytics';
UPDATE cw_config_categories SET category='google_base' WHERE category='GoogleBase';
UPDATE cw_config_categories SET category='google_checkout' WHERE category='google-checkout';
UPDATE cw_config_categories SET category='image_verification' WHERE category='Image_Verification';
UPDATE cw_config_categories SET category='import_3x_4x' WHERE category='Import_3x_4x';
UPDATE cw_config_categories SET category='interneka' WHERE category='Interneka';
UPDATE cw_config_categories SET category='magnifier' WHERE category='Magnifier';
UPDATE cw_config_categories SET category='mailchimp_subscription' WHERE category='Mailchimp_Subscription';
UPDATE cw_config_categories SET category='manufacturers' WHERE category='Manufacturers';
UPDATE cw_config_categories SET category='mobile' WHERE category='Mobile';
UPDATE cw_config_categories SET category='modules_manager' WHERE category='Modules_Manager';
UPDATE cw_config_categories SET category='multi_domains' WHERE category='multi-domains';
UPDATE cw_config_categories SET category='multimedia_products' WHERE category='Multimedia_Products';
UPDATE cw_config_categories SET category='news' WHERE category='news';
UPDATE cw_config_categories SET category='now_online' WHERE category='NowOnLine';
UPDATE cw_config_categories SET category='order_tracking' WHERE category='Order_Tracking';
UPDATE cw_config_categories SET category='payment_authorize_sim' WHERE category='payment-authorize-sim';
UPDATE cw_config_categories SET category='payment_system' WHERE category='payment-system';
UPDATE cw_config_categories SET category='paypal_express' WHERE category='paypal-express';
UPDATE cw_config_categories SET category='pos' WHERE category='POS';
UPDATE cw_config_categories SET category='ppd' WHERE category='ppd';
UPDATE cw_config_categories SET category='product_options' WHERE category='product-options';
UPDATE cw_config_categories SET category='product_tabs' WHERE category='product_tabs';
UPDATE cw_config_categories SET category='promo' WHERE category='promo';
UPDATE cw_config_categories SET category='quickbooks' WHERE category='QuickBooks';
UPDATE cw_config_categories SET category='recommended_products' WHERE category='Recommended_Products';
UPDATE cw_config_categories SET category='remember_anonymouse_carts' WHERE category='remember_anonymouse_carts';
UPDATE cw_config_categories SET category='serial_numbers' WHERE category='serial_numbers';
UPDATE cw_config_categories SET category='shipping_fedex' WHERE category='shipping-fedex';
UPDATE cw_config_categories SET category='shipping_label_generator' WHERE category='Shipping_Label_Generator';
UPDATE cw_config_categories SET category='shipping_system' WHERE category='shipping-system';
UPDATE cw_config_categories SET category='shipping_tnt' WHERE category='shipping-tnt';
UPDATE cw_config_categories SET category='shipping_ups' WHERE category='shipping-ups';
UPDATE cw_config_categories SET category='sitemap_xml' WHERE category='Sitemap_XML';
UPDATE cw_config_categories SET category='sn' WHERE category='SN';
UPDATE cw_config_categories SET category='special_offers' WHERE category='Special_Offers';
UPDATE cw_config_categories SET category='stop_list' WHERE category='Stop_List';
UPDATE cw_config_categories SET category='subscriptions' WHERE category='Subscriptions';
UPDATE cw_config_categories SET category='survey' WHERE category='Survey';
UPDATE cw_config_categories SET category='top_menu' WHERE category='Top_Menu';
UPDATE cw_config_categories SET category='users_online' WHERE category='Users_online';
UPDATE cw_config_categories SET category='wholesale_trading' WHERE category='Wholesale_Trading';

update cw_languages set name=replace(name,'https_module','[HTTPS~MOD]') where name like '%https_module%';
update cw_languages set name=replace(name,'module','addon'), value=replace(replace(value,'module','addon'),'Module','Addon') where name like '%module%';
update cw_languages set name=replace(name,'[HTTPS~MOD]','https_module') where name like '%[HTTPS~MOD]%';

update cw_navigation_menu set title=replace(title,'module','addon') where title like '%module%';

update cw_navigation_sections set title='lbl_section_addons', link='index.php?target=addons' where title='lbl_section_modules';

update cw_navigation_tabs set title=replace(title,'module','addon'), link=replace(link,'module','addon') where title like '%module%';

update cw_navigation_targets set target='addons_manager' where target='modules_manager';

--
-- Дамп данных таблицы `cw_addons`
--

INSERT INTO `cw_addons` (`addon`, `descr`, `active`, `status`, `parent`, `version`, `orderby`) VALUES
('accessories', 'Shows to customer related products and accessories lists', 0, 0, '', '0.1', 0),
('addons_manager', 'Manager for installation of new addons or upgrades', 1, 1, '', '0.1', 0),
('ad_banners', 'Allows to manage banners or any other extra content in frontend', 0, 0, '', '0.1', 0),
('ajax_add2cart', 'Provides ajax minicart and add2cart button', 1, 0, '', '0.1', 0),
('amazon', 'Amazon Export', 0, 1, '', '0.1', 0),
('auto_update', 'This module will allow to receive the automaticall updates and bug fixes for te softwware.', 1, 0, '', '0.1', 0),
('barcode', 'Allo to generate different barcodes', 0, 0, '', '0.1', 0),
('bestsellers', 'This module enables bestsellers list', 0, 0, '', '0.1', 0),
('bookmarks', 'Provides bookmarks panel', 0, 1, '', '0.1', 0),
('clean_urls', 'Clena urls for the products, categories, manufactirers and statis pages.', 1, 0, '', '0.1', 0),
('dashboard', 'Admin dashboard', 1, 0, '', '0.1', 0),
('demo_module', 'Demo of modular structure', 1, 1, '', '0.1', 0),
('detailed_product_images', 'Enable to allow creation of detailed images for products.', 1, 0, '', '0.1', 0),
('discount_coupons', 'Enable this if you wish to activate discount coupons feature.', 1, 0, '', '0.1', 0),
('ebay', 'Ebay Export', 1, 1, '', '0.1', 0),
('egoods', 'Activate this module if you wish to sell Electronically distributed products.', 1, 0, '', '0.1', 0),
('estore_category_tree', 'This module enables several alternative styles of categories menu (JavaScript, Explorer-like, decorated menus)', 1, 0, '', '0.1', 0),
('estore_gift', 'This module will allow your customers to create the customer wishlists, gift registries. Also the gift certificates will be able for sale.', 1, 0, '', '0.1', 0),
('estore_products_review', 'Allows voting and writing reviews on products', 1, 0, '', '0.1', 0),
('faq', 'FAQ module', 0, 0, '', '0.1', 0),
('fbauth', 'Facebook auth/login', 0, 1, '', '0.1', 0),
('feedback_report', 'Feedback report', 0, 1, '', '0.1', 0),
('froogle', 'This module allows to export products in Froogle compatible format', 0, 0, '', '0.1', 0),
('google_analytics', 'Google Analytics Tool', 1, 0, '', '0.1', 0),
('google_base', '"Google Product Search" is a service based on "Google Base" and formerly known as "Froogle"', 1, 0, '', '0.1', 0),
('google_checkout', 'Google Checkout integration', 1, 0, '', '0.1', 0),
('image_verification', 'This module enables image validation preventing automated submissions of ''Register'', ''Send to friend'' and ''Contact us'' forms', 1, 0, '', '0.1', 0),
('interneka', 'This module enables the support for Interneka affiliates software.', 1, 0, '', '0.1', 0),
('magnifier', 'This module allows you to zoom product images.', 0, 0, '', '0.1', 0),
('mailchimp_subscription', 'This module allows to use MailChimp newsletters management service. To create a MailChimp account please <a href="http://www.mailchimp.com/signup/" target="_blank">click here</a>.', 1, 0, '', '0.1', 0),
('manufacturers', 'This module allows to classify products by manufacturers', 1, 0, '', '0.1', 0),
('mobile', 'Module allows to manage the system on mobile devices', 1, 1, '', '0.1', 20),
('multi_domains', 'Multi domains', 1, 0, '', '0.1', 0),
('news', 'This module allows to create and manage news lists', 1, 0, '', '0.1', 0),
('now_online', 'This module enables the users online tracking', 0, 0, '', '0.1', 0),
('order_tracking', 'This allows to track order shipment from UPS, USPS and FedEx postal services', 1, 0, '', '0.1', 0),
('payment_authorize_sim', 'Authorize.Net payment solutions - Server Integration Method', 1, 1, 'payment_system', '0.1', 0),
('payment_cc', 'Offline CC payment', 1, 0, 'payment_system', '0.1', 0),
('payment_ch', 'Offline CH payment', 1, 0, 'payment_system', '0.1', 0),
('payment_dd', 'Offline DD payment', 0, 0, 'payment_system', '0.1', 0),
('payment_gift_certificate', 'Gift Certificates processor', 1, 1, 'payment_system', '0.1', 0),
('payment_offline', 'Offline payment integration', 1, 0, 'payment_system', '0.1', 0),
('payment_sagepay_form', 'Credit Card processor SagePay Go - Form protocol', 1, 1, 'payment_system', '0.1', 0),
('payment_sagepay_server', 'Credit Card processor SagePay Go - Server protocol', 1, 1, 'payment_system', '0.1', 0),
('payment_system', 'Interface between the core and payment methods', 1, 0, '', '0.1', 0),
('paypal', 'PayPal Website Payments Standard (Customers shop on your website and pay on PayPal)', 1, 0, 'payment_system', '0.1', 0),
('paypal_express', 'PayPal Express Checkout (PayPal is provided to customers as an additional payment option. Customers use the shipping and billing information stored at PayPal to pay at your store)', 1, 0, '', '0.1', 0),
('paypal_pro', 'PayPal Website Payments Pro (Customers shop and pay on your website. Available only to US merchants)', 1, 0, 'payment_system', '0.1', 0),
('paypal_pro_payflow', 'PayPal Website Payments Pro Payflow Edition (Customers shop and pay on your website. Please only select this option after talking with a US or UK sales representative or account manager from PayPal)', 1, 0, 'payment_system', '0.1', 0),
('pos', 'POS module allow the cash sellings', 0, 0, '', '0.1', 0),
('ppd', 'Product Page Downloads', 1, 0, '', '0.1', 0),
('product_options', 'Activate this module if you want to have product options.', 1, 0, '', '0.1', 0),
('product_tabs', 'Allow to define additional tabs for product details', 1, 0, '', '0.1', 0),
('promotion_suite', 'Promotion suite', 1, 0, '', '0.1', -1),
('quote_system', 'Quote system allow to request special quote for prepared cart content', 1, 1, '', '0.1', 0),
('redirect_after_login', 'Module makes redirect not-logged user after login to his/her actual desired page.', 0, 1, '', '0.1', 0),
('remember_anonymouse_carts', 'Remember Anonymous Carts', 1, 0, '', '0.1', 0),
('salesman', 'This module enables the affiliate programs with your store', 0, 0, '', '0.1', 0),
('seo_map', 'This module will allow you to generate different sitemaps for different search engines', 1, 0, '', '0.1', 0),
('shipping_australia_post', 'Australia Post online shipping methods', 0, 1, 'shipping_system', '0.1', 0),
('shipping_canada_post', 'Canada Post online shipping methods', 0, 1, 'shipping_system', '0.1', 0),
('shipping_dhl', 'DHL shipping methods', 0, 1, 'shipping_system', '0.1', 0),
('shipping_fedex', 'FedEe shipping methods', 1, 0, 'shipping_system', '0.1', 0),
('shipping_intershipper', 'InterShipper shipping methods', 0, 1, 'shipping_system', '0.1', 0),
('shipping_label_generator', 'This module allows you to create and print shipping labels for orders that are going to be shipped by UPS and USPS.', 1, 1, '', '0.1', 0),
('shipping_system', 'Enable the shipping system for the cart', 1, 0, '', '0.1', 0),
('shipping_tnt', 'TNT shipping methods', 0, 1, 'shipping_system', '0.1', 0),
('shipping_ups', 'UPS shipping methods', 1, 0, 'shipping_system', '0.1', 0),
('shipping_usps', 'USPS shipping methods', 0, 1, 'shipping_system', '0.1', 0),
('sitemap_xml', 'This module allows you to create xml sitemap', 1, 0, '', '0.1', 0),
('sn', 'Serial numbers for products', 0, 0, '', '0.1', 0),
('special_offers', 'This module enables create the special offers for your store', 0, 0, '', '0.1', 0),
('special_products', 'This module is allow to have the special products sections, like recommended and related products', 1, 0, '', '0.1', 0),
('survey', 'This module allows you to create customer surveys and polls.', 1, 0, '', '0.1', 0),
('top_menu', 'Module to add top menu with both selected product categories and user defined web links', 1, 0, '', '0.1', 0),
('warehouse', 'Allow to manage a few warehouses', 1, 0, '', '0.1', 0),
('wholesale_trading', 'Enable this if you want to have advanced pricing (wholesale)', 1, 0, '', '0.1', 0);

