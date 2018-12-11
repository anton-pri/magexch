delete from cw_breadcrumbs where link='/index.php?target=import&mode=flexible_import';
replace into cw_breadcrumbs (link,title, parent_id) values ('/index.php?target=import&mode=flexible_import', 'lbl_flexible_import', 1);
delete from cw_breadcrumbs where link='/index.php?target=import&mode=flexible_import_profile&action=check&profile_id=[[ANY]]';
replace into cw_breadcrumbs (link,title, parent_id) values ('/index.php?target=import&mode=flexible_import_profile&action=check&profile_id=[[ANY]]', 'lbl_flexible_import', 1);
delete from cw_breadcrumbs where link='/index.php?target=import&mode=flexible_import_profile&profile_id=[[ANY]]';
replace into cw_breadcrumbs (link,title, parent_id) values ('/index.php?target=import&mode=flexible_import_profile&profile_id=[[ANY]]', 'lbl_flexible_import', 1);


replace into cw_languages (code, name, value, topic) values ('EN', 'lbl_flexible_import_manual', 'Manual run of import profile', 'Labels');
replace into cw_languages (code, name, value, topic) values ('EN', 'lbl_delete_success', 'Files deleted successfully!', 'Labels');

alter table cw_flexible_import_profiles add column active_reccuring int(1) not null default 0;
alter table cw_flexible_import_profiles add column recurring_import_path mediumtext not null default '';
alter table cw_flexible_import_profiles add column recurring_import_days int(11) not null default 0;
alter table cw_flexible_import_profiles add column recurring_import_hours int(11) not null default 0;
alter table cw_flexible_import_profiles add column recurring_last_run_date int(11) not null default 0;
alter table cw_flexible_import_profiles add column parsed_columns text not null default '';
alter table cw_flexible_import_profiles add column map_process text not null default '';
