CREATE TABLE IF NOT EXISTS `cw_logs_settings` (
  `log_name` varchar(255) NOT NULL DEFAULT '',
  `email_notify` int(1) NOT NULL DEFAULT '0',
  `max_days` int(11) NOT NULL DEFAULT '14',
  `action_after_max_days` char(1) NOT NULL DEFAULT 'D',
  `active` int(1) NOT NULL DEFAULT '1',
  `backtrace_hashes` text NOT NULL,
  `email_notify_once` int(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`log_name`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
