alter table cw_products_reviews add column main_title varchar(255) not null default '' after email;
replace into cw_languages set name='lbl_title_and_review', topic='Labels', value='Title and message', code='EN';
