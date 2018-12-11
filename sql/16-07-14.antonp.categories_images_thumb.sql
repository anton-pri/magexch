select @cid:=config_category_id from cw_config_categories where category='Appearance';
insert IGNORE into cw_config (name, comment, value, config_category_id, orderby, type) values ('categories_images_thumb_width', 'Category icon width, px', '200', @cid, 460, 'numeric');
insert IGNORE into cw_config (name, comment, value, config_category_id, orderby, type) values ('categories_images_thumb_height', 'Category icon height, px', '200', @cid, 461, 'numeric');
