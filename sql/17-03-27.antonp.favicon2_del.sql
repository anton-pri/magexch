delete from cw_config where name='favicon_path' and config_category_id = (select config_category_id from cw_config_categories where category='General');
