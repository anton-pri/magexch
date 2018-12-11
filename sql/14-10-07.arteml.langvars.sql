REPLACE INTO `cw_languages` (`code`, `name`, `value`, `topic`) VALUES ('EN', 'lbl_facebook', 'Facebook', 'Labels');
REPLACE INTO `cw_languages` (`code`, `name`, `value`, `topic`) VALUES ('EN', 'lbl_twitter', 'Twitter', 'Labels');
REPLACE INTO `cw_languages` (`code`, `name`, `value`, `topic`) VALUES ('EN', 'lbl_there_are_x_items_in_cart', 'There are {{items}} items in your cart', 'Labels');
REPLACE INTO `cw_languages` (`code`, `name`, `value`, `topic`) VALUES ('EN', 'lbl_successfully_added', 'Product successfully added to your shopping cart', 'Labels');
REPLACE INTO `cw_languages` (`code`, `name`, `value`, `topic`) VALUES ('EN', 'lbl_proceed_to_checkout', 'Proceed to checkout <i class="icon-chevron-right"></i>', 'Labels');
REPLACE INTO `cw_languages` (`code`, `name`, `value`, `topic`) VALUES ('EN', 'lbl_standart_view_icon', '<i class="icon-th-list"></i>List', 'Labels');
REPLACE INTO `cw_languages` (`code`, `name`, `value`, `topic`) VALUES ('EN', 'lbl_gallery_view_icon', '<i class="icon-th-large"></i>Grid', 'Labels');

update cw_navigation_menu set title='lbl_reports' where title='lbl_profit_reports';

delete from cw_navigation_tabs where link='index.php?target=news&list_id=';


REPLACE INTO `cw_languages` (`code`, `name`, `value`, `tooltip`, `topic`) VALUES
('EN', 'option_title_Advanced_Statistics', 'Advanced Statistics Options', '', 'Options'),
('EN', 'option_title_Appearance', 'Appearance Options', '', 'Options'),
('EN', 'option_title_bestsellers', 'Bestsellers Options', '', 'Options'),
('EN', 'option_title_CMPI', '3-D Secure Transaction Options', '', 'Options'),
('EN', 'option_title_Company', 'Company Options', '', 'Options'),
('EN', 'option_title_Contact_Us', 'Contact us form Options', '', 'Options'),
('EN', 'option_title_Customer_Reviews', 'Customer reviews Options', '', 'Options'),
('EN', 'option_title_detailed_product_images', 'Detailed Product Images Options', '', 'Options'),
('EN', 'option_title_egoods', 'Egoods Options', '', 'Options'),
('EN', 'option_title_Email', 'Email Options', '', 'Options'),
('EN', 'option_title_Extra_Fields', 'Extra fields Options', '', 'Options'),
('EN', 'option_title_froogle', 'Froogle/GoogleBase Options', '', 'Options'),
('EN', 'option_title_General', 'General Options', '', 'Options'),
('EN', 'option_title_Gift_Certificates', 'Gift Certificates Options', '', 'Options'),
('EN', 'option_title_GnuPG', 'GnuPG Options', '', 'Options'),
('EN', 'option_title_Images', 'Image Options', '', 'Options'),
('EN', 'option_title_image_verification', 'Image Verification Options', '', 'Options'),
('EN', 'option_title_import_3x_4x', 'Import 3x-4x', '', 'Options'),
('EN', 'option_title_interneka', 'Interneka Options', '', 'Options'),
('EN', 'option_title_Logging', 'Logging Options', '', 'Options'),
('EN', 'option_title_Maintenance_Agent', 'Maintenance Agent Options', '', 'Options'),
('EN', 'option_title_manufacturers', 'Manufacturers Options', '', 'Options'),
('EN', 'option_title_Modules', 'Add-ons Options', '', 'Options'),
('EN', 'option_title_News_Management', 'News management Options', '', 'Options'),
('EN', 'option_title_PayPal', 'PayPal Options', '', 'Options'),
('EN', 'option_title_PGP', 'PGP Options', '', 'Options'),
('EN', 'option_title_Product_Options', 'Product Options Options', '', 'Options'),
('EN', 'option_title_quickbooks', 'QuickBooks Options', '', 'Options'),
('EN', 'option_title_recommended_products', 'Recommended Products Options', '', 'Options'),
('EN', 'option_title_Search_products', 'Product search Options', '', 'Options');
REPLACE INTO `cw_languages` (`code`, `name`, `value`, `tooltip`, `topic`) VALUES
('EN', 'lbl_usually_ship', 'Usually ship in 24 hours', '', 'Labels');
