CREATE TABLE cw_linked_products (product_id int(11) NOT NULL default '0',linked_product_id int(11) NOT NULL default '0',orderby int(11) NOT NULL default '0',active char(1) NOT NULL default 'Y',PRIMARY KEY (product_id,linked_product_id),KEY products_order (product_id,orderby)) TYPE=MyISAM;

-- new addon record
REPLACE INTO `cw_addons` (`addon`, `descr`, `active`, `status`, `parent`)
VALUES ('accessories', 'Shows to customer related products and accessories lists', 1, 0, '');

-- configuration options
REPLACE INTO `cw_config_categories` (`config_category_id` ,`category` ,`is_main`) VALUES (NULL , 'accessories', '0');
SET @config_category_id = LAST_INSERT_ID();
REPLACE INTO cw_config SET name='accessories_settings', comment='accessories list', value='', config_category_id=@config_category_id, orderby='0', type='separator', defvalue='', variants='';
REPLACE INTO cw_config SET name='ac_acc_display_columns', comment='Display accessories in multiple columns (1-4)', value='4', config_category_id=@config_category_id, orderby='900', type='numeric', defvalue='2', variants='';
REPLACE INTO cw_config SET name='ac_acc_display_options', comment='Options', value='Y', config_category_id=@config_category_id, orderby='200', type='checkbox', defvalue='Y', variants='';
REPLACE INTO cw_config SET name='ac_acc_display_price', comment='Price', value='Y', config_category_id=@config_category_id, orderby='400', type='checkbox', defvalue='Y', variants='';
REPLACE INTO cw_config SET name='ac_acc_display_productcode', comment='SKU', value='Y', config_category_id=@config_category_id, orderby='600', type='checkbox', defvalue='Y', variants='';
REPLACE INTO cw_config SET name='ac_acc_display_qty_in_stock', comment='Quantity in stock', value='Y', config_category_id=@config_category_id, orderby='800', type='checkbox', defvalue='Y', variants='';
REPLACE INTO cw_config SET name='ac_acc_display_qty_selector', comment='Quantity selector', value='Y', config_category_id=@config_category_id, orderby='300', type='checkbox', defvalue='Y', variants='';
REPLACE INTO cw_config SET name='ac_acc_display_thumbnail', comment='Thumbnail', value='Y', config_category_id=@config_category_id, orderby='100', type='checkbox', defvalue='Y', variants='';
REPLACE INTO cw_config SET name='ac_acc_display_weight', comment='Product weight', value='Y', config_category_id=@config_category_id, orderby='500', type='checkbox', defvalue='Y', variants='';
REPLACE INTO cw_config SET name='ac_acc_display_wholesale', comment='Wholesale prices', value='Y', config_category_id=@config_category_id, orderby='700', type='checkbox', defvalue='Y', variants='';
REPLACE INTO cw_config SET name='ac_acc_display_manufacturer', comment='Manufacturer', value='Y', config_category_id=@config_category_id, orderby='800', type='checkbox', defvalue='Y', variants='';

REPLACE INTO cw_config SET name='recommended_products_settings', comment='Recommended products list settings', value='', config_category_id=@config_category_id, orderby='2000', type='separator', defvalue='', variants='';
REPLACE INTO cw_config SET name='ac_rec_display_columns', comment='Display recommended products in multiple columns (1-4)', value='3', config_category_id=@config_category_id, orderby='2900', type='numeric', defvalue='2', variants='';
REPLACE INTO cw_config SET name='ac_rec_display_options', comment='Options', value='Y', config_category_id=@config_category_id, orderby='2200', type='checkbox', defvalue='Y', variants='';
REPLACE INTO cw_config SET name='ac_rec_display_price', comment='Price', value='Y', config_category_id=@config_category_id, orderby='2400', type='checkbox', defvalue='Y', variants='';
REPLACE INTO cw_config SET name='ac_rec_display_productcode', comment='SKU', value='Y', config_category_id=@config_category_id, orderby='2600', type='checkbox', defvalue='Y', variants='';
REPLACE INTO cw_config SET name='ac_rec_display_qty_in_stock', comment='Quantity in stock', value='Y', config_category_id=@config_category_id, orderby='2800', type='checkbox', defvalue='Y', variants='';
REPLACE INTO cw_config SET name='ac_rec_display_qty_selector', comment='Quantity selector', value='Y', config_category_id=@config_category_id, orderby='2300', type='checkbox', defvalue='Y', variants='';
REPLACE INTO cw_config SET name='ac_rec_display_thumbnail', comment='Thumbnail in recommnded products list', value='Y', config_category_id=@config_category_id, orderby='2100', type='checkbox', defvalue='Y', variants='';
REPLACE INTO cw_config SET name='ac_rec_display_weight', comment='Product weight', value='Y', config_category_id=@config_category_id, orderby='2500', type='checkbox', defvalue='Y', variants='';
REPLACE INTO cw_config SET name='ac_rec_display_wholesale', comment='Wholesale prices', value='Y', config_category_id=@config_category_id, orderby='2700', type='checkbox', defvalue='Y', variants='';
REPLACE INTO cw_config SET name='ac_rec_display_manufacturer', comment='Manufacturer', value='Y', config_category_id=@config_category_id, orderby='2750', type='checkbox', defvalue='Y', variants='';
REPLACE INTO cw_config SET name='ac_rec_list_source', comment='Recommend products which are', value='S', config_category_id=@config_category_id, orderby='3100', type='selector', defvalue='T', variants='S:lbl_ac_sales_hits\r\nT:lbl_ac_bought_together';
REPLACE INTO cw_config SET name='ac_rec_products_limit', comment='Max number of the recommended products list (1-100)', value='20', config_category_id=@config_category_id, orderby='3000', type='numeric', defvalue='20', variants='';

REPLACE INTO cw_languages SET code='EN', name='ac_acc_display_wholesale', value='Display wholesale prices in the accessories list', topic='Options';
REPLACE INTO cw_languages SET code='EN', name='lbl_ac_accessories', value='accessories', topic='Labels';
REPLACE INTO cw_languages SET code='EN', name='lbl_ac_accessories_list_settings', value='accessories list settings', topic='Labels';
REPLACE INTO cw_languages SET code='EN', name='lbl_ac_accessories_settings', value='accessories settings', topic='Labels';
REPLACE INTO cw_languages SET code='EN', name='lbl_ac_acc_display_columns', value='Display accessories in multiple columns (1-4)', topic='Labels';
REPLACE INTO cw_languages SET code='EN', name='lbl_ac_acc_display_options', value='Display options in the accessories list', topic='Labels';
REPLACE INTO cw_languages SET code='EN', name='lbl_ac_acc_display_price', value='Display price in the accessories list', topic='Labels';
REPLACE INTO cw_languages SET code='EN', name='lbl_ac_acc_display_productcode', value='Display SKU in the accessories list', topic='Labels';
REPLACE INTO cw_languages SET code='EN', name='lbl_ac_acc_display_qty_in_stock', value='Display quantity in stock in the accessories list', topic='Labels';
REPLACE INTO cw_languages SET code='EN', name='lbl_ac_acc_display_qty_selector', value='Display quantity selector in the accessories list', topic='Labels';
REPLACE INTO cw_languages SET code='EN', name='lbl_ac_acc_display_thumbnail', value='Display thumbnail in the accessories list', topic='Labels';
REPLACE INTO cw_languages SET code='EN', name='lbl_ac_acc_display_weight', value='Display product weight in the accessories list', topic='Labels';
REPLACE INTO cw_languages SET code='EN', name='lbl_ac_acc_display_wholesale', value='Display wholesale prices in the accessories list', topic='Labels';
REPLACE INTO cw_languages SET code='EN', name='lbl_ac_active', value='Active', topic='Labels';
REPLACE INTO cw_languages SET code='EN', name='lbl_ac_add_linked_product', value='Add linked product', topic='Labels';
REPLACE INTO cw_languages SET code='EN', name='lbl_ac_bidirectional', value='Bidirectional', topic='Labels';
REPLACE INTO cw_languages SET code='EN', name='lbl_ac_bought_together', value='Bought together', topic='Labels';
REPLACE INTO cw_languages SET code='EN', name='lbl_ac_delete_linked_products', value='Delete linked products', topic='Labels';
REPLACE INTO cw_languages SET code='EN', name='lbl_ac_linked_products', value='Linked products', topic='Labels';
REPLACE INTO cw_languages SET code='EN', name='lbl_ac_linked_product_s', value='Linked product(s)', topic='Labels';
REPLACE INTO cw_languages SET code='EN', name='lbl_ac_recommended_products', value='Recommended products', topic='Labels';
REPLACE INTO cw_languages SET code='EN', name='lbl_ac_recommended_products_list_settings', value='Recommended products list settings', topic='Labels';
REPLACE INTO cw_languages SET code='EN', name='lbl_ac_rec_display_columns', value='Display recommended products in multiple columns (1-4)', topic='Labels';
REPLACE INTO cw_languages SET code='EN', name='lbl_ac_rec_display_options', value='Display options in the recommended products list', topic='Labels';
REPLACE INTO cw_languages SET code='EN', name='lbl_ac_rec_display_price', value='Display price in the recommended products list', topic='Labels';
REPLACE INTO cw_languages SET code='EN', name='lbl_ac_rec_display_productcode', value='Display SKU in the recommended products list', topic='Labels';
REPLACE INTO cw_languages SET code='EN', name='lbl_ac_rec_display_qty_in_stock', value='Display quantity in stock in the recommended products list', topic='Labels';
REPLACE INTO cw_languages SET code='EN', name='lbl_ac_rec_display_qty_selector', value='Display quantity selector in the recommended products list', topic='Labels';
REPLACE INTO cw_languages SET code='EN', name='lbl_ac_rec_display_thumbnail', value='Display thumbnail in recommnded products list', topic='Labels';
REPLACE INTO cw_languages SET code='EN', name='lbl_ac_rec_display_weight', value='Display product weight in the recommended products list', topic='Labels';
REPLACE INTO cw_languages SET code='EN', name='lbl_ac_rec_display_wholesale', value='Display wholesale prices in the recommended products list', topic='Labels';
REPLACE INTO cw_languages SET code='EN', name='lbl_ac_rec_list_source', value='Recommend products, which are', topic='Labels';
REPLACE INTO cw_languages SET code='EN', name='lbl_ac_rec_products_limit', value='Max number of the recommended products list (1-100)', topic='Labels';
REPLACE INTO cw_languages SET code='EN', name='lbl_ac_sales_hits', value='Sales hits', topic='Labels';
REPLACE INTO cw_languages SET code='EN', name='lbl_ac_sort_by', value='Sort by', topic='Labels';
REPLACE INTO cw_languages SET code='EN', name='lbl_ac_update_linked_products', value='Update linked products', topic='Labels';
REPLACE INTO cw_languages SET code='EN', name='addon_descr_accessories', value='Shows to customer related products lists.', topic='Addons';
REPLACE INTO cw_languages SET code='EN', name='addon_name_accessories', value='accessories', topic='Addons';
REPLACE INTO cw_languages SET code='EN', name='msg_ac_err_no_linked_products_selected', value='There are no linked products been selected.', topic='Errors';
REPLACE INTO cw_languages SET code='EN', name='option_title_accessories', value='accessories and recommended products', topic='Options';
REPLACE INTO cw_languages SET code='EN', name='opt_accessories_settings', value='accessories list settings', topic='Options';
REPLACE INTO cw_languages SET code='EN', name='opt_ac_acc_display_add_to_cart', value='Display add to cart button in the accessories list', topic='Options';
REPLACE INTO cw_languages SET code='EN', name='opt_ac_acc_display_columns', value='Display accessories in multiple columns (1-4)', topic='Options';
REPLACE INTO cw_languages SET code='EN', name='opt_ac_acc_display_options', value='Display options in the accessories list', topic='Options';
REPLACE INTO cw_languages SET code='EN', name='opt_ac_acc_display_price', value='Display price in the accessories list', topic='Options';
REPLACE INTO cw_languages SET code='EN', name='opt_ac_acc_display_productcode', value='Display SKU in the accessories list', topic='Options';
REPLACE INTO cw_languages SET code='EN', name='opt_ac_acc_display_qty_in_stock', value='Display quantity in stock in the accessories list', topic='Options';
REPLACE INTO cw_languages SET code='EN', name='opt_ac_acc_display_thumbnail', value='Display thumbnail in the accessories list', topic='Options';
REPLACE INTO cw_languages SET code='EN', name='opt_ac_acc_display_weight', value='Display product weight in the accessories list', topic='Options';
REPLACE INTO cw_languages SET code='EN', name='opt_ac_rec_display_add_to_cart', value='Display add to cart button in the recommended products list', topic='Options';
REPLACE INTO cw_languages SET code='EN', name='opt_ac_rec_display_columns', value='Display recommended products in multiple columns (1-4)', topic='Options';
REPLACE INTO cw_languages SET code='EN', name='opt_ac_rec_display_options', value='Display options in the recommended products list', topic='Options';
REPLACE INTO cw_languages SET code='EN', name='opt_ac_rec_display_price', value='Display price in the recommended products list', topic='Options';
REPLACE INTO cw_languages SET code='EN', name='opt_ac_rec_display_productcode', value='Display SKU in the recommended products list', topic='Options';
REPLACE INTO cw_languages SET code='EN', name='opt_ac_rec_display_qty_in_stock', value='Display quantity in stock in the recommended products list', topic='Options';
REPLACE INTO cw_languages SET code='EN', name='opt_ac_rec_display_thumbnail     ', value='Display thumbnail in the recommended products list', topic='Options';
REPLACE INTO cw_languages SET code='EN', name='opt_ac_rec_display_weight', value='Display product weight in the recommended products list', topic='Options';
REPLACE INTO cw_languages SET code='EN', name='opt_ac_rec_products_limit', value='Max number of the recommended products list (1-100)', topic='Options';
REPLACE INTO cw_languages SET code='EN', name='opt_recommended_products_settings', value='Recommended products list settings', topic='Options';
REPLACE INTO cw_languages SET code='EN', name='txt_ac_admin_edit_accessories_top_text', value='Here you can select products, which will be offered to customer on the details page of main product.', topic='Text';
REPLACE INTO cw_languages SET code='EN', name='txt_ac_delete_selected_linked_products_warning', value='Are you sure you want to delete selected links?', topic='Text';
REPLACE INTO cw_languages SET code='EN', name='txt_ac_there_are_no_linked_products', value='There are no linked products.', topic='Text';
REPLACE INTO cw_languages SET code='EN', name='txt_ac_there_are_no_product_accessories', value='There are no product accessories.', topic='Text';
REPLACE INTO cw_languages SET code='EN', name='txt_ac_there_are_no_recommended_products', value='There are no recommended products.', topic='Text';



