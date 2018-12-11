-- Addon
REPLACE INTO `cw_addons` (`addon`, `descr`, `active`, `status`, `parent`, `version`, `orderby`) VALUES ('webmaster', 'Webmaster tools', 1, 1, '', '0.1', 0);

-- configuration options
SELECT @config_category_id:=config_category_id WHERE category='webmaster';
REPLACE INTO cw_config SET name='webmaster_flag_sep', comment='Webmaster mode control', value='', config_category_id=@config_category_id, orderby='0', type='separator', defvalue='', variants='';
REPLACE INTO cw_config SET name='webmaster_flag', comment="<span id='webmaster_status' href='index.php?target=webmaster&mode=status' class='onload'></span>", value='Y', config_category_id=@config_category_id, orderby='10', type='link', defvalue='Y', variants='';
REPLACE INTO cw_config SET name='webmaster_features_sep', comment='Webmaster features', value='', config_category_id=@config_category_id, orderby='100', type='separator', defvalue='', variants='';
REPLACE INTO cw_config SET name='webmaster_langvars', comment='Edit language variables', value='Y', config_category_id=@config_category_id, orderby='110', type='checkbox', defvalue='Y', variants='';

-- Addon name/description
REPLACE INTO cw_languages SET code='EN', name='addon_descr_webmaster', value='Webmaster tools', topic='Addons';
REPLACE INTO cw_languages SET code='EN', name='addon_name_webmaster', value='Webmaster', topic='Addons';
REPLACE INTO cw_languages SET code='EN', name='option_title_webmaster', value='Webmaster options', topic='Options';

-- Langvars
REPLACE INTO `cw_languages` (`code`, `name`, `value`, `topic`) VALUES ('EN', 'lbl_webmaster', 'Webmaster', 'Labels');

