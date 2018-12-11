SELECT @config_category_id:=config_category_id FROM cw_config_categories WHERE category='google_base';
replace into cw_config (name, comment, value, config_category_id, orderby, type, defvalue) values ('gb_low_price_limit', 'Low price export limit', 12.24, @config_category_id, 1, 'numeric', 0.0);
