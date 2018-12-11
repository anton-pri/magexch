insert into cw_addons set addon='payment_litleco', descr='LitleCo', active=1, status=0, parent='payment_system', version='0.1', orderby=0;

insert into cw_config_categories set category='payment_litleco', is_local=0;
SELECT @config_category_id:=config_category_id FROM cw_config_categories WHERE category = 'payment_litleco';

insert into cw_config set name='litleco_id', comment='LitleCo ID', value='123', config_category_id=@config_category_id, orderby=10, type='text';

insert into cw_config set name='litleco_user', comment='LitleCo User', value='test', config_category_id=@config_category_id, orderby=14, type='text';
insert into cw_config set name='litleco_password', comment='LitleCo Password', value='password', config_category_id=@config_category_id, orderby=18, type='text';
insert into cw_config set name='litleco_mid', comment='LitleCo merchantID', value='123', config_category_id=@config_category_id, orderby=22, type='text';
insert into cw_config set name='litleco_test', comment='Test mode', value='Y', config_category_id=@config_category_id, orderby=26, type='checkbox';

