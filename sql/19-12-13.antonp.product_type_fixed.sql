delete from cw_attributes where name='Custom Product Type' and field='magexch_product_tab_title1';
insert into cw_attributes (name, type, field, is_required, active, orderby, addon, item_type, is_show_addon, protection) values ('Custom Product Type', 'text', 'magexch_custom_product_type', 0, 1, 204, 'custom_magazineexchange', 'P', 1, 15);
