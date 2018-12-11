CREATE TABLE IF NOT EXISTS `cw_datascraper_attributes` (
  `ds_attribute_id` int(5) NOT NULL AUTO_INCREMENT,
  `site_id` int(5) NOT NULL,
  `name` varchar(255) NOT NULL,
  `pattern` varchar(1000) NOT NULL,
  `type` varchar(100) NOT NULL,
  `table_field` varchar(100) NOT NULL,
  `table_field_hub` varchar(32) NOT NULL DEFAULT '',
  `mandatory` tinyint(2) NOT NULL DEFAULT '0',
  PRIMARY KEY (`ds_attribute_id`),
  KEY `idx_cw_datascraper_attributes_id` (`ds_attribute_id`)
) ENGINE=MyISAM AUTO_INCREMENT=171 DEFAULT CHARSET=utf8;
alter table cw_datascraper_attributes add unique key (name, site_id);
