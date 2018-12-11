-- manufacturer URL should not have default values
SELECT @aid:=attribute_id FROM cw_attributes where addon='manufacturers' and field='manufacturer_web';
delete FROM `cw_attributes_default` WHERE `attribute_id` =@aid;

-- tooltip for subscription by membership
UPDATE `cw_languages` SET `tooltip` = 'You may be subscribed to news list of your membership' WHERE `cw_languages`.`name` = 'lbl_by_membership';

-- PayPal Express addon name
UPDATE `cw_languages` SET `value` = 'PayPal Express' WHERE `cw_languages`.`name` = 'addon_name_paypal_express';

REPLACE INTO `cw_languages` ( `code` , `name` , `value` , `tooltip` , `topic`) VALUES ( 'en', 'addon_name_addons_manager', 'Addons Manager', '', 'Addons');

DELETE FROM `cw_addons` WHERE `cw_addons`.`addon` = 'redirect_after_login';
