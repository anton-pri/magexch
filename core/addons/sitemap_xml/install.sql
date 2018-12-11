SELECT @config_cid:=config_category_id FROM cw_config_categories WHERE category='sitemap_xml';
DELETE FROM cw_config_categories WHERE config_category_id=@config_cid;
DELETE FROM cw_config WHERE config_category_id=@config_cid;

INSERT INTO `cw_config_categories` (`config_category_id` ,`category` ,`is_local`) VALUES (NULL , 'sitemap_xml', '0');
SET @config_category_id = LAST_INSERT_ID();
INSERT INTO cw_config SET name='sep_xml_1', comment='Priority (0-1)', value='', config_category_id = @config_category_id, orderby='10', type='separator', defvalue='', variants='';
INSERT INTO cw_config SET name='sep_xml_2', comment='Change fequency', value='', config_category_id = @config_category_id, orderby='110', type='separator', defvalue='', variants='';
INSERT INTO cw_config SET name='sep_xml_3', comment='Other', value='', config_category_id = @config_category_id, orderby='210', type='separator', defvalue='', variants='';
INSERT INTO cw_config SET name='sm_cron_period', comment='Automatically update the sitemap', value='monthly', config_category_id = @config_category_id, orderby='330', type='selector', defvalue='', variants='0:Never\nweekly:Weekly\nbiweekly:Biweekly\nmonthly:Monthly\nannually:Annually';
INSERT INTO cw_config SET name='sm_filename', comment='Filename', value='sitemap.xml', config_category_id = @config_category_id, orderby='320', type='text', defvalue='sitemap.xml', variants='';
INSERT INTO cw_config SET name='sm_ping_google', comment='Ping google server with update request after sitemap generation', value='N', config_category_id = @config_category_id, orderby='230', type='checkbox', defvalue='N', variants='';
INSERT INTO cw_config SET name='sm_frequency_cat_manuf', comment='Category/manufacturer page', value='weekly', config_category_id = @config_category_id, orderby='130', type='selector', defvalue='weekly', variants='always:always\r\nhourly:hourly\r\ndaily:daily\r\nweekly:weekly\r\nmonthly:monthly\r\nyearly:yearly\r\nnever:never\r\n';
INSERT INTO cw_config SET name='sm_frequency_home', comment='Home page', value='daily', config_category_id = @config_category_id, orderby='120', type='selector', defvalue='daily', variants='always:always\r\nhourly:hourly\r\ndaily:daily\r\nweekly:weekly\r\nmonthly:monthly\r\nyearly:yearly\r\nnever:never\r\n';
INSERT INTO cw_config SET name='sm_frequency_product', comment='Product page', value='monthly', config_category_id = @config_category_id, orderby='140', type='selector', defvalue='monthly', variants='always:always\r\nhourly:hourly\r\ndaily:daily\r\nweekly:weekly\r\nmonthly:monthly\r\nyearly:yearly\r\nnever:never\r\n';
INSERT INTO cw_config SET name='sm_frequency_static', comment='Static page', value='never', config_category_id = @config_category_id, orderby='150', type='selector', defvalue='never', variants='always:always\r\nhourly:hourly\r\ndaily:daily\r\nweekly:weekly\r\nmonthly:monthly\r\nyearly:yearly\r\nnever:never\r\n';
INSERT INTO cw_config SET name='sm_pack_result', comment='Pack result XML to GZ archive', value='N', config_category_id = @config_category_id, orderby='220', type='checkbox', defvalue='N', variants='';
INSERT INTO cw_config SET name='sm_priority_cat_manuf', comment='Category/manufacturer page', value='0.8', config_category_id = @config_category_id, orderby='30', type='numeric', defvalue='0.8', variants='';
INSERT INTO cw_config SET name='sm_priority_home', comment='Home page', value='1', config_category_id = @config_category_id, orderby='20', type='numeric', defvalue='1', variants='';
INSERT INTO cw_config SET name='sm_priority_product', comment='Product page', value='0.6', config_category_id = @config_category_id, orderby='40', type='numeric', defvalue='0.6', variants='';
INSERT INTO cw_config SET name='sm_priority_static', comment='Static page', value='0.2', config_category_id = @config_category_id, orderby='50', type='numeric', defvalue='0.2', variants='';


