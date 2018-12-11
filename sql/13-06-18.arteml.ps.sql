UPDATE `cw_products` SET `discount_avail`=1;
REPLACE INTO `cw_addons` (`addon`, `descr`, `active`, `status`, `parent`, `version`, `orderby`) VALUES ('promotion_suite', 'Promotion suite', 1, 0, '', '0.1', -1);

REPLACE INTO `cw_languages` (`code`, `name`, `value`, `topic`) VALUES ('EN', 'lbl_ps_offers', 'Offers', 'Labels');
REPLACE INTO `cw_languages` (`code`, `name`, `value`, `topic`) VALUES ('EN', 'lbl_ps_modify_offer', 'Modify Offer', 'Labels');
REPLACE INTO `cw_languages` (`code`, `name`, `value`, `topic`) VALUES ('EN', 'lbl_ps_details', 'Offer details', 'Labels'), ('EN', 'lbl_ps_offer_date', 'Offer period', 'Labels');
REPLACE INTO `cw_languages` (`code`, `name`, `value`, `topic`) VALUES ('EN', 'lbl_ps_offer_title', 'Offer title', 'Labels'), ('EN', 'lbl_ps_unknown', 'n/a', 'Labels');
REPLACE INTO `cw_languages` (`code`, `name`, `value`, `topic`) VALUES ('EN', 'lbl_ps_offer_desc', 'Offer description', 'Labels'), ('EN', 'lbl_ps_offer_active', 'Active', 'Labels');
REPLACE INTO `cw_languages` (`code`, `name`, `value`, `topic`) VALUES ('EN', 'lbl_ps_offer_position', 'Position', 'Labels'), ('EN', 'lbl_ps_offer_image', 'Image', 'Labels');
REPLACE INTO `cw_languages` (`code`, `name`, `value`, `topic`) VALUES ('EN', 'lbl_ps_button_save', 'Save', 'Labels');
REPLACE INTO `cw_languages` (`code`, `name`, `value`, `topic`) VALUES ('EN', 'lbl_ps_manage_offers', 'Manage Offers', 'Labels'), ('EN', 'msg_ps_empty_fields', 'All the required offer fields should be filled in.', 'Text');
REPLACE INTO `cw_languages` (`code`, `name`, `value`, `topic`) VALUES ('EN', 'msg_ps_incorrect_field_type', 'Incorrect type of the field: {{field_name}}.', 'Text'), ('EN', 'msg_ps_updated_succes', 'Offers have been successfully updated.', 'Text');
REPLACE INTO `cw_languages` (`code`, `name`, `value`, `topic`) VALUES ('EN', 'lbl_ps_offer_details', 'Details', 'Labels'), ('EN', 'lbl_ps_offer_bonuses', 'Bonuses', 'Labels');
REPLACE INTO `cw_languages` (`code`, `name`, `value`, `topic`) VALUES ('EN', 'lbl_ps_offer_conditions', 'Conditions', 'Labels');


REPLACE INTO `cw_available_images` VALUES ('ps_offer_images','U',0,70,1,'default_image_70.gif');

