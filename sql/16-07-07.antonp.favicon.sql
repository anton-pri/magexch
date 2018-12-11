select @cid:=config_category_id from cw_config_categories where category='Appearance';
insert into cw_config (name, comment, value, config_category_id, orderby, type) values ('favicon_path', 'Favorite icon path', '', @cid, 705, 'text');
