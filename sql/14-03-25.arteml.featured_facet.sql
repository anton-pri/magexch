CREATE TABLE `cw_manufacturers_categories` (
  `manufacturer_id` int(11) NOT NULL,
  `url_id` int(11) NOT NULL,
  `pos` smallint(6) NOT NULL,
  PRIMARY KEY (`manufacturer_id`,`url_id`)
);
