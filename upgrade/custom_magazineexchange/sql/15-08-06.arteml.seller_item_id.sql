ALTER TABLE cw_magazine_sellers_product_data DROP PRIMARY KEY;
ALTER TABLE `cw_magazine_sellers_product_data` ADD `seller_item_id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST ;
ALTER TABLE `cw_magazine_sellers_product_data` ADD INDEX product_id(product_id);
ALTER TABLE `cw_magazine_sellers_product_data` ADD INDEX seller_id(seller_id);
