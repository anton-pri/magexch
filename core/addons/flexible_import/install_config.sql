delete from cw_config_categories WHERE category = 'flexible_import';
insert into cw_config_categories set category='flexible_import';
SELECT @config_category_id:=config_category_id FROM cw_config_categories WHERE category = 'flexible_import';

replace into cw_config set name='fi_extra_tables_databases', comment='List of databases used for extra mapped tabes', type='text', config_category_id=@config_category_id, orderby = 10;

