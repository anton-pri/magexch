-- Addon
REPLACE INTO `cw_addons` (`addon`, `descr`, `active`, `status`, `parent`, `version`, `orderby`) VALUES ('instagram_login', 'Login with Instagram account', 1, 1, '', '0.1', 0);

-- configuration options
REPLACE INTO `cw_config_categories` (`config_category_id` ,`category` ,`is_local`) VALUES (NULL , 'instagram_login', '0');
SET @config_category_id = LAST_INSERT_ID();
REPLACE INTO cw_config SET name='instagram_login_settings', comment='Instagram Login settings', value='', config_category_id=@config_category_id, orderby='0', type='separator', defvalue='', variants='';
REPLACE INTO cw_config SET name='instagram_login_client_id', comment='Client ID', value='', config_category_id=@config_category_id, orderby='200', type='text', defvalue='', variants='';
REPLACE INTO cw_config SET name='instagram_login_client_secret', comment='Client Secret', value='', config_category_id=@config_category_id, orderby='300', type='text', defvalue='', variants='';

-- Addon name/description
REPLACE INTO cw_languages SET code='EN', name='addon_descr_instagram_login', value='Login with Instagram account', topic='Addons';
REPLACE INTO cw_languages SET code='EN', name='addon_name_instagram_login', value='Instagram Login', topic='Addons';
REPLACE INTO cw_languages SET code='EN', name='option_title_instagram_login', value='Instagram Login options', topic='Options';
