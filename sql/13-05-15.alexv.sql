INSERT INTO `cw_languages` (`code`, `name`, `value`, `topic`) VALUES ('EN', 'lbl_dropdown', 'Dropdown', 'Labels');
INSERT INTO `cw_languages` (`code`, `name`, `value`, `topic`) VALUES ('EN', 'lbl_list', 'List', 'Labels');
INSERT INTO `cw_languages` (`code`, `name`, `value`, `topic`) VALUES ('EN', 'lbl_side_section', 'Side section', 'Labels');

SELECT @config_category_id:=config_category_id FROM cw_config_categories WHERE category='Manufacturers' LIMIT 1;
INSERT INTO cw_config SET name='view_list_manufacturers', comment='Type of a list for manufacturers section', value='0', config_category_id = @config_category_id, orderby='30', type='selector', defvalue='0', variants='0:lbl_dropdown\n1:lbl_list';

DELETE FROM cw_config_categories WHERE category='ajax-add2cart';
INSERT INTO cw_config_categories (`config_category_id` ,`category` ,`is_main`) VALUES (NULL , 'ajax-add2cart', '0');
SET @config_category_id = LAST_INSERT_ID();
INSERT INTO cw_config SET name='place_where_display_minicart', comment='The place where need to display the minicart', value='0', config_category_id = @config_category_id, orderby='10', type='selector', defvalue='0', variants='0:lbl_side_section\n1:lbl_top';