INSERT INTO `cw_menu` ( `menu_id` , `parent_menu_id` , `title` , `link` , `orderby` , `area` , `access_level` , `addon` , `is_loggedin`) VALUES ( NULL , '3', 'lbl_dod_generators', 'index.php?target=deal_of_day', '400', 'A', '33', 'deal_of_day', '1');

insert into cw_breadcrumbs (addon, parent_id, link, title) values ('deal_of_day', 1, '/index.php?target=deal_of_day', 'lbl_dod_generators');
insert into cw_breadcrumbs (addon, parent_id, link, title) values ('deal_of_day', 1, '/index.php?target=deal_of_day&action=form&generator_id=', 'lbl_dod_new_generator');
insert into cw_breadcrumbs (addon, parent_id, link, title) values ('deal_of_day', 1, '/index.php?target=deal_of_day&mode=details&action=details&generator_id=[[ANY]]', 'lbl_dod_modify_generator');
insert into cw_breadcrumbs (addon, parent_id, link, title) values ('deal_of_day', 1, '/index.php?target=deal_of_day&mode=details&action=details&generator_id=[[ANY]]&js_tab=dod_generator_bonuses', 'lbl_dod_modify_generator');
insert into cw_breadcrumbs (addon, parent_id, link, title) values ('deal_of_day', 1, '/index.php?target=deal_of_day&mode=details&action=details&generator_id=[[ANY]]&js_tab=dod_generator_details', 'lbl_dod_modify_generator');

