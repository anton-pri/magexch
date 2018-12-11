delete from cw_languages where name IN ('lbl_register_field_sections_actions_log','lbl_section_actions_log');
drop table if exists cw_user_actions, cw_user_actions_log, cw_user_actions_rules;

/*
CREATE TABLE IF NOT EXISTS `cw_user_actions` (
  `action_id` int(11) NOT NULL AUTO_INCREMENT,
  `format` mediumtext NOT NULL,
  `title` varchar(64) NOT NULL DEFAULT '',
  PRIMARY KEY (`action_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `cw_user_actions_log` (
  `action_log_id` int(11) NOT NULL AUTO_INCREMENT,
  `customer_id` int(11) NOT NULL DEFAULT '0',
  `action_id` int(11) NOT NULL DEFAULT '0',
  `date` int(11) NOT NULL DEFAULT '0',
  `area` char(1) NOT NULL DEFAULT '',
  `params` mediumtext NOT NULL,
  PRIMARY KEY (`action_log_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `cw_user_actions_rules` (
  `rule_id` int(11) NOT NULL AUTO_INCREMENT,
  `action_id` int(11) NOT NULL DEFAULT '0',
  `target` varchar(32) NOT NULL DEFAULT '0',
  `area` char(1) NOT NULL DEFAULT '0',
  `params` mediumtext NOT NULL,
  `save` mediumtext NOT NULL,
  `post_correction` int(1) NOT NULL DEFAULT '0',
  `orderby` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`rule_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;
*/
