REPLACE INTO `cw_navigation_menu` (`menu_id`, `parent_menu_id`, `title`, `link`, `orderby`, `area`, `access_level`, `addon`, `is_loggedin`) VALUES (163, 8, 'lbl_maintenance', 'index.php?target=maintenance', 470, 'A', '', '', 1);
UPDATE cw_breadcrumbs SET title='lbl_maintenance' WHERE breadcrumb_id=360;
UPDATE cw_breadcrumbs SET parent_id=1 WHERE breadcrumb_id=360;
