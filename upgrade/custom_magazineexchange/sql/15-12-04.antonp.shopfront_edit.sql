insert into cw_available_images values ('shopfront_images', 'U', 0, 200, 1, 'default_image_70.gif');

CREATE TABLE IF NOT EXISTS `cw_shopfront_images` (
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
) ENGINE=MyISAM AUTO_INCREMENT=24 DEFAULT CHARSET=utf8;
