DELETE FROM cw_addons WHERE addon='static_home_page';
INSERT INTO `cw_addons` (`addon`, `descr`, `active`, `status`, `parent`,`version`,`orderby`)
VALUES ('static_home_page', 'Allows to create static copy of home page, suits only skins with no cart info at the home page', 0, 1, '', '0.1',0);
