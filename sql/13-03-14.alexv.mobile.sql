UPDATE `ars_modules` SET `orderby` = '10' WHERE `ars_modules`.`module` = 'multi-domains';

ALTER TABLE `ars_domains` ADD `http_alias_hosts` MEDIUMTEXT NOT NULL AFTER `https_host`;

-- new module record
REPLACE INTO `ars_modules` (`module`, `module_descr`, `active`, `status`, `parent_module`, `version`)
VALUES ('Mobile', 'Module allows to manage the system on mobile devices', 0, 1, '', '0.1');

INSERT INTO `ars_languages` (`code`, `name`, `value`, `topic`) VALUES ('EN', 'lbl_mobile_full', 'Mobile/Full', 'Label');
INSERT INTO `ars_languages` (`code`, `name`, `value`, `topic`) VALUES ('EN', 'lbl_mobile_host', 'Mobile host', 'Label');
INSERT INTO `ars_languages` (`code`, `name`, `value`, `topic`) VALUES ('EN', 'lbl_full_version', 'Full version', 'Label');
INSERT INTO `ars_languages` (`code`, `name`, `value`, `topic`) VALUES ('EN', 'lbl_mobile_version', 'Mobile version', 'Label');

INSERT INTO `ars_attributes`
(`attribute_id`, `name`, `type`, `field`, `is_required`, `active`, `orderby`, `module`, `item_type`, `is_sortable`, `is_comparable`, `is_show`) VALUES
(NULL, "Mobile host", 'text', 'mobile_host', 0, '1', 10, 'Mobile', 'DM', 0, 0, 1);

INSERT INTO `ars_attributes`
(`attribute_id`, `name`, `type`, `field`, `is_required`, `active`, `orderby`, `module`, `item_type`, `is_sortable`, `is_comparable`, `is_show`) VALUES
(NULL, "Mobile/Full", 'selectbox', 'display_mode', 0, '1', 10, 'Mobile', 'AB', 0, 0, 1);
SET @attribute_id = LAST_INSERT_ID();
INSERT INTO `ars_attributes_default` (`value`, `value_key`, `attribute_id`, `is_default`, `orderby`, `active`, `image_id`, `pf_image_id`) VALUES
('Both', '0', @attribute_id, 1, 10, 1, 0, 0),
('Mobile only', '1', @attribute_id, 0, 20, 1, 0, 0),
('Full only', '2', @attribute_id, 0, 30, 1, 0, 0);