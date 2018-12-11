REPLACE INTO `cw_config` ( `name` , `comment` , `value` , `config_category_id` , `orderby` , `type` , `defvalue` , `variants`) VALUES ( 'products_images_det_width', 'The product image width on product page', '150', '4', '150', 'numeric', '0', '');
UPDATE cw_config SET name='products_images_thumb_width' WHERE name='thumbnail_width';

DELETE FROM `cw_available_images` WHERE `cw_available_images`.`name` = 'products_images_add';
DELETE FROM `cw_available_images` WHERE `cw_available_images`.`name` = 'products_images_small';

DROP TABLE cw_products_images_add, cw_products_images_small;
--
-- Структура таблицы `cw_products_images_add`
--
/*
CREATE TABLE IF NOT EXISTS `cw_products_images_add` (
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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `cw_products_images_small`
--

CREATE TABLE IF NOT EXISTS `cw_products_images_small` (
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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8
*/
