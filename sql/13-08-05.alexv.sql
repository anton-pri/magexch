INSERT INTO `cw_languages` (`code`, `name`, `value`, `topic`) VALUES ('EN', 'lbl_report_as_sold', 'Report as sold', 'Labels');
INSERT INTO `cw_languages` (`code`, `name`, `value`, `topic`) VALUES ('EN', 'lbl_reported', 'Reported', 'Labels');
INSERT INTO `cw_languages` (`code`, `name`, `value`, `topic`) VALUES ('EN', 'addon_name_printdrop_com', 'printdrop.com', 'Addons');
INSERT INTO `cw_languages` (`code`, `name`, `value`, `topic`) VALUES ('EN', 'lbl_register_field_sections_photo', 'Photo', 'Labels');

DELETE FROM cw_addons WHERE addon='printdrop_com';
REPLACE INTO `cw_addons` (`addon`, `descr`, `active`, `status`, `parent`,`version`,`orderby`)
VALUES ('printdrop_com', 'printdrop.com', 0, 1, '', '0.1',0);

INSERT INTO `cw_register_fields_sections` (`section_id`, `name`, `type`, `is_default`, `orderby`) VALUES (NULL, 'photo', 'U', '1', '1');

SELECT @config_category_id:=config_category_id FROM cw_config_categories WHERE category='Appearance' AND is_main=0;

INSERT INTO `cw_config` (`name`, `comment`, `value`, `config_category_id`, `orderby`, `type`, `defvalue`, `variants`) 
VALUES ('size_user_avatar', 'Size of user avatar (width of square placeholder in px)', '0', @config_category_id, '450', 'numeric', '0', '');