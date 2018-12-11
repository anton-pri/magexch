SELECT @config_category_id:=config_category_id FROM cw_config_categories WHERE category='custom_magazineexchange_sellers';
REPLACE INTO cw_config SET name='mag_seller_minimal_price', comment='Minimal price limit for digital products', value='0.31', config_category_id=@config_category_id, orderby='0', type='numeric', defvalue='', variants='';
