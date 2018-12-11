CREATE TABLE IF NOT EXISTS `cw_categories_extra` (
  `category_id` int(11) NOT NULL,
  `parent_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`category_id`,`parent_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
