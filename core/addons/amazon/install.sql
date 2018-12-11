-- get menu_id for Sections
SELECT @sections:=menu_id FROM cw_navigation_menu WHERE title='lbl_sections' AND parent_menu_id=0 AND area='A' LIMIT 1;
-- insert new entry to menu
DELETE FROM cw_navigation_menu WHERE title='lbl_amazon_export';
INSERT INTO `cw_navigation_menu` (`menu_id`, `parent_menu_id`, `title`, `link`, `orderby`, `area`, `access_level`, `addon`, `is_loggedin`) VALUES
(NULL, @sections, 'lbl_amazon_export', 'index.php?target=amazon_export', 100, 'A', '', 'amazon', 1);

-- lang var
REPLACE INTO `cw_languages` (`code`, `name`, `value`, `topic`) VALUES ('EN', 'lbl_amazon_export', 'amazon Export', 'Labels');
REPLACE INTO `cw_languages` (`code` , `name` , `value` , `topic`)
VALUES
('EN', 'txt_amazon_export_note', '<p>txt_amazon_export_note</p>', 'Text');

-- new addon record
REPLACE INTO `cw_addons` (`addon`, `descr`, `active`, `status`, `parent`,`version`)
VALUES ('amazon', 'amazon Export', 1, 1, '','0.1');

-- configuration options
DELETE FROM cw_config_categories WHERE category='amazon';
INSERT INTO `cw_config_categories` (`config_category_id` ,`category` ,`is_main`) VALUES (NULL , 'amazon', '0');
SET @config_category_id = LAST_INSERT_ID();

REPLACE INTO cw_config SET name='product_id_type', comment='An industry standard product identifier', value='4', config_category_id = @config_category_id, orderby='10', type='selector', defvalue='4', variants='1:ASIN\n2:ISBN\n3:UPC\n4:EAN';
REPLACE INTO cw_config SET name='product_id', comment='Field/attribute where standard product identifier is stored', value='ean', config_category_id = @config_category_id, orderby='20', type='text', defvalue='ean', variants='';

REPLACE INTO cw_config SET name='item_condition', comment='Field/attribute where Item condition is stored', value='item-condition', config_category_id = @config_category_id, orderby='50', type='text', defvalue='item-condition', variants='';
REPLACE INTO cw_config SET name='default_item_condition', comment='Default Item condition', value='11', config_category_id = @config_category_id, orderby='60', type='selector', defvalue='11', variants='1:Used; Like New\n2:Used; Very Good\n3:Used; Good\n4:Used; Acceptable\n5:Collectible; Like New\n6:Collectible; Very Good\n7:Collectible; Good\n8:Collectible; Acceptable\n9:Not used\n10:Refurbished (for computers, kitchen & housewares, electronics, and camera & photo only)\n11:New';


REPLACE INTO cw_config SET name='ship_internationally', comment='Field/attribute where "Ship internationally" flag is stored', value='', config_category_id = @config_category_id, orderby='100', type='text', defvalue='', variants='';
REPLACE INTO cw_config SET name='default_ship_internationally', comment='Default "Ship internationally" value', value='0', config_category_id = @config_category_id, orderby='110', type='selector', defvalue='0', variants='0:Not specified\nY:Yes\nN:No';


REPLACE INTO cw_config SET name='expedited_shipping', comment='Field/attribute where Expedited shipping flag or methods are stored', value='', config_category_id = @config_category_id, orderby='150', type='text', defvalue='', variants='';
REPLACE INTO cw_config SET name='default_expedited_shipping', comment='Default Expedited shipping flag or methods', value='', config_category_id = @config_category_id, orderby='160', type='text', defvalue='', variants='';

REPLACE INTO cw_config SET name='standard_plus', comment='Field/attribute where "standard plus" flag is stored', value='', config_category_id = @config_category_id, orderby='180', type='text', defvalue='', variants='';
REPLACE INTO cw_config SET name='default_standard_plus', comment='Default "standard plus" value', value='0', config_category_id = @config_category_id, orderby='190', type='selector', defvalue='0', variants='0:Not specified\nY:Yes\nN:No';

REPLACE INTO cw_config SET name='item_note', comment='Field/attribute where Item note is stored', value='descr', config_category_id = @config_category_id, orderby='200', type='text', defvalue='descr', variants='';

REPLACE INTO cw_config SET name='fulfillment_center_id', comment='Fullfillment center ID', value='', config_category_id = @config_category_id, orderby='250', type='text', defvalue='', variants='';

REPLACE INTO cw_config SET name='default_product_tax_code', comment="amazon.com's standard tax code", value='', config_category_id = @config_category_id, orderby='300', type='text', defvalue='', variants='';

REPLACE INTO cw_config SET name='default_leadtime_to_ship', comment='Leadtime to ship', value='', config_category_id = @config_category_id, orderby='400', type='text', defvalue='', variants='';

-- new product attributes
DELETE FROM `cw_attributes_default_lng` WHERE attribute_value_id IN (SELECT attribute_value_id FROM cw_attributes_default
WHERE attribute_id IN (SELECT attribute_id FROM `cw_attributes` WHERE addon='amazon'));
DELETE FROM `cw_attributes_default` WHERE attribute_id IN (SELECT attribute_id FROM `cw_attributes` WHERE addon='amazon');
DELETE FROM `cw_attributes_values` WHERE attribute_id IN (SELECT attribute_id FROM `cw_attributes` WHERE addon='amazon');
DELETE FROM `cw_attributes` WHERE addon='amazon';


INSERT INTO `cw_attributes`
(`attribute_id`, `name`, `type`, `field`, `is_required`, `active`, `orderby`, `addon`, `item_type`, `is_sortable`, `is_comparable`, `is_show`) VALUES
(NULL, "amazon's Item condition", 'selectbox', 'item-condition', 0, '1', 200, 'amazon', 'P', 0, 0, 0);
SET @attribute_id = LAST_INSERT_ID();

INSERT INTO cw_attributes_default (`value`, 	`value_key`, 	`attribute_id`, 	`active`) VALUES
('Used; Like New','1',@attribute_id,1),
('Used; Very Good','2',@attribute_id,1),
('Used; Good','3',@attribute_id,1),
('Used; Acceptable','4',@attribute_id,1),
('Collectible; Like New','5',@attribute_id,1),
('Collectible; Very Good','6',@attribute_id,1),
('Collectible; Good','7',@attribute_id,1),
('Collectible; Acceptable','8',@attribute_id,1),
('Not used','9',@attribute_id,1),
('Refurbished (for computers, kitchen & housewares, electronics, and camera & photo only)','10',@attribute_id,1),
('New','11',@attribute_id,1);

