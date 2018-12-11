-- Addon
REPLACE INTO `cw_addons` (`addon`, `descr`, `active`, `status`, `parent`, `version`, `orderby`) VALUES ('webmaster', 'Webmaster tools', 1, 1, '', '0.1', 0);

SELECT @config_category_id:=config_category_id FROM cw_config_categories WHERE category='webmaster';
REPLACE INTO cw_config SET name='webmaster_flag_sep', comment='Webmaster mode control', value='', config_category_id=@config_category_id, orderby='0', type='separator', defvalue='', variants='';
REPLACE INTO cw_config SET name='webmaster_flag', comment="Webmaster mode", value="<span id='webmaster_status' href='index.php?target=webmaster&mode=status' class='onload'></span>", config_category_id=@config_category_id, orderby='10', type='link', defvalue='Y', variants='';

UPDATE cw_config SET orderby=100 WHERE name='robots';

-- Addon name/description
REPLACE INTO cw_languages SET code='EN', name='addon_descr_webmaster', value='Webmaster tools', topic='Addons';
REPLACE INTO cw_languages SET code='EN', name='addon_name_webmaster', value='Webmaster', topic='Addons';
REPLACE INTO cw_languages SET code='EN', name='option_title_webmaster', value='Webmaster options', topic='Options';

-- Langvars
REPLACE INTO `cw_languages` (`code`, `name`, `value`, `topic`) VALUES ('EN', 'lbl_webmaster', 'Webmaster', 'Labels');

