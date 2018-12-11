REPLACE INTO `cw_languages` (`code`, `name`, `value`, `tooltip`, `topic`) VALUES ('EN', 'lbl_seller', 'Seller', '', 'Labels');
REPLACE INTO `cw_languages` (`code`, `name`, `value`, `tooltip`, `topic`) VALUES ('EN', 'lbl_sellers', 'Sellers', '', 'Labels');
REPLACE INTO `cw_languages` (`code`, `name`, `value`, `tooltip`, `topic`) VALUES ('EN', 'lbl_user_V', 'Seller', '', 'Labels');
REPLACE INTO `cw_languages` (`code`, `name`, `value`, `tooltip`, `topic`) VALUES ('EN', 'lbl_user_create_V', 'Create seller', '', 'Labels');
REPLACE INTO `cw_languages` (`code`, `name`, `value`, `tooltip`, `topic`) VALUES ('EN', 'lbl_user_modify_V', 'Modify seller', '', 'Labels');
REPLACE INTO `cw_languages` (`code`, `name`, `value`, `tooltip`, `topic`) VALUES ('EN', 'lbl_user_delete_V', 'Delete seller', '', 'Labels');
REPLACE INTO `cw_languages` (`code`, `name`, `value`, `tooltip`, `topic`) VALUES ('EN', 'lbl_users_V', 'Sellers', '', 'Labels');
REPLACE INTO `cw_languages` (`code`, `name`, `value`, `tooltip`, `topic`) VALUES ('EN', 'lbl_search_user_V', 'Search sellers', '', 'Labels');
REPLACE INTO `cw_languages` (`code`, `name`, `value`, `tooltip`, `topic`) VALUES ('EN', 'addon_name_seller', 'Seller', '', 'Addons');
REPLACE INTO `cw_languages` (`code`, `name`, `value`, `tooltip`, `topic`) VALUES ('EN', 'addon_descr_seller', 'New type of users and new membership for seller usertype', '', 'Addons');
REPLACE INTO `cw_languages` (`code`, `name`, `value`, `tooltip`, `topic`) VALUES ('EN', 'lbl_login_seller', 'Seller login', '', 'Labels');
REPLACE INTO `cw_languages` (`code`, `name`, `value`, `tooltip`, `topic`) VALUES ('EN', 'lbl_register_seller', 'Seller registration', '', 'Labels');
REPLACE INTO `cw_languages` (`code`, `name`, `value`, `tooltip`, `topic`) VALUES ('EN', 'lbl_V_membership_levels', 'Seller membership levels', '', 'Labels');
REPLACE INTO `cw_languages` (`code`, `name`, `value`, `tooltip`, `topic`) VALUES ('EN', 'msg_user_has_been_added_V', 'Seller account has been created', '', 'Labels');

/* +++ seller addon +++ */
DELETE FROM cw_addons WHERE addon='seller';
INSERT INTO `cw_addons` (`addon`, `descr`, `active`, `status`, `parent`, `version`, `orderby`)
VALUES ('seller', 'New type of users and new membership for seller usertype', 1, 0, '', '0.1', 0);
/* --- seller addon --- */

/* +++ delete records +++ */
SELECT @message_section_id:=section_id FROM cw_navigation_sections WHERE link='index.php?target=message_box' AND area='V' LIMIT 1;
DELETE FROM cw_navigation_targets WHERE section_id = @message_section_id;

SELECT @products_section_id:=section_id FROM cw_navigation_sections WHERE link='index.php?target=products' AND area='V' LIMIT 1;
DELETE FROM cw_navigation_tabs WHERE tab_id IN (SELECT tab_id FROM cw_navigation_targets WHERE section_id = @products_section_id);

DELETE FROM cw_navigation_menu WHERE title = 'lbl_help' AND parent_menu_id = 0;

DELETE FROM cw_navigation_menu WHERE area = 'V' OR addon = 'seller';
DELETE FROM cw_navigation_sections WHERE area = 'V' OR addon = 'seller';
DELETE FROM cw_navigation_targets WHERE addon='seller';
/* --- delete records --- */

