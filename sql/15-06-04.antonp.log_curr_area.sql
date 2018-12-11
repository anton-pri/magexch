alter table cw_logged_data add column current_area char(2) not null default '';
update cw_logged_data set current_area='C';
