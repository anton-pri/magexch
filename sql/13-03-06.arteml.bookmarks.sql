CREATE TABLE IF NOT EXISTS `ars_bookmarks` (
  `customer_id` int(11) NOT NULL,
  `sess_id` char(40) NOT NULL,
  `url` varchar(512) NOT NULL,
  `name` varchar(128) NOT NULL,
  `pos` tinyint(4) NOT NULL,
  KEY `customer_id` (`customer_id`,`sess_id`)
);

INSERT INTO `ars_modules` (
`module` ,
`module_descr` ,
`active` ,
`status` ,
`parent_module` ,
`version` ,
`orderby`
)
VALUES (
'bookmarks', 'Provides bookmarks panel', '1', '1', '', '0.1', '0'
);

