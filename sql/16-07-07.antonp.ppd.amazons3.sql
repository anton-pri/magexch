SELECT @config_category_id:=config_category_id FROM cw_config_categories WHERE category = 'ppd';

replace into cw_config (name, comment, value, config_category_id, type, orderby) values ('ppd_awsS3_enabled', 'ppd_awsS3_enabled', 'N', @config_category_id, 'checkbox',200);
replace into cw_config (name, comment, value, config_category_id, type, orderby) values ('ppd_awsAccessKey', 'ppd_awsAccessKey', '', @config_category_id, 'text',210);
replace into cw_config (name, comment, value, config_category_id, type, orderby) values ('ppd_awsSecretKey', 'ppd_awsSecretKey', '', @config_category_id, 'text', 220);
replace into cw_languages (code, topic, name, value) values ('EN', 'Options', 'opt_ppd_awsS3_enabled', 'Enable Amazon S3 storage service');
replace into cw_languages (code, topic, name, value) values ('EN', 'Options', 'opt_ppd_awsAccessKey', 'Amazon S3 Access Key');
replace into cw_languages (code, topic, name, value) values ('EN', 'Options', 'opt_ppd_awsSecretKey', 'Amazon S3 Secret Key');
SELECT @config_category_id:=config_category_id FROM cw_config_categories WHERE category = 'ppd';

replace into cw_config (name, comment, value, config_category_id, type, orderby) values ('ppd_aws3_bucketName', 'ppd_aws3_bucketName', '', @config_category_id, 'text',230);
replace into cw_languages (code, topic, name, value) values ('EN', 'Options', 'opt_ppd_aws3_bucketName', 'Amazon S3 Bucket Name');
