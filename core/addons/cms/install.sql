--
-- Table structure `cw_cms`
--

CREATE TABLE IF NOT EXISTS `cw_cms` (
  `contentsection_id` int(11) NOT NULL AUTO_INCREMENT,
  `service_code` varchar(64) NOT NULL DEFAULT '',
  `content` text NOT NULL,
  `type` varchar(32) NOT NULL DEFAULT 'html',
  `skin` varchar(64) NOT NULL DEFAULT 'vertical' COMMENT 'skin sub-folder',
  `name` varchar(64) NOT NULL DEFAULT '',
  `target` varchar(8) NOT NULL DEFAULT '_self',
  `url` varchar(128) NOT NULL DEFAULT '',
  `alt` varchar(128) NOT NULL DEFAULT '',
  `start_date` int(11) NOT NULL DEFAULT '0',
  `end_date` int(11) NOT NULL DEFAULT '0',
  `show_limit` int(11) NOT NULL DEFAULT '0',
  `orderby` int(11) NOT NULL DEFAULT '0',
  `active` char(1) NOT NULL DEFAULT 'Y',
  PRIMARY KEY (`contentsection_id`),
  KEY `start_date` (`start_date`,`end_date`),
  KEY `active` (`active`),
  KEY `orderby` (`orderby`),
  KEY `service_code` (`service_code`)
) ENGINE=MyISAM;

-- --------------------------------------------------------

--
-- Table structure `cw_cms_alt_languages`
--

CREATE TABLE IF NOT EXISTS `cw_cms_alt_languages` (
  `contentsection_id` int(11) NOT NULL DEFAULT '0',
  `code` char(2) NOT NULL DEFAULT 'EN',
  `name` varchar(64) NOT NULL DEFAULT '',
  `url` varchar(128) NOT NULL DEFAULT '',
  `alt` varchar(128) NOT NULL DEFAULT '',
  `content` text NOT NULL,
  PRIMARY KEY (`contentsection_id`,`code`)
) ENGINE=MyISAM;

-- --------------------------------------------------------

--
-- Table structure `cw_cms_categories`
--

CREATE TABLE IF NOT EXISTS `cw_cms_categories` (
  `contentsection_id` int(11) NOT NULL DEFAULT '0',
  `category_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`contentsection_id`,`category_id`),
  KEY `category_id` (`category_id`)
) ENGINE=MyISAM;

-- --------------------------------------------------------

--
-- Table structure `cw_cms_images`
--

CREATE TABLE IF NOT EXISTS `cw_cms_images` (
  `image_id` int(11) NOT NULL AUTO_INCREMENT,
  `code` char(2) NOT NULL DEFAULT 'EN',
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
  UNIQUE KEY `id` (`id`),
  KEY `image_path` (`image_path`)
) ENGINE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure `cw_cms_manufacturers`
--

CREATE TABLE IF NOT EXISTS `cw_cms_manufacturers` (
  `contentsection_id` int(11) NOT NULL DEFAULT '0',
  `manufacturer_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`contentsection_id`,`manufacturer_id`),
  KEY `manufacturer_id` (`manufacturer_id`)
) ENGINE=MyISAM;

-- --------------------------------------------------------

--
-- Table structure `cw_cms_products`
--

CREATE TABLE IF NOT EXISTS `cw_cms_products` (
  `contentsection_id` int(11) NOT NULL DEFAULT '0',
  `product_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`contentsection_id`,`product_id`),
  KEY `product_id` (`product_id`)
) ENGINE=MyISAM;

-- --------------------------------------------------------

--
-- Table structure `cw_cms_user_counters`
--
CREATE TABLE IF NOT EXISTS `cw_cms_user_counters` (
  `contentsection_id` int(11) NOT NULL DEFAULT '0',
  `count` int(11) NOT NULL DEFAULT '0',
  `clicked` int(11) NOT NULL DEFAULT '0',
  KEY `contentsection_id` (`contentsection_id`)
) ENGINE=MyISAM;

-- new addon record
REPLACE INTO `cw_addons` (`addon`, `descr`, `active`)
VALUES ('cms', 'Allows to manage contentsections or any other extra content in frontend', '1');

-- lang vars
REPLACE INTO cw_languages SET code='EN', name='lbl_cs_add_contentsection', value='Add content section', topic='Labels';
REPLACE INTO cw_languages SET code='EN', name='lbl_cs_add_new_contentsection', value='Add new content section', topic='Labels';
REPLACE INTO cw_languages SET code='EN', name='lbl_cs_cms', value='Content Sections', topic='Labels';
REPLACE INTO cw_languages SET code='EN', name='lbl_cs_alt_text', value='Alt text', topic='Labels';
REPLACE INTO cw_languages SET code='EN', name='lbl_cs_contentsections', value='Content sections', topic='Labels';
REPLACE INTO cw_languages SET code='EN', name='lbl_cs_contentsections_skin', value='Content section skin', topic='Labels';
REPLACE INTO cw_languages SET code='EN', name='lbl_cs_contentsections_skin_comment', value='The skin setting of this content section will affect all contentsections with the same contentsection code (<b>{{service_code}}</b>).', topic='Text';
REPLACE INTO cw_languages SET code='EN', name='lbl_cs_contentsections_search', value='Search', topic='Labels';
REPLACE INTO cw_languages SET code='EN', name='lbl_cs_service_code', value='Content section code', topic='Labels';
REPLACE INTO cw_languages SET code='EN', name='lbl_cs_contentsection_id', value='Content section ID', topic='Labels';
REPLACE INTO cw_languages SET code='EN', name='lbl_cs_clicked', value='Clicked', topic='Labels';
REPLACE INTO cw_languages SET code='EN', name='lbl_cs_end_date', value='Stop date', topic='Labels';
REPLACE INTO cw_languages SET code='EN', name='lbl_cs_limit', value='Limit', topic='Labels';
REPLACE INTO cw_languages SET code='EN', name='lbl_cs_name', value='Name', topic='Labels';
REPLACE INTO cw_languages SET code='EN', name='lbl_cs_quick_search', value='Quick search', topic='Labels';
REPLACE INTO cw_languages SET code='EN', name='lbl_cs_restrict_to_categories', value='Restrict to Categories', topic='Labels';
REPLACE INTO cw_languages SET code='EN', name='lbl_cs_restrict_to_manufacturers', value='Restrict to manufacturers', topic='Labels';
REPLACE INTO cw_languages SET code='EN', name='lbl_cs_restrict_to_products', value='Restrict to Products', topic='Labels';
REPLACE INTO cw_languages SET code='EN', name='lbl_cs_save_contentsection', value='Save content section', topic='Labels';
REPLACE INTO cw_languages SET code='EN', name='lbl_cs_select_product', value='Select product', topic='Labels';
REPLACE INTO cw_languages SET code='EN', name='lbl_cs_start_date', value='Start date', topic='Labels';
REPLACE INTO cw_languages SET code='EN', name='lbl_cs_target', value='Open link in', topic='Labels';
REPLACE INTO cw_languages SET code='EN', name='lbl_cs_target_new_window', value='New window', topic='Labels';
REPLACE INTO cw_languages SET code='EN', name='lbl_cs_target_same_window', value='Same window', topic='Labels';
REPLACE INTO cw_languages SET code='EN', name='lbl_cs_type', value='Type', topic='Labels';
REPLACE INTO cw_languages SET code='EN', name='lbl_cs_type_html', value='HTML', topic='Labels';
REPLACE INTO cw_languages SET code='EN', name='lbl_cs_type_image', value='Image', topic='Labels';
REPLACE INTO cw_languages SET code='EN', name='lbl_cs_update_contentsection', value='Update content section', topic='Labels';
REPLACE INTO cw_languages SET code='EN', name='lbl_cs_url', value='URL', topic='Labels';
REPLACE INTO cw_languages SET code='EN', name='lbl_cs_viewed', value='Viewed', topic='Labels';
REPLACE INTO cw_languages SET code='EN', name='addon_descr_adb', value='Allows to display ad content sections to customers.', topic='Addons';
REPLACE INTO cw_languages SET code='EN', name='addon_name_adb', value='Ad content sections', topic='Addons';
REPLACE INTO cw_languages SET code='EN', name='msg_cs_err_contentsectioncode_is_empty', value='The content section code is not specified.', topic='Errors';
REPLACE INTO cw_languages SET code='EN', name='msg_cs_err_contentsection_image_not_uploaded_or_saved', value='content section image is not saved.', topic='Errors';
REPLACE INTO cw_languages SET code='EN', name='msg_cs_err_wrong_contentsectioncode_format', value='Wrong content section code format. The alphanumeric characters and underscores can be used only. For example, my_contentsection_12.', topic='Errors';
REPLACE INTO cw_languages SET code='EN', name='msg_cs_warn_empty_contentsection_alt_text', value='The content section alt text is not specified.', topic='Errors';
REPLACE INTO cw_languages SET code='EN', name='msg_cs_warn_empty_contentsection_name', value='The content section name is not specified. We strongly recommend to set a name of the contentsection.', topic='Errors';
REPLACE INTO cw_languages SET code='EN', name='msg_cs_warn_empty_contentsection_url', value='The content section URL is not specified and this contentsection will be unclickable. Are you sure to leave URL empty?', topic='Errors';
REPLACE INTO cw_languages SET code='EN', name='txt_cs_there_are_no_contentsections_found', value='There are no content sections', topic='Text';
REPLACE INTO cw_languages SET code='EN', name='txt_delete_selected_contentsections_warning', value='Are you sure you want to delete the selected content sections?', topic='Text';

REPLACE INTO `cw_available_images` (`name`, `type`, `multiple`, `max_width`, `md5_check`, `default_image`) VALUES ('adb_images', 'U', '0', '0', '1', '');

ALTER TABLE `cw_attributes` CHANGE `item_type` `item_type` CHAR(2) NOT NULL DEFAULT '';
ALTER TABLE `cw_attributes_values` CHANGE `item_type` `item_type` CHAR(2) NOT NULL DEFAULT '';

DELETE FROM `cw_attributes` WHERE item_type='AB';
INSERT INTO `cw_attributes` (`name`, `type`, `field`, `is_required`, `active`, `orderby`, `addon`, `item_type`, `is_sortable`, `is_comparable`, `default_value`, `is_show`) VALUES
('Domains', 'domain-selector', 'domains', 1, '1', 1, 'multi_domains', 'AB', 0, 0, '', 0);

-- get menu_id for Sections
SELECT @sections:=menu_id FROM cw_menu WHERE title='lbl_content' AND parent_menu_id=0 AND area='A' LIMIT 1;
-- insert new entry to menu
DELETE FROM cw_menu WHERE title='lbl_cs_cms';
INSERT INTO `cw_menu` (`menu_id`, `parent_menu_id`, `title`, `link`, `orderby`, `area`, `access_level`, `addon`, `is_loggedin`) VALUES
(NULL, @sections, 'lbl_cs_cms', 'index.php?target=cms&mode=list', 200, 'A', '', 'cms', 1);
