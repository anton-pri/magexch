SELECT @config_category_id:=config_category_id FROM cw_config_categories WHERE category = 'Watermarks' AND is_main = 0;
DELETE FROM cw_navigation_settings WHERE config_category_id = @config_category_id;

SELECT @config_category_id:=config_category_id FROM cw_config_categories WHERE category = 'product_settings' AND is_main = 0;
DELETE FROM cw_navigation_settings WHERE config_category_id = @config_category_id;