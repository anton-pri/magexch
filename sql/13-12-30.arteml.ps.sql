ALTER TABLE `cw_ps_offers` CHANGE `position` `position` INT( 11 ) NOT NULL ;
ALTER TABLE `cw_ps_offers` CHANGE `priority` `priority` INT( 11 ) NOT NULL ;
ALTER TABLE `cw_ps_offers` CHANGE `repeat` `repeatable` INT( 11 ) NOT NULL DEFAULT '1' COMMENT 'times for repeatable offers';
ALTER TABLE `cw_ps_bonuses` CHANGE `discount` `discount` DECIMAL( 12, 2 ) UNSIGNED NOT NULL ;
