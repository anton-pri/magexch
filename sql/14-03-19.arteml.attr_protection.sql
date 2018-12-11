ALTER TABLE `cw_attributes` ADD `protection` SMALLINT UNSIGNED NOT NULL DEFAULT '0' COMMENT 'protection binary flags' AFTER `facet` ;
-- By default protect name, type and values. Also attr cannot be deleted
update cw_attributes set protection=protection|15 where addon!='';

update cw_languages set name='err_protected_attribute' where name='err_addon_attribute';
