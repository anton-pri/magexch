CREATE TABLE IF NOT EXISTS `cw_doc_history_manufacturers` (
  `doc_id` int(11) NOT NULL DEFAULT '0',
  `manufacturer_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`doc_id`,`manufacturer_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
