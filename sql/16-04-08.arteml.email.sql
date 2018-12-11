UPDATE cw_config SET variants='0:0\r\n\1:1\r\n2:2\r\n3:3\r\n4:4\r\n5:5\r\n6:6\r\n7:7\r\n8:8\r\n9:9\r\n10:10' WHERE name='ship_date' AND `type`='selector';
ALTER TABLE `cw_mail_spool` ADD `created` INT(11) NOT NULL COMMENT 'time mail created' AFTER `pdf_copy`; 
ALTER TABLE `cw_mail_spool` CHANGE `send` `send` INT NOT NULL DEFAULT '0' COMMENT 'time mail shall be sent';
UPDATE cw_mail_spool SET created = UNIX_TIMESTAMP(), send = UNIX_TIMESTAMP();
INSERT INTO `cw_languages` (`code`, `name`, `value`, `tooltip`, `topic`) VALUES ('en', 'txt_mail_spool_obsolete', 'This email is considered as obsolete because it was not sent during TTL period {{ttl}} hrs, please Delete it or Extend.', 'Possible reasons: crontab task is not configured; email SMTP server is not configured; wrong recipient email address ', 'Text'); 
