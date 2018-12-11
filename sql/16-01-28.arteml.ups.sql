SELECT @config_category_id:=config_category_id FROM cw_config_categories WHERE category='shipping_ups';

REPLACE INTO cw_config SET name='shipper_number', comment='Shipper number', value='', config_category_id=@config_category_id, orderby='60', type='text', defvalue='', variants='';
REPLACE INTO cw_config SET name='test_mode', comment='Test mode', value='N', config_category_id=@config_category_id, orderby='61', type='checkbox', defvalue='N', variants='';

