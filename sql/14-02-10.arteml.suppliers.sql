
ALTER TABLE `cw_register_fields` CHANGE `is_non_modify` `is_protected` TINYINT( 1 ) NOT NULL DEFAULT '0';

ALTER TABLE `cw_products_system_info` ADD `supplier_customer_id` INT( 11 ) NOT NULL DEFAULT '0';

-- Langvars
REPLACE INTO `cw_languages` (`code`, `name`, `value`, `tooltip`, `topic`) VALUES ('EN', 'lbl_supplier', 'Supplier', '', 'Labels');
REPLACE INTO `cw_languages` (`code`, `name`, `value`, `tooltip`, `topic`) VALUES ('EN', 'lbl_suppliers', 'Suppliers', '', 'Labels');
REPLACE INTO `cw_languages` (`code`, `name`, `value`, `tooltip`, `topic`) VALUES ('EN', 'lbl_user_S', 'Supplier', '', 'Labels');
REPLACE INTO `cw_languages` (`code`, `name`, `value`, `tooltip`, `topic`) VALUES ('EN', 'lbl_user_create_S', 'Create supplier', '', 'Labels');
REPLACE INTO `cw_languages` (`code`, `name`, `value`, `tooltip`, `topic`) VALUES ('EN', 'lbl_user_modify_S', 'Modify supplier', '', 'Labels');
REPLACE INTO `cw_languages` (`code`, `name`, `value`, `tooltip`, `topic`) VALUES ('EN', 'lbl_user_delete_S', 'Delete supplier', '', 'Labels');
REPLACE INTO `cw_languages` (`code`, `name`, `value`, `tooltip`, `topic`) VALUES ('EN', 'lbl_users_S', 'Suppliers', '', 'Labels');
REPLACE INTO `cw_languages` (`code`, `name`, `value`, `tooltip`, `topic`) VALUES ('EN', 'lbl_search_user_S', 'Search suppliers', '', 'Labels');
REPLACE INTO `cw_languages` (`code`, `name`, `value`, `tooltip`, `topic`) VALUES ('EN', 'lbl_login_supplier', 'Supplier login', '', 'Labels');
REPLACE INTO `cw_languages` (`code`, `name`, `value`, `tooltip`, `topic`) VALUES ('EN', 'lbl_register_supplier', 'Supplier registration', '', 'Labels');
REPLACE INTO `cw_languages` (`code`, `name`, `value`, `tooltip`, `topic`) VALUES ('EN', 'lbl_S_membership_levels', 'Supplier membership levels', '', 'Labels');
UPDATE `cw_languages` SET `value` = 'Usually ships in {{delivery_time}} days' WHERE `cw_languages`.`code` = 'EN' AND `cw_languages`.`name` = 'lbl_ships_in';
UPDATE `cw_languages` SET `topic` = 'Labels' WHERE `cw_languages`.`code` = 'EN' AND `cw_languages`.`name` = 'lbl_profile_options_customer';
REPLACE INTO `cw_languages` ( `code` , `name` , `value` , `tooltip` , `topic`) VALUES ( 'EN', 'lbl_profile_options_supplier', 'Profile options for suppliers', '', 'Labels');

delete from cw_languages where name='lbl_usually_ship';

-- Left profile options menu
DELETE FROM cw_navigation_tabs WHERE link = 'index.php?target=user_profiles&user_type=S';
INSERT INTO `cw_navigation_tabs` (`tab_id`, `access_level`, `title`, `link`, `orderby`) VALUES
(null, '2502', 'lbl_profile_options_supplier', 'index.php?target=user_profiles&user_type=S', 50);
SET @tid1=LAST_INSERT_ID();

SELECT @sid:=section_id FROM cw_navigation_sections WHERE title='lbl_profile_options' and area='A';

INSERT INTO `cw_navigation_targets` (`target_id`, `target`, `params`, `visible`, `section_id`, `tab_id`, `orderby`, `addon`) VALUES
(null, 'user_profiles', "$_GET['user_type']=='S'", '', @sid, @tid1, 50, '');

-- Navigation top menu
SELECT @menu_id:=menu_id FROM `cw_navigation_menu` WHERE `title`='lbl_users' AND area='A';
INSERT INTO `cw_navigation_menu` ( `menu_id` , `parent_menu_id` , `title` , `link` , `orderby` , `area` , `access_level` , `addon` , `is_loggedin`) VALUES ( NULL , @menu_id, 'lbl_suppliers', 'index.php?target=user_S', '172', 'A', '', '', '1');

/* +++ admin user menu, seller add, modify, search, delete submenu +++ */
INSERT INTO cw_navigation_sections (`title`, `link`, `addon`, `access_level`, `area`, `main`, `orderby`, `skins_subdir`) VALUES ('lbl_suppliers', 'index.php?target=user_S', '', '02', 'A', 'Y', 50, 'users');
SET @section_id = LAST_INSERT_ID();

DELETE FROM cw_navigation_tabs WHERE title = 'lbl_user_create_S';
INSERT INTO cw_navigation_tabs (`access_level`, `title`, `link`, `orderby`) VALUES ('02', 'lbl_user_create_S', 'index.php?target=user_S&mode=add', 40);
SET @tab_id = LAST_INSERT_ID();

