-- Addon
REPLACE INTO `cw_addons` (`addon`, `descr`, `active`, `status`, `parent`, `version`, `orderby`) VALUES ('mslive_login', 'Login via Microsoft Live account', 1, 1, '', '0.1', 0);

-- configuration options
REPLACE INTO `cw_config_categories` (`config_category_id` ,`category` ,`is_local`) VALUES (NULL , 'mslive_login', '0');
SET @config_category_id = LAST_INSERT_ID();
REPLACE INTO cw_config SET name='mslive_login_consumer_key', comment='MS Live API consumer key', value='', config_category_id=@config_category_id, orderby='100', type='text', defvalue='', variants='';
REPLACE INTO cw_config SET name='mslive_login_consumer_secret', comment='MS Live API consumer secret key', value='', config_category_id=@config_category_id, orderby='200', type='text', defvalue='', variants='';


-- Addon name/description
REPLACE INTO cw_languages SET code='EN', name='addon_descr_mslive_login', value='Login via Microsoft Live account', topic='Addons';
REPLACE INTO cw_languages SET code='EN', name='addon_name_mslive_login', value='Microsoft Live Login', topic='Addons';
