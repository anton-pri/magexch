UPDATE `cw_addons` SET `orderby` = '10' WHERE `cw_addons`.`addon` = 'vertical_response' LIMIT 1;

REPLACE INTO `cw_languages` (`code`, `name`, `value`, `tooltip`, `topic`) VALUES ('EN', 'lbl_manual_list', 'Manual list', '', 'Label');
REPLACE INTO `cw_languages` (`code`, `name`, `value`, `tooltip`, `topic`) VALUES ('EN', 'lbl_saved_customers_search', 'Saved customers search', '', 'Label');
REPLACE INTO `cw_languages` (`code`, `name`, `value`, `tooltip`, `topic`) VALUES ('EN', 'lbl_type_list_control', 'Type of the subscribers list control', '', 'Label');

DROP TABLE IF EXISTS `cw_newsletter_products`;
CREATE TABLE IF NOT EXISTS `cw_newsletter_products` (
`list_id` INT( 11 ) NOT NULL ,
`product_id` INT( 11 ) NOT NULL ,
`product` VARCHAR( 255 ) NOT NULL ,
`product_num` SMALLINT( 1 ) NOT NULL
) ENGINE = MYISAM ;