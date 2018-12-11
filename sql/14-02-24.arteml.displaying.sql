delete from cw_languages where name in ('lbl_displaying_type','lbl_create_new_displaying');
DROP TABLE IF EXISTS `cw_displaying_custom`;
DROP TABLE IF EXISTS `cw_displaying_custom_fields`;
DROP TABLE IF EXISTS `cw_displaying_fields`;
DROP TABLE IF EXISTS `cw_displaying_sections`;
/*
CREATE TABLE `cw_displaying_sections` (
  `section_id` int(11) NOT NULL AUTO_INCREMENT,
  `section` varchar(32) NOT NULL DEFAULT '',
  `title` varchar(64) NOT NULL DEFAULT '',
  PRIMARY KEY (`section_id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
CREATE TABLE `cw_displaying_fields` (
  `field_id` int(11) NOT NULL AUTO_INCREMENT,
  `section_id` int(11) NOT NULL DEFAULT '0',
  `title` varchar(64) NOT NULL DEFAULT '',
  `field` varchar(32) NOT NULL DEFAULT '',
  `descr` varchar(64) NOT NULL DEFAULT '',
  PRIMARY KEY (`field_id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;
CREATE TABLE `cw_displaying_custom_fields` (
  `custom_field` int(11) NOT NULL AUTO_INCREMENT,
  `custom_id` int(11) NOT NULL DEFAULT '0',
  `field_id` int(11) NOT NULL DEFAULT '0',
  `orderby` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`custom_field`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
CREATE TABLE `cw_displaying_custom` (
  `custom_id` int(11) NOT NULL AUTO_INCREMENT,
  `section_id` int(11) NOT NULL DEFAULT '0',
  `title` varchar(64) NOT NULL DEFAULT '',
  `def` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`custom_id`) 
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
*/
