select @cid:=config_category_id from cw_config_categories where category='Appearance';
insert into cw_config (name, comment, value, config_category_id, orderby, type) values ('enable_meta_kaywords', 'Enable meta keywords tag', 'N', @cid, 710, 'checkbox');
