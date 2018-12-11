SELECT @config_category_id:=config_category_id FROM cw_config_categories WHERE category='webmaster' limit 1;
update cw_config set config_category_id=@config_category_id where name IN ('webmaster_flag_sep','webmaster_flag','webmaster_features_sep','webmaster_A','webmaster_langvar','webmaster_other_sep');
