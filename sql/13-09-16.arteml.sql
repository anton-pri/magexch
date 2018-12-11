UPDATE `cw_languages` SET `value` = 'The number in parentheses is the number of products/categories including all subcategories.' WHERE `cw_languages`.`code` = 'EN' AND `cw_languages`.`name` = 'txt_categoryies_management_note';
ALTER TABLE `cw_attributes` CHANGE `addon` `addon` VARCHAR( 255 ) NOT NULL DEFAULT '';
