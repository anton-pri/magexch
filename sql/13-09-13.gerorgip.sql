UPDATE `cw_attributes` SET `type` = 'ebay_category_selector' WHERE `cw_attributes`.`addon` = 'ebay' AND `cw_attributes`.`name` = 'Category';
UPDATE `cw_attributes` SET `type` = 'google_product_category_selector' WHERE `cw_attributes`.`addon` = 'google_base' AND `cw_attributes`.`name` = 'Google product taxonomy';
INSERT INTO `cw_languages` (`code`, `name`, `value`, `tooltip`, `topic`) VALUES ('EN', 'lbl_choose_category', 'Choose Category', '', 'Labels');
INSERT INTO `cw_languages` (`code`, `name`, `value`, `tooltip`, `topic`) VALUES ('EN', 'lbl_selected', 'Selected', '', 'Labels');
INSERT INTO `cw_languages` (`code`, `name`, `value`, `tooltip`, `topic`) VALUES ('EN', 'lbl_select_from_existing', 'Select From Existing', '', 'Labels');