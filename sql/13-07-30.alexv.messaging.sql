DELETE FROM cw_addons WHERE addon='messaging_system';
REPLACE INTO `cw_addons` (`addon`, `descr`, `active`, `status`, `parent`,`version`,`orderby`)
VALUES ('messaging_system', 'Messaging system. Allows users to communicate in the system.', 1, 0, '', '1.0',0);

UPDATE `cw_addons` SET `descr` = 'Allow to generate different barcodes' WHERE `cw_addons`.`addon` = 'barcode' LIMIT 1;

INSERT INTO `cw_sections_pos` (`section`, `orderby`, `location`, `addon`) VALUES ('message_box', '120', 'L', 'messaging_system');

INSERT INTO `cw_languages` (`code`, `name`, `value`, `topic`) VALUES ('EN', 'addon_name_messaging_system', 'Messaging system', 'Addons');
INSERT INTO `cw_languages` (`code`, `name`, `value`, `topic`) VALUES ('EN', 'lbl_message_box', 'Message box', 'Labels');
INSERT INTO `cw_languages` (`code`, `name`, `value`, `topic`) VALUES ('EN', 'lbl_archive', 'Archive', 'Labels');
INSERT INTO `cw_languages` (`code`, `name`, `value`, `topic`) VALUES ('EN', 'lbl_new_message', 'New message', 'Labels');
INSERT INTO `cw_languages` (`code`, `name`, `value`, `topic`) VALUES ('EN', 'lbl_sender', 'Sender', 'Labels');
INSERT INTO `cw_languages` (`code`, `name`, `value`, `topic`) VALUES ('EN', 'txt_recipient_not_found', 'Recipient is not found', 'Text');
INSERT INTO `cw_languages` (`code`, `name`, `value`, `topic`) VALUES ('EN', 'lbl_sending_date', 'Sending date', 'Labels');
INSERT INTO `cw_languages` (`code`, `name`, `value`, `topic`) VALUES ('EN', 'lbl_read_status', 'Read status', 'Labels');
INSERT INTO `cw_languages` (`code`, `name`, `value`, `topic`) VALUES ('EN', 'lbl_do_not_read', 'Do not read', 'Labels');
INSERT INTO `cw_languages` (`code`, `name`, `value`, `topic`) VALUES ('EN', 'lbl_read', 'Read', 'Labels');
INSERT INTO `cw_languages` (`code`, `name`, `value`, `topic`) VALUES ('EN', 'lbl_also_in_this_conversation', 'Also in this conversation', 'Labels');
INSERT INTO `cw_languages` (`code`, `name`, `value`, `topic`) VALUES ('EN', 'lbl_mark_messages_read_unread', 'Mark as read/unread', 'Labels');
INSERT INTO `cw_languages` (`code`, `name`, `value`, `topic`) VALUES ('EN', 'lbl_sent_to_archive', 'Sent to Archive', 'Labels');
INSERT INTO `cw_languages` (`code`, `name`, `value`, `topic`) VALUES ('EN', 'lbl_reply_to', 'Reply-To', 'Labels');
INSERT INTO `cw_languages` (`code`, `name`, `value`, `topic`) VALUES ('EN', 'lbl_contact_this_person', 'Contact this person', 'Labels');
INSERT INTO `cw_languages` (`code`, `name`, `value`, `topic`) VALUES ('EN', 'lbl_recipient_status', 'Recipient status', 'Labels');

DROP TABLE IF EXISTS `cw_messages`;
CREATE TABLE `cw_messages` (
`message_id` INT( 11 ) NOT NULL AUTO_INCREMENT ,
`subject` VARCHAR( 255 ) NOT NULL ,
`body` MEDIUMTEXT NOT NULL ,
`sender_id` INT( 11 ) NOT NULL ,
`recipient_id` INT( 11 ) NOT NULL ,
`sending_date` INT( 11 ) NOT NULL ,
`read_status` SMALLINT( 1 ) NOT NULL DEFAULT '0' ,
`conversation_id` INT( 11 ) NOT NULL ,
`link_id` INT( 11 ) NOT NULL COMMENT 'link between sent and incoming message copies',
`type` SMALLINT( 1 ) NOT NULL DEFAULT '1' COMMENT '1-incoming,2-sent',
`is_archive` SMALLINT( 1 ) NOT NULL DEFAULT '0',
PRIMARY KEY ( `message_id` )
) ENGINE = MYISAM ;


SELECT @sections:=menu_id FROM cw_navigation_menu WHERE title='lbl_tools' AND parent_menu_id=0 AND area='A' LIMIT 1;

DELETE FROM cw_navigation_menu WHERE title='lbl_messages';
INSERT INTO `cw_navigation_menu` (`menu_id`, `parent_menu_id`, `title`, `link`, `orderby`, `area`, `access_level`, `addon`, `is_loggedin`) 
VALUES (NULL, @sections, 'lbl_messages', 'index.php?target=message_box', 350, 'A', '', 'messaging_system', 1);

INSERT INTO `cw_navigation_sections` (`title`, `link`, `addon`, `access_level`, `area`, `main`, `orderby`, `skins_subdir`) 
VALUES ('lbl_avail_type_incoming', 'index.php?target=message_box', 'messaging_system', '', 'A', 'N', 10, '');
SET @section_id = LAST_INSERT_ID();

INSERT INTO `cw_navigation_tabs` (`access_level`, `title`, `link`, `orderby`) 
VALUES ('', 'lbl_new_message', 'index.php?target=message_box&mode=new', 0);
SET @tab_id = LAST_INSERT_ID();

INSERT INTO `cw_navigation_targets` (`target`, `params`, `visible`, `section_id`, `tab_id`, `orderby`, `addon`) 
VALUES ('message_box', '$_GET[\'mode\']==\'new\' || $_POST[\'mode\']==\'new\'', '', @section_id, @tab_id, 0, 'messaging_system');

INSERT INTO `cw_navigation_tabs` (`access_level`, `title`, `link`, `orderby`) 
VALUES ('', 'lbl_avail_type_incoming', 'index.php?target=message_box', 10);
SET @tab_id = LAST_INSERT_ID();

INSERT INTO `cw_navigation_targets` (`target`, `params`, `visible`, `section_id`, `tab_id`, `orderby`, `addon`) 
VALUES ('message_box', '($_GET[\'mode\']==\'\' && $_POST[\'mode\']==\'\') || $_POST[\'mode\']==\'incoming\'', '', @section_id, @tab_id, 10, 'messaging_system');

INSERT INTO `cw_navigation_tabs` (`access_level`, `title`, `link`, `orderby`) 
VALUES ('', 'lbl_sent', 'index.php?target=message_box&mode=sent', 20);
SET @tab_id = LAST_INSERT_ID();

INSERT INTO `cw_navigation_targets` (`target`, `params`, `visible`, `section_id`, `tab_id`, `orderby`, `addon`) 
VALUES ('message_box', '$_GET[\'mode\']==\'sent\' || $_POST[\'mode\']==\'sent\'', '', @section_id, @tab_id, 20, 'messaging_system');

INSERT INTO `cw_navigation_tabs` (`access_level`, `title`, `link`, `orderby`) 
VALUES ('', 'lbl_archive', 'index.php?target=message_box&mode=archive', 30);
SET @tab_id = LAST_INSERT_ID();

INSERT INTO `cw_navigation_targets` (`target`, `params`, `visible`, `section_id`, `tab_id`, `orderby`, `addon`) 
VALUES ('message_box', '$_GET[\'mode\']==\'archive\' || $_POST[\'mode\']==\'archive\'', '', @section_id, @tab_id, 30, 'messaging_system');