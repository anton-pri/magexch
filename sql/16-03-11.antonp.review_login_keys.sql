update cw_languages set topic='Labels' where topic='Label';
create table if not exists cw_products_reviews_login_keys (login_key varchar(41) not null default '', date_created int(11) not null default 0, customer_id int(11) not null default 0, PRIMARY KEY (login_key));
replace into cw_config (name, config_category_id, `comment`, `type`, value, orderby) values ('login_key_active_days', (SELECT config_category_id FROM cw_config_categories WHERE category='estore_products_review'), 'Amount of days the review follow up email login key is active', 'numeric', 14, 120);
