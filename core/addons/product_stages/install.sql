-- Addon
REPLACE INTO `cw_addons` (`addon`, `descr`, `active`, `status`, `parent`, `version`, `orderby`) VALUES ('product_stages', 'Product stages feature', 1, 1, '', '0.1', 0);

-- configuration options
-- REPLACE INTO `cw_config_categories` (`config_category_id` ,`category` ,`is_local`) VALUES (NULL , 'product_stages', '0');
-- SET @config_category_id = LAST_INSERT_ID();
-- REPLACE INTO cw_config SET name='skeleton_settings', comment='accessories list', value='', config_category_id=@config_category_id, orderby='0', type='separator', defvalue='', variants='';
-- REPLACE INTO cw_config SET name='product_stages_flag', comment='Flag explanation', value='Y', config_category_id=@config_category_id, orderby='200', type='checkbox', defvalue='Y', variants='';

-- Addon name/description
REPLACE INTO cw_languages SET code='EN', name='addon_descr_product_stages', value='Product stages feature which allows admin to setup emails following the product purchase', topic='Addons';
REPLACE INTO cw_languages SET code='EN', name='addon_name_product_stages', value='Product stages', topic='Addons';
REPLACE INTO cw_languages SET code='EN', name='option_title_product_stages', value='Product stages options', topic='Options';

-- Create necessary tables
CREATE TABLE IF NOT EXISTS `cw_product_stages_library` (
  `stage_lib_id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(64) NOT NULL default '',
  `subject` varchar(255) NOT NULL default '',
  `default_period` smallint(6) NOT NULL default 0,
  `default_status` varchar(255) NOT NULL default '',
  `body` text NOT NULL default '', 
  `pos` smallint(6) NOT NULL default 0,
   PRIMARY KEY stage_lib_id (stage_lib_id)
);

CREATE TABLE IF NOT EXISTS `cw_product_stages_product_settings` (
  `setting_id` int(11) not null auto_increment,
  `product_id` int(11) NOT NULL default 0,
  `stage_lib_id` int(11) NOT NULL default 0,
  `period` int(11) NOT NULL default -1,
  `status` varchar(255) NOT NULL default '-1',
  `active` int(1) not null default 1,
  PRIMARY KEY setting_id (setting_id)  
);

create table IF NOT EXISTS `cw_docs_statuses_log` (
doc_id int(11) not null default 0, 
status varchar(2) not null default '', 
date int(11) not null default 0
)TYPE=MYISAM;

create table if not exists `cw_product_stages_process_log` (
setting_id int(11) not null default 0, 
doc_item_id int(11) not null default 0, 
status varchar(2) not null default '', 
date int(11) not null default 0
)TYPE=MyISAM;

replace into cw_languages set code='EN', topic='Labels', name='lbl_email_subject', value='Email Subject';
replace into cw_languages set code='EN', topic='Labels', name='lbl_email_body', value='Email Body';
replace into cw_languages set code='EN', topic='Labels', name='lbl_default_period', value='Default Period (working days)';
replace into cw_languages set code='EN', topic='Labels', name='lbl_default_order_status', value='Default Order Status';
replace into cw_languages set code='EN', topic='Labels', name='lbl_new_product_stage', value='New Product Stage';
replace into cw_languages set code='EN', topic='Labels', name='lbl_order_stages', value='Order stages';
replace into cw_languages set name='lbl_product_stages_period', value='Period (working days)', topic='Labels', code='EN';

-- get menu_id for General Settings
SELECT @genset:=menu_id FROM cw_navigation_menu WHERE title='lbl_settings' AND area='A' LIMIT 1;
-- insert new entry to menu
DELETE FROM cw_navigation_menu WHERE title='lbl_product_stages';
INSERT INTO `cw_navigation_menu` (`menu_id`, `parent_menu_id`, `title`, `link`, `orderby`, `area`, `access_level`, `addon`, `is_loggedin`) VALUES
(NULL, @genset, 'lbl_product_stages', 'index.php?target=product_stages', 1040, 'A', '', 'product_stages', 1);


-- Langvars
REPLACE INTO `cw_languages` (`code`, `name`, `value`, `topic`) VALUES ('EN', 'lbl_product_stages', 'Product Stages', 'Labels');

