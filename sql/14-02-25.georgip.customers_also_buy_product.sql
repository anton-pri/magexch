SELECT @config_category_id := config_category_id FROM `cw_config_categories` WHERE category = 'accessories';

REPLACE INTO cw_config SET name='cart_customers_also_bought', comment='Customers who bought items in cart also bought settings', value='', config_category_id=@config_category_id, orderby='5000', type='separator', defvalue='', variants='';
REPLACE INTO cw_languages SET code='EN', name='lbl_ac_cart_customers_also_bought', value='Customers who bought items in cart also bought settings', topic='Labels';
REPLACE INTO cw_languages SET code='EN', name='opt_cart_customers_also_bought', value='Customers who bought items in cart also bought settings', topic='Options';
REPLACE INTO cw_config SET name='ac_cab_display', comment='Display cusomers also bought products list on cart page', value='Y', config_category_id=@config_category_id, orderby='5100', type='checkbox', defvalue='Y', variants='';
REPLACE INTO cw_languages SET code='EN', name='lbl_ac_cab_display', value='Display cusomers also bought products list on cart page', topic='Labels';
REPLACE INTO cw_config SET name='ac_cab_max_products', comment='Max number items of cusomers also bought list', value='6', config_category_id=@config_category_id, orderby='5100', type='numeric', defvalue='6', variants='';
REPLACE INTO cw_languages SET code='EN', name='lbl_ac_cab_max_products', value='Max number items of cusomers also bought list', topic='Labels';
REPLACE INTO cw_languages SET code='EN', name='lbl_ac_customers_also_bought', value='Customers who bought these items also bought', topic='Labels';

