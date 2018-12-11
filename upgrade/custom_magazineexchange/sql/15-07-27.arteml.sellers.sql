
REPLACE INTO `cw_languages` (code,name,value,topic) VALUES ('EN','txt_seller_request_promotion_page_2','Use the form below to request a new Promotion Page - Magazine Exchange will then set-up a page for you, and pass control of it to you. At this point the page will be blank (ie, empty), and it will appear in the \'Edit Existing Pages\' area of your Seller Account ready for you to add your own content as required.\r\n<br><br>\r\n\r\nA nominal fee of £1 per month is charged for each Promotion Page. If this is your first page will contact you to make payment arrangements.\r\n\r\n<br><br><br>\r\n','Text'),('EN','txt_seller_about_title_basic_2','<br><div align=\"center\"><FONT SIZE=\"2\"\r\n          FACE=\"Arial,Helvetica,Verdana,Sans-serif\">Each magazine title on the website has an \'About this title\' page; if you are the current publisher of a magazine you have exclusive rights over the information which appears on that page. </font><br><br>\r\n<FONT SIZE=\"2\"\r\n          F><b><u>This is a valuable opportunity to promote your company and products.</u></b></font>\r\n<br><br>\r\n<FONT SIZE=\"2\"\r\n          FACE=\"Arial,Helvetica,Verdana,Sans-serif\">Publishers have two options for controlling what appears on this page; you may activate the \'free-but-basic\' option by submitting information using the form below. (Note: If you have several titles please submit a separate form for each one).\r\n</font></div><br><br><br>','Text'),('EN','txt_seller_about_title_custom_2','<br><div align=\"center\"><FONT SIZE=\"2\"\r\n          FACE=\"Arial,Helvetica,Verdana,Sans-serif\">Each magazine title on the website has an \'About this title\' page; if you are the current publisher of a magazine you have exclusive rights over the information which appears on that page. </font><br><br>\r\n<FONT SIZE=\"2\"\r\n          FACE=\"Arial,Helvetica,Verdana,Sans-serif\"><b><u>With the \'custom\' option you directly control and update the page yourself.<br><Br>You may also include any content in the page, including external links if you wish.</u></b></font>\r\n<br><br>\r\n<FONT SIZE=\"2\"\r\n          FACE=\"Arial,Helvetica,Verdana,Sans-serif\">Use the form below to request that a \'blank\' page is created, and control of it assigned to you. The blank page will appear in your \'Promotion Pages\' list, where you can edit it. <br><br>(Note: If you wish to set-up pages for several titles please submit separate forms for each one).\r\n</font></div><br><br><br>','Text'),('EN','txt_add_single_issue_2','<div align=\"center\"><FONT SIZE=\"2\"\r\n          FACE=\"Arial,Helvetica,Verdana,Sans-serif\">Anyone may add new issues to our database; by \'new\' we mean magazine issues which aren\'t already listed on the website - the issues themselves can be of any age or date. Once added the new issues will appear on the site, and may be bought & sold by anyone. </font><br><br>\r\n\r\n<FONT SIZE=\"2\"\r\n          FACE=\"Arial,Helvetica,Verdana,Sans-serif\">Use the form below to contribute information about <u>one</u> magazine issue. <br><br>Click the image on the right for advice on preparing magazine images >> \r\n</font></div>','Text'),('EN','txt_seller_payment_info_1','Please use the form below to tell us how you\'d like to receive payments for magazines you\'ve sold.\r\n<br><br>\r\nYou don\'t need to complete this form until you\'ve received your first order, and then we will keep your information on-file to use for any subsequent orders. If you would like to change your preferences at any time simply complete the form again and we will update our files with the new information.\r\n<br><br>\r\n\r\n\r\nClick the figure on the right for more information.','Text'),('EN','txt_add_bulk_issues_2','<br>\r\nUse this form to send Magazine Exchange a .zip archive file containing your new <br>magazine data in a flat file format, plus all associated image files.\r\n<br><br><hr><br><br>\r\n<div align=\"middle\"><b>Refer to full instructions in the <a class=\"ProductBlue\" href=\"/help-centre-selling-trade-services.html\"> Trade Services Help Guide</a> first!</b></div>\r\n<br><br><hr><br><br>\r\n<i><u>Upload .zip file:</u></i>','Text');

-- Addon
REPLACE INTO `cw_addons` (`addon`, `descr`, `active`, `status`, `parent`, `version`, `orderby`) VALUES ('custom_magazineexchange_sellers', 'Magazine Sellers', 1, 1, 'custom_magazineexchange', '0.1', 0);

-- configuration options
REPLACE INTO `cw_config_categories` (`config_category_id` ,`category` ,`is_local`) VALUES (NULL , 'custom_magazineexchange_sellers', '0');
SET @config_category_id = LAST_INSERT_ID();
REPLACE INTO cw_config SET name='mag_seller_sep_1', comment='Main options', value='', config_category_id=@config_category_id, orderby='0', type='separator', defvalue='', variants='';
REPLACE INTO cw_config SET name='mag_seller_fees', comment='Seller fees', value='', config_category_id=@config_category_id, orderby='0', type='', defvalue='', variants='';

