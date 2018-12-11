SELECT @mem:=field_id FROM cw_register_fields WHERE field='membership_id';
UPDATE cw_register_fields_avails SET is_avail='1' WHERE field_id=@mem;
