SELECT @config_category_id:=config_category_id FROM cw_config_categories WHERE category='google_base';
UPDATE cw_config SET variants='0:Never\ndaily:Daily\nweekly:Weekly\nbeweekly:Beweekly\nmonthly:Monthly\nannually:Annually' WHERE name='gb_cron_period';

select @cid:=config_category_id from cw_config_categories where category='paypal';
replace INTO `cw_config` ( `name` , `comment` , `value` , `config_category_id` , `orderby` , `type` , `defvalue` , `variants`) VALUES ( 'use_preauth', 'Perform Auth only transaction on order placement', 'N', @cid, '9', 'checkbox', 'N', '');

