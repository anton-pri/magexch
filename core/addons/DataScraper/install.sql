-- Addon
REPLACE INTO `cw_addons` (`addon`, `descr`, `active`, `status`, `parent`, `version`, `orderby`) VALUES ('DataScraper', 'Allows scraping data from other sites', 1, 1, '', '0.1', 0);

-- configuration options
REPLACE INTO `cw_config_categories` (`config_category_id` ,`category` ,`is_local`) VALUES (NULL , 'DataScraper', '0');
SET @config_category_id = LAST_INSERT_ID();

-- Addon name/description
REPLACE INTO cw_languages SET code='EN', `name`='addon_descr_DataScraper', `value`='Allows scraping data from other sites', topic='Addons';
REPLACE INTO cw_languages SET code='EN', `name`='addon_name_DataScraper', `value`='Data Scraper', topic='Addons';
REPLACE INTO cw_languages SET code='EN', `name`='option_title_DataScraper', `value`='Data Scraper options', topic='Options';

-- Create necessary tables
CREATE TABLE IF NOT EXISTS `cw_datascraper_sites_config` (
  `siteid` int(5) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `test_url` text NOT NULL,
  `pages` varchar(5) NOT NULL DEFAULT '',
  `minute` varchar(5) NOT NULL DEFAULT '',
  `parsed` int(2) NOT NULL DEFAULT '0',
  `active` int(2) NOT NULL DEFAULT '0',
  `wget_path_list` varchar(255) NOT NULL DEFAULT '',
  `wget_run_hrs` int(11) NOT NULL DEFAULT '23',
  `wget_run_day` int(11) NOT NULL DEFAULT '1',
  `parsing_active` int(2) NOT NULL DEFAULT '0',
  PRIMARY KEY (`siteid`)
) ENGINE=MyISAM;


-- Navigation menu
SELECT @m_id:=menu_id FROM cw_navigation_menu WHERE title='lbl_settings' AND area='A' LIMIT 1;
-- insert new entry to menu
DELETE FROM cw_menu WHERE title='lbl_DataScraper';
INSERT INTO `cw_menu` (`menu_id`, `parent_menu_id`, `title`, `link`, `target`, `orderby`, `area`, `access_level`, `func_visible`, `addon`, `skins_subdir`, `is_loggedin`) VALUES
(null, @m_id, 'lbl_DataScraper', 'index.php?target=datascraper_sites', 'datascraper_sites', 1000, 'A', '', '', 'DataScraper', '', 1);

-- Langvars
REPLACE INTO `cw_languages` (`code`, `name`, `value`, `topic`) VALUES ('EN', 'lbl_DataScraper', 'Data Scraper', 'Labels');
