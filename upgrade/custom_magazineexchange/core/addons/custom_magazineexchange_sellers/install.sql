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
CREATE TABLE `cw_magazine_sellers_product_data` (
  `seller_item_id` int(11) NOT NULL AUTO_INCREMENT,
  `seller_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `condition` tinyint(4) NOT NULL,
  `quantity` smallint(6) NOT NULL,
  `price` decimal(12,2) NOT NULL,
  `comments` tinytext NOT NULL,
  PRIMARY KEY (`seller_item_id`),
  KEY `product_id` (`product_id`),
  KEY `seller_id` (`seller_id`)
) ENGINE=InnoDB  COMMENT='Magazine Sellers product data for custom_magazineexchange_sellers addon'

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