/* +++ products menu +++ */
INSERT INTO `cw_navigation_menu` (`parent_menu_id`, `title`, `link`, `orderby`, `area`, `access_level`, `addon`, `is_loggedin`) VALUES ('0', 'lbl_catalog', 'index.php?target=products', '10', 'V', '', 'seller', '1');
SET @menu_id = LAST_INSERT_ID();
INSERT INTO `cw_navigation_menu` (`parent_menu_id`, `title`, `link`, `orderby`, `area`, `access_level`, `addon`, `is_loggedin`) VALUES (@menu_id, 'lbl_products', 'index.php?target=products', '10', 'V', '', 'seller', '1');

INSERT INTO cw_navigation_sections (`title`, `link`, `addon`, `access_level`, `area`, `main`, `orderby`, `skins_subdir`) VALUES ('lbl_products', 'index.php?target=products', 'seller', 12, 'V', 'Y', 10, 'products');
SET @section_id = LAST_INSERT_ID();

INSERT INTO cw_navigation_tabs (`access_level`, `title`, `link`, `orderby`) VALUES ('', 'lbl_products', 'index.php?target=products', 10);
SET @tab_id = LAST_INSERT_ID();
INSERT INTO cw_navigation_targets (`target`, `params`, `visible`, `section_id`, `tab_id`, `orderby`, `addon`) VALUES 
('products', '', '', @section_id, @tab_id, 30, 'seller');

INSERT INTO cw_navigation_tabs (`access_level`, `title`, `link`, `orderby`) VALUES ('', 'lbl_product_modify', 'index.php?target=products&mode=details&product_id={$product_id}', 20);
SET @tab_id = LAST_INSERT_ID();
INSERT INTO cw_navigation_targets (`target`, `params`, `visible`, `section_id`, `tab_id`, `orderby`, `addon`) VALUES 
('products', '!empty($_GET[\'product_id\']) && $_GET[\'mode\'] == \'details\'', '!empty($_GET[\'product_id\']) && $_GET[\'mode\'] == \'details\'', @section_id, @tab_id, 20, 'seller');

INSERT INTO cw_navigation_tabs (`access_level`, `title`, `link`, `orderby`) VALUES ('', 'lbl_add_product', 'index.php?target=products&mode=add', 30);
SET @tab_id = LAST_INSERT_ID();
INSERT INTO cw_navigation_targets (`target`, `params`, `visible`, `section_id`, `tab_id`, `orderby`, `addon`) VALUES 
('products', '$_GET[\'mode\'] == \'add\'', '', @section_id, @tab_id, 10, 'seller');
/* --- products menu --- */

/* +++ messages menu +++ */
INSERT INTO `cw_navigation_menu` (`parent_menu_id`, `title`, `link`, `orderby`, `area`, `access_level`, `addon`, `is_loggedin`) VALUES ('0', 'lbl_tools', '', '20', 'V', '', 'seller', '1');
SET @menu_id = LAST_INSERT_ID();
INSERT INTO `cw_navigation_menu` (`parent_menu_id`, `title`, `link`, `orderby`, `area`, `access_level`, `addon`, `is_loggedin`) VALUES (@menu_id, 'lbl_messages', 'index.php?target=message_box', '30', 'V', '', 'messaging_system', '1');

INSERT INTO `cw_navigation_sections` (`title`, `link`, `addon`, `access_level`, `area`, `main`, `orderby`, `skins_subdir`) 
VALUES ('lbl_avail_type_incoming', 'index.php?target=message_box', 'messaging_system', '', 'V', 'N', 10, '');
SET @section_id = LAST_INSERT_ID();

SELECT @tab_id:=tab_id FROM cw_navigation_tabs WHERE link='index.php?target=message_box&mode=new' AND title='lbl_new_message' LIMIT 1;
INSERT INTO `cw_navigation_targets` (`target`, `params`, `visible`, `section_id`, `tab_id`, `orderby`, `addon`) 
VALUES ('message_box', '$_GET[\'mode\']==\'new\' || $_POST[\'mode\']==\'new\'', '', @section_id, @tab_id, 0, 'messaging_system');

