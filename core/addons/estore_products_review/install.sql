-- new addon record
REPLACE INTO `cw_addons` (`addon`, `descr`, `active`, `status`, `parent`,`version`)
VALUES ('estore_products_review', 'Products rating', 1, 1, '','0.1');


-- new product attributes
DELETE FROM `cw_attributes_default_lng` WHERE attribute_value_id IN (SELECT attribute_value_id FROM cw_attributes_default
WHERE attribute_id IN (SELECT attribute_id FROM `cw_attributes` WHERE addon='estore_products_review'));
DELETE FROM `cw_attributes_default` WHERE attribute_id IN (SELECT attribute_id FROM `cw_attributes` WHERE addon='estore_products_review');
DELETE FROM `cw_attributes_values` WHERE attribute_id IN (SELECT attribute_id FROM `cw_attributes` WHERE addon='estore_products_review');
DELETE FROM `cw_attributes` WHERE addon='estore_products_review';

INSERT INTO `cw_attributes` (`attribute_id`, `name`, `type`, `field`, `is_required`, `active`, `orderby`, `addon`, `item_type`, `is_sortable`, `is_comparable`, `is_show`, `pf_is_use`, `pf_orderby`, `pf_display_type`, `is_show_addon`) VALUES
(null, 'Product rating', 'decimal', 'rating', 0, 1, 100, 'estore_products_review', 'P', 0, 0, 1, 1, 0, 'S', 0);
