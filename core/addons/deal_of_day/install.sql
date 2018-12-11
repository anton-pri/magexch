REPLACE INTO `cw_addons` (`addon`, `descr`, `active`, `status`, `parent`, `version`, `orderby`)
                  VALUES ('deal_of_day', 'Deal of the day', 1, 1, '', '0.1', 0);

REPLACE INTO `cw_config_categories` (`config_category_id` ,`category` ,`is_local`) VALUES (NULL , 'deal_of_day', '0');
SET @config_category_id = LAST_INSERT_ID();

replace into cw_config set name='dod_newslist', comment='Deal of the day subscription', config_category_id = @config_category_id, orderby=0, type='newslists';
replace into cw_config set name='dod_news_template_subject', comment='DOD email subject template code', config_category_id = @config_category_id, orderby=8, type='textarea';
replace into cw_config set name='dod_news_template', comment='DOD email template code', config_category_id = @config_category_id, orderby=10, type='textarea';

REPLACE INTO cw_languages SET code='EN', name='addon_descr_deal_of_day', value='Generates \'deal of the day\' special offers', topic='Addons';
REPLACE INTO cw_languages SET code='EN', name='addon_name_deal_of_day', value='Deal of the day', topic='Addons';
