update cw_navigation_menu set title='lbl_export_data' where title='lbl_export';
update cw_navigation_menu set title='lbl_import_data' where title='lbl_import';

INSERT INTO `cw_navigation_menu` ( `menu_id` , `parent_menu_id` , `title` , `link` , `orderby` , `area` , `access_level` , `addon` , `is_loggedin`) VALUES ( NULL , '8', 'lbl_license', 'index.php?target=license', '450', 'A', '', '', '1');

INSERT INTO `cw_navigation_menu` ( `menu_id` , `parent_menu_id` , `title` , `link` , `orderby` , `area` , `access_level` , `addon` , `is_loggedin`) VALUES ( NULL , '7', 'lbl_mail_queue', 'index.php?target=mail_queue', '100', 'A', '', '', '1');


delete from cw_navigation_menu where title in ('lbl_google_base','lbl_amazon_export','lbl_ebay_export');

REPLACE INTO `cw_languages` ( `code` , `name` , `value` , `topic`) VALUES ( 'EN', 'lbl_google_export', 'Google Merchant Center Export', 'Labels');

REPLACE INTO `cw_languages` ( `code` , `name` , `value` , `topic`) VALUES ( 'EN', 'lbl_mail_queue', 'Mail Queue', 'Labels');
REPLACE INTO `cw_languages` ( `code` , `name` , `value` , `topic`) VALUES ( 'EN', 'lbl_alias', 'Alias', 'Labels');
UPDATE `cw_languages` SET `value` = 'Version' WHERE `cw_languages`.`name` = 'lbl_version';

delete from cw_languages where name='lbl_export_orders_add';

DROP TABLE cw_export_ranges;
/*
--
-- Структура таблицы `cw_import_cache`
--

CREATE TABLE IF NOT EXISTS `cw_import_cache` (
  `data_type` varchar(3) NOT NULL DEFAULT '',
  `id` varchar(255) NOT NULL DEFAULT '',
  `value` varchar(255) NOT NULL DEFAULT '',
  `customer_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`data_type`,`id`,`customer_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `cw_import_layout`
--

CREATE TABLE IF NOT EXISTS `cw_import_layout` (
  `layout_id` int(11) NOT NULL AUTO_INCREMENT,
  `type` char(1) NOT NULL DEFAULT '',
  `title` varchar(32) NOT NULL DEFAULT '',
  `ignore_header` int(1) NOT NULL DEFAULT '0',
  `delimiter` varchar(4) NOT NULL DEFAULT '',
  PRIMARY KEY (`layout_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=34 ;

-- --------------------------------------------------------

--
-- Структура таблицы `cw_import_layout_elements`
--

CREATE TABLE IF NOT EXISTS `cw_import_layout_elements` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `layout_id` int(11) NOT NULL DEFAULT '0',
  `field` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1807 ;

-- --------------------------------------------------------

--
-- Структура таблицы `cw_import_layout_supported`
--

CREATE TABLE IF NOT EXISTS `cw_import_layout_supported` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` char(1) NOT NULL DEFAULT '',
  `field` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=64 ;

*/

DROP TABLE `cw_import_cache`, `cw_import_layout`, `cw_import_layout_elements`, `cw_import_layout_supported`;

DELETE FROM `cw_addons` WHERE `cw_addons`.`addon` = 'seo_map';
DELETE FROM `cw_languages` WHERE `cw_languages`.`name` = 'addon_name_seo_map';
