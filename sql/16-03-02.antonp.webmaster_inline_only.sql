INSERT INTO cw_register_fields (section_id, field, `type`, orderby, is_protected) values (1, 'only_inline_edit', 'C', 10, 1);
SET @field_id=LAST_INSERT_ID();
INSERT INTO cw_register_fields_avails (field_id, area, is_avail, is_required) values (@field_id, 'A', 1, 0);
INSERT cw_register_fields_lng (field_id, code, field) values (@field_id, 'EN', 'Inline Edit Only'); 