REPLACE INTO cw_languages SET code='EN', name='lbl_generate_sitemap', value='Generate Sitemap', topic='Labels';
REPLACE INTO cw_languages SET code='EN', name='lbl_sitemap_xml', value='Sitemap XML', topic='Labels';
REPLACE INTO cw_languages SET code='EN', name='addon_descr_sitemap_xml', value='This addon allows you to create xml sitemap.', topic='Addons';
REPLACE INTO cw_languages SET code='EN', name='addon_name_sitemap_xml', value='Sitemap XML', topic='Addons';
REPLACE INTO cw_languages SET code='EN', name='msg_sitemap_xml', value='Sitemap has been successfully created', topic='Text';
REPLACE INTO cw_languages SET code='EN', name='opt_sep_xml_1', value='Priority (0-1)', topic='Options';
REPLACE INTO cw_languages SET code='EN', name='opt_sep_xml_2', value='Fequency', topic='Options';
REPLACE INTO cw_languages SET code='EN', name='opt_sep_xml_3', value='Other', topic='Options';
REPLACE INTO cw_languages SET code='EN', name='opt_sm_cron_key', value='Cron key', topic='Options';
REPLACE INTO cw_languages SET code='EN', name='opt_sm_filename', value='Filename', topic='Options';
REPLACE INTO cw_languages SET code='EN', name='opt_sm_frequency_cat_manuf', value='Category/manufacturer page', topic='Options';
REPLACE INTO cw_languages SET code='EN', name='opt_sm_frequency_home', value='Home page', topic='Options';
REPLACE INTO cw_languages SET code='EN', name='opt_sm_frequency_product', value='Product page', topic='Options';
REPLACE INTO cw_languages SET code='EN', name='opt_sm_frequency_static', value='Static page', topic='Options';
REPLACE INTO cw_languages SET code='EN', name='opt_sm_pack_result', value='Pack result XML to archive', topic='Options';
REPLACE INTO cw_languages SET code='EN', name='opt_sm_priority_cat_manuf', value='Category/manufacturer page', topic='Options';
REPLACE INTO cw_languages SET code='EN', name='opt_sm_priority_home', value='Home page', topic='Options';
REPLACE INTO cw_languages SET code='EN', name='opt_sm_priority_product', value='Product page', topic='Options';
REPLACE INTO cw_languages SET code='EN', name='opt_sm_priority_static', value='Static page', topic='Options';
REPLACE INTO cw_languages SET code='EN', name='txt_sitemap_xml_note', value= 'This section allows you to quickly create XML sitemap. The sitemap corresponds to Sitemap Protocol as defined by <a href=''www.sitemap.org''>sitemaps.org</a> and can be used for Google Sitemap tool. Read more at <a href=''http://www.google.com/support/webmasters/bin/topic.py?topic=8476''>http://www.google.com/support/webmasters/bin/topic.py?topic=8476</a>', topic='Text';

REPLACE INTO cw_addons SET addon='sitemap_xml', addon_descr='This addon allows you to create xml sitemap', active='1';

-- get menu_id for Sections
SELECT @sections:=menu_id FROM cw_menu WHERE title='lbl_content' AND parent_menu_id=0 AND area='A' LIMIT 1;
-- insert new entry to menu
DELETE FROM cw_menu WHERE title='lbl_sitemap_xml';
INSERT INTO cw_menu (`menu_id`, `parent_menu_id`, `title`, `link`, `orderby`, `area`, `access_level`, `addon`, `is_loggedin`) VALUES
(NULL, @sections, 'lbl_sitemap_xml', 'index.php?target=sitemap_xml', 200, 'A', '', 'sitemap_xml', 1);
