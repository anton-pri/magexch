CREATE TABLE IF NOT EXISTS `cw_langvars_statistics` (
`name` VARCHAR( 128 ) NOT NULL ,
`code` VARCHAR( 2 ) NOT NULL ,
`counter` INT( 11 ) NOT NULL ,
`is_exists` SMALLINT( 1 ) NOT NULL ,
INDEX ( `name` ), 
UNIQUE ( `name` )
) ENGINE = MYISAM ;