-- Addon
REPLACE INTO `cw_addons` (`addon`, `descr`, `active`, `status`, `parent`, `version`, `orderby`) VALUES ('product_shipping_options', 'Custom product shipping options', 1, 1, 'custom_industrialdepot', '0.1', 0);

-- configuration options
REPLACE INTO `cw_config_categories` (`config_category_id` ,`category` ,`is_local`) VALUES (NULL , 'product_shipping_options', '0');
SET @config_category_id = LAST_INSERT_ID();

insert into cw_config set name='dummy_shipping_method', config_category_id=@config_category_id, type='singleshipping', comment='Dummy shipping method for display as common shipping name in cart totals';

REPLACE INTO cw_languages SET code='EN', name='addon_descr_product_shipping_options', value='Custom product shipping options', topic='Addons';
REPLACE INTO cw_languages SET code='EN', name='addon_name_product_shipping_options', value='Product Shipping Options', topic='Addons';
REPLACE INTO cw_languages SET code='EN', name='option_title_product_shipping_options', value='Product Shipping Addon options', topic='Options';

create table if not exists cw_product_shipping_options_values (product_id int(11) not null default 0, shipping_id int(11) not null default 0, price decimal(12,2) not null default 0.00, primary key product_id_shipping_id (product_id, shipping_id))TYPE=MyISAM;

replace into cw_languages set name='lbl_product_shipping_option_invoice', topic='Label', code='EN', value='Shipping';
