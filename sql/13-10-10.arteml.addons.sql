-- Bad addons
update cw_addons set status=2 where addon IN ('barcode','faq','froogle','google_checkout','salesman','special_offers','shipping_label_generator','estore_category_tree','pos','sn','survey');

-- Dev addons
update cw_addons set status=1 where addon IN ('seller','printdrop_com','interneka','egoods','wordpress','promotion_suite','now_online','magnifier','catalog_product');

-- System addons
update cw_addons set status=-1 where addon IN ('manufacturers','mobile','multi_domains','product_options','addons_manager','breadcrumbs','dashboard','demo_module');



DROP TABLE IF EXISTS cw_ws_errors, cw_ws_products, cw_user_contracts;
DELETE FROM `cw_sections_pos` WHERE `cw_sections_pos`.`section` = 'pconf_items';

/*
CREATE TABLE IF NOT EXISTS `cw_ws_errors` (
  `err_id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(32) NOT NULL DEFAULT '',
  `date` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`err_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `cw_ws_products` (
  `product_id` int(11) NOT NULL DEFAULT '0',
  `ws_item_id` varchar(32) NOT NULL DEFAULT '',
  `status` int(2) NOT NULL DEFAULT '0',
  PRIMARY KEY (`product_id`,`ws_item_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE `cw_user_contracts` (
  `contract_id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(32) NOT NULL DEFAULT '0',
  `content` mediumtext NOT NULL,
  PRIMARY KEY (`contract_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

*/
