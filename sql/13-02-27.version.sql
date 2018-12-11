DELETE FROM `ars_config` WHERE `ars_config`.`name` = 'version_skin' AND `ars_config`.`config_category_id` = 1;
UPDATE `ars_config` SET `name` = 'version' WHERE `ars_config`.`name` = 'version_core' AND `ars_config`.`config_category_id` =1;
