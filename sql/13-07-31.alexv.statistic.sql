SELECT @config_category_id:=config_category_id FROM cw_config_categories WHERE category='Appearance' AND is_main=0;

INSERT INTO `cw_config` (`name`, `comment`, `value`, `config_category_id`, `orderby`, `type`, `defvalue`, `variants`) 
VALUES ('show_views_on_product_page', 'Show number of views on product(s) pages', 'N', @config_category_id, '170', 'checkbox', 'N', '');

ALTER TABLE `cw_products_stats` ADD `add_to_cart` INT( 11 ) NOT NULL DEFAULT '0';

INSERT INTO `cw_languages` (`code`, `name`, `value`, `topic`) VALUES ('EN', 'lbl_number_of_views', 'Number of views', 'Labels');
INSERT INTO `cw_languages` (`code`, `name`, `value`, `topic`) VALUES ('EN', 'lbl_purchases', 'Purchases', 'Labels');
INSERT INTO `cw_languages` (`code`, `name`, `value`, `topic`) VALUES ('EN', 'lbl_deletions_from_cart', 'Deletions from cart', 'Labels');
INSERT INTO `cw_languages` (`code`, `name`, `value`, `topic`) VALUES ('EN', 'lbl_additions_to_cart', 'Additions to cart', 'Labels');
