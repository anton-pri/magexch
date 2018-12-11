REPLACE INTO `cw_addons` (`addon`, `descr`, `active`, `status`, `parent`, `version`, `orderby`) 
                  VALUES ('linkedin_login', 'Allow customers to sign in with LinkedIn account', 1, 1, '', '0.1', 0);

REPLACE INTO `cw_config_categories` (`config_category_id` ,`category` ,`is_local`) VALUES (NULL , 'linkedin_login', '0');
SET @config_category_id = LAST_INSERT_ID();
REPLACE INTO cw_config SET name='linkedin_login_key', comment='LinkedIn API key', value='', config_category_id=@config_category_id, orderby='100', type='text', defvalue='', variants='';
REPLACE INTO cw_config SET name='linkedin_login_secret', comment='LinkedIn API secret', value='', config_category_id=@config_category_id, orderby='200', type='text', defvalue='', variants='';

REPLACE INTO cw_languages SET code='EN', name='addon_descr_linkedin_login', value='Allow customers to sign in with LinkedIn account', topic='Addons';
REPLACE INTO cw_languages SET code='EN', name='addon_name_linkedin_login', value='LinkedIn Login', topic='Addons';
