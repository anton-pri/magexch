delete from cw_languages where name='lbl_ps_update_selected';
select @fid:=field_id from cw_register_fields where field='membership_id';
UPDATE `cw_register_fields_avails` SET `is_required` = '0' WHERE `cw_register_fields_avails`.`field_id` =@fid AND `cw_register_fields_avails`.`area` = 'S';
delete from cw_register_fields_values where field_id=0;
