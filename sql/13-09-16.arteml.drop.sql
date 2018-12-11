drop table IF EXISTS cw_shipping_cache;
drop table IF EXISTS cw_users_online;

/*
--
-- Структура таблицы `cw_shipping_cache`
--

CREATE TABLE IF NOT EXISTS `cw_shipping_cache` (
  `md5_request` varchar(32) NOT NULL DEFAULT '',
  `session_id` varchar(32) NOT NULL DEFAULT '',
  `response` mediumtext NOT NULL,
  `expiration_date` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`md5_request`,`session_id`),
  KEY `expire` (`session_id`,`expiration_date`),
  KEY `expiration_date` (`expiration_date`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `cw_users_online`
--

CREATE TABLE IF NOT EXISTS `cw_users_online` (
  `sess_id` varchar(40) NOT NULL DEFAULT '',
  `usertype` char(1) NOT NULL DEFAULT '',
  `is_registered` char(1) NOT NULL DEFAULT '',
  `expiry` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`sess_id`),
  KEY `usertype` (`usertype`),
  KEY `iu` (`is_registered`,`usertype`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
*/
