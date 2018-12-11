replace into cw_config_categories set category='advanced_search';
replace into cw_languages (code,name,value,topic) values ('EN', 'option_title_advanced_search', 'Advanced Search Options', 'Options');
replace into cw_config set name='adv_search_attributes_config', value='', config_category_id =1, type='text', comment='Advanced Search options (serialized)';
