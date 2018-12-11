-- new module record
REPLACE INTO `cw_modules` (`module`, `module_descr`, `active`, `status`, `parent_module`,`version`)
VALUES ('payment-authorize-sim', 'Authorize.Net payment solutions - Server Integration Method', 1, 1, 'payment-system', '0.1');


-- configuration options
DELETE FROM cw_config_categories WHERE category='payment-authorize-sim';

INSERT INTO `cw_config_categories` (`config_category_id` ,`category` ,`is_main`) VALUES (NULL , 'payment-authorize-sim', '0');
SET @config_category_id = LAST_INSERT_ID();

REPLACE INTO cw_config SET name='asim_api_login_id', comment='API Login ID', value='', config_category_id = @config_category_id, orderby='10', type='text', defvalue='', variants='';
REPLACE INTO cw_config SET name='asim_transaction_key', comment='Transaction Key', value='', config_category_id = @config_category_id, orderby='20', type='text', defvalue='', variants='';
REPLACE INTO cw_config SET name='asim_md5_hash', comment='MD5 Hash', value='', config_category_id = @config_category_id, orderby='30', type='text', defvalue='', variants='';
REPLACE INTO cw_config SET name='asim_currency', comment='Currency', value='GBP', config_category_id = @config_category_id, orderby='40', type='selector', defvalue='GBP', variants='GBP:British Pound\nCAD:Canadian Dollar\nUSD:US Dollar';
REPLACE INTO cw_config SET name='asim_test_live_mode', comment='Test/Live mode', value='test', config_category_id = @config_category_id, orderby='50', type='selector', defvalue='test', variants='test:Test\nlive:Live';
REPLACE INTO cw_config SET name='asim_prefix', comment='Prefix for sequence number of the transaction (Numeric)', value='', config_category_id = @config_category_id, orderby='60', type='text', defvalue='', variants='';
