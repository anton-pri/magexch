DELETE FROM cw_config_categories WHERE category='google_analytics';
DELETE FROM cw_config WHERE name like 'google_analytics_%';
-- configuration options
REPLACE INTO `cw_config_categories` (`config_category_id` ,`category` ,`is_local`) VALUES (NULL , 'google_analytics', '0');
SET @config_category_id = LAST_INSERT_ID();
REPLACE INTO cw_config SET name='google_analytics_account', comment='Google Analytics Account', value='', config_category_id=@config_category_id, orderby='100', type='text', defvalue='', variants='';
REPLACE INTO cw_config SET name='google_analytics_type', comment='Google Analytics code version', value='traditional',  config_category_id=@config_category_id, orderby='200', type='selector', defvalue='', variants='traditional:lbl_google_analytics_traditional\r\nasynchronous:lbl_google_analytics_asynchronous';
replace into cw_config set name='google_analytics_e_commerce', comment='Use E-Commerce analysis', value='Y', config_category_id=@config_category_id, orderby=300, type='checkbox';
