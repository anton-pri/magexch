REPLACE INTO `cw_addons` (`addon`, `descr`, `active`, `status`, `parent`,`version`) 
	VALUES ('wordpress', 'Connector with wordpress for layout integration', 1, 0, 'printdrop_com', '0.1');

REPLACE INTO `cw_languages` (`code`, `name`, `value`, `topic`) 
	VALUES ('EN', 'addon_name_wordpress', 'Wordpress', 'Addons');
