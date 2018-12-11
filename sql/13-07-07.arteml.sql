DROP TABLE `cw_configurations`, `cw_configuration_changes_log`;
delete from cw_config_categories where category='Feature_Comparison';

DELETE FROM `cw_config` WHERE `cw_config`.`name` = 'fcomparison_show_product_list';
DELETE FROM `cw_config` WHERE `cw_config`.`name` = 'fcomparison_max_product_list';
DELETE FROM `cw_config` WHERE `cw_config`.`name` = 'fcomparison_disp_product_limit';
DELETE FROM `cw_config` WHERE `cw_config`.`name` = 'fcomparison_comp_product_limit';
DELETE FROM `cw_config` WHERE `cw_config`.`name` = 'feature_image_width';

update cw_addons set status=0;

delete from cw_config_categories where category='Advanced_Statistics';


DELETE FROM `cw_config` WHERE `cw_config`.`name` = 'enable_shop_statistics';
DELETE FROM `cw_config` WHERE `cw_config`.`name` = 'enable_tracking_statistics';