INSERT INTO cw_navigation_targets (`target`, `params`, `visible`, `section_id`, `tab_id`, `orderby`, `addon`) VALUES
('user_S', '$_GET[\'mode\'] == \'add\'', '', @section_id, @tab_id, 10, '');

DELETE FROM cw_navigation_tabs WHERE title = 'lbl_user_modify_S';
INSERT INTO cw_navigation_tabs (`access_level`, `title`, `link`, `orderby`) VALUES ('02', 'lbl_user_modify_S', '', 30);
SET @tab_id = LAST_INSERT_ID();

INSERT INTO cw_navigation_targets (`target`, `params`, `visible`, `section_id`, `tab_id`, `orderby`, `addon`) VALUES
('user_S', '$_GET[\'mode\']==\'modify\' && !empty($_GET[\'user\'])', '$_GET[\'mode\']==\'modify\' && !empty($_GET[\'user\'])', @section_id, @tab_id, 20, '');

DELETE FROM cw_navigation_tabs WHERE title = 'lbl_user_delete_S';
INSERT INTO cw_navigation_tabs (`access_level`, `title`, `link`, `orderby`) VALUES ('02', 'lbl_user_delete_S', '', 20);
SET @tab_id = LAST_INSERT_ID();

INSERT INTO cw_navigation_targets (`target`, `params`, `visible`, `section_id`, `tab_id`, `orderby`, `addon`) VALUES
('user_S', '$_GET[\'mode\'] == \'delete\'', '$_GET[\'mode\'] == \'delete\'', @section_id, @tab_id, 30, '');

DELETE FROM cw_navigation_tabs WHERE title = 'lbl_users_S';
INSERT INTO cw_navigation_tabs (`access_level`, `title`, `link`, `orderby`) VALUES ('02', 'lbl_users_S', 'index.php?target=user_S', 10);
SET @tab_id = LAST_INSERT_ID();

INSERT INTO cw_navigation_targets (`target`, `params`, `visible`, `section_id`, `tab_id`, `orderby`, `addon`) VALUES
('user_S', '', '', @section_id, @tab_id, 40, '');

/* --- admin user menu, seller add, modify, search, delete submenu --- */


DELETE FROM cw_register_fields_values WHERE customer_id NOT IN (SELECT customer_id FROM cw_customers);


INSERT INTO `cw_register_fields` ( `field_id` , `section_id` , `field` , `type` , `variants` , `def` , `orderby` , `is_protected`) VALUES ( NULL , '1', 'min_delivery_time', 'T', '', '0', '10', '1');
SET @fid_min=LAST_INSERT_ID();
INSERT INTO `cw_register_fields_lng` ( `field_id` , `code` , `field`) VALUES ( @fid_min, 'EN', 'Min delivery time');

INSERT INTO `cw_register_fields` ( `field_id` , `section_id` , `field` , `type` , `variants` , `def` , `orderby` , `is_protected`) VALUES ( NULL , '1', 'max_delivery_time', 'T', '', '0', '11', '1');
SET @fid_max=LAST_INSERT_ID();
INSERT INTO `cw_register_fields_lng` ( `field_id` , `code` , `field`) VALUES ( @fid_max, 'EN', 'Max delivery time');

/* +++ user(supplier) profile fields and rights +++ */
DELETE FROM cw_register_fields_avails WHERE area = 'S' OR area = '#S';
DELETE FROM cw_register_fields_by_types WHERE area = 'S';

REPLACE INTO `cw_register_fields_avails` (`field_id`, `area`, `is_avail`, `is_required`) VALUES 
(31, 'S', 0, 0), (76, 'S', 0, 0), (65, 'S', 1, 0), (41, '#S', 1, 1), (34, 'S', 0, 0), (37, 'S', 1, 0), (78, 'S', 1, 0), (33, 'S', 0, 0), (35, 'S', 0, 0), (107, '#S', 1, 0), (38, 'S', 1, 0), (36, 'S', 0, 0), (30, 'S', 0, 0), (39, 'S', 0, 0), (50, 'S', 1, 1), (107, 'S', 1, 0), (50, '#S', 1, 1), (41, 'S', 1, 1), (27, 'S', 0, 0), (28, 'S', 1, 1), (29, 'S', 1, 1), (32, 'S', 0, 0), (132, 'S', 0, 0), (37, '#S', 1, 0), (28, '#S', 1, 0), (29, '#S', 1, 0), (@fid_max, '#S', 1, 0), (@fid_max, 'S', 1, 0), (@fid_min, '#S', 1, 0), (@fid_min, 'S', 1, 0), (38, '#S', 1, 0), (136, 'S', 1, 0), (136, '#S', 1, 0), (140, 'S', 0, 0), (135, 'S', 0, 0);

delete from cw_register_fields_avails where field_id not in (select field_id from cw_register_fields);

REPLACE INTO `cw_register_fields_by_types` (`field_id`, `area`, `is_required`, `is_avail`) VALUES
(37, 'S', 0, 1),
(78, 'S', 0, 1),
(50, 'S', 1, 1),
(41, 'S', 1, 1);
/* --- user(supplier) profile fields and rights --- */
