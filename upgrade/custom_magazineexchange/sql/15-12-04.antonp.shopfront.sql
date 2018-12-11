SELECT @mid:=menu_id FROM cw_navigation_menu WHERE area='V' and (title='lbl_mag_magazineexchange') LIMIT 1;
INSERT INTO `cw_navigation_menu` ( `menu_id` , `parent_menu_id` , `title` , `link` , `orderby` , `area` , `access_level` , `addon` , `is_loggedin`) VALUES (NULL ,@mid, 'lbl_mag_my_shopfront', 'index.php?target=seller_shopfront', '50', 'V', '', 'custom_magazineexchange', '1');

REPLACE INTO `cw_languages` (`code`, `name`, `value`, `topic`) VALUES ('EN', 'lbl_mag_my_shopfront', 'My Shopfront', 'Labels');

