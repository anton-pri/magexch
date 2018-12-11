ALTER TABLE cw_cms_restrictions DROP PRIMARY KEY, ADD PRIMARY KEY ( `contentsection_id` , `object_type` , `object_id` , `value_id`, `value` );
INSERT INTO `cw_config_categories` ( `config_category_id` , `category` , `is_local`) VALUES ( NULL , 'cms', '0');
SET @cid=LAST_INSERT_ID();

INSERT INTO `cw_config` ( `name` , `comment` , `value` , `config_category_id` , `orderby` , `type` , `defvalue` , `variants`) VALUES ( 'allow_edit_from_customer_area', 'Hightlight all sections placeholders', 'N', @cid, '0', 'checkbox', '', ''); 
