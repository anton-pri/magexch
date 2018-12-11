# Anti_Fraud
SELECT @af:=config_category_id FROM cw_config_categories WHERE category='Anti_Fraud';
DELETE FROM `cw_config_categories` WHERE config_category_id=@af;
DELETE FROM `cw_config` WHERE config_category_id=@af;

# auto_update
SELECT @au:=config_category_id FROM cw_config_categories WHERE category='AutoUpdate';
DELETE FROM `cw_config_categories` WHERE config_category_id=@au;
DELETE FROM `cw_config` WHERE config_category_id=@au;

DELETE FROM `cw_config` WHERE `cw_config`.`name` = 'next_auto_update';

DELETE FROM `cw_addons` WHERE `cw_addons`.`addon` = 'auto_update';
