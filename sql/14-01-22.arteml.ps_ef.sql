ALTER TABLE `cw_ps_cond_details` ADD `param1` VARCHAR( 128 ) NULL , ADD `param2` VARCHAR( 128 ) NULL ;

INSERT INTO `cw_languages` ( `code` , `name` , `value` , `tooltip` , `topic`) VALUES ( 'en', 'txt_ps_alphabetical_order', 'alphabetical comparison', 'If values are not numeric, they will be treated as string and will be compared in alphabetical order. E.g. "A" < "B", but "1 GB" < "10 GB" < "2 GB"', 'Text');

INSERT INTO `cw_languages` ( `code` , `name` , `value` , `tooltip` , `topic`) VALUES ( 'en', 'lbl_discount_bundles', 'Discount Bundles', '', 'Labels'); 

-- get menu_id for navigation menu
SELECT @sections:=menu_id FROM cw_navigation_menu WHERE title='lbl_catalog' AND parent_menu_id=0 AND area='A' LIMIT 1;

-- insert new entry to menu
DELETE FROM cw_navigation_menu WHERE title='lbl_discount_bundles';
INSERT INTO `cw_navigation_menu` (`menu_id`, `parent_menu_id`, `title`, `link`, `orderby`, `area`, `access_level`, `addon`, `is_loggedin`) VALUES (NULL, @sections, 'lbl_discount_bundles', 'index.php?target=discount_bundles', 490, 'A', '', 'promotion_suite', 1);


