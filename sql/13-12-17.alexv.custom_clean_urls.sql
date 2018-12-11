INSERT INTO `cw_languages` (`code`, `name`, `value`, `tooltip`, `topic`) VALUES ('EN', 'attribute_name_av', 'Attributes values', '', 'Labels');
INSERT INTO `cw_languages` (`code`, `name`, `value`, `tooltip`, `topic`) VALUES ('EN', 'lbl_custom_facet_url', 'Custom facet url', '', 'Labels');
INSERT INTO `cw_languages` (`code`, `name`, `value`, `tooltip`, `topic`) VALUES ('EN', 'lbl_add_custom_facet_url', 'Add custom facet url', '', 'Labels');
INSERT INTO `cw_languages` (`code`, `name`, `value`, `tooltip`, `topic`) VALUES ('EN', 'lbl_custom_facet_urls', 'Custom facet urls', '', 'Labels');
INSERT INTO `cw_languages` (`code`, `name`, `value`, `tooltip`, `topic`) VALUES ('EN', 'lbl_attributes_options', 'Attributes options', '', 'Labels');
INSERT INTO `cw_languages` (`code`, `name`, `value`, `tooltip`, `topic`) VALUES ('EN', 'lbl_custom_clean_url', 'Custom clean url', '', 'Labels');
INSERT INTO `cw_languages` (`code`, `name`, `value`, `tooltip`, `topic`) VALUES ('EN', 'lbl_clean_urls_combination', 'Clean urls combination', '', 'Labels');
INSERT INTO `cw_languages` (`code`, `name`, `value`, `tooltip`, `topic`) VALUES ('EN', 'lbl_filter_options', 'Filter options', '', 'Labels');
INSERT INTO `cw_languages` (`code`, `name`, `value`, `tooltip`, `topic`) VALUES ('EN', 'lbl_error_param_unique', 'The "{{paramname}}" must be unique', '', 'Labels');
INSERT INTO `cw_languages` (`code`, `name`, `value`, `tooltip`, `topic`) VALUES ('EN', 'txt_select_attributes_options', 'Please select an attributes options', '', 'Text');
INSERT INTO `cw_languages` (`code`, `name`, `value`, `tooltip`, `topic`) VALUES ('EN', 'txt_fill_custom_clean_url_field', 'Please fill in the "Custom clean url" field', '', 'Text');
INSERT INTO `cw_languages` (`code`, `name`, `value`, `tooltip`, `topic`) VALUES ('EN', 'txt_custom_facet_url_updated', 'The custom facet url have been successfully updated.', '', 'Text');
INSERT INTO `cw_languages` (`code`, `name`, `value`, `tooltip`, `topic`) VALUES ('EN', 'txt_custom_facet_url_added', 'The new custom facet url have been successfully added.', '', 'Text');
INSERT INTO `cw_languages` (`code`, `name`, `value`, `tooltip`, `topic`) VALUES ('EN', 'txt_no_custom_facet_urls', 'There is now custom facet urls.', '', 'Text');
INSERT INTO `cw_languages` (`code`, `name`, `value`, `tooltip`, `topic`) VALUES ('EN', 'txt_selected_custom_facet_urls_deleted', 'The selected custom facet urls have been deleted', '', 'Text');

INSERT INTO `cw_navigation_tabs` (`access_level`, `title`, `link`, `orderby`) VALUES (2800, 'lbl_custom_facet_urls', 'index.php?target=custom_facet_urls', 20);
SET @tab_id = LAST_INSERT_ID();

SELECT @section_id:=section_id FROM cw_navigation_sections WHERE link = 'index.php?target=meta_tags' AND area = 'A';

INSERT INTO `cw_navigation_targets` (`target`, `params`, `visible`, `section_id`, `tab_id`, `orderby`, `addon`) VALUES ('custom_facet_urls', '', '', @section_id, @tab_id, 20, 'clean_urls');
SET @target_id = LAST_INSERT_ID();

SELECT @config_category_id:=config_category_id FROM cw_config_categories WHERE category = 'SEO' AND is_local = 0;

INSERT INTO `cw_navigation_settings` (`target_id` ,`config_category_id`) VALUES (@target_id, @config_category_id);

CREATE TABLE `cw_clean_urls_custom_facet_urls` (
`url_id` INT( 11 ) NOT NULL AUTO_INCREMENT ,
`custom_facet_url` VARCHAR( 255 ) NOT NULL ,
`clean_urls` VARCHAR( 255 ) NOT NULL ,
`attribute_value_ids` VARCHAR( 255 ) NOT NULL ,
PRIMARY KEY ( `url_id` ) ,
INDEX ( `url_id` )
) ENGINE = MYISAM ;