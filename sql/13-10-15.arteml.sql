drop table cw_docs_items_rma;
/*
CREATE TABLE `cw_docs_items_rma` (
  `item_id` int(11) NOT NULL DEFAULT '0',
  `return_reason` int(11) NOT NULL DEFAULT '0',
  `return_action` int(11) NOT NULL DEFAULT '0',
  `return_comment` mediumtext NOT NULL,
  `serial_number` varchar(32) NOT NULL DEFAULT '',
  `purchase_date` int(11) NOT NULL DEFAULT '0',
  `status` char(1) NOT NULL DEFAULT '',
  `warranty` char(1) NOT NULL DEFAULT '',
  PRIMARY KEY (`item_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
*/
INSERT INTO `cw_languages` (
`code` ,
`name` ,
`value` ,
`tooltip` ,
`topic`
)
VALUES (
'EN', 'msg_product_deleted_from_cart', 'Product has been deleted from cart', '', 'Text'
);
