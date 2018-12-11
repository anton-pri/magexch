SELECT @config_category_id:=config_category_id FROM cw_config_categories WHERE category='General';
replace into cw_config (name, comment, value, type, orderby, config_category_id) values ('disabled_products_access_by_direct_link', 'Allow disabled products by direct link in customer area', 'Y', 'checkbox', 430, @config_category_id);
