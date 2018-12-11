-- Addon
REPLACE INTO `cw_addons` (`addon`, `descr`, `active`, `status`, `parent`, `version`, `orderby`) VALUES ('addon_skeleton', 'Base for new addons', 1, 1, '', '0.1', 0);

-- configuration options
REPLACE INTO `cw_config_categories` (`config_category_id` ,`category` ,`is_local`) VALUES (NULL , 'addon_skeleton', '0');
SET @config_category_id = LAST_INSERT_ID();
REPLACE INTO cw_config SET `name`='skeleton_settings', `comment`='accessories list', `value`='', config_category_id=@config_category_id, orderby='0', `type`='separator', defvalue='', variants='';
REPLACE INTO cw_config SET `name`='addon_skeleton_flag', `comment`='Flag explanation', `value`='Y', config_category_id=@config_category_id, orderby='200', `type`='checkbox', defvalue='Y', variants='';

-- Addon name/description
REPLACE INTO cw_languages SET code='EN', `name`='addon_descr_addon_skeleton', `value`='Base for new addons', topic='Addons';
REPLACE INTO cw_languages SET code='EN', `name`='addon_name_addon_skeleton', `value`='Addon Skeleton', topic='Addons';
REPLACE INTO cw_languages SET code='EN', `name`='option_title_addon_skeleton', `value`='Skeleton options', topic='Options';

-- Create necessary tables
CREATE TABLE IF NOT EXISTS `cw_addon_skeleton` (
  `name` varchar(64) NOT NULL,
  `pos` smallint(6) NOT NULL,
  `id` tinyint(1) NOT NULL,
    PRIMARY id (id)
);


-- Navigation menu
SELECT @m_id:=menu_id FROM cw_navigation_menu WHERE title='lbl_settings' AND area='A' LIMIT 1;
-- insert new entry to menu
DELETE FROM cw_menu WHERE title='lbl_addon_skeleton';
INSERT INTO `cw_menu` (`menu_id`, `parent_menu_id`, `title`, `link`, `target`, `orderby`, `area`, `access_level`, `func_visible`, `addon`, `skins_subdir`, `is_loggedin`) VALUES
(null, @m_id, 'lbl_addon_skeleton', 'index.php?target=addon_main_target', 'addon_main_target', 1000, 'A', '', '', 'addon_skeleton', '', 1);



-- Langvars
REPLACE INTO `cw_languages` (`code`, `name`, `value`, `topic`) VALUES ('EN', 'lbl_addon_skeleton', 'Skeleton', 'Labels');

