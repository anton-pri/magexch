ALTER TABLE `cw_products_reviews_ratings` ADD `remote_ip` VARCHAR( 47 ) NOT NULL AFTER `customer_id` , ADD `sess_id` CHAR( 40 ) NOT NULL AFTER `remote_ip` ;
