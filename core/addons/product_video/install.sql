-- Addon
REPLACE INTO `cw_addons` (`addon`, `descr`, `active`, `status`, `parent`, `version`, `orderby`) VALUES ('product_video', 'Attach product video from external sites to products', 1, 1, '', '1.0', 0);

-- configuration options
-- ??
REPLACE INTO `cw_config_categories` (`config_category_id` ,`category` ,`is_main`) VALUES (NULL , 'product_video', '0');
SET @config_category_id = LAST_INSERT_ID();
REPLACE INTO cw_config SET name='skeleton_settings', comment='accessories list', value='', config_category_id=@config_category_id, orderby='0', type='separator', defvalue='', variants='';
REPLACE INTO cw_config SET name='product_video_flag', comment='Flag explanation', value='Y', config_category_id=@config_category_id, orderby='200', type='checkbox', defvalue='Y', variants='';
-- /??

-- Addon name/description
REPLACE INTO cw_languages SET code='EN', name='addon_descr_product_video', value='Attach product video from external sites to products', topic='Addons';
REPLACE INTO cw_languages SET code='EN', name='addon_name_product_video', value='Product Video', topic='Addons';
-- REPLACE INTO cw_languages SET code='EN', name='option_title_product_video', value='Product Video', topic='Options';

-- Langvars
REPLACE INTO `cw_languages` (`code`, `name`, `value`, `topic`) VALUES ('EN', 'lbl_product_video', 'Product video', 'Labels');

-- Create necessary tables
CREATE TABLE IF NOT EXISTS `cw_product_video` (
  `video_id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) NOT NULL,
  `pos` SMALLINT(6) NOT NULL,
  `title` varchar(128) NOT NULL,
  `descr` text NOT NULL,
  `code` text NOT NULL,
  PRIMARY KEY (`video_id`),
  KEY `product_id` (`product_id`)
);
