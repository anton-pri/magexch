ALTER TABLE `cw_products` ADD `cost` DECIMAL( 12, 2 ) NOT NULL;
INSERT INTO `cw_languages` (`code`, `name`, `value`, `topic`) VALUES ('EN', 'lbl_cost_surcharge', 'Cost surcharge', 'Label');
ALTER TABLE `cw_product_options_values` ADD `cost_modifier` DECIMAL( 12, 4 ) NOT NULL;
ALTER TABLE `cw_product_options_values` ADD `cost_modifier_type` INT( 1 ) NOT NULL;

SELECT @config_category_id:=config_category_id FROM cw_config_categories WHERE category='General' AND is_main=0;
REPLACE INTO cw_config SET name='include_shipping_in_margin_calc', comment='Include shipping cost in margin calculation', value='Y', config_category_id = @config_category_id, orderby='640', type='checkbox', defvalue='Y', variants='';