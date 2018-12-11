REPLACE INTO `cw_languages` (`code`, `name`, `value`, `tooltip`, `topic`) VALUES ('EN', 'lbl_restrict_clean_urls', 'Restrict to Clean URLs', '', 'Labels');
REPLACE INTO `cw_languages` (`code`, `name`, `value`, `tooltip`, `topic`) VALUES ('EN', 'lbl_valid_clean_url', 'Valid Clean URL', '', 'Labels');
REPLACE INTO `cw_languages` (`code`, `name`, `value`, `tooltip`, `topic`) VALUES ('EN', 'lbl_clean_url_not_exist', 'Clean URL not exists', '', 'Labels');

--
-- Table structure `cw_adb_categories`
--

CREATE TABLE IF NOT EXISTS `cw_adb_clean_urls` (
  `banner_id` int(11) NOT NULL DEFAULT '0',
  `valid_url` int(1) NOT NULL DEFAULT '0',
  `clean_url` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`banner_id`,`clean_url`),
  KEY `clean_url` (`clean_url`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------