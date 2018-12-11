-- Addon
REPLACE INTO `cw_addons` (`addon`, `descr`, `active`, `status`, `parent`, `version`, `orderby`) VALUES ('google_analytics', 'Google Analytics Tool', 1, 0, '', '0.1', 0);

-- configuration options
REPLACE INTO `cw_config_categories` (`config_category_id` ,`category` ,`is_local`) VALUES (NULL , 'google_analytics', '0');
SET @config_category_id = LAST_INSERT_ID();
delete from cw_config where name='account';
REPLACE INTO cw_config SET name='google_analytics_account', comment='Google Analytics Account', value='', config_category_id=@config_category_id, orderby='100', type='text', defvalue='', variants='';
REPLACE INTO cw_config SET name='google_analytics_type', comment='Google Analytics code version', value='traditional',  config_category_id=@config_category_id, orderby='200', type='selector', defvalue='', variants='traditional:lbl_google_analytics_traditional\nasynchronous:lbl_google_analytics_asynchronous';
replace into cw_config set name='google_analytics_e_commerce', comment='Use E-Commerce analysis', value='Y', config_category_id=@config_category_id, orderby=300, type='checkbox';

-- Addon name/description
REPLACE INTO cw_languages SET code='EN', name='addon_descr_google_analytics', value='Google Analytics Tool', topic='Addons';
REPLACE INTO cw_languages SET code='EN', name='addon_name_google_analytics', value='Google Analytics', topic='Addons';
REPLACE INTO cw_languages SET code='EN', name='option_title_google_analytics', value='Google Analytics options', topic='Options';

REPLACE INTO `cw_languages` (`code`, `name`, `value`, `topic`) VALUES ('EN', 'lbl_google_analytics_traditional', 'Traditional', 'Labels');
REPLACE INTO `cw_languages` (`code`, `name`, `value`, `topic`) VALUES ('EN', 'lbl_google_analytics_asynchronous', 'Asynchronous', 'Labels');
