ALTER TABLE `cw_clean_urls_history` ADD `attribute_id` INT( 11 ) NOT NULL AFTER `item_type`;
UPDATE cw_clean_urls_history h1 SET h1.attribute_id=(SELECT a.attribute_id FROM cw_attributes a WHERE a.item_type=h1.item_type AND a.module='clean-urls' AND a.field='clean_url');

INSERT INTO `cw_attributes` (`attribute_id`, `name`, `type`, `field`, `is_required`, `active`, `orderby`, `module`, `item_type`, `is_sortable`, `is_comparable`, `is_show`, `pf_is_use`, `pf_orderby`, `pf_display_type`, `is_show_module`) VALUES (NULL, 'Search', 'text', 'clean_url', '0', '1', '0', 'clean-urls', 'Q', '0', '0', '0', '0', '0', '', '0');
SET @attribute_id = LAST_INSERT_ID();
INSERT INTO `cw_attributes_values` (`item_id`, `attribute_id`, `value`, `code`, `item_type`) VALUES ('0', @attribute_id, 'search', 'EN', 'Q');

INSERT INTO `cw_attributes` (`attribute_id`, `name`, `type`, `field`, `is_required`, `active`, `orderby`, `module`, `item_type`, `is_sortable`, `is_comparable`, `is_show`, `pf_is_use`, `pf_orderby`, `pf_display_type`, `is_show_module`) VALUES (NULL, 'Attribute', 'text', 'clean_url', '0', '1', '0', 'clean-urls', 'PA', '0', '0', '0', '0', '0', '', '0');

SELECT @attribute_id:=attribute_id FROM cw_attributes WHERE field='has_review' AND module='EStoreProductsReview';
UPDATE cw_attributes SET type='selectbox' WHERE attribute_id=@attribute_id;
INSERT INTO `cw_attributes_default` (`attribute_value_id`, `value`, `value_key`, `attribute_id`, `is_default`, `orderby`, `active`, `image_id`, `pf_image_id`) VALUES (NULL, 'Yes', '', @attribute_id, '0', '0', '1', '0', '0');
SET @attribute_value_id = LAST_INSERT_ID();
INSERT INTO `cw_attributes_default_lng` (`attribute_value_id`, `code`, `value`) VALUES (@attribute_value_id, 'EN', 'Yes');
UPDATE cw_attributes_values SET value=@attribute_value_id WHERE attribute_id=@attribute_id AND item_type='P';