ALTER TABLE `cw_docs` CHANGE `prefix` `prefix` VARCHAR( 24 );
update cw_map_countries set display_states=0 where code='GB';

