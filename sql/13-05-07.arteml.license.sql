REPLACE INTO `cw_config` ( `name` , `comment` , `value` , `config_category_id` , `orderby` , `type` , `defvalue` , `variants`) VALUES ( 'license_check_result', '', '', '1', '0', 'text', '', '');

REPLACE INTO `cw_languages` (`code`, `name`, `value`, `topic`) VALUES ('EN', 'lbl_license', 'License', 'Labels');

DELETE FROM cw_navigation_tabs WHERE title='lbl_license';
DELETE FROM cw_navigation_targets WHERE target='license';

INSERT INTO `cw_navigation_tabs` (`tab_id`, `access_level`, `title`, `link`, `orderby`) VALUES(null, '2501', 'lbl_license', 'index.php?target=license', 50);
SET @tab_id=LAST_INSERT_ID();
INSERT INTO `cw_navigation_targets` (`target_id`, `target`, `params`, `visible`, `section_id`, `tab_id`, `orderby`, `module`) VALUES(null, 'license', '', '', 95, @tab_id, 0, '');

