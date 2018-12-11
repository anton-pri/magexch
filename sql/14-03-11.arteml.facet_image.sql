INSERT INTO `cw_available_images` ( `name` , `type` , `multiple` , `max_width` , `md5_check` , `default_image`) VALUES ( 'facet_categories_images', 'U', '0', '300', '1', 'default_image_70.gif');
CREATE TABLE `cw_facet_categories_images` (
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
  UNIQUE KEY `id` (`id`),
  KEY `image_path` (`image_path`)
) ENGINE=MyISAM;

ALTER TABLE `cw_available_images` CHANGE `max_width` `max_width` INT( 11 ) NOT NULL DEFAULT '150' COMMENT 'crop image during upload';
ALTER TABLE cw_docs DROP INDEX doc_info_id_2;
ALTER TABLE `cw_clean_urls_custom_facet_urls`  DROP INDEX url_id;
