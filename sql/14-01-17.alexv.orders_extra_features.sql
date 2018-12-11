REPLACE INTO `cw_addons` (`addon`, `descr`, `active`, `status`, `parent`,`version`) VALUES ('orders_extra_features', 'Orders Extra Features', 1, 1, '', '0.1');
REPLACE INTO `cw_languages` (`code`, `name`, `value`, `topic`) VALUES ('EN', 'addon_name_orders_extra_features', 'Orders Extra Features', 'Addons');
REPLACE INTO `cw_languages` (`code`, `name`, `value`, `tooltip`, `topic`) VALUES ('EN', 'lbl_order_profit', 'Order profit', '', 'Labels');
REPLACE INTO `cw_languages` (`code`, `name`, `value`, `tooltip`, `topic`) VALUES ('EN', 'lbl_avg_price', 'Avg. price', '', 'Labels');
REPLACE INTO `cw_languages` (`code`, `name`, `value`, `tooltip`, `topic`) VALUES ('EN', 'lbl_avg_profit', 'Avg. profit', '', 'Labels');
REPLACE INTO `cw_languages` (`code`, `name`, `value`, `tooltip`, `topic`) VALUES ('EN', 'lbl_total_cost', 'Total cost', '', 'Labels');
REPLACE INTO `cw_languages` (`code`, `name`, `value`, `tooltip`, `topic`) VALUES ('EN', 'lbl_markup', 'Markup', '', 'Labels');
REPLACE INTO `cw_languages` (`code`, `name`, `value`, `tooltip`, `topic`) VALUES ('EN', 'lbl_averages', 'Averages', '', 'Labels');

REPLACE INTO `cw_breadcrumbs` (`breadcrumb_id`, `link`, `title`, `parent_id`, `addon`, `uniting`) VALUES (NULL, '/index.php?target=report_cost_history', 'lbl_profits_cost_history', '1', 'orders_extra_features', '1');
REPLACE INTO `cw_breadcrumbs` (`breadcrumb_id`, `link`, `title`, `parent_id`, `addon`, `uniting`) VALUES (NULL, '/index.php?target=profit_reports', 'lbl_profit_reports', '1', 'orders_extra_features', '1');

REPLACE INTO `cw_languages` (`code`, `name`, `value`, `tooltip`, `topic`) VALUES ('EN', 'lbl_profits_cost_history', 'Profits by cost history', '', 'Labels');
REPLACE INTO `cw_languages` (`code`, `name`, `value`, `tooltip`, `topic`) VALUES ('EN', 'lbl_profit_reports', 'Profit reports', '', 'Labels');

INSERT INTO `cw_navigation_tabs` (`access_level`, `title`, `link`, `orderby`) VALUES (1200, 'lbl_profits_cost_history', 'index.php?target=report_cost_history', 50);
SET @tab_id = LAST_INSERT_ID();

SELECT @section_id:=section_id FROM cw_navigation_sections WHERE link = 'index.php?target=docs_O' AND area = 'A';
INSERT INTO `cw_navigation_targets` (`target`, `params`, `visible`, `section_id`, `tab_id`, `orderby`, `addon`) VALUES ('report_cost_history', '', '', @section_id, @tab_id, 50, 'orders_extra_features');
SET @target_id = LAST_INSERT_ID();

SELECT @config_category_id:=config_category_id FROM cw_config_categories WHERE category = 'docs' AND is_local = 0;
INSERT INTO `cw_navigation_settings` (`target_id` ,`config_category_id`) VALUES (@target_id, @config_category_id);

INSERT INTO `cw_navigation_tabs` (`access_level`, `title`, `link`, `orderby`) VALUES (1200, 'lbl_profit_reports', 'index.php?target=profit_reports', 60);
SET @tab_id = LAST_INSERT_ID();
INSERT INTO `cw_navigation_targets` (`target`, `params`, `visible`, `section_id`, `tab_id`, `orderby`, `addon`) VALUES ('profit_reports', '', '', @section_id, @tab_id, 60, 'orders_extra_features');
SET @target_id = LAST_INSERT_ID();
INSERT INTO `cw_navigation_settings` (`target_id` ,`config_category_id`) VALUES (@target_id, @config_category_id);