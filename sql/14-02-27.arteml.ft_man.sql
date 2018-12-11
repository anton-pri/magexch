REPLACE INTO `cw_languages` (`code`, `name`, `value`, `tooltip`, `topic`) VALUES ('EN', 'lbl_featured', 'Featured', '', 'Labels');
ALTER TABLE `cw_manufacturers` ADD `featured` TINYINT( 1 ) NOT NULL DEFAULT '0';
