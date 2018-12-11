RENAME TABLE `cw_cms_attributes` TO `cw_cms_restrictions` ;
ALTER TABLE `cw_cms_restrictions` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
ALTER TABLE `cw_cms_restrictions` CHANGE `attribute_id` `object_id` INT( 11 ) NOT NULL DEFAULT '0';
ALTER TABLE `cw_cms_restrictions` ADD `object_type` CHAR( 4 ) NOT NULL AFTER `contentsection_id` , ADD INDEX ( `object_type` ) ;
ALTER TABLE cw_cms_restrictions DROP PRIMARY KEY, ADD PRIMARY KEY ( `contentsection_id` , `object_type` , `object_id` , `value_id`, `value` );
UPDATE cw_cms_restrictions SET object_type='A' WHERE object_type='';

INSERT INTO cw_cms_restrictions (contentsection_id, object_type, object_id, operation) SELECT contentsection_id, 'P', product_id, 'eq' FROM cw_cms_products;
INSERT INTO cw_cms_restrictions (contentsection_id, object_type, object_id, operation) SELECT contentsection_id, 'C', category_id, 'eq' FROM cw_cms_categories;
INSERT INTO cw_cms_restrictions (contentsection_id, object_type, object_id, operation) SELECT contentsection_id, 'M', manufacturer_id, 'eq' FROM cw_cms_manufacturers;
INSERT INTO cw_cms_restrictions (contentsection_id, object_type, value, operation) SELECT contentsection_id, 'URL', clean_url, 'eq' FROM cw_cms_clean_urls;

DROP TABLE cw_cms_products, cw_cms_categories, cw_cms_manufacturers, cw_cms_clean_urls;

/*
CREATE TABLE `cw_cms_clean_urls` (
  `contentsection_id` int(11) NOT NULL DEFAULT '0',
  `valid_url` int(1) NOT NULL DEFAULT '0',
  `clean_url` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`contentsection_id`,`clean_url`),
  KEY `clean_url` (`clean_url`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `cw_cms_categories` (
  `contentsection_id` int(11) NOT NULL DEFAULT '0',
  `category_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`contentsection_id`,`category_id`),
  KEY `category_id` (`category_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Структура таблицы `cw_cms_manufacturers`
--

CREATE TABLE IF NOT EXISTS `cw_cms_manufacturers` (
  `contentsection_id` int(11) NOT NULL DEFAULT '0',
  `manufacturer_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`contentsection_id`,`manufacturer_id`),
  KEY `manufacturer_id` (`manufacturer_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Структура таблицы `cw_cms_products`
--

CREATE TABLE IF NOT EXISTS `cw_cms_products` (
  `contentsection_id` int(11) NOT NULL DEFAULT '0',
  `product_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`contentsection_id`,`product_id`),
  KEY `product_id` (`product_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
*/
