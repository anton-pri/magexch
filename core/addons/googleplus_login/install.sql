-- Addon
REPLACE INTO `cw_addons` (`addon`, `descr`, `active`, `status`, `parent`, `version`, `orderby`) VALUES ('googleplus_login', 'Login with Google Plus account', 1, 1, '', '0.1', 0);

-- configuration options
REPLACE INTO `cw_config_categories` (`config_category_id` ,`category` ,`is_local`) VALUES (NULL , 'googleplus_login', '0');
SET @config_category_id = LAST_INSERT_ID();
REPLACE INTO cw_config SET name='googleplus_login_settings', comment='Google Plus Login settings', value='', config_category_id=@config_category_id, orderby='0', type='separator', defvalue='', variants='';
REPLACE INTO cw_config SET name='googleplus_login_client_id', comment='Client ID', value='', config_category_id=@config_category_id, orderby='200', type='text', defvalue='', variants='';
REPLACE INTO cw_config SET name='googleplus_login_client_secret', comment='Client Secret Key', value='', config_category_id=@config_category_id, orderby='300', type='text', defvalue='', variants='';
REPLACE INTO cw_config SET name='googleplus_login_developer_key', comment='Developer Key', value='', config_category_id=@config_category_id, orderby='400', type='text', defvalue='', variants='';

-- Addon name/description
REPLACE INTO cw_languages SET code='EN', name='addon_descr_googleplus_login', value='Login with Google Plus account', topic='Addons';
REPLACE INTO cw_languages SET code='EN', name='addon_name_googleplus_login', value='Google+ Login', topic='Addons';
REPLACE INTO cw_languages SET code='EN', name='option_title_googleplus_login', value='Google+ Login options', topic='Options';
