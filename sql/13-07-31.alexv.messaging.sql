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
`conversation_customer_id` INT( 11 ) NOT NULL ,
`link_id` INT( 11 ) NOT NULL COMMENT 'link between sent and incoming message copies',
`type` SMALLINT( 1 ) NOT NULL DEFAULT '1' COMMENT '1-incoming,2-sent',
`is_archive` SMALLINT( 1 ) NOT NULL DEFAULT '0',
PRIMARY KEY ( `message_id` )
) ENGINE = MYISAM ;
