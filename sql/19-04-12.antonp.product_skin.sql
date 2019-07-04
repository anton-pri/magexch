insert into cw_attributes (name, type, field, is_required, active, orderby, addon, item_type, is_show_addon, protection) values ('Custom Product Skin', 'selectbox', 'magexch_product_skin', 0, 1, 200, 'custom_magazineexchange', 'P', 1, 15);

select @aid:=attribute_id from cw_attributes where field='magexch_product_skin';

insert into cw_attributes_default (value, attribute_id, is_default, active) values ('', @aid, 1, 1);

insert into cw_attributes_default (value, attribute_id, is_default, active) values ('ImageDoubleWidth', @aid, 0, 1);

-- insert into cw_attributes_default (value, attribute_id, is_default, active) values ('', @aid, 1, 1), ('/skins_custom_products/test1', @aid, 0, 1), ('/skins_custom_products/test2', @aid, 0, 1), ('/skins_custom_products/test3', @aid, 0, 1);

