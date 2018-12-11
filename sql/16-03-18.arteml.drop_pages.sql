delete from cw_languages where name in ('lbl_giftcerts');
delete from cw_menu where title in ('lbl_giftcerts','lbl_maintenance');
INSERT INTO `cw_menu` VALUES (null,0,'lbl_reports','','',65,'A','','','','',1);
SET @mid=LAST_INSERT_ID();
UPDATE cw_menu SET parent_menu_id=@mid WHERE target IN ('profit_reports','report_cost_history');
REPLACE INTO `cw_languages` (`code`, `name`, `value`, `topic`) VALUES ('EN', 'lbl_custom_attribute', 'Custom Attributes', 'Labels');

-- Backup pages for 180 days
INSERT INTO cw_temporary_data (id, data, expire) SELECT CONCAT('pages_bak_',page_id), CONCAT(title,':',active,':',content), UNIX_TIMESTAMP()+(3600*24*180) FROM cw_pages;
drop table cw_pages;
-- Delete attributes
delete from cw_attributes_values where attribute_id IN (select attribute_id  from cw_attributes where item_type='S');
delete from cw_attributes_lng where attribute_id IN (select attribute_id  from cw_attributes where item_type='S');
delete from cw_attributes where item_type='S';
-- Delete langvars
delete from cw_languages where name in ('lbl_static_pages','txt_static_pages_top_text','lbl_modify_static_page');
-- Delete navigation
delete from cw_menu where title in ('lbl_static_pages');
