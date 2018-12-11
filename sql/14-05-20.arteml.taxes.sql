ALTER TABLE `cw_taxes` CHANGE `active` `active` TINYINT( 1 ) NOT NULL DEFAULT '0';
ALTER TABLE `cw_taxes` CHANGE `price_includes_tax` `price_includes_tax` TINYINT( 1 ) NOT NULL DEFAULT '0';
ALTER TABLE `cw_taxes` CHANGE `display_including_tax` `display_including_tax` TINYINT( 1 ) NOT NULL DEFAULT '0';
