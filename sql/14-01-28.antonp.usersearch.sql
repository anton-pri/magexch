CREATE TABLE IF NOT EXISTS `cw_doc_history_attributes` (
  `doc_id` int(11) NOT NULL DEFAULT '0',
  `attribute_id` int(11) NOT NULL DEFAULT '0',
  `attribute_name` varchar(128) NOT NULL DEFAULT '',
  `value` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`doc_id`,`attribute_id`,`value`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `cw_doc_history_categories` (
  `doc_id` int(11) NOT NULL DEFAULT '0',
  `category_id` int(11) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS`cw_customers_docs_stats_processed_docs` (
  `doc_id` int(11) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `cw_customers_docs_stats` (
  `customer_id` int(11) NOT NULL DEFAULT '0',
  `avg_subtotal` decimal(12,4) NOT NULL DEFAULT '0.0000',
  `total_spent` decimal(12,4) NOT NULL DEFAULT '0.0000',
  `orders_count` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`customer_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `cw_saved_search` (
  `ss_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(128) NOT NULL DEFAULT '',
  `type` char(1) NOT NULL DEFAULT '',
  `sql_query` text NOT NULL,
  `params` text NOT NULL,
  PRIMARY KEY (`ss_id`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=latin1;
