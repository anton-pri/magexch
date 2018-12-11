SELECT @config_category_id:=config_category_id FROM cw_config_categories WHERE category='google_base';
INSERT INTO cw_config SET name='gb_cron_period', comment='Automatically update the feed', value='monthly', config_category_id = @config_category_id, orderby='70', type='selector', defvalue='', variants='0:Never\nweekly:Weekly\nbeweekly:Beweekly\nmonthly:Monthly\nannually:Annually';

SELECT @config_category_id:=config_category_id FROM cw_config_categories WHERE category='sitemap_xml';
UPDATE cw_config SET variants='0:Never\nweekly:Weekly\nbeweekly:Beweekly\nmonthly:Monthly\nannually:Annually', comment='Automatically update the sitemap' WHERE name='sm_cron_period';

