INSERT INTO `cw_languages` (`code`, `name`, `value`, `topic`) VALUES ('EN', 'lbl_history', 'History', 'Labels');

UPDATE cw_attributes_values SET code = 'EN' WHERE attribute_id IN (SELECT attribute_id FROM cw_attributes WHERE field = 'clean_url');

CREATE TABLE IF NOT EXISTS `cw_clean_urls_history` (
`id` INT( 11 ) NOT NULL AUTO_INCREMENT ,
`item_id` INT( 11 ) NOT NULL ,
`item_type` CHAR( 2 ) NOT NULL ,
`url` TEXT NOT NULL ,
`ctime` INT( 11 ) NOT NULL ,
PRIMARY KEY ( `id` ) ,
INDEX ( `id` , `item_id` )
) ENGINE = MYISAM DEFAULT CHARSET=utf8;