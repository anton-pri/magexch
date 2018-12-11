REPLACE INTO `cw_config_categories` (`config_category_id` ,`category` ,`is_local`) VALUES (NULL , 'clean_urls', '0');
SET @config_category_id = LAST_INSERT_ID();
REPLACE INTO cw_config SET name='clean_urls_allow_html_C', comment='Enable category pages HTML clean url extension', value='', config_category_id=@config_category_id, orderby='100', type='checkbox', defvalue='', variants='';
REPLACE INTO cw_config SET name='clean_urls_allow_html_P', comment='Enable product pages HTML clean url extension', value='', config_category_id=@config_category_id, orderby='200', type='checkbox', defvalue='', variants='';
REPLACE INTO cw_config SET name='clean_urls_allow_html_M', comment='Enable manufacturer pages HTML clean url extension', value='', config_category_id=@config_category_id, orderby='300', type='checkbox', defvalue='', variants='';
REPLACE INTO cw_config SET name='clean_urls_allow_html_AB', comment='Enable static content pages HTML clean url extension', value='', config_category_id=@config_category_id, orderby='400', type='checkbox', defvalue='', variants='';

