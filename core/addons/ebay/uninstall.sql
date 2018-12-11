-- delete from menu
DELETE FROM cw_navigation_menu WHERE title='lbl_ebay_export';

-- delete from addons
DELETE FROM cw_addons WHERE addon='ebay';

-- get config_category_id for cw_config_categories
SELECT @category_id:=config_category_id FROM cw_config_categories WHERE category='ebay';
-- delete from configuration options
DELETE FROM cw_config_categories WHERE config_category_id = @category_id;
DELETE FROM cw_config WHERE config_category_id = @category_id;

-- delete from product attributes
DELETE FROM `cw_attributes_default_lng` WHERE attribute_value_id IN (SELECT attribute_value_id FROM cw_attributes_default
WHERE attribute_id IN (SELECT attribute_id FROM `cw_attributes` WHERE addon='ebay'));
DELETE FROM `cw_attributes_default` WHERE attribute_id IN (SELECT attribute_id FROM `cw_attributes` WHERE addon='ebay');
DELETE FROM `cw_attributes_values` WHERE attribute_id IN (SELECT attribute_id FROM `cw_attributes` WHERE addon='ebay');
DELETE FROM `cw_attributes` WHERE addon='ebay';
