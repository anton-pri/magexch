CREATE TABLE IF NOT EXISTS `cw_dod_bonuses` (
  `bonus_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `generator_id` int(11) unsigned NOT NULL,
  `type` char(1) NOT NULL,
  `apply` tinyint(1) unsigned NOT NULL,
  `coupon` varchar(16) NOT NULL,
  `discount` decimal(12,2) unsigned NOT NULL,
  `disctype` tinyint(1) unsigned NOT NULL,
  PRIMARY KEY (`bonus_id`)
);

CREATE TABLE IF NOT EXISTS `cw_dod_bonus_details` (
  `bd_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `bonus_id` int(11) unsigned NOT NULL,
  `generator_id` int(11) unsigned NOT NULL,
  `object_id` int(11) unsigned NOT NULL,
  `quantity` int(11) unsigned NOT NULL,
  `object_type` tinyint(1) unsigned NOT NULL,
  PRIMARY KEY (`bd_id`),
  KEY `bonus_id` (`bonus_id`)
);

CREATE TABLE IF NOT EXISTS `cw_dod_generators` (
  `generator_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `description` mediumtext NOT NULL,
  `startdate` int(11) unsigned NOT NULL,
  `enddate` int(11) unsigned NOT NULL,
  `position` int(11) NOT NULL,
  `active` tinyint(1) unsigned NOT NULL,
  `pid` int(11) NOT NULL DEFAULT '0' COMMENT 'offer attached to product',
  `auto` tinyint(4) NOT NULL DEFAULT '0' COMMENT 'auto generated offer',
  `repeatable` int(11) NOT NULL DEFAULT '1' COMMENT 'times for repeatable generators',
  PRIMARY KEY (`generator_id`),
  KEY `pid` (`pid`)
);

alter table cw_dod_generators add column dod_interval int(11) not null default 0;
alter table cw_dod_generators add column no_item_repeat int(1) not null default 0;
alter table cw_dod_generators add column dod_interval_type char(1) not null default 'D' after dod_interval;

alter table cw_dod_generators add column current_offer_id int(11) not null default 0;
alter table cw_dod_generators add column current_offer_date int(11) not null default 0;
alter table cw_dod_generators add column used_pids text not null default '';

alter table cw_dod_bonuses add column unused int(1) not null default 0;

alter table cw_dod_bonus_details add column param1 varchar(128) not null default '';
alter table cw_dod_bonus_details add column param2 varchar(128) not null default '';
