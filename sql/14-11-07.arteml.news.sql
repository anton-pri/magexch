INSERT IGNORE INTO `cw_languages` ( `code` , `name` , `value` , `tooltip` , `topic`) VALUES ( 'en', 'lbl_email_already_used', 'This email is already in use', '', 'Labels');
ALTER TABLE `cw_newslists` ADD `usertype` CHAR( 1 ) NOT NULL DEFAULT 'C';
