SELECT @config_category_id:=config_category_id FROM cw_config_categories WHERE category='google_base';
INSERT INTO cw_config SET name='gb_file_format', comment='File format', value='xml', config_category_id = @config_category_id, orderby='12', type='selector', defvalue='new', variants="xml:XML\ncsv:CSV";

