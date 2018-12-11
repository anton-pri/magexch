delete from cw_breadcrumbs where link like '%user_S%';
INSERT INTO `cw_breadcrumbs` (`breadcrumb_id`, `link`, `title`, `parent_id`, `addon`, `uniting`) VALUES(null, '/index.php?target=user_S', 'lbl_suppliers', 1, '', 0);
set @bid=LAST_INSERT_ID();
INSERT INTO `cw_breadcrumbs` (`breadcrumb_id`, `link`, `title`, `parent_id`, `addon`, `uniting`) VALUES(null, '/index.php?target=user_S&mode=add', 'lbl_user_create_S', @bid, '', 0);
INSERT INTO `cw_breadcrumbs` (`breadcrumb_id`, `link`, `title`, `parent_id`, `addon`, `uniting`) VALUES(null, '/index.php?target=user_S&mode=modify&user=[[ANY]]', 'lbl_user_modify_S', @bid, '', 0);

update cw_attributes set protection = protection&(1+2+8) where field='predefined_ranges';
