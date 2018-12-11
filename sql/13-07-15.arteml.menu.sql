UPDATE cw_languages SET value = replace( value, ' options', ' Options' ) WHERE name LIKE 'option_title_%';
DELETE FROM `cw_languages` WHERE `cw_languages`.`name` = 'option_title_subscriptions';
DELETE FROM `cw_config_categories` WHERE `cw_config_categories`.`category`='subscription';
delete from cw_config where name in ('subscription_key','eml_recurring_notification');
REPLACE INTO `cw_languages` ( `code` , `name` , `value` , `topic`) VALUES ( 'en', 'option_title_price', 'Price Options', 'Option');
REPLACE INTO `cw_languages` ( `code` , `name` , `value` , `topic`) VALUES ( 'en', 'option_title_product_filter', 'Product Filter Options', 'Option');
DELETE FROM `cw_languages` WHERE `cw_languages`.`name` = 'option_title_Email_Note';

SELECT @c:=config_category_id FROM cw_config_categories WHERE `cw_config_categories`.`category`='Email_Note';
SELECT @c2:=config_category_id FROM cw_config_categories WHERE `cw_config_categories`.`category`='Email';
DELETE FROM `cw_config_categories` WHERE `cw_config_categories`.`category`='Email_Note';
UPDATE cw_config SET orderby = orderby+200 where config_category_id=@c;
UPDATE cw_config SET config_category_id=@c2 WHERE config_category_id=@c;

UPDATE `cw_languages` SET `value` = 'Recommended Products Options' WHERE `cw_languages`.`name` = 'option_title_recommended_products';

UPDATE `cw_config_categories` SET `category` = 'Images' WHERE `cw_config_categories`.`category`='Smarty_plugins';

DELETE FROM `cw_config_categories` WHERE `cw_config_categories`.`category`='Warehouse';

UPDATE `cw_languages` SET `value` = 'Payment Methods' WHERE `cw_languages`.`code` = 'EN' AND `cw_languages`.`name` = 'lbl_payment_methods';
UPDATE `cw_languages` SET `value` = 'Add Payment Method' WHERE `cw_languages`.`code` = 'EN' AND `cw_languages`.`name` = 'lbl_payment_method_add';

REPLACE INTO `cw_languages` ( `code` , `name` , `value` , `topic`) VALUES ( 'en', 'lbl_seo_setting','SEO Settings','Labels');

UPDATE cw_navigation_tabs set title='lbl_seo_setting' where title='lbl_meta_tags';

DELETE FROM `cw_navigation_menu` WHERE title='lbl_dashboard';

update cw_languages set topic='Labels' where topic='Label';
update cw_languages set topic='Options' where topic='Option';

update cw_navigation_menu set parent_menu_id = 6  where title='lbl_countries_and_states';
