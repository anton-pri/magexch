REPLACE INTO `cw_languages` (`code`, `name`, `value`, `tooltip`, `topic`) VALUES ('EN', 'lbl_magazines', 'Magazines', '', 'Labels');
REPLACE INTO `cw_languages` (`code`, `name`, `value`, `tooltip`, `topic`) VALUES ('EN', 'lbl_magazine_reviews', 'Magazine reviews', '', 'Labels');
REPLACE INTO `cw_languages` (`code`, `name`, `value`, `tooltip`, `topic`) VALUES ('EN', 'addon_name_magazines', 'Magazines', '', 'Addons');
REPLACE INTO `cw_languages` (`code`, `name`, `value`, `tooltip`, `topic`) VALUES ('EN', 'lbl_update_selected', 'Update selected', '', 'Labels');
REPLACE INTO `cw_languages` (`code`, `name`, `value`, `tooltip`, `topic`) VALUES ('EN', 'err_field_please_fill', 'Please fill in the {{field}} fields', '', 'Errors');
REPLACE INTO `cw_languages` (`code`, `name`, `value`, `tooltip`, `topic`) VALUES ('EN', 'err_orders_field_number', 'Order field must be number', '', 'Errors');
REPLACE INTO `cw_languages` (`code`, `name`, `value`, `tooltip`, `topic`) VALUES ('EN', 'err_name_service_name_unique', 'Name and Service name fields must be unique', '', 'Errors');

REPLACE INTO `cw_addons` (`addon`, `descr`, `active`) VALUES ('magazines', 'Allows to display magazines reviews to customers.', '0');

INSERT INTO `cw_breadcrumbs` (`breadcrumb_id`, `link`, `title`, `parent_id`, `addon`, `uniting`) VALUES (NULL, '/index.php?target=magazines', 'lbl_magazines', '1', 'magazines', '0');

SELECT @section:=section_id FROM cw_navigation_sections WHERE title='lbl_products' AND area='A' LIMIT 1;
INSERT INTO `cw_navigation_tabs` (`tab_id`,  `title`, `link`, `orderby`) VALUES (NULL,  'lbl_magazines', 'index.php?target=magazines', '1300');

SET @tab_id = LAST_INSERT_ID();
INSERT INTO `cw_navigation_targets` ( `target`,`section_id`, `tab_id`, `orderby`, `addon`) VALUES ('magazines',  @section, @tab_id, '100', 'magazines');



CREATE TABLE IF NOT EXISTS `cw_magazines` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `display_name` varchar(255) NOT NULL,
  `service_name` varchar(255) NOT NULL,
  `order_by` int(11) NOT NULL,
  `active_flag` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `display_name` (`display_name`),
  UNIQUE KEY `service_name` (`service_name`)
) ENGINE=MyISAM;