SELECT @tab_id:=tab_id FROM cw_navigation_tabs WHERE link='index.php?target=message_box' AND title='lbl_avail_type_incoming' LIMIT 1;
INSERT INTO `cw_navigation_targets` (`target`, `params`, `visible`, `section_id`, `tab_id`, `orderby`, `addon`) 
VALUES ('message_box', '($_GET[\'mode\']==\'\' && $_POST[\'mode\']==\'\') || $_POST[\'mode\']==\'incoming\'', '', @section_id, @tab_id, 10, 'messaging_system');

SELECT @tab_id:=tab_id FROM cw_navigation_tabs WHERE link='index.php?target=message_box&mode=sent' AND title='lbl_sent' LIMIT 1;
INSERT INTO `cw_navigation_targets` (`target`, `params`, `visible`, `section_id`, `tab_id`, `orderby`, `addon`) 
VALUES ('message_box', '$_GET[\'mode\']==\'sent\' || $_POST[\'mode\']==\'sent\'', '', @section_id, @tab_id, 20, 'messaging_system');

SELECT @tab_id:=tab_id FROM cw_navigation_tabs WHERE link='index.php?target=message_box&mode=archive' AND title='lbl_archive' LIMIT 1;
INSERT INTO `cw_navigation_targets` (`target`, `params`, `visible`, `section_id`, `tab_id`, `orderby`, `addon`) 
VALUES ('message_box', '$_GET[\'mode\']==\'archive\' || $_POST[\'mode\']==\'archive\'', '', @section_id, @tab_id, 30, 'messaging_system');
/* --- messages menu --- */

/* +++ admin user menu, seller user profiles submenu +++ */
SELECT @menu_id:=menu_id FROM cw_navigation_menu WHERE link='index.php?target=user_C' AND parent_menu_id=0;
INSERT INTO cw_navigation_menu (`parent_menu_id`, `title`, `link`, `orderby`, `area`, `access_level`, `addon`, `is_loggedin`) VALUES (@menu_id, 'lbl_sellers', 'index.php?target=user_V', 175, 'A', '', 'seller', 1);

DELETE FROM cw_navigation_tabs WHERE link = 'index.php?target=user_profiles&user_type=V';
INSERT INTO cw_navigation_tabs (`access_level`, `title`, `link`, `orderby`) VALUES (2502, 'lbl_sellers', 'index.php?target=user_profiles&user_type=V', 70);
SET @tab_id = LAST_INSERT_ID();

SELECT @section_id:=section_id FROM cw_navigation_sections WHERE link='index.php?target=user_profiles';

INSERT INTO cw_navigation_targets (`target`, `params`, `visible`, `section_id`, `tab_id`, `orderby`, `addon`) VALUES 
('user_profiles', '$_GET[\'user_type\']==\'V\'', '', @section_id, @tab_id, 90, 'seller');
/* --- admin user menu, seller user profiles submenu --- */

/* +++ admin user menu, seller add, modify, search, delete submenu +++ */
INSERT INTO cw_navigation_sections (`title`, `link`, `addon`, `access_level`, `area`, `main`, `orderby`, `skins_subdir`) VALUES ('lbl_sellers', 'index.php?target=user_V', 'seller', 02, 'A', 'Y', 50, 'users');
SET @section_id = LAST_INSERT_ID();

DELETE FROM cw_navigation_tabs WHERE title = 'lbl_user_create_V';
INSERT INTO cw_navigation_tabs (`access_level`, `title`, `link`, `orderby`) VALUES ('02', 'lbl_user_create_V', 'index.php?target=user_V&mode=add', 40);
SET @tab_id = LAST_INSERT_ID();

INSERT INTO cw_navigation_targets (`target`, `params`, `visible`, `section_id`, `tab_id`, `orderby`, `addon`) VALUES 
('user_V', '$_GET[\'mode\'] == \'add\'', '', @section_id, @tab_id, 10, 'seller');

