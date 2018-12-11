UPDATE `cw_addons` SET `descr` = 'Clean urls for the products, categories, manufactirers and statis pages.' WHERE `cw_addons`.`addon` = 'clean_urls' LIMIT 1;

INSERT INTO `cw_languages` (`code`, `name`, `value`, `topic`) VALUES ('EN', 'lbl_clean_urls_list', 'Clean urls list', 'Labels');
INSERT INTO `cw_languages` (`code`, `name`, `value`, `topic`) VALUES ('EN', 'lbl_history_clean_urls_list', 'History clean urls list', 'Labels');
INSERT INTO `cw_languages` (`code`, `name`, `value`, `topic`) VALUES ('EN', 'lbl_from_dynamic_url', 'From URL (dynamic URL)', 'Labels');
INSERT INTO `cw_languages` (`code`, `name`, `value`, `topic`) VALUES ('EN', 'lbl_to_static_url', 'To URL (static URL)', 'Labels');
INSERT INTO `cw_languages` (`code`, `name`, `value`, `topic`) VALUES ('EN', 'lbl_name_of_entity', 'Name of entity', 'Labels');
INSERT INTO `cw_languages` (`code`, `name`, `value`, `topic`) VALUES ('EN', 'txt_new_redirect_rule_added', 'The new redirect rule has been added', 'Text');
INSERT INTO `cw_languages` (`code`, `name`, `value`, `topic`) VALUES ('EN', 'txt_error_new_redirect_rule_added', 'Warning! Unable to add a new rule', 'Text');
INSERT INTO `cw_languages` (`code`, `name`, `value`, `topic`) VALUES ('EN', 'txt_redirect_rule_deleted', 'Redirect rule has been deleted', 'Text');
INSERT INTO `cw_languages` (`code`, `name`, `value`, `topic`) VALUES ('EN', 'txt_item_delete_confirmation', 'Do you really want to delete this item?', 'Text');

INSERT INTO `cw_attributes` (`attribute_id`, `name`, `type`, `field`, `is_required`, `active`, `orderby`, `addon`, `item_type`, `is_sortable`, `is_comparable`, `is_show`, `pf_is_use`, `pf_orderby`, `pf_display_type`, `is_show_addon`) VALUES (NULL, 'Clean url', 'text', 'clean_url', '0', '1', '0', 'clean_urls', 'O', '0', '0', '0', '0', '0', '0', '0');

DELETE FROM cw_navigation_tabs WHERE link = 'index.php?target=clean_urls_list';
DELETE FROM cw_navigation_targets WHERE target = 'clean_urls_list' AND addon = 'clean_urls';
SELECT @section_id:=section_id FROM cw_navigation_sections WHERE link='index.php?target=meta_tags' LIMIT 1;
INSERT INTO cw_navigation_tabs (`access_level`, `title`, `link`, `orderby`) VALUES (2801 , 'lbl_clean_urls_list', 'index.php?target=clean_urls_list', 10);
SET @tab_id = LAST_INSERT_ID();
INSERT INTO cw_navigation_targets (`target`, `section_id`, `tab_id`, `orderby`, `addon`) VALUES ('clean_urls_list', @section_id, @tab_id, 10, 'clean_urls');