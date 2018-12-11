-- Change default language to en
ALTER TABLE `cw_attributes_lng` CHANGE `code` `code` CHAR( 2 ) NOT NULL DEFAULT 'en';

-- New product type
REPLACE INTO `cw_products_types` ( `product_type_id` , `title`) VALUES ( 4 , 'Catalog');

-- New addon
REPLACE INTO `cw_addons` (`addon` ,`descr` ,`active` ,`status` , `parent` , `version` , `orderby`) VALUES ( 'catalog_product', 'New product type "catalog" which is sold on other sites', '1', '0', '', '0.1', '0');

-- Langvars for addon

-- New attribute
INSERT INTO `cw_attributes` ( `attribute_id` , `name` , `type` , `field` , `is_required` , `active` , `orderby` , `addon` , `item_type` , `is_sortable` , `is_comparable` , `is_show` , `pf_is_use` , `pf_orderby` , `pf_display_type` , `is_show_addon`) VALUES ( NULL , 'original_url', 'text', 'original_url', '0', '1', '0', 'catalog_product', 'P', '0', '0', '0', '0', '0', '', '1');
SET @att_id = LAST_INSERT_ID();
INSERT INTO `cw_attributes_lng` ( `attribute_id` , `code` , `name`) VALUES ( @att_id, 'en', 'Original URL');

REPLACE INTO `cw_languages` (
`code` ,
`name` ,
`value` ,
`topic`
)
VALUES (
'en', 'addon_name_catalog_product', 'Catalog Product', 'Addons'
);

REPLACE INTO `cw_languages` (
`code` ,
`name` ,
`value` ,
`topic`
)
VALUES (
'en', 'lbl_catalog_product_button', 'Visit site', 'Labels'
);
REPLACE INTO `cw_languages` (
`code` ,
`name` ,
`value` ,
`topic`
)
VALUES (
'en', 'err_field_catalog_products', 'Original URL is required for Catalog product', 'Errors'
);
