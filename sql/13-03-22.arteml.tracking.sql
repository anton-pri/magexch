-- Nav link typo
REPLACE INTO `ars_navigation_menu` VALUES (13,2,'lbl_invoices','index.php?target=docs_I',130,'A','','',1), (10,2,'lbl_orders','index.php?target=docs_O',100,'A','','',1);

DELETE FROM `ars_config` WHERE `name`='ppm_gateway_data';
DELETE FROM `ars_languages` WHERE `ars_languages`.`code` = 'EN' AND `ars_languages`.`name` = 'txt_how_setup_store_bottom';
DELETE FROM `ars_languages` WHERE `ars_languages`.`code` = 'EN' AND `ars_languages`.`name` = 'txt_salesman_orders_bottom';

INSERT INTO `ars_config_categories` (
`config_category_id` ,
`category` ,
`is_main`
)
VALUES (
NULL , 'Warehouse', '0'
);

SET @cat_id = LAST_INSERT_ID();

UPDATE ars_config SET config_category_id=@cat_id WHERE name IN ('send_notifications_to_warehouse','eml_lowlimit_warning_warehouse','eml_order_p_notif_warehouse','sep15');

