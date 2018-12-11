DELETE FROM `cw_languages` WHERE `cw_languages`.`name` like '%anti_frau%';

DELETE FROM `cw_languages` WHERE `cw_languages`.`code` = 'EN' AND `cw_languages`.`name` = 'opt_auto_product_number';
DELETE FROM `cw_languages` WHERE `cw_languages`.`code` = 'EN' AND `cw_languages`.`name` = 'eml_auto_update_error';

UPDATE `cw_config_categories` SET `category` = replace(category,'-','_');
