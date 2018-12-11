CREATE TABLE `cw_system_messages` (
  `code` varchar(64) NOT NULL,
  `date` int(10) unsigned NOT NULL,
  `hidden` tinyint(4) NOT NULL,
  `type` tinyint(4) NOT NULL,
  `severity` char(1) NOT NULL,
  `message` text NOT NULL,
  `data` blob NOT NULL,
  PRIMARY KEY (`code`),
  KEY `type` (`type`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='system messages, warnings, errors and awaitings'

-- get menu_id for General Settings
SELECT @genset:=menu_id FROM cw_menu WHERE title='lbl_settings' AND parent_menu_id=0 AND area='A' LIMIT 1;
-- insert new entry to menu
DELETE FROM cw_menu WHERE title='lbl_dashboard';
INSERT INTO `cw_menu` (`menu_id`, `parent_menu_id`, `title`, `link`, `target`, `orderby`, `area`, `access_level`, `func_visible`, `addon`, `skins_subdir`, `is_loggedin`) VALUES
(null, 0, 'lbl_dashboard', 'index.php?target=dashboard', 'dashboard', 20, 'A', '', '', 'dashboard', '', 1);

-- lang var
REPLACE INTO `cw_languages` (`code`, `name`, `value`, `topic`) VALUES ('EN', 'lbl_dashboard', 'Dashboard', 'Labels');

-- new addon record
REPLACE INTO `cw_addons` (`addon`, `descr`, `active`, `status`) VALUES ('dashboard', 'Admin dashboard', 1, 0);


