INSERT INTO `cw_languages` (`code`, `name`, `value`, `tooltip`, `topic`) VALUES ('EN', 'lbl_pfdp_predefined_ranges', 'Predefined ranges', '', 'Labels');
INSERT INTO `cw_attributes` (`attribute_id`, `name`, `type`, `field`, `is_required`, `active`, `orderby`, `addon`, `item_type`, `is_sortable`, `is_comparable`, `is_show`, `pf_is_use`, `pf_orderby`, `pf_display_type`, `is_show_addon`) VALUES (NULL, 'Predefined ranges', 'selectbox', 'predefined_ranges', '0', '1', '0', 'core', 'P', '0', '0', '1', '1', '0', 'P', '0');
SET @attribute_id = LAST_INSERT_ID();

INSERT INTO `cw_attributes_default` (`attribute_value_id`, `value`, `value_key`, `attribute_id`, `is_default`, `orderby`, `active`, `image_id`, `pf_image_id`) VALUES (NULL, 'Low costed', '0-19.99', @attribute_id, '0', '10', '1', '0', '0');
SET @attribute_value_id =LAST_INSERT_ID();
INSERT INTO `cw_attributes_values` (`item_id`, `attribute_id`, `value`, `code`, `item_type`) VALUES (@attribute_value_id, @attribute_id, 'Predefined-ranges-low-costed', 'EN', 'PA');

INSERT INTO `cw_attributes_default` (`attribute_value_id`, `value`, `value_key`, `attribute_id`, `is_default`, `orderby`, `active`, `image_id`, `pf_image_id`) VALUES (NULL, 'Budget', '20-99.99', @attribute_id, '0', '20', '1', '0', '0');
SET @attribute_value_id =LAST_INSERT_ID();
INSERT INTO `cw_attributes_values` (`item_id`, `attribute_id`, `value`, `code`, `item_type`) VALUES (@attribute_value_id, @attribute_id, 'Predefined-ranges-budget', 'EN', 'PA');

INSERT INTO `cw_attributes_default` (`attribute_value_id`, `value`, `value_key`, `attribute_id`, `is_default`, `orderby`, `active`, `image_id`, `pf_image_id`) VALUES (NULL, 'Luxury', '100-1000', @attribute_id, '0', '30', '1', '0', '0');
SET @attribute_value_id =LAST_INSERT_ID();
INSERT INTO `cw_attributes_values` (`item_id`, `attribute_id`, `value`, `code`, `item_type`) VALUES (@attribute_value_id, @attribute_id, 'Predefined-ranges-luxury', 'EN', 'PA');