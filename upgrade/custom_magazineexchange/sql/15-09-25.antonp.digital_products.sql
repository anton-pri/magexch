replace into cw_languages (code, name, value, topic) values ('EN', 'lbl_digital_products', 'Digital Products', 'Labels');
insert into cw_navigation_sections (title, link, addon, access_level, area, main, orderby, skins_subdir) values ('lbl_digital_products', 'index.php?target=digital_products', 'custom_magazineexchange_sellers', 12, 'V', 'Y', 20, 'products'); 
select @section_id:=section_id from cw_navigation_sections where title='lbl_digital_products';
insert into cw_navigation_tabs (title, link, orderby) values ('lbl_digital_products', 'index.php?target=digital_products', 25);
select @tab_id:=tab_id from cw_navigation_tabs where title='lbl_digital_products';

insert into cw_navigation_targets (target, section_id, tab_id, orderby, addon, params) values ('digital_products', @section_id, @tab_id, 30, 'custom_magazineexchange_sellers', '$target==\'digital_products\'');

select @product_tab_id:=tab_id from cw_navigation_tabs where access_level=0 and title='lbl_products' and link='index.php?target=products';
insert into cw_navigation_targets (target, section_id, tab_id, orderby, addon, params) values ('digital_products', @section_id, @product_tab_id, 20, 'custom_magazineexchange_sellers', '$target==\'products\'');

insert into cw_navigation_menu (parent_menu_id, title, link, orderby, area, addon, is_loggedin) values (151, 'lbl_digital_products', 'index.php?target=digital_products', 20, 'V', 'custom_magazineexchange_sellers',1);

select @products_section_id:=section_id from cw_navigation_sections where title='lbl_products' and link='index.php?target=products' and area='V';
insert into cw_navigation_targets (target, section_id, tab_id, orderby, addon, params) values ('products', @products_section_id, @tab_id, 30, 'custom_magazineexchange_sellers','$target==\'digital_products\'');
