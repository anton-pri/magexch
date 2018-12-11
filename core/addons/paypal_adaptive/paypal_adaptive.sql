insert into cw_addons set addon='paypal_adaptive', descr='PayPal Adaptive', parent='payment_system', version='0.1', active=0;
insert into cw_config_categories set config_category_id=150, category='paypal_adaptive';

insert into cw_config set config_category_id=150, name='test_mode', comment='Test mode', value='Y', orderby=70, type='checkbox';
insert into cw_config set config_category_id=150, name='currency', comment='Currency', value='USD', orderby=60, type='selector', variants='USD:USD\nCAD:CAD\nEUR:EUR\nGBP:GBP\nAUD:AUD\nJPY:JPY';
insert into cw_config set config_category_id=150, name='api_access', comment='API access username', value='kornev_1308220364_biz_api1.arscommunity.com', orderby=10, type='text';
insert into cw_config set config_category_id=150, name='api_password', comment='API access password', value='1308220376', orderby=20, type='text';
insert into cw_config set config_category_id=150, name='api_signature', comment='API signature', value='A5ygiqfbgsbnG72ZD3otJX6x.-2AAtl1XNOhs7qNzUfoukNVkNCK5rdH', orderby=30, type='text';
insert into cw_config set config_category_id=150, name='prefix', comment='Order prefix', value='adapt', orderby=180, type='text';

insert into cw_config set config_category_id=150, name='email_acc', comment='Admin PP email', value='dmitriy-facilitator@shabaev.com', orderby=5, type='text';
insert into cw_config set config_category_id=150, name='pp_method', comment='Payment method', value='C', orderby=80, type='selector', variants='C:Basic Chained Payment\nP:Parallel Payment';
insert into cw_config set config_category_id=150, name='fee_payer', comment='Fee Payer', value='EACHRECEIVER', orderby=85, type='selector', variants='PRIMARYRECEIVER:Primary receiver\nEACHRECEIVER:Each receiver\nSECONDARYONLY:Secondary receivers';
