alter table cw_customers add column oauth_type char(2) not null default 'F';
alter table cw_customers modify column oauth_uid varchar(255) not null default '';
