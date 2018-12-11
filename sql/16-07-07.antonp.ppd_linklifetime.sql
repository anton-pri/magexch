SELECT @config_category_id:=config_category_id FROM cw_config_categories WHERE category = 'ppd';

replace into cw_config (name, comment, value, config_category_id, type, orderby) values ('ppd3_aws_item_lifetime', 'ppd3_aws_item_lifetime', '1440', @config_category_id, 'text',240);
replace into cw_languages (code, topic, name, value) values ('EN', 'Options', 'opt_ppd3_aws_item_lifetime', 'Amazon S3 Download link lifetime (minutes)');
