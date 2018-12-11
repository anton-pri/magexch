update cw_register_fields set field = REPLACE(field,', ','');
update cw_register_fields set field = REPLACE(field,',','');
update cw_register_fields set field = REPLACE(field,'%','');
update cw_register_fields set field = REPLACE(field,' ','_');
update cw_register_fields set field = LOWER(field);

select 'WARNING. Please correct field name.', field_id, field  from cw_register_fields where field regexp '[^A-Z_0-9]';

