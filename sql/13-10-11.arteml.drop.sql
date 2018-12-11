ALTER TABLE cw_tags DROP INDEX tag_id;
ALTER TABLE `cw_tags` ADD INDEX ( `product_count` );
ALTER TABLE `cw_tags_products` ADD INDEX ( `tag_id` );
ALTER TABLE `cw_tags_products` ADD INDEX ( `product_id` );
ALTER TABLE `cw_attributes_values` ADD INDEX ( `item_type` );
ALTER TABLE cw_attributes_values DROP INDEX ia;
ALTER TABLE `cw_attributes_values` ADD INDEX ( `item_type` );
ALTER TABLE `cw_attributes` ADD INDEX ( `addon` );
ALTER TABLE `cw_attributes` ADD INDEX ( `item_type` );

DROP TABLE IF EXISTS cw_products_profits;
/*
CREATE TABLE `cw_products_profits` (
  `price_id` int(11) NOT NULL DEFAULT '0',
  `price_list_id` int(11) NOT NULL DEFAULT '0',
  `quantity` int(11) NOT NULL DEFAULT '0',
  `profit` decimal(12,4) NOT NULL DEFAULT '0.0000',
  `list_price` decimal(12,4) NOT NULL DEFAULT '0.0000',
  `discount` decimal(12,4) NOT NULL DEFAULT '0.0000',
  `discount_type` int(2) NOT NULL DEFAULT '0',
  PRIMARY KEY (`price_id`,`price_list_id`,`quantity`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
*/

DROP TABLE IF EXISTS cw_quick_flags; 
/*
CREATE TABLE `cw_quick_flags` (
  `product_id` int(11) NOT NULL DEFAULT '0',
  `is_variants` char(1) NOT NULL DEFAULT '',
  `is_product_options` char(1) NOT NULL DEFAULT '',
  `image_path_T` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`product_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
*/

DROP TABLE IF EXISTS cw_products_search_groups, cw_products_search_groups_lng, cw_products_search_items, cw_products_search_items_lng;
/*
-- Структура таблицы `cw_products_search_groups`
CREATE TABLE IF NOT EXISTS `cw_products_search_groups` (
  `search_group_id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL DEFAULT '',
  `avail` int(1) NOT NULL DEFAULT '0',
  `orderby` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`search_group_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- Структура таблицы `cw_products_search_groups_lng`
CREATE TABLE IF NOT EXISTS `cw_products_search_groups_lng` (
  `search_group_id` int(11) NOT NULL DEFAULT '0',
  `code` varchar(2) NOT NULL DEFAULT '',
  `title` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`search_group_id`,`code`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- Структура таблицы `cw_products_search_items`
CREATE TABLE IF NOT EXISTS `cw_products_search_items` (
  `search_item_id` int(11) NOT NULL AUTO_INCREMENT,
  `search_group_id` int(11) NOT NULL DEFAULT '0',
  `title` varchar(255) NOT NULL DEFAULT '',
  `avail` int(1) NOT NULL DEFAULT '0',
  `orderby` int(11) NOT NULL DEFAULT '0',
  `data` mediumtext NOT NULL,
  PRIMARY KEY (`search_item_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Структура таблицы `cw_products_search_items_lng`
--

CREATE TABLE IF NOT EXISTS `cw_products_search_items_lng` (
  `search_item_id` int(11) NOT NULL DEFAULT '0',
  `code` varchar(2) NOT NULL DEFAULT '',
  `title` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`search_item_id`,`code`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
*/

DROP TABLE IF EXISTS cw_products_suppliers;
/*
CREATE TABLE `cw_products_suppliers` (
  `supplier_customer_id` int(11) NOT NULL DEFAULT '0',
  `product_id` int(11) NOT NULL DEFAULT '0',
  `price` decimal(12,2) NOT NULL DEFAULT '0.00',
  `productcode` varchar(32) COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `date` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`supplier_customer_id`,`product_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;
*/

DROP TABLE IF EXISTS `cw_rma_actions`;
DROP TABLE IF EXISTS `cw_rma_coupons`;
DROP TABLE IF EXISTS `cw_rma_reasons`;
DELETE FROM `cw_languages` WHERE `name` LIKE '%\_rma\_%';
DELETE FROM `cw_languages` WHERE `name` LIKE '%\_rma';
DELETE FROM `cw_languages` WHERE `name`='lbl_returns';
/*
-- Структура таблицы `cw_rma_actions`
DROP TABLE IF EXISTS `cw_rma_actions`;
CREATE TABLE IF NOT EXISTS `cw_rma_actions` (
  `action_id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`action_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- Структура таблицы `cw_rma_coupons`
DROP TABLE IF EXISTS `cw_rma_coupons`;
CREATE TABLE IF NOT EXISTS `cw_rma_coupons` (
  `cid` varchar(32) NOT NULL DEFAULT '',
  `amount` decimal(12,4) NOT NULL DEFAULT '0.0000',
  `spend` decimal(12,4) NOT NULL DEFAULT '0.0000',
  `one_time_use` int(11) NOT NULL DEFAULT '0',
  `item_id` int(11) NOT NULL DEFAULT '0',
  `weight` decimal(12,4) NOT NULL DEFAULT '0.0000',
  `order_id` int(11) NOT NULL DEFAULT '0',
  `customer_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`cid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Структура таблицы `cw_rma_reasons`
DROP TABLE IF EXISTS `cw_rma_reasons`;
CREATE TABLE IF NOT EXISTS `cw_rma_reasons` (
  `reason_id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`reason_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
*/


