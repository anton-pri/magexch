-- Latest version of mysql does not allow default value for BLOB and TEXT
ALTER TABLE `cw_languages` CHANGE `tooltip` `tooltip` MEDIUMTEXT DEFAULT NULL;
ALTER TABLE `cw_config` CHANGE `defvalue` `defvalue` MEDIUMTEXT DEFAULT NULL, CHANGE `variants` `variants` MEDIUMTEXT DEFAULT NULL;
ALTER TABLE `cw_system_messages` CHANGE `message` `message` TEXT NULL DEFAULT NULL, CHANGE `data` `data` BLOB NULL DEFAULT NULL; 

UPDATE `cw_languages` SET `value` = 'B2B account {{email}} created successfully but suspended until approval by administrator' WHERE `cw_languages`.`code` = 'EN' AND `cw_languages`.`name` = 'lbl_b2b_account_suspended';
