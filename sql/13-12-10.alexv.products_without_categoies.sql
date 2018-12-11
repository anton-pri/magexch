INSERT INTO `cw_languages` (`code`, `name`, `value`, `tooltip`, `topic`) VALUES ('EN', 'lbl_do_not_use_categories', 'Do not use categories', '', 'Labels');
INSERT INTO `cw_languages` (`code`, `name`, `value`, `tooltip`, `topic`) VALUES ('EN', 'lbl_use_categories', 'Use categories', '', 'Labels');

SELECT @config_category_id:=config_category_id FROM cw_config_categories WHERE category='Appearance' AND is_local=0;

INSERT INTO `cw_config` (`name`, `comment`, `value`, `config_category_id`, `orderby`, `type`, `defvalue`, `variants`) 
VALUES ('categories_in_products', 'Use categories in products', '1', @config_category_id, '460', 'selector', '1', '0:lbl_do_not_use_categories\n1:lbl_use_categories');

INSERT INTO `cw_categories` (`category_id`, `parent_id`, `category`, `description`, `order_by`, `threshold_bestsellers`, `featured`, `short_list`, `tm_title`, `tm_pos`, `tm_active`, `status`) VALUES ('1', '0', 'Root', 'Root', '0', '1', '0', '0', '', '0', '1', '1');
INSERT INTO `cw_categories_lng` (`code`, `category_id`, `category`, `description`) VALUES ('EN', '1', 'Root', 'Root');
INSERT INTO `cw_categories_memberships` (`category_id`, `membership_id`) VALUES ('1', '0');
INSERT INTO `cw_categories_parents` (`category_id`, `parent_id`, `level`) VALUES ('1', '1', '0');
SELECT @attribute_id:=`attribute_id` FROM `cw_attributes` WHERE `field` = 'domains' AND `active` = 1 AND `addon` = 'multi_domains' AND `item_type` = 'C';
INSERT INTO `cw_attributes_values` (`item_id`, `attribute_id`, `value`, `code`, `item_type`) VALUES ('1', @attribute_id, '0', 'EN', 'C');