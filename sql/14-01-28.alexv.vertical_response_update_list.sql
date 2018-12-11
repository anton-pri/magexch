DROP TABLE IF EXISTS `cw_recurring_list_update`;

CREATE TABLE `cw_recurring_list_update` (
`id` INT( 11 ) NOT NULL AUTO_INCREMENT ,
`list_id` INT( 11 ) NOT NULL ,
`list_name` VARCHAR( 255 ) NOT NULL ,
`saved_search_id` INT( 11 ) NOT NULL ,
`active` TINYINT( 1 ) NOT NULL DEFAULT '0',
`created` TIMESTAMP NOT NULL ,
PRIMARY KEY ( `id` ) ,
INDEX ( `id` , `saved_search_id` )
) ENGINE = MYISAM ;

REPLACE INTO `cw_languages` (`code`, `name`, `value`, `tooltip`, `topic`) VALUES ('EN', 'lbl_recurring_list_update', 'Recurring VR list update', '', 'Labels');
REPLACE INTO `cw_languages` (`code`, `name`, `value`, `tooltip`, `topic`) VALUES ('EN', 'lbl_recurring_list_update_full', 'Recurring Vertical Response emails list update', '', 'Labels');
REPLACE INTO `cw_languages` (`code`, `name`, `value`, `tooltip`, `topic`) VALUES ('EN', 'txt_no_avail_items', 'There is no available items.', '', 'Text');
REPLACE INTO `cw_languages` (`code`, `name`, `value`, `tooltip`, `topic`) VALUES ('EN', 'lbl_email_list', 'Email list', '', 'Labels');
REPLACE INTO `cw_languages` (`code`, `name`, `value`, `tooltip`, `topic`) VALUES ('EN', 'lbl_saved_search', 'Saved search', '', 'Labels');
REPLACE INTO `cw_languages` (`code`, `name`, `value`, `tooltip`, `topic`) VALUES ('EN', 'lbl_new_profile', 'New profile', '', 'Labels');
REPLACE INTO `cw_languages` (`code`, `name`, `value`, `tooltip`, `topic`) VALUES ('EN', 'txt_recurring_list_profile_updated', 'The recurring emails list update profile have been successfully updated.', '', 'Text');
REPLACE INTO `cw_languages` (`code`, `name`, `value`, `tooltip`, `topic`) VALUES ('EN', 'txt_recurring_list_profile_added', 'The new recurring emails list update profile have been successfully added.', '', 'Text');
REPLACE INTO `cw_languages` (`code`, `name`, `value`, `tooltip`, `topic`) VALUES ('EN', 'txt_selected_recurring_list_profiles_deleted', 'The selected recurring emails list update profiles have been deleted', '', 'Text');
REPLACE INTO `cw_languages` (`code`, `name`, `value`, `tooltip`, `topic`) VALUES ('EN', 'err_profile_already_exists', 'The profile already exists', '', 'Text');

SELECT @menu_id:=menu_id FROM cw_navigation_menu WHERE title = 'lbl_tools' AND area = 'A';
INSERT INTO `cw_navigation_menu` (`parent_menu_id`, `title`, `link`, `orderby`, `area`, `access_level`, `addon`, `is_loggedin`) 
VALUES (@menu_id, 'lbl_recurring_list_update', 'index.php?target=recurring_vr_list_update', 360, 'A', '', 'vertical_response', 1);

REPLACE INTO `cw_breadcrumbs` (`link`, `title`, `parent_id`, `addon`, `uniting`) VALUES ('/index.php?target=recurring_vr_list_update', 'lbl_recurring_list_update_full', '1', 'vertical_response', '1');
