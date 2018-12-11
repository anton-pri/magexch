CREATE TABLE IF NOT EXISTS `xfer_temp` (
  `catalogid` int(11) NOT NULL,
  `xref` varchar(255) DEFAULT NULL,
  `cost` decimal(19,4) DEFAULT NULL,
  `cstock` int(11) DEFAULT NULL,
  KEY `catalogid` (`catalogid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
