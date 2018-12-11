ALTER TABLE  `cw_attributes_values` DROP INDEX item_type_2;
ALTER TABLE  `cw_attributes_values` ADD INDEX `attribute_id` ( `attribute_id` );