INSERT INTO `cw_languages` (`code`, `name`, `value`, `topic`) VALUES ('EN', 'lbl_att_type_hidden', 'Hidden', 'Labels');
INSERT INTO `cw_languages` (`code`, `name`, `value`, `topic`) VALUES ('EN', 'lbl_att_type_rating', 'Rating', 'Labels');

ALTER TABLE `cw_products_votes` ADD `attribute_id` INT NOT NULL DEFAULT '0' AFTER `customer_id`;