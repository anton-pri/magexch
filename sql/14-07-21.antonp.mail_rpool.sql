CREATE TABLE IF NOT EXISTS `cw_mail_rpool` (
  `mail_id` int(11) NOT NULL AUTO_INCREMENT,
  `mail_from` varchar(128) NOT NULL DEFAULT '',
  `mail_to` varchar(128) NOT NULL DEFAULT '',
  `subject` mediumtext NOT NULL,
  `body` mediumtext NOT NULL,
  `header` mediumtext NOT NULL,
  PRIMARY KEY (`mail_id`)
);
