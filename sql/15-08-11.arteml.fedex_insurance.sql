select @cid:=config_category_id from cw_config_categories where category='shipping_fedex';
update cw_config set orderby=orderby*10 where config_category_id=@cid;
INSERT INTO `cw_config` ( `name` , `comment` , `value` , `config_category_id` , `orderby` , `type` , `defvalue` , `variants`) VALUES ( 'send_insured_value', 'Send insured value', 'N', @cid, '115', 'checkbox', 'N', ''); 
