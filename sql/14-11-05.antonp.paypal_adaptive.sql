CREATE TABLE IF NOT EXISTS `cw_paypal_adaptive_doc_accounts` (
  `doc_id` int(11) NOT NULL DEFAULT '0',
  `email` varchar(128) NOT NULL DEFAULT '',
  PRIMARY KEY (`doc_id`)
) ENGINE=MyISAM;
