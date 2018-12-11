DELETE FROM `cw_config` WHERE `cw_config`.`name` = '' AND `cw_config`.`config_category_id` = 0 LIMIT 1;
DELETE FROM `cw_config` WHERE `cw_config`.`name` = 'data_cache_expiration' AND `cw_config`.`config_category_id` = 0 LIMIT 1;
DELETE FROM `cw_config` WHERE `cw_config`.`name` = 'next_auto_update' AND `cw_config`.`config_category_id` = 0 LIMIT 1;

INSERT INTO `cw_languages` (`code`, `name`, `value`, `topic`) VALUES ('EN', 'lbl_filetypes', 'File types', 'Labels');