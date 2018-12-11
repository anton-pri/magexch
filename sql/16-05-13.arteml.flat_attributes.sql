-- Addon
REPLACE INTO `cw_addons` (`addon`, `descr`, `active`, `status`, `parent`, `version`, `orderby`) VALUES ('flat_attributes_search', 'This addon helps to speed up search by attributes when DB has 10K and more products. It builds temp flat table for product attributes and optimize search query by reducing joint tables', '0', '1', '', '1', '10');

-- Create necessary tables
CREATE TABLE IF NOT EXISTS `cw_flat_attributes_search` (
  `product_id` int(11) NOT NULL,
    PRIMARY KEY product_id (product_id)
);

-- configuration options
REPLACE INTO `cw_config_categories` (`config_category_id` ,`category` ,`is_local`) VALUES (NULL , 'flat_attributes_search', '0');
SET @config_category_id = LAST_INSERT_ID();
REPLACE INTO cw_config SET name='flat_attributes', comment='Attributes IDs for flatten table', value='', config_category_id=@config_category_id, orderby='0', type='', defvalue='', variants='';