REPLACE INTO `cw_languages` (`code`, `name`, `value`, `topic`) VALUES
('EN', 'lbl_ps_bonuses', 'Bonuses', 'Labels'),
('EN', 'lbl_ps_bonus_coupon', 'Give a discount coupon', 'Labels'),
('EN', 'lbl_ps_bonus_discount', 'Give a discount', 'Labels'),
('EN', 'lbl_ps_bonus_forfree', 'Give products for free', 'Labels'),
('EN', 'lbl_ps_bonus_freeship', 'Give free shipping', 'Labels'),
('EN', 'lbl_ps_delete_selected', 'Delete selected', 'Labels'),
('EN', 'lbl_ps_disc_for_products_incondition', 'apply a discount for products defined in conditions', 'Labels'),
('EN', 'lbl_ps_disc_for_selcted_products', 'apply a discount for selected below products', 'Labels'),
('EN', 'lbl_ps_disc_for_whole_cart', 'apply a discount for whole cart', 'Labels'),
('EN', 'lbl_ps_offer_enddate', 'End date', 'Labels'),
('EN', 'lbl_ps_offer_excl', 'Exclusive', 'Labels'),
('EN', 'lbl_ps_offer_startdate', 'Start date', 'Labels'),
('EN', 'lbl_ps_update_selected', 'Update selected', 'Labels'),
('EN', 'txt_ps_no_elements', 'No records were found.', 'Text'),
('EN', 'lbl_ps_new_offer','New Offer','Labels'),
('EN','lbl_ps_discount_value','Discount:','Labels'),
('EN','lbl_ps_dtype_fixed','$','Labels'),
('EN','lbl_ps_dtype_percent','%','Labels'),
('EN','lbl_ps_browse','Browse...','Labels'),
('EN','lbl_ps_products','Products','Labels'),
('EN','lbl_ps_categories','Categories','Labels'),
('EN', 'txt_ps_top_text', 'txt_ps_top_text', 'Text');

REPLACE INTO `cw_languages` (`code`, `name`, `value`, `topic`) VALUES
('EN', 'msg_ps_deleted', 'The chosen offers have been successfully deleted.', 'Text'),
('EN', 'msg_ps_incorrect_field_type', 'Incorrect type of the field: {{field_name}}.', 'Text');


REPLACE INTO `cw_languages` (`code`, `name`, `value`, `topic`) VALUES
('EN', 'msg_ps_bonus_incorrect', '"{{bonus}}" bonus type was filled in incorrectly.', 'Text');


REPLACE INTO `cw_languages` (`code`, `name`, `value`, `topic`) VALUES
('EN', 'msg_ps_empty_bonus_fields', '"{{bonus}}" bonus type, all the required fields should be filled in', 'Text');


REPLACE INTO `cw_languages` (`code`, `name`, `value`, `topic`) VALUES ('EN', 'lbl_ps_conditions', 'Conditions', 'Labels'), ('EN', 'lbl_ps_cond_subtotal', 'Shopping cart discounted subtotal', 'Labels');

REPLACE INTO `cw_languages` (`code`, `name`, `value`, `topic`) VALUES ('EN', 'lbl_ps_disc_subtotal', 'Discounted cart subtotal should be equal to or greater than', 'Labels'), ('EN', 'lbl_ps_cond_shipping', 'Customer''s shipping address', 'Labels');

REPLACE INTO `cw_languages` (`code`, `name`, `value`, `topic`) VALUES ('EN', 'lbl_ps_refresh_list', 'Refresh the list', 'Labels'), ('EN', 'lbl_ps_new_zone_title', 'Add new Destination Zone* -new window', 'Labels');

REPLACE INTO `cw_languages` (`code`, `name`, `value`, `topic`) VALUES ('EN', 'lbl_ps_new_zone', 'Add new Destination Zone', 'Labels'), ('EN', 'lbl_ps_new_coupon_title', 'Add new Coupon* -new window', 'Labels');

REPLACE INTO `cw_languages` (`code`, `name`, `value`, `topic`) VALUES ('EN', 'lbl_ps_new_coupon', 'Add new Coupon', 'Labels');

REPLACE INTO `cw_languages` (`code`, `name`, `value`, `topic`) VALUES ('EN', 'lbl_ps_cond_products', 'Specific products', 'Labels');

REPLACE INTO `cw_languages` (`code`, `name`, `value`, `topic`) VALUES ('EN', 'lbl_ps_manufacturers', 'Manufacturers', 'Labels');

REPLACE INTO `cw_languages` (`code`, `name`, `value`, `topic`) VALUES ('EN', 'msg_ps_cond_incorrect', '"{{cond}}" condition type was filled in incorrectly.', 'Text');

REPLACE INTO `cw_languages` (`code`, `name`, `value`, `topic`) VALUES ('EN', 'lbl_ps_select_element', 'Choose a value', 'Labels');

REPLACE INTO `cw_languages` (`code`, `name`, `value`, `topic`) VALUES ('EN', 'lbl_ps_not_saved', 'Incorrect and not saved!', 'Labels');

INSERT INTO `cw_attributes` (`attribute_id`, `name`, `type`, `field`, `is_required`, `active`, `orderby`, `addon`, `item_type`, `is_sortable`, `is_comparable`, `is_show`, `pf_is_use`, `pf_orderby`, `pf_display_type`, `is_show_addon`) VALUES
(108, 'Domains', 'domain-selector', 'domains', 1, 1, 1, 'multi_domains', 'PS', 0, 0, 0, 0, 0, '', 1);

REPLACE INTO `cw_languages` (`code`, `name`, `value`, `topic`) VALUES ('EN', 'lbl_ps_cust_offers', 'Special Offers', 'Labels');

REPLACE INTO `cw_languages` (`code`, `name`, `value`, `topic`) VALUES ('EN', 'msg_ps_no_offers', 'At this moment there are no special offers.', 'Text');

REPLACE INTO `cw_languages` (`code`, `name`, `value`, `topic`) VALUES ('EN', 'lbl_ps_offer_priority', 'Priority', 'Labels');

REPLACE INTO `cw_languages` (`code`, `name`, `value`, `topic`) VALUES ('EN', 'lbl_ps_exlusive_note', 'Exclusive', 'Labels');

delete from cw_navigation_menu where title='lbl_ps_offers';
delete from cw_navigation_sections where title='lbl_ps_offers';
delete from cw_navigation_tabs where title like 'lbl_ps_' and access_level IN ('33','__33');
delete from cw_navigation_targets where target='promosuite';

INSERT INTO `cw_navigation_menu` ( `menu_id` , `parent_menu_id` , `title` , `link` , `orderby` , `area` , `access_level` , `addon` , `is_loggedin`) VALUES ( NULL , '3', 'lbl_ps_offers', 'index.php?target=promosuite', '400', 'A', '33', 'promotion_suite', '1');

INSERT INTO `cw_navigation_sections` (`section_id`, `title`, `link`, `addon`, `access_level`, `area`, `main`, `orderby`, `skins_subdir`) VALUES
(null, 'lbl_ps_offers', 'index.php?target=promosuite', 'promotion_suite', '', 'A', 'Y', 0, '');
SET @sid=LAST_INSERT_ID();

INSERT INTO `cw_navigation_tabs` (`tab_id`, `access_level`, `title`, `link`, `orderby`) VALUES
(null, '33', 'lbl_ps_offers', 'index.php?target=promosuite', 0);
SET @tid1=LAST_INSERT_ID();

INSERT INTO `cw_navigation_tabs` (`tab_id`, `access_level`, `title`, `link`, `orderby`) VALUES
(null, '__33', 'lbl_ps_new_offer', 'index.php?target=promosuite&action=form&offer_id=', 20);
SET @tid2=LAST_INSERT_ID();

INSERT INTO `cw_navigation_tabs` (`tab_id`, `access_level`, `title`, `link`, `orderby`) VALUES
(null, '33', 'lbl_ps_modify_offer', '', 10);
SET @tid3=LAST_INSERT_ID();

INSERT INTO `cw_navigation_targets` (`target_id`, `target`, `params`, `visible`, `section_id`, `tab_id`, `orderby`, `addon`) VALUES
(null, 'promosuite', '', '', @sid, @tid1, 20, 'promotion_suite'),
(null, 'promosuite', 'isset($_GET[''offer_id'']) && empty($_GET[''offer_id''])', '', @sid, @tid2, 0, 'promotion_suite'),
(null, 'promosuite', 'isset($_GET[''offer_id'']) && !empty($_GET[''offer_id''])', 'isset($_GET[''offer_id'']) && !empty($_GET[''offer_id''])', @sid, @tid3, 10, 'promotion_suite');


