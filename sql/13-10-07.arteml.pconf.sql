DROP TABLE IF EXISTS `cw_pconf_class_requirements`, `cw_pconf_class_specifications`, `cw_pconf_products_add`, `cw_pconf_products_classes`, `cw_pconf_product_types`, `cw_pconf_slots`, `cw_pconf_slot_markups`, `cw_pconf_slot_rules`, `cw_pconf_specifications`, `cw_pconf_wizards`;

update cw_languages set name='lbl_all_products' where name='lbl_pconf_search_allproducts';
delete FROM `cw_languages` WHERE `name` LIKE '%pconf%';

DELETE FROM `cw_products_types` WHERE `cw_products_types`.`product_type_id` = 3;

/*
-- --------------------------------------------------------

--
-- Структура таблицы `cw_pconf_class_requirements`
--

CREATE TABLE IF NOT EXISTS `cw_pconf_class_requirements` (
  `class_id` int(11) NOT NULL DEFAULT '0',
  `ptype_id` int(11) NOT NULL DEFAULT '0',
  `specid` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`class_id`,`ptype_id`,`specid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `cw_pconf_class_specifications`
--

CREATE TABLE IF NOT EXISTS `cw_pconf_class_specifications` (
  `class_id` int(11) NOT NULL DEFAULT '0',
  `specid` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`class_id`,`specid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `cw_pconf_products_add`
--

CREATE TABLE IF NOT EXISTS `cw_pconf_products_add` (
  `product_id` int(11) NOT NULL DEFAULT '0',
  `variantid` int(11) NOT NULL DEFAULT '0',
  `slotid` int(11) NOT NULL DEFAULT '0',
  `suboption` char(1) NOT NULL DEFAULT 'N',
  `selection` char(1) NOT NULL DEFAULT '',
  `recommend` char(1) NOT NULL DEFAULT '',
  `multiplicator` char(1) NOT NULL DEFAULT '',
  `def` char(1) NOT NULL DEFAULT '',
  `hidden` char(1) NOT NULL DEFAULT '',
  `highlight` char(1) NOT NULL DEFAULT '',
  `non_modify` char(1) NOT NULL DEFAULT '',
  `orderby` int(11) NOT NULL DEFAULT '0',
  `comment` mediumtext NOT NULL,
  `main_product_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`product_id`,`variantid`,`slotid`,`main_product_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `cw_pconf_products_classes`
--

CREATE TABLE IF NOT EXISTS `cw_pconf_products_classes` (
  `class_id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) NOT NULL DEFAULT '0',
  `ptype_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`class_id`),
  UNIQUE KEY `product_type` (`product_id`,`ptype_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `cw_pconf_product_types`
--

CREATE TABLE IF NOT EXISTS `cw_pconf_product_types` (
  `ptype_id` int(11) NOT NULL AUTO_INCREMENT,
  `warehouse_customer_id` int(11) NOT NULL DEFAULT '0',
  `ptype_name` varchar(255) NOT NULL DEFAULT '',
  `orderby` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`ptype_id`),
  UNIQUE KEY `warehouse_customer_id` (`warehouse_customer_id`,`ptype_name`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `cw_pconf_slots`
--

CREATE TABLE IF NOT EXISTS `cw_pconf_slots` (
  `slotid` int(11) NOT NULL AUTO_INCREMENT,
  `stepid` int(11) NOT NULL DEFAULT '0',
  `slot_name` varchar(255) NOT NULL DEFAULT '',
  `slot_descr` mediumtext NOT NULL,
  `status` char(1) NOT NULL DEFAULT 'O',
  `orderby` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`slotid`),
  KEY `product` (`stepid`,`orderby`,`slotid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `cw_pconf_slot_markups`
--

CREATE TABLE IF NOT EXISTS `cw_pconf_slot_markups` (
  `markupid` int(11) NOT NULL AUTO_INCREMENT,
  `slotid` int(11) NOT NULL DEFAULT '0',
  `markup` decimal(12,4) NOT NULL DEFAULT '0.0000',
  `markup_type` char(1) NOT NULL DEFAULT '%',
  `membership_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`markupid`),
  UNIQUE KEY `slotid` (`slotid`,`membership_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `cw_pconf_slot_rules`
--

CREATE TABLE IF NOT EXISTS `cw_pconf_slot_rules` (
  `slotid` int(11) NOT NULL DEFAULT '0',
  `ptype_id` int(11) NOT NULL DEFAULT '0',
  `index_by_and` int(11) NOT NULL DEFAULT '0',
  KEY `slotid` (`slotid`),
  KEY `ptype_id` (`ptype_id`),
  KEY `index_by_and` (`index_by_and`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `cw_pconf_specifications`
--

CREATE TABLE IF NOT EXISTS `cw_pconf_specifications` (
  `specid` int(11) NOT NULL AUTO_INCREMENT,
  `ptype_id` int(11) NOT NULL DEFAULT '0',
  `spec_name` varchar(255) NOT NULL DEFAULT '',
  `orderby` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`specid`),
  UNIQUE KEY `name` (`ptype_id`,`spec_name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `cw_pconf_wizards`
--

CREATE TABLE IF NOT EXISTS `cw_pconf_wizards` (
  `stepid` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) NOT NULL DEFAULT '0',
  `step_name` varchar(255) NOT NULL DEFAULT '',
  `step_descr` mediumtext NOT NULL,
  `orderby` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`stepid`),
  KEY `product` (`product_id`,`orderby`,`stepid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;
*/
