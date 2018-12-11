SELECT @config_category_id := config_category_id FROM `cw_config_categories` WHERE category = 'general';
REPLACE INTO cw_config set name='product_descr_is_required', comment='Product description field is mandatory on the product modify/add page in admin', value='Y', config_category_id=@config_category_id, orderby='652', type='checkbox',  defvalue='Y', variants='';
