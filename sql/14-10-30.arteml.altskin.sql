select @cid:=config_category_id from cw_config_categories where category='Email';
select @skin:=skin from cw_domains order by domain_id ASC limit 1;
REPLACE INTO `cw_config` ( `name` , `comment` , `value` , `config_category_id` , `orderby` , `type` , `defvalue` , `variants`) VALUES ( 'alt_skin', 'Alternative skin for email templates', @skin, @cid, '20', 'text', '', '');