DELETE FROM cw_navigation_tabs WHERE title = 'lbl_user_modify_V';
INSERT INTO cw_navigation_tabs (`access_level`, `title`, `link`, `orderby`) VALUES ('02', 'lbl_user_modify_V', '', 30);
SET @tab_id = LAST_INSERT_ID();

INSERT INTO cw_navigation_targets (`target`, `params`, `visible`, `section_id`, `tab_id`, `orderby`, `addon`) VALUES 
('user_V', '$_GET[\'mode\']==\'modify\' && !empty($_GET[\'user\'])', '$_GET[\'mode\']==\'modify\' && !empty($_GET[\'user\'])', @section_id, @tab_id, 20, 'seller');

DELETE FROM cw_navigation_tabs WHERE title = 'lbl_user_delete_V';
INSERT INTO cw_navigation_tabs (`access_level`, `title`, `link`, `orderby`) VALUES ('02', 'lbl_user_delete_V', '', 20);
SET @tab_id = LAST_INSERT_ID();

INSERT INTO cw_navigation_targets (`target`, `params`, `visible`, `section_id`, `tab_id`, `orderby`, `addon`) VALUES 
('user_V', '$_GET[\'mode\'] == \'delete\'', '$_GET[\'mode\'] == \'delete\'', @section_id, @tab_id, 30, 'seller');

DELETE FROM cw_navigation_tabs WHERE title = 'lbl_users_V';
INSERT INTO cw_navigation_tabs (`access_level`, `title`, `link`, `orderby`) VALUES ('02', 'lbl_users_V', 'index.php?target=user_V', 10);
SET @tab_id = LAST_INSERT_ID();

INSERT INTO cw_navigation_targets (`target`, `params`, `visible`, `section_id`, `tab_id`, `orderby`, `addon`) VALUES 
('user_V', '', '', @section_id, @tab_id, 40, 'seller');
/* --- admin user menu, seller add, modify, search, delete submenu --- */

/* +++ user(seller) profile fields and rights +++ */
DELETE FROM cw_register_fields_avails WHERE area = 'V' OR area = '#V';
DELETE FROM cw_register_fields_by_types WHERE area = 'V';

INSERT INTO `cw_register_fields_avails` (`field_id`, `area`, `is_avail`, `is_required`) VALUES
(65, 'V', 1, 0),
(41, '#V', 1, 1),
(34, 'V', 1, 0),
(37, 'V', 1, 0),
(78, 'V', 1, 0),
(33, 'V', 1, 0),
(35, 'V', 1, 0),
(107, '#V', 1, 0),
(38, 'V', 1, 0),
(36, 'V', 1, 0),
(30, 'V', 1, 0),
(31, 'V', 1, 0),
(76, 'V', 1, 0),
(39, 'V', 1, 0),
(50, 'V', 1, 1),
(107, 'V', 1, 0),
(50, '#V', 1, 1),
(41, 'V', 1, 1),
(27, 'V', 0, 0),
(28, 'V', 1, 0),
(29, 'V', 1, 0),
(32, 'V', 1, 0),
(132, 'V', 0, 0),
(37, '#V', 1, 0),
(27, '#V', 1, 0),
(28, '#V', 1, 0),
(29, '#V', 1, 0),
(30, '#V', 1, 0),
(31, '#V', 1, 0),
(35, '#V', 1, 0),
(76, '#V', 1, 0),
(34, '#V', 1, 0),
(33, '#V', 1, 0),
(32, '#V', 1, 0),
(36, '#V', 1, 0),
(38, '#V', 1, 0),
(39, '#V', 1, 0),
(136, 'V', 1, 0),
(136, '#V', 1, 0);

INSERT INTO `cw_register_fields_by_types` (`field_id`, `area`, `is_required`, `is_avail`) VALUES
(37, 'V', 0, 1),
(78, 'V', 0, 1),
(50, 'V', 1, 1),
(41, 'V', 1, 1);
/* --- user(seller) profile fields and rights --- */
