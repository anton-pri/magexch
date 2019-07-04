insert into cw_attributes (name, type, field, is_required, active, orderby, addon, item_type, is_show_addon, protection) values ('Only first tab enabled', 'selectbox', 'magexch_product_single_tab', 0, 1, 201, 'custom_magazineexchange', 'P', 1, 15);

select @aid:=attribute_id from cw_attributes where field='magexch_product_single_tab';

insert into cw_attributes_default (value, attribute_id, is_default, active) values ('N', @aid, 1, 1);
insert into cw_attributes_default (value, attribute_id, is_default, active) values ('Y', @aid, 0, 1);

insert into cw_attributes (name, type, field, is_required, active, orderby, addon, item_type, is_show_addon, protection) values ('Tab background color', 'text', 'magexch_product_tab_color', 0, 1, 202, 'custom_magazineexchange', 'P', 1, 15);

insert into cw_attributes (name, type, field, is_required, active, orderby, addon, item_type, is_show_addon, protection) values ('External links off', 'selectbox', 'magexch_product_external_links_off', 0, 1, 202, 'custom_magazineexchange', 'P', 1, 15);

select @aid:=attribute_id from cw_attributes where field='magexch_product_external_links_off';

insert into cw_attributes_default (value, attribute_id, is_default, active) values ('N', @aid, 1, 1);
insert into cw_attributes_default (value, attribute_id, is_default, active) values ('Y', @aid, 0, 1);
