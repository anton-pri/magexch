create table if not exists cw_clean_urls_custom_facet_urls_options (
option_id int(11) not null auto_increment, 
url_id int(11) not null default 0, 
attribute_value_ids varchar(255) not null default '', 
clean_urls varchar(255) not null default '', 
PRIMARY KEY(option_id)
);

alter table cw_clean_urls_custom_facet_urls drop column clean_urls;
alter table cw_clean_urls_custom_facet_urls drop column attribute_value_ids;
alter table cw_clean_urls_custom_facet_urls add column description text not null default '';
alter table cw_clean_urls_custom_facet_urls add column title varchar(255) not null default '';
delete from cw_clean_urls_custom_facet_urls;
