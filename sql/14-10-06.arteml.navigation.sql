INSERT INTO `cw_navigation_sections` ( `section_id` , `title` , `link` , `addon` , `access_level` , `area` , `main` , `orderby` , `skins_subdir`) VALUES ( NULL , 'lbl_profit_reports', '', '', '18', 'A', 'Y', '90', 'orders');

SET @sid = LAST_INSERT_ID();

update cw_navigation_targets set section_id=@sid WHERE target IN ('report_cost_history','profit_reports');

select @mid:=menu_id from cw_navigation_menu where title='lbl_orders' and parent_menu_id = 0 and area = 'A';

INSERT INTO `cw_navigation_menu` ( `menu_id` , `parent_menu_id` , `title` , `link` , `orderby` , `area` , `access_level` , `addon` , `is_loggedin`) VALUES ( NULL , @mid, 'lbl_profit_reports', 'index.php?target=profit_reports', '100', 'A', '', 'orders_extra_features', '1');
