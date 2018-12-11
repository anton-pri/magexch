CREATE TABLE IF NOT EXISTS `cw_items_attribute_classes` (
  `item_id` int(11) unsigned NOT NULL default 0,
  `attribute_class_id` int(11) unsigned NOT NULL,
  `item_type` char(2) NOT NULL default '',
  PRIMARY KEY (`item_id`, `attribute_class_id`, `item_type`),
  INDEX  `cwpac_pid` ( `item_id` ),
  INDEX  `cwpac_cid` ( `attribute_class_id`)
);

REPLACE INTO `cw_items_attribute_classes` (item_id, attribute_class_id, item_type) SELECT product_id, attribute_class_id, 'P' FROM cw_products; 
