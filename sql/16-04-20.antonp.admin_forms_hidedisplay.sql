CREATE TABLE IF NOT EXISTS `cw_admin_forms_hidedisplay` (
  `customer_id` int(11) NOT NULL DEFAULT '0',
  `target` varchar(32) NOT NULL DEFAULT '',
  `mode` varchar(32) NOT NULL DEFAULT '',
  `element_name` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`customer_id`,`target`,`mode`,`element_name`)
) DEFAULT CHARSET=latin1;
