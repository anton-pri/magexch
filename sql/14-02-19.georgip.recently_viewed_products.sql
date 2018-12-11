SELECT @config_category_id := config_category_id FROM `cw_config_categories` WHERE category = 'accessories';
REPLACE INTO cw_config SET name='recently_viewed_products_list_settings', comment='Recently viewed products list settings', value='', config_category_id=@config_category_id, orderby='4000', type='separator', defvalue='', variants='';
REPLACE INTO cw_languages SET code='EN', name='lbl_ac_recently_viewed_products_list_settings', value='Recently viewed products list settings', topic='Labels';
REPLACE INTO cw_languages SET code='EN', name='opt_recently_viewed_products_list_settings', value='Recently viewed products list settings', topic='Options';
REPLACE INTO cw_config SET name='ac_rv_display_recently_viewed_products', comment='Display recently viewed products list on cart page', value='Y', config_category_id=@config_category_id, orderby='4100', type='checkbox', defvalue='Y', variants='';
REPLACE INTO cw_languages SET code='EN', name='lbl_ac_rv_display_recently_viewed_products', value='Display recently viewed products list on cart page', topic='Labels';
REPLACE INTO cw_languages SET code='EN', name='lbl_your_recently_viewed_items', value='Your recently viewed items', topic='Labels';

