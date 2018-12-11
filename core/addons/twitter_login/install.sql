REPLACE INTO `cw_addons` (`addon`, `descr`, `active`, `status`, `parent`, `version`, `orderby`) 
                  VALUES ('twitter_login', 'Allow customers to sign in with twitter account', 1, 1, '', '0.1', 0);

REPLACE INTO `cw_config_categories` (`config_category_id` ,`category` ,`is_local`) VALUES (NULL , 'twitter_login', '0');
SET @config_category_id = LAST_INSERT_ID();
REPLACE INTO cw_config SET name='twitter_login_consumer_key', comment='Twitter API consumer key', value='', config_category_id=@config_category_id, orderby='100', type='text', defvalue='', variants='';
REPLACE INTO cw_config SET name='twitter_login_consumer_secret', comment='Twitter API consumer secret key', value='', config_category_id=@config_category_id, orderby='200', type='text', defvalue='', variants='';

REPLACE INTO cw_languages SET code='EN', name='addon_descr_twitter_login', value='Allow customers to sign in with twitter account', topic='Addons';
REPLACE INTO cw_languages SET code='EN', name='addon_name_twitter_login', value='Twitter Login', topic='Addons';
