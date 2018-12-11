alter table cw_product_options add column text_type char(1) not null default '';
replace into cw_languages set code='EN', name='lbl_text_input', topic='Labels', value='Text Input';
replace into cw_languages set code='EN', name='lbl_text_type', topic='Labels', value='Text Type';
alter table cw_product_options add column text_limit int(11) not null default 100;
replace into cw_languages set code='EN', name='lbl_text_limit', topic='Labels', value='Text Limit';
replace into cw_languages set code='EN', name='lbl_chars_left', topic='Labels', value='Chars left';
replace into cw_config set name='textarea_rows', comment='Rows count of textarea product option', value='5', config_category_id=32, orderby=30, type='text', defvalue=5;
replace into cw_config set name='textarea_cols', comment='Columns count of textarea product option', value='30', config_category_id=32, orderby=40, type='text', defvalue=30;
