-- Add seller config group
INSERT INTO `cw_config_categories` ( `config_category_id` , `category` , `is_local`) VALUES ( NULL , 'seller', '0');
SET @gid=LAST_INSERT_ID();

 -- Add default seller commission rate
INSERT INTO `cw_config` ( `name` , `comment` , `value` , `config_category_id` , `orderby` , `type` , `defvalue` , `variants`) VALUES ( 'seller_admin_commission_rate', 'Percentage from seller order which goes to admin account', '0', @gid, '0', 'numeric', '0', '');

INSERT INTO `cw_register_fields` ( `field_id` , `section_id` , `field` , `type` , `variants` , `def` , `orderby` , `is_protected`) VALUES ( NULL , '1', 'admin_commission_rate', 'T', '', '0', '20', '1');
SET @fid=LAST_INSERT_ID();
INSERT INTO `cw_register_fields_lng` ( `field_id` , `code` , `field`) VALUES ( @fid, 'EN', 'Admin commission');
REPLACE INTO `cw_register_fields_by_types` (`field_id`, `area`, `is_required`, `is_avail`) VALUES (@fid, 'V', 0, 1);
REPLACE INTO `cw_register_fields_avails` (`field_id`, `area`, `is_avail`, `is_required`) VALUES (@fid, 'V', 1, 0);
