select @cid:=config_category_id from cw_config_categories where category='estore_products_review';

insert into cw_config set name='negative_review_treshold', comment='Negative Review Score Treshold', orderby=100, config_category_id=@cid, type='numeric', value='2.00';
insert into cw_config set name='positive_review_treshold', comment='Positive Review Score Treshold', orderby=110, config_category_id=@cid, type='numeric', value='4.00';

replace into cw_languages set code='EN', name='lbl_att_type_global_rating', value='Global rating', topic='Labels';

alter table cw_products_reviews add column name varchar(128) not null default '' after remote_ip;
