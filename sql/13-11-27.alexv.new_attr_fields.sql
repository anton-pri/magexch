INSERT INTO `cw_attributes` (`attribute_id`, `name`, `type`, `field`, `is_required`, `active`, `orderby`, `addon`, `item_type`, `is_sortable`, `is_comparable`, `is_show`, `pf_is_use`, `pf_orderby`, `pf_display_type`, `is_show_addon`) VALUES (NULL, 'Attributes values', 'text', 'clean_url', '0', '1', '0', 'clean_urls', 'AV', '0', '0', '0', '0', '0', '', '0');
SET @attribute_id = LAST_INSERT_ID();
SELECT @addon_attribute_id:=attribute_id FROM `cw_attributes` WHERE `field` = 'clean_url' AND `addon` = 'clean_urls' AND `item_type` = 'PA' LIMIT 1;
CREATE TEMPORARY TABLE IF NOT EXISTS temp_attr_table AS (SELECT `item_id` FROM `cw_attributes_values` WHERE `attribute_id`=@addon_attribute_id AND `item_type` = 'PA');
UPDATE `cw_attributes_values` SET `attribute_id` = @attribute_id, `item_type` = 'AV'
WHERE `attribute_id` IN (SELECT `item_id` FROM `temp_attr_table`) 
AND `item_type` = 'PA';

ALTER TABLE `cw_attributes` ADD `facet` TINYINT( 1 ) NOT NULL DEFAULT '0';
ALTER TABLE `cw_attributes` ADD `description` TEXT NOT NULL;
ALTER TABLE `cw_attributes_default` ADD `facet` TINYINT( 1 ) NOT NULL DEFAULT '0';
ALTER TABLE `cw_attributes_default` ADD `description` TEXT NOT NULL;

INSERT INTO `cw_languages` (`code`, `name`, `value`, `tooltip`, `topic`) VALUES ('EN', 'lbl_facet', 'Facet', '', 'Labels');
INSERT INTO `cw_languages` (`code`, `name`, `value`, `tooltip`, `topic`) VALUES ('EN', 'lbl_show_hide_hidden_fields', 'Click to show or hide additional fields', '', 'Labels');
INSERT INTO `cw_languages` (`code`, `name`, `value`, `tooltip`, `topic`) VALUES ('EN', 'lbl_attr_description', 'Description', 'Special instructions<br>
<b>{\'FIELD_NAME.name\'|attribute_data}</b> - name of attribute with field code FIELD_NAME<br>
<b>{\'FIELD_NAME.value\'|attribute_data}</b> - value of attribute with field code FIELD_NAME<br>
<b>{\'FIELD_NAME.description\'|attribute_data}</b> - description of attribute with field code FIELD_NAME', 'Labels');