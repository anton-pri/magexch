SELECT @cid:=config_category_id FROM cw_config_categories WHERE category='shipping_ups';
UPDATE `cw_config` SET `name` = 'ups_sep4' WHERE `cw_config`.`name` = 'sep4' AND `cw_config`.`config_category_id` = @cid;
UPDATE `cw_config` SET `name` = 'ups_sep3' WHERE `cw_config`.`name` = 'sep3' AND `cw_config`.`config_category_id` = @cid;
UPDATE `cw_config` SET `name` = 'ups_sep2' WHERE `cw_config`.`name` = 'sep2' AND `cw_config`.`config_category_id` = @cid;
UPDATE `cw_config` SET `name` = 'ups_sep1' WHERE `cw_config`.`name` = 'sep1' AND `cw_config`.`config_category_id` = @cid;
UPDATE `cw_config` SET `name` = 'ups_sep' WHERE `cw_config`.`name` = 'sep' AND `cw_config`.`config_category_id` = @cid;
DELETE FROM cw_config WHERE `config_category_id` = @cid  and name IN ('handling_charge_flat','handling_charge_currency','handling_charge_percent','ups_sep3','ups_sep2','ups_sep4');
