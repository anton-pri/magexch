ALTER TABLE `cw_products` DROP `list_price`;

select @cid:=config_category_id from cw_config_categories where category='Product_Filter';
INSERT INTO `cw_config` ( `name` , `comment` , `value` , `config_category_id` , `orderby` , `type` , `defvalue` , `variants`) VALUES ( 'show_from_price', 'Display min price for options in PF menu', 'Y', @cid, '10', 'checkbox', 'Y', '');
