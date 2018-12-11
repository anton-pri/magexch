REPLACE INTO `cw_languages` (`code`, `name`, `value`, `topic`) VALUES ('EN', 'addon_name_google_remarketing', 'Google Remarketing', 'Addons');
-- lang var
REPLACE INTO `cw_languages` (`code`, `name`, `value`, `topic`) VALUES ('EN', 'lbl_google_remarketing', 'Google Remarketing', 'Labels');
-- new addon record
REPLACE INTO `cw_addons` (`addon`, `descr`, `active`, `status`, `parent`)
VALUES ('google_remarketing', 'Google Remarketing Tool', 1, 0, '');

-- configuration options
INSERT INTO `cw_config_categories` (`config_category_id` ,`category` ,`is_main`) VALUES (NULL , 'google_remarketing', '0');
SET @config_category_id = LAST_INSERT_ID();
INSERT INTO cw_config SET name='gr_conversion_id', comment='Google Conversion ID', value='', config_category_id = @config_category_id, orderby='15', type='text', defvalue='', variants='';
INSERT INTO cw_config SET name='gr_conversion_label', comment='Google Conversion Label', value='', config_category_id = @config_category_id, orderby='15', type='text', defvalue='', variants='';
INSERT INTO cw_config SET name='gr_custom_params', comment='Google Custom Params', value='window.google_tag_params', config_category_id = @config_category_id, orderby='15', type='text', defvalue='window.google_tag_params', variants='';
INSERT INTO cw_config SET name='gr_only', comment='Google Remarketing Only', value='true', config_category_id = @config_category_id, orderby='20', type='selector', defvalue='true', variants='true:True\r\nfalse:False';