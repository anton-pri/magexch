INSERT INTO `cw_register_fields` ( `field_id` , `section_id` , `field` , `type` , `variants` , `def` , `orderby` , `is_protected`) VALUES ( NULL , '1', 'allow_digital_sales', 'C', '', '', '30', '1');
SELECT @field_id:=field_id FROM cw_register_fields WHERE field='allow_digital_sales';
INSERT INTO `cw_register_fields_lng` (field_id, code, field) VALUES (@field_id, 'EN', 'Allow Digital Sales'); 
INSERT INTO `cw_register_fields_avails` (field_id, area, is_avail, is_required) VALUES (@field_id, 'V', 1, 0);
