insert into cw_attributes (name, type, field, is_required, active, orderby, addon, item_type, is_show_addon, protection) values ('Custom Category Skin', 'selectbox', 'magexch_index_skin', 0, 1, 200, 'custom_magazineexchange', 'C', 1, 15);

select @aid:=attribute_id from cw_attributes where field='magexch_index_skin';

insert into cw_attributes_default (value, attribute_id, is_default, active) values ('', @aid, 1, 1);

insert into cw_attributes_default (value, attribute_id, is_default, active) values ('/skins_custom_categories/GalleryPage', @aid, 0, 1);

