-- Addon
REPLACE INTO `cw_addons` (`addon`, `descr`, `active`, `status`, `parent`, `version`, `orderby`) VALUES ('clickatell_sms', 'Clickatell.com SMS gate for notifications', 1, 1, '', '0.1', 0);

-- configuration options
REPLACE INTO `cw_config_categories` (`config_category_id` ,`category` ,`is_local`) VALUES (NULL , 'clickatell_sms', '0');
SET @config_category_id = LAST_INSERT_ID();
REPLACE INTO cw_config SET name='sms_api_key', comment='Clickatell integration API key', value='', config_category_id=@config_category_id, orderby='100', type='text', defvalue='', variants='';
REPLACE INTO cw_config SET name='sms_from_number', comment='2-Way integration number (leave empty for one-way integration)', value='', config_category_id=@config_category_id, orderby='105', type='text', defvalue='', variants='';
REPLACE INTO cw_config SET name='sms_custom_field', comment='Customer profile field to be used as cell phone. E.g. main_address.phone (default) or current_address.phone', value='', config_category_id=@config_category_id, orderby='110', type='text', defvalue='', variants='';
REPLACE INTO cw_config SET name='sms_lifetime', comment='SMS lifetime (days)', value='2', config_category_id=@config_category_id, orderby='115', type='numeric', defvalue='2', variants='';
REPLACE INTO cw_config SET name='sms_pause', comment='Pause SMS sending', value='Y', config_category_id=@config_category_id, orderby='120', type='checkbox', defvalue='', variants='';

-- Addon name/description
REPLACE INTO cw_languages SET code='EN', `name`='addon_descr_clickatell_sms', `value`='Clickatell.com SMS gate for notifications', topic='Addons';
REPLACE INTO cw_languages SET code='EN', `name`='addon_name_clickatell_sms', `value`='Clickatell SMS', topic='Addons';
REPLACE INTO cw_languages SET code='EN', `name`='option_title_clickatell_sms', `value`='Clickatell SMS options', topic='Options';


ALTER TABLE `cw_order_statuses` ADD `sms_customer` INT(1) NOT NULL COMMENT 'send sms notification' AFTER `inventory_decreasing`, ADD `sms_message` VARCHAR(255) NOT NULL COMMENT 'sms content' AFTER `sms_customer`;

CREATE TABLE `cw_sms_spool` (
  `sms_id` int(11) NOT NULL AUTO_INCREMENT,
  `sms_to` varchar(32) NOT NULL DEFAULT '',
  `body` text NOT NULL,
  `date_added` int(11) NOT NULL DEFAULT '0',
  `date_send` int(11) NOT NULL DEFAULT '0',
  `error` varchar(512) NOT NULL,
  `doc_id` int(11) NOT NULL DEFAULT '0',
  `doc_status` char(2) NOT NULL DEFAULT '',
  PRIMARY KEY (`sms_id`),
  KEY `date_sent` (`date_send`)
) ENGINE=InnoDB ;

-- Navigation menu
SELECT @m_id:=menu_id FROM cw_menu WHERE title='lbl_tools' AND area='A' LIMIT 1;
SELECT @orderby:=orderby FROM cw_menu WHERE target='mail_queue' AND area='A' LIMIT 1;

-- insert new entry to menu
DELETE FROM cw_menu WHERE addon='clickatell_sms';

INSERT INTO `cw_menu` (`menu_id`, `parent_menu_id`, `title`, `link`, `target`, `orderby`, `area`, `access_level`, `func_visible`, `addon`, `skins_subdir`, `is_loggedin`) VALUES
(null, @m_id, 'lbl_sms_queue', 'index.php?target=clickatell_sms&mode=spool', 'clickatell_sms', @orderby+5, 'A', '', '', 'clickatell_sms', '', 1);

-- Langvars
REPLACE INTO `cw_languages` (`code`, `name`, `value`, `topic`) VALUES ('EN', 'lbl_sms_queue', 'SMS Queue', 'Labels');

