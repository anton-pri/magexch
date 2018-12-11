alter table cw_docs modify column status char(2) not null default 'Q';

insert into cw_languages set name ='lbl_status_deleted', topic='Labels', code='EN', value='deleted';

CREATE TABLE IF NOT EXISTS `cw_order_statuses` (
  `code` varchar(2) NOT NULL DEFAULT '',
  `name` varchar(32) NOT NULL DEFAULT '',
  `email_customer` int(1) NOT NULL DEFAULT '0',
  `email_admin` int(1) NOT NULL DEFAULT '0',
  `email_message_customer` text NOT NULL,
  `email_subject_customer` varchar(255) NOT NULL DEFAULT '',
  `email_message_admin` text NOT NULL,
  `email_subject_admin` varchar(255) NOT NULL DEFAULT '',
  `orderby` int(11) NOT NULL DEFAULT '0',
  `is_system` int(1) NOT NULL DEFAULT '0',
  `deleted` int(1) NOT NULL DEFAULT '0',
  `color` varchar(8) NOT NULL DEFAULT '',
  PRIMARY KEY (`code`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

INSERT INTO `cw_order_statuses` VALUES ('I','{$lng.lbl_not_finished}',0,0,'{$lng.eml_order_notification|substitute:\"doc_id\":$order.display_id} this is test cartworks orders','CartWork Company Test: {$lng.eml_order_notification_subj|substitute:\"doc_id\":$order.display_id}','{$lng.eml_order_notification|substitute:\"doc_id\":$order.display_id}','{$config.Company.company_name}: {$lng.eml_order_notification_subj|substitute:\"doc_id\":$order.display_id}',10,1,0,'#b5f4b7'),('Q','{$lng.lbl_queued}',0,0,'{$lng.eml_order_notification|substitute:\"doc_id\":$order.display_id}','{$config.Company.company_name}: {$lng.eml_order_notification_subj|substitute:\"doc_id\":$order.display_id}','{$lng.eml_order_notification|substitute:\"doc_id\":$order.display_id}','{$config.Company.company_name}: {$lng.eml_order_notification_subj|substitute:\"doc_id\":$order.display_id}',20,1,0,'#f0e8ab'),('P','{$lng.lbl_processed}',0,0,'{$lng.eml_order_notification|substitute:\"doc_id\":$order.display_id}','{$config.Company.company_name}: {$lng.eml_order_notification_subj|substitute:\"doc_id\":$order.display_id}','{$lng.eml_order_notification|substitute:\"doc_id\":$order.display_id}','{$config.Company.company_name}: {$lng.eml_order_notification_subj|substitute:\"doc_id\":$order.display_id}',30,1,0,'#c7cbee'),('B','{$lng.lbl_backordered}',0,0,'{$lng.eml_order_notification|substitute:\"doc_id\":$order.display_id}','{$config.Company.company_name}: {$lng.eml_order_notification_subj|substitute:\"doc_id\":$order.display_id}','{$lng.eml_order_notification|substitute:\"doc_id\":$order.display_id}','{$config.Company.company_name}: {$lng.eml_order_notification_subj|substitute:\"doc_id\":$order.display_id}',40,1,0,'#aff096'),('D','{$lng.lbl_declined}',0,0,'{$lng.eml_order_notification|substitute:\"doc_id\":$order.display_id}','{$config.Company.company_name}: {$lng.eml_order_notification_subj|substitute:\"doc_id\":$order.display_id}','{$lng.eml_order_notification|substitute:\"doc_id\":$order.display_id}','{$config.Company.company_name}: {$lng.eml_order_notification_subj|substitute:\"doc_id\":$order.display_id}',50,1,0,'#f2cce9'),('F','{$lng.lbl_failed}',0,0,'{$lng.eml_order_notification|substitute:\"doc_id\":$order.display_id}','{$config.Company.company_name}: {$lng.eml_order_notification_subj|substitute:\"doc_id\":$order.display_id}','{$lng.eml_order_notification|substitute:\"doc_id\":$order.display_id}','{$config.Company.company_name}: {$lng.eml_order_notification_subj|substitute:\"doc_id\":$order.display_id}',70,1,0,'#f8bbbb'),('C','{$lng.lbl_complete}',0,0,'{$lng.eml_order_notification|substitute:\"doc_id\":$order.display_id}','{$config.Company.company_name}: {$lng.eml_order_notification_subj|substitute:\"doc_id\":$order.display_id}','{$lng.eml_order_notification|substitute:\"doc_id\":$order.display_id}','{$config.Company.company_name}: {$lng.eml_order_notification_subj|substitute:\"doc_id\":$order.display_id}',80,1,0,'#78f476'),('L','{$lng.lbl_deposited}',0,0,'{$lng.eml_order_notification|substitute:\"doc_id\":$order.display_id}','{$config.Company.company_name}: {$lng.eml_order_notification_subj|substitute:\"doc_id\":$order.display_id}','{$lng.eml_order_notification|substitute:\"doc_id\":$order.display_id}','{$config.Company.company_name}: {$lng.eml_order_notification_subj|substitute:\"doc_id\":$order.display_id}',60,1,0,'#afcb68'),('T','Test status',0,0,'{$lng.eml_order_notification|substitute:\"doc_id\":$order.display_id}','{$config.Company.company_name}: {$lng.eml_order_notification_subj|substitute:\"doc_id\":$order.display_id}','{$lng.eml_order_notification|substitute:\"doc_id\":$order.display_id}','{$config.Company.company_name}: {$lng.eml_order_notification_subj|substitute:\"doc_id\":$order.display_id}',90,1,0,'');

REPLACE INTO cw_languages SET code='EN', name='lbl_orders_statuses', value='Orders statuses', topic='Labels';

SELECT @sections:=menu_id FROM cw_navigation_menu WHERE title='lbl_settings' AND parent_menu_id=0 AND area='A' LIMIT 1;

DELETE FROM cw_navigation_menu WHERE title='lbl_orders_statuses';

INSERT INTO `cw_navigation_menu` (`menu_id`, `parent_menu_id`, `title`, `link`, `orderby`, `area`, `access_level`, `addon`, `is_loggedin`)
VALUES (NULL, @sections, 'lbl_orders_statuses', 'index.php?target=orders_statuses', 460, 'A', '', '', 1);

INSERT INTO `cw_navigation_sections` (`title`, `link`, `addon`, `access_level`, `area`, `main`, `orderby`, `skins_subdir`)
VALUES ('lbl_orders_statuses', 'index.php?target=orders_statuses', '', '', 'A', 'N', 10, '');
SET @section_id = LAST_INSERT_ID();

INSERT INTO `cw_navigation_tabs` (`access_level`, `title`, `link`, `orderby`)
VALUES ('', 'lbl_orders_statuses', 'index.php?target=orders_statuses', 10);
SET @tab_id = LAST_INSERT_ID();

INSERT INTO `cw_navigation_targets` (`target`, `params`, `visible`, `section_id`, `tab_id`, `orderby`, `addon`)
VALUES ('orders_statuses', '($_GET[\'mode\']==\'\' && $_POST[\'mode\']==\'\')', '', @section_id, @tab_id, 10, '');


REPLACE INTO cw_breadcrumbs SET link = '/index.php?target=orders_statuses', title='lbl_orders_statuses', parent_id=1, addon='', uniting=0;