-- Addon name/description
REPLACE INTO cw_languages SET code='EN', name='addon_descr_custom_magazineexchange_sellers', value='Magazine Sellers (Marketplace mod)', topic='Addons';
REPLACE INTO cw_languages SET code='EN', name='addon_name_custom_magazineexchange_sellers', value='Magazine Sellers', topic='Addons';
REPLACE INTO cw_languages SET code='EN', name='option_title_custom_magazineexchange_sellers', value='Magazine Sellers options', topic='Options';



-- Create necessary tables
CREATE TABLE IF NOT EXISTS `cw_magazine_sellers_product_data` (
 `seller_id` int(11) NOT NULL,
 `product_id` int(11) NOT NULL,
 `condition` tinyint(4) NOT NULL,
 `quantity` smallint(6) NOT NULL,
 `price` decimal(12,2) NOT NULL,
 `comments` tinytext NOT NULL,
 PRIMARY KEY (`seller_id`,`product_id`)
) ENGINE=InnoDB COMMENT='Magazine Sellers product data for custom_magazineexchange_sellers addon';

/*
-- get menu_id for General Settings
SELECT @genset:=menu_id FROM cw_navigation_menu WHERE title='lbl_settings' AND area='A' LIMIT 1;
*/
-- insert new entry to menu
DELETE FROM cw_navigation_menu WHERE addon='custom_magazineexchange_sellers';
INSERT INTO `cw_navigation_menu` ( `menu_id` , `parent_menu_id` , `title` , `link` , `orderby` , `area` , `access_level` , `addon` , `is_loggedin`) VALUES 
(NULL , '0', 'lbl_orders', 'index.php?target=docs_O', '10', 'V', '', 'seller', '1'),
(NULL , '0', 'lbl_mag_magazineexchange', 'index.php?target=seller_about_title_basic', '100', 'V', '', 'custom_magazineexchange_sellers', '1');
SELECT @mid:=menu_id FROM cw_navigation_menu WHERE area='V' and (link='index.php?target=seller_about_title_basic' or title='MagazineExchange') LIMIT 1;
INSERT INTO `cw_navigation_menu` ( `menu_id` , `parent_menu_id` , `title` , `link` , `orderby` , `area` , `access_level` , `addon` , `is_loggedin`) VALUES 
(NULL ,@mid, 'lbl_mag_about_title_basic', 'index.php?target=seller_about_title_basic', '10', 'V', '', 'custom_magazineexchange_sellers', '1'),
(NULL ,@mid, 'lbl_mag_about_title_custom', 'index.php?target=seller_about_title_custom', '20', 'V', '', 'custom_magazineexchange_sellers', '1'),
-- (NULL ,@mid, 'lbl_mag_newpage_req', 'index.php?target=seller_newpage_req', '20', 'V', '', 'custom_magazineexchange_sellers', '1'),
(NULL ,@mid, 'lbl_mag_add_single_issue', 'index.php?target=seller_add_single_issue', '30', 'V', '', 'custom_magazineexchange_sellers', '1'),
(NULL ,@mid, 'lbl_mag_add_bulk_issues', 'index.php?target=seller_add_bulk_issues', '40', 'V', '', 'custom_magazineexchange_sellers', '1'),
(NULL ,@mid, 'lbl_mag_payment_info', 'index.php?target=seller_payment_info', '50', 'V', '', 'custom_magazineexchange_sellers', '1');

REPLACE INTO `cw_languages` (`code`, `name`, `value`, `topic`) VALUES 
('EN', 'lbl_mag_magazineexchange', 'MagazineExchange', 'Labels'),
('EN', 'lbl_mag_about_title_basic', 'About Title Basic', 'Labels'),
('EN', 'lbl_mag_about_title_custom', 'About Title Custom', 'Labels'),
-- ('EN', 'lbl_mag_newpage_req', 'New Page request', 'Labels'),
('EN', 'lbl_mag_add_single_issue', 'Add Single Issue', 'Labels'),
('EN', 'lbl_mag_add_bulk_issues', 'Add Bulk Issues', 'Labels'),
('EN', 'lbl_mag_payment_info', 'Add Payment Info', 'Labels');



-- Langvars
 REPLACE INTO `cw_languages` (`code`, `name`, `value`, `topic`) VALUES ('EN', 'lbl_mag_seller_product_condition_0', 'New', 'Labels');
 REPLACE INTO `cw_languages` (`code`, `name`, `value`, `topic`) VALUES ('EN', 'lbl_mag_seller_product_condition_1', 'Good', 'Labels');
 REPLACE INTO `cw_languages` (`code`, `name`, `value`, `topic`) VALUES ('EN', 'lbl_mag_seller_product_condition_2', 'Average', 'Labels');
 REPLACE INTO `cw_languages` (`code`, `name`, `value`, `topic`) VALUES ('EN', 'lbl_mag_seller_product_condition_3', 'Poor', 'Labels');

INSERT INTO `cw_order_statuses` ( `code` , `name` , `email_customer` , `email_admin` , `email_message_customer` , `email_subject_customer` , `email_message_admin` , `email_subject_admin` , `email_message_customer_mode` , `email_message_admin_mode` , `orderby` , `is_system` , `deleted` , `color` , `inventory_decreasing`) VALUES ( 'PO', 'Paid Out', '0', '1', '', '', '{$lng.eml_order_notification|substitute:"doc_id":$order.display_id}', '{$config.Company.company_name}: {$lng.eml_order_notification_subj|substitute:"doc_id":$order.display_id}', 'I', 'I', '200', '1', '0', '#78f476', '1');
