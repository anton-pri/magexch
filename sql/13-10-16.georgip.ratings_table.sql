CREATE TABLE IF NOT EXISTS `cw_products_reviews_ratings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `review_id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `rate` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `review_id_customer_id` (`review_id`,`customer_id`)
) ENGINE=MyISAM;