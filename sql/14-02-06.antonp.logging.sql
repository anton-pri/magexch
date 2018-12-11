CREATE TABLE IF NOT EXISTS `cw_logged_data` (
  `logid` int(11) NOT NULL AUTO_INCREMENT,
  `date` int(11) NOT NULL DEFAULT '0',
  `cwsid` varchar(32) NOT NULL DEFAULT '',
  `customer_id` int(11) NOT NULL DEFAULT '0',
  `REQUEST_METHOD` varchar(16) NOT NULL DEFAULT '',
  `REQUEST_URI` text NOT NULL,
  `GET` text NOT NULL,
  `POST` text NOT NULL,
  `target` varchar(64) NOT NULL DEFAULT '',
  `page_code` varchar(64) NOT NULL DEFAULT '',
  `HTTP_REFERER` text NOT NULL,
  `REDIRECT_URL` text NOT NULL,
  PRIMARY KEY (`logid`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `cw_logged_data_sessions` (
  `cwsid` varchar(32) NOT NULL DEFAULT '',
  `SERVER` text NOT NULL,
  `user_account` text NOT NULL,
  `customer_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`cwsid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

SELECT @sections:=menu_id FROM cw_navigation_menu WHERE title='lbl_tools' AND parent_menu_id=0 AND area='A' LIMIT 1;

DELETE FROM cw_navigation_menu WHERE title='lbl_logging';

INSERT INTO `cw_navigation_menu` (`menu_id`, `parent_menu_id`, `title`, `link`, `orderby`, `area`, `access_level`, `addon`, `is_loggedin`)
VALUES (NULL, @sections, 'lbl_logging', 'index.php?target=logging', 300, 'A', '', '', 1);

INSERT INTO `cw_navigation_sections` (`title`, `link`, `addon`, `access_level`, `area`, `main`, `orderby`, `skins_subdir`)
VALUES ('lbl_logging_visits', 'index.php?target=logging', '', '', 'A', 'N', 10, '');
SET @section_id = LAST_INSERT_ID();

INSERT INTO `cw_navigation_tabs` (`access_level`, `title`, `link`, `orderby`)
VALUES ('', 'lbl_logging_visits', 'index.php?target=logging', 10);
SET @tab_id = LAST_INSERT_ID();

INSERT INTO `cw_navigation_targets` (`target`, `params`, `visible`, `section_id`, `tab_id`, `orderby`, `addon`)
VALUES ('logging', '($_GET[\'mode\']==\'\' && $_POST[\'mode\']==\'\')', '', @section_id, @tab_id, 10, '');

REPLACE INTO cw_breadcrumbs SET link = '/index.php?target=logging', title='lbl_logging', parent_id=1, addon='', uniting=0;

REPLACE INTO cw_languages SET code='EN', name='lbl_logging', value='Logging', topic='Labels';

REPLACE INTO cw_languages SET code='EN', name='lbl_logging_visits', value='Logging Customers Visits', topic='Labels';

REPLACE INTO `cw_languages` (`code`, `name`, `value`, `tooltip`, `topic`) VALUES
('EN', 'txt_error_http_404_upper_message', 'txt_error_http_404_upper_message', '', 'Text');

REPLACE INTO `cw_languages` (`code`, `name`, `value`, `tooltip`, `topic`) VALUES
('EN', 'lbl_please_try_find_products', 'Please, try to find products which refer to these words:', '', 'Labels');

REPLACE INTO `cw_languages` (`code`, `name`, `value`, `tooltip`, `topic`) VALUES
('EN', 'txt_error_http_404_lower_message', 'txt_error_http_404_lower_message', '', 'Text');

REPLACE INTO `cw_languages` (`code`, `name`, `value`, `tooltip`, `topic`) VALUES
('EN', 'lbl_code_404_page', 'CODE', '', 'Labels');

REPLACE INTO `cw_languages` (`code`, `name`, `value`, `tooltip`, `topic`) VALUES
('EN', 'lbl_delete_archived_entries_from_database', 'Delete archived log entries from the database', '', 'Labels');

REPLACE INTO `cw_languages` (`code`, `name`, `value`, `tooltip`, `topic`) VALUES
('EN', 'lbl_archive_listed_log', 'archive listed log', '', 'Labels');

REPLACE INTO `cw_languages` (`code`, `name`, `value`, `tooltip`, `topic`) VALUES
('EN', 'lbl_archived_logs', 'Archived Logs', '', 'Labels');

