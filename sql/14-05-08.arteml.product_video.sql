-- Addon
REPLACE INTO `cw_addons` (`addon`, `descr`, `active`, `status`, `parent`, `version`, `orderby`) VALUES ('product_video', 'Attach product video from external sites to products', 1, 1, '', '1.0', 0);

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
