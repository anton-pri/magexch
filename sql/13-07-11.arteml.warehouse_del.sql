UPDATE `cw_config` SET `comment` = 'Enable email notifications for admin about initially placed orders' WHERE `cw_config`.`name` = 'enable_init_order_notif';

UPDATE `cw_languages` SET `value` = 'Enable email notifications for orders department about initially placed orders' WHERE `cw_languages`.`code` = 'EN' AND `cw_languages`.`name` = 'opt_enable_init_order_notif';

-- INSERT INTO `cw_addons` (`addon`, `descr`, `active`, `status`, `parent`, `version`, `orderby`) VALUES ('warehouse', 'Allow to manage a few warehouses', 0, 0, '', '0.1', 0);

delete from cw_addons where addon='warehouse';
