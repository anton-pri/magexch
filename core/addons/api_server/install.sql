-- Addon
REPLACE INTO `cw_addons` (`addon`, `descr`, `active`, `status`, `parent`, `version`, `orderby`) VALUES ('api_server', 'API.Server', 1, 0, '', '1', 0);


-- Addon name/description
REPLACE INTO cw_languages SET code='EN', name='addon_descr_api_server', value='API Server provides common features to build own API', topic='Addons';
REPLACE INTO cw_languages SET code='EN', name='addon_name_api_server', value='API.Server', topic='Addons';
REPLACE INTO cw_languages SET code='EN', name='option_title_api_server', value='API Server options', topic='Options';

-- Langvars
REPLACE INTO `cw_languages` (`code`, `name`, `value`, `topic`) VALUES ('EN', 'lbl_api_server', 'API Server', 'Labels');
