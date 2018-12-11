SELECT @config_category_id:=config_category_id FROM cw_config_categories WHERE category='Appearance' AND is_main=0;

REPLACE INTO cw_config SET name='infinite_scroll', comment='Infinite scroll', value='Y', config_category_id = @config_category_id, orderby='20', type='checkbox', defvalue='Y', variants='';
