
select @cid:=config_category_id from cw_config_categories where category='paypal_express';

REPLACE INTO `cw_config` ( `name` , `comment` , `value` , `config_category_id` , `orderby` , `type` , `defvalue` , `variants`) VALUES ( 'paypal_express_email', 'E-Mail address to receive PayPal payment', '',@cid, '0', 'text', '', '');
update cw_config set orderby = (orderby+1)*10 where config_category_id = @cid;

UPDATE `cw_config` SET `orderby` = '2' WHERE `cw_config`.`name` = 'test_mode' AND `cw_config`.`config_category_id` =@cid;
UPDATE `cw_config` SET `orderby` = '6' WHERE `cw_config`.`name` = 'prefix' AND `cw_config`.`config_category_id` =@cid;
UPDATE `cw_config` SET `orderby` = '4' WHERE `cw_config`.`name` = 'currency' AND `cw_config`.`config_category_id` =@cid;
replace INTO `cw_config` ( `name` , `comment` , `value` , `config_category_id` , `orderby` , `type` , `defvalue` , `variants`) VALUES ( 'pp_api_sep', 'PayPal Pro API credentials', '', @cid, '15', 'separator', '', '');
