update cw_available_images set default_image='default_image_70.gif' where default_image='';

update cw_config_categories SET is_local=0;
update cw_attributes_values SET value='' WHERE value='Array';

select @sid:=section_id from cw_navigation_sections where title='lbl_products' and area='A';

replace INTO `cw_navigation_menu` ( `menu_id` , `parent_menu_id` , `title` , `link` , `orderby` , `area` , `access_level` , `addon` , `is_loggedin`) VALUES ( NULL , '2', 'lbl_discount_coupons', 'index.php?target=coupons', '150', 'A', '1209', 'discount_coupons', '1');


delete from cw_navigation_tabs where tab_id in (SELECT tab_id from cw_navigation_targets where section_id=@sid);
delete from cw_navigation_targets where section_id=@sid;

REPLACE INTO `cw_navigation_tabs` (`tab_id`, `access_level`, `title`, `link`, `orderby`) VALUES(1024, '1200', 'lbl_categories', 'index.php?target=categories', 100);
REPLACE INTO `cw_navigation_tabs` (`tab_id`, `access_level`, `title`, `link`, `orderby`) VALUES(1025, '1201', 'lbl_manufacturers', 'index.php?target=manufacturers', 300);
REPLACE INTO `cw_navigation_tabs` (`tab_id`, `access_level`, `title`, `link`, `orderby`) VALUES(1029, '1202', 'lbl_warranties', 'index.php?target=warranties', 1100);
REPLACE INTO `cw_navigation_tabs` (`tab_id`, `access_level`, `title`, `link`, `orderby`) VALUES(1075, '120300', 'lbl_products', 'index.php?target=products', 200);
REPLACE INTO `cw_navigation_tabs` (`tab_id`, `access_level`, `title`, `link`, `orderby`) VALUES(1076, '120300', 'lbl_product_modify', 'index.php?target=products&mode=details&product_id={$product_id}', 210);
REPLACE INTO `cw_navigation_tabs` (`tab_id`, `access_level`, `title`, `link`, `orderby`) VALUES(1093, '1200', 'lbl_modify_category', '', 150);
REPLACE INTO `cw_navigation_tabs` (`tab_id`, `access_level`, `title`, `link`, `orderby`) VALUES(1095, '1200', 'lbl_delete_category', '', 110);
REPLACE INTO `cw_navigation_tabs` (`tab_id`, `access_level`, `title`, `link`, `orderby`) VALUES(1096, '1200', 'lbl_add_category', '', 160);
REPLACE INTO `cw_navigation_tabs` (`tab_id`, `access_level`, `title`, `link`, `orderby`) VALUES(1107, '__120300', 'lbl_add_product', 'index.php?target=products&mode=add', 220);
REPLACE INTO `cw_navigation_tabs` (`tab_id`, `access_level`, `title`, `link`, `orderby`) VALUES(1128, '1205', 'lbl_special_sections', 'index.php?target=special_sections', 800);
REPLACE INTO `cw_navigation_tabs` (`tab_id`, `access_level`, `title`, `link`, `orderby`) VALUES(1200, '1200', 'lbl_category_products', '', 120);
REPLACE INTO `cw_navigation_tabs` (`tab_id`, `access_level`, `title`, `link`, `orderby`) VALUES(1225, '1208', 'lbl_offers', 'index.php?target=offers', 900);
REPLACE INTO `cw_navigation_tabs` (`tab_id`, `access_level`, `title`, `link`, `orderby`) VALUES(1283, '31', 'lbl_feature_classes', 'index.php?target=attributes', 650);
REPLACE INTO `cw_navigation_tabs` (`tab_id`, `access_level`, `title`, `link`, `orderby`) VALUES(1284, '31', 'lbl_features', 'index.php?target=attributes&mode=att', 600);
REPLACE INTO `cw_navigation_tabs` (`tab_id`, `access_level`, `title`, `link`, `orderby`) VALUES(2288, '', 'lbl_filetypes', 'index.php?target=filetypes', 700);
REPLACE INTO `cw_navigation_tabs` (`tab_id`, `access_level`, `title`, `link`, `orderby`) VALUES(2322, '', 'lbl_pt_global', 'index.php?target=product_tabs', 400);
REPLACE INTO `cw_navigation_tabs` (`tab_id`, `access_level`, `title`, `link`, `orderby`) VALUES(2323, '', 'lbl_top_menu', 'index.php?target=top_menu', 1200);

REPLACE INTO `cw_navigation_targets` (`target_id`, `target`, `params`, `visible`, `section_id`, `tab_id`, `orderby`, `addon`) VALUES(2389, 'top_menu', '', '', 66, 2323, 0, '');
REPLACE INTO `cw_navigation_targets` (`target_id`, `target`, `params`, `visible`, `section_id`, `tab_id`, `orderby`, `addon`) VALUES(163, 'categories', '$_GET[''mode''] == ''add''', '$_GET[''mode''] == ''add''', 66, 1096, 100, '');
REPLACE INTO `cw_navigation_targets` (`target_id`, `target`, `params`, `visible`, `section_id`, `tab_id`, `orderby`, `addon`) VALUES(2351, 'filetypes', '', '', 66, 2288, 100, 'ppd');
REPLACE INTO `cw_navigation_targets` (`target_id`, `target`, `params`, `visible`, `section_id`, `tab_id`, `orderby`, `addon`) VALUES(160, 'categories', '$_GET[''mode'']==''edit'' && !empty($cat)', '$_GET[''mode'']==''edit'' && !empty($cat)', 66, 1093, 110, '');
REPLACE INTO `cw_navigation_targets` (`target_id`, `target`, `params`, `visible`, `section_id`, `tab_id`, `orderby`, `addon`) VALUES(262, 'categories', '$_GET[''mode'']==''products''', '$_GET[''mode'']==''products''', 66, 1200, 120, '');
REPLACE INTO `cw_navigation_targets` (`target_id`, `target`, `params`, `visible`, `section_id`, `tab_id`, `orderby`, `addon`) VALUES(88, 'categories', '', '', 66, 1024, 130, '');
REPLACE INTO `cw_navigation_targets` (`target_id`, `target`, `params`, `visible`, `section_id`, `tab_id`, `orderby`, `addon`) VALUES(89, 'manufacturers', '', '', 66, 1025, 170, 'manufacturers');
REPLACE INTO `cw_navigation_targets` (`target_id`, `target`, `params`, `visible`, `section_id`, `tab_id`, `orderby`, `addon`) VALUES(93, 'warranties', '', '', 66, 1029, 180, '');
REPLACE INTO `cw_navigation_targets` (`target_id`, `target`, `params`, `visible`, `section_id`, `tab_id`, `orderby`, `addon`) VALUES(143, 'products', '!empty($_GET[''product_id'']) && $_GET[''mode''] == ''details''', '!empty($_GET[''product_id'']) && $_GET[''mode''] == ''details''', 66, 1076, 220, '');
REPLACE INTO `cw_navigation_targets` (`target_id`, `target`, `params`, `visible`, `section_id`, `tab_id`, `orderby`, `addon`) VALUES(173, 'products', '$_GET[''mode''] == ''add''', '', 66, 1107, 230, '');
REPLACE INTO `cw_navigation_targets` (`target_id`, `target`, `params`, `visible`, `section_id`, `tab_id`, `orderby`, `addon`) VALUES(91, 'products', '', '', 66, 1075, 250, '');
REPLACE INTO `cw_navigation_targets` (`target_id`, `target`, `params`, `visible`, `section_id`, `tab_id`, `orderby`, `addon`) VALUES(2388, 'product_tabs', '', '', 66, 2322, 400, 'product_tabs');
REPLACE INTO `cw_navigation_targets` (`target_id`, `target`, `params`, `visible`, `section_id`, `tab_id`, `orderby`, `addon`) VALUES(192, 'special_sections', '', '', 66, 1128, 500, '');
REPLACE INTO `cw_navigation_targets` (`target_id`, `target`, `params`, `visible`, `section_id`, `tab_id`, `orderby`, `addon`) VALUES(288, 'offers', '', '', 66, 1225, 600, 'special_offers');
REPLACE INTO `cw_navigation_targets` (`target_id`, `target`, `params`, `visible`, `section_id`, `tab_id`, `orderby`, `addon`) VALUES(347, 'attributes', 'in_array($_GET[''mode''], array(''att'', ''add_att''))', '', 66, 1284, 800, '');
REPLACE INTO `cw_navigation_targets` (`target_id`, `target`, `params`, `visible`, `section_id`, `tab_id`, `orderby`, `addon`) VALUES(346, 'attributes', '', '', 66, 1283, 900, '');
REPLACE INTO `cw_navigation_targets` (`target_id`, `target`, `params`, `visible`, `section_id`, `tab_id`, `orderby`, `addon`) VALUES(162, 'categories', '$_GET[''mode''] == ''delete''', '$_GET[''mode''] == ''delete''', 66, 1095, 990, '');


