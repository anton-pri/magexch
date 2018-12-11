-- Addon
REPLACE INTO `cw_addons` (`addon`, `descr`, `active`, `status`, `parent`, `version`, `orderby`) VALUES ('payment_ogoneweb', 'Payment. Ogone', 1, 0, 'payment_system', '1', 0);

-- configuration options
REPLACE INTO `cw_config_categories` (`config_category_id` ,`category` ,`is_local`) VALUES (NULL , 'payment_ogoneweb', '0');
SET @config_category_id = LAST_INSERT_ID();
REPLACE INTO cw_config SET name='ogoneweb_pspid', comment='PSPID', value='', config_category_id=@config_category_id, orderby='10', type='text', defvalue='', variants='';
REPLACE INTO cw_config SET name='ogoneweb_sign', comment='SHA-1 Signature', value='', config_category_id=@config_category_id, orderby='20', type='text', defvalue='', variants='';
REPLACE INTO cw_config SET name='ogoneweb_cur', comment='Currency', value='USD', config_category_id=@config_category_id, orderby='30', type='text', defvalue='USD', variants='';
REPLACE INTO cw_config SET name='ogoneweb_test', comment='Test mode', value='N', config_category_id=@config_category_id, orderby='40', type='checkbox', defvalue='N', variants='';
REPLACE INTO cw_config SET name='ogoneweb_prefix', comment='Order prefix', value='', config_category_id=@config_category_id, orderby='50', type='text', defvalue='', variants='';
REPLACE INTO cw_config SET name='ogoneweb_tp', comment='Template URL', value='', config_category_id=@config_category_id, orderby='60', type='text', defvalue='', variants='';

-- Addon name/description
REPLACE INTO cw_languages SET code='EN', name='addon_descr_payment_ogoneweb', value='OGONE web based payment', topic='Addons';
REPLACE INTO cw_languages SET code='EN', name='addon_name_payment_ogoneweb', value='Payment. Ogone web', topic='Addons';
REPLACE INTO cw_languages SET code='EN', name='option_title_payment_ogoneweb', value='Payment. Ogone web options', topic='Options';



