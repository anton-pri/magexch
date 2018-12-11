CREATE TABLE IF NOT EXISTS `pos_cost_temp` (
  `ID` int(11) NOT NULL DEFAULT '0',
  `cost` decimal(19,4) DEFAULT '0.0000',
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `item_price_twelve_bottle` (
  `store_id` int(11) NOT NULL DEFAULT '0',
  `item_id` int(11) NOT NULL DEFAULT '0',
  `price` decimal(19,2) DEFAULT '0.00',
  `cost` decimal(19,2) NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`store_id`,`item_id`),
  KEY `store_id` (`store_id`),
  KEY `item_id` (`item_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