CREATE TABLE IF NOT EXISTS `cw_ps_bonuses` (
  `bonus_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `offer_id` int(11) unsigned NOT NULL,
  `type` char(1) NOT NULL,
  `apply` tinyint(1) unsigned NOT NULL,
  `coupon` varchar(16) NOT NULL,
  `discount` decimal(12,4) unsigned NOT NULL,
  `disctype` tinyint(1) unsigned NOT NULL,
  PRIMARY KEY (`bonus_id`)
);

-- --------------------------------------------------------

--
-- Структура таблицы `cw_ps_bonus_details`
--

CREATE TABLE IF NOT EXISTS `cw_ps_bonus_details` (
  `bd_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `bonus_id` int(11) unsigned NOT NULL,
  `offer_id` int(11) unsigned NOT NULL,
  `object_id` int(11) unsigned NOT NULL,
  `quantity` int(11) unsigned NOT NULL,
  `object_type` tinyint(1) unsigned NOT NULL,
  PRIMARY KEY (`bd_id`),
  KEY `bonus_id` (`bonus_id`)
);

-- --------------------------------------------------------

--
-- Структура таблицы `cw_ps_conditions`
--

CREATE TABLE IF NOT EXISTS `cw_ps_conditions` (
  `cond_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `type` char(1) NOT NULL,
  `total` decimal(12,4) unsigned NOT NULL,
  `offer_id` int(11) unsigned NOT NULL,
  PRIMARY KEY (`cond_id`)
);

-- --------------------------------------------------------

--
-- Структура таблицы `cw_ps_cond_details`
--

CREATE TABLE IF NOT EXISTS `cw_ps_cond_details` (
  `cd_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `cond_id` int(11) unsigned NOT NULL,
  `offer_id` int(11) unsigned NOT NULL,
  `object_id` int(11) unsigned NOT NULL,
  `quantity` int(11) unsigned NOT NULL,
  `object_type` tinyint(1) unsigned NOT NULL,
  PRIMARY KEY (`cd_id`),
  KEY `cond_id` (`cond_id`)
);

-- --------------------------------------------------------

--
-- Структура таблицы `cw_ps_offers`
--

CREATE TABLE IF NOT EXISTS `cw_ps_offers` (
  `offer_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `description` mediumtext NOT NULL,
  `startdate` int(11) unsigned NOT NULL,
  `enddate` int(11) unsigned NOT NULL,
  `exclusive` tinyint(1) unsigned NOT NULL,
  `position` int(11) unsigned NOT NULL,
  `active` tinyint(1) unsigned NOT NULL,
  `priority` int(11) unsigned NOT NULL,
  `pid` int(11) NOT NULL DEFAULT '0' COMMENT 'offer attached to product',
  `auto` tinyint(4) NOT NULL DEFAULT '0' COMMENT 'auto generated offer',
  `repeat` int(11) NOT NULL DEFAULT '1' COMMENT 'times for repeatable offers',
  PRIMARY KEY (`offer_id`),
  KEY `pid` (`pid`)
);

-- --------------------------------------------------------

--
-- Структура таблицы `cw_ps_offer_images`
--

CREATE TABLE IF NOT EXISTS `cw_ps_offer_images` (
  `image_id` int(11) NOT NULL AUTO_INCREMENT,
  `id` int(11) NOT NULL DEFAULT '0',
  `image_path` varchar(255) NOT NULL DEFAULT '',
  `image_type` varchar(64) NOT NULL DEFAULT 'image/jpeg',
  `image_x` int(11) NOT NULL DEFAULT '0',
  `image_y` int(11) NOT NULL DEFAULT '0',
  `image_size` int(11) NOT NULL DEFAULT '0',
  `filename` varchar(255) NOT NULL DEFAULT '',
  `date` int(11) NOT NULL DEFAULT '0',
  `alt` varchar(255) NOT NULL DEFAULT '',
  `avail` int(1) NOT NULL DEFAULT '1',
  `orderby` int(11) NOT NULL DEFAULT '0',
  `md5` varchar(32) NOT NULL DEFAULT '',
  PRIMARY KEY (`image_id`),
  KEY `image_path` (`image_path`),
  KEY `id` (`id`)
);

