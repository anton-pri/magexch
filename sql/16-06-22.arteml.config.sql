update cw_config set value=CONCAT(value,"\n",'/files/images/default_image_') WHERE name='css_sprite_disallow_patterns';

-- Fix menu
SELECT @mid:=menu_id FROM cw_menu WHERE title='lbl_other' AND parent_menu_id=0;
SELECT @flag:=count(*) FROM cw_menu WHERE target='import' AND area='A' AND parent_menu_id=@mid;
DELETE FROM cw_menu WHERE target='import' AND area='A' AND parent_menu_id=@mid AND @flag>1 LIMIT 1;
DELETE FROM cw_menu WHERE target='products' AND area='A' AND parent_menu_id=@mid LIMIT 1;

SELECT @midV:=menu_id FROM cw_menu WHERE title='lbl_tools' AND parent_menu_id=0 AND area='V';
UPDATE cw_menu SET parent_menu_id=@midV WHERE parent_menu_id=@mid AND area='V';

-- Langvars
INSERT IGNORE INTO `cw_languages` (`code`, `name`, `value`, `topic`) VALUES ('EN', 'lbl_clear_filter', 'Reset Filter', 'Labels') ;
delete from cw_languages where `name` in ('lbl_section_anagraphic','lbl_anagraphic');

-- Rearrange menu
UPDATE `cw_menu` SET `skins_subdir` = '' WHERE `cw_menu`.`target` = 'license';
UPDATE `cw_menu` SET `skins_subdir` = '' WHERE `cw_menu`.`target` = 'configuration';

UPDATE `cw_languages` SET `value` = 'Search Options' WHERE `cw_languages`.`code` = 'EN' AND `cw_languages`.`name` = 'option_title_advanced_search';

-- Delete 3D-Secure settings (CMPI)
SELECT @cidCMPI:=config_category_id FROM cw_config_categories WHERE category='CMPI';
DELETE FROM cw_config WHERE config_category_id=@cidCMPI;
DELETE FROM cw_config_categories WHERE config_category_id=@cidCMPI;
delete from cw_languages WHERE `name` IN (
'option_title_CMPI','config_cmpi','err_cmpi_declined_order','lbl_cmpi_activate_now','txt_cmpi_customer_message',
'lbl_cmpi_customer_note','lbl_cmpi_jcbjs','lbl_cmpi_mcsc','lbl_cmpi_vbv','txt_cmpi_frame_customer_note',
'txt_cmpi_vbv_popup_note');
delete from cw_languages WHERE `name` LIKE 'opt_cmpi_%';

-- Delete DB backup/restore functionality
delete from cw_menu where target='db_backup';
delete from cw_languages WHERE `name` IN (
'lbl_dumping_table_n', 'lbl_db_backup_in_progress','msg_adm_db_backup_success','msg_adm_err_sql_file_not_found',
'lbl_restoring_table_n','lbl_db_restored_successfully','lbl_backup_database','lbl_database_backup_restore',
'txt_write_sql_dump_to_file','lbl_generate_sql_file','txt_backup_database_text','txt_backup_database_note',
'lbl_restore_database','txt_restore_database_from_file','lbl_restore_from_file','txt_restore_database_note'
);

-- Delete "Images" settings. Rename "Smarty_plugins" to "Images"
SELECT @cidImages:=config_category_id FROM cw_config_categories WHERE category='Images';
SELECT @cidSm:=config_category_id FROM cw_config_categories WHERE category='Smarty_plugins';
UPDATE cw_config SET config_category_id=@cidSm, orderby='1000' WHERE config_category_id=@cidImages AND `name`='xcm_thumb_sep';
DELETE FROM cw_config WHERE config_category_id=@cidImages;
DELETE FROM cw_config_categories WHERE config_category_id=@cidImages;
UPDATE cw_config_categories SET category='Images' WHERE category='Smarty_plugins';
delete from cw_languages WHERE `name` IN ('option_title_Smarty_plugins');

-- Delete "shipping_docs" settings
SELECT @cidSh:=config_category_id FROM cw_config_categories WHERE category='shipping_docs';
DELETE FROM cw_config WHERE config_category_id=@cidSh;
DELETE FROM cw_config_categories WHERE config_category_id=@cidSh;
delete from cw_languages WHERE `name` IN ('lbl_supplier_ship_docs');

-- Disable "custom_css" menu
delete from cw_menu where target='custom_css';
update cw_config_categories SET is_local=1 WHERE category='CSS';

-- Move two params from General to Category
SELECT @cidCat:=config_category_id FROM cw_config_categories WHERE category='category_settings';
UPDATE cw_config SET config_category_id=@cidCat WHERE `name` IN ('recent_categories_amount','root_categories');

-- Move Tags config category fully to Appearance
SELECT @cidtags:=config_category_id FROM cw_config_categories WHERE category='tags';
SELECT @cidApp:=config_category_id FROM cw_config_categories WHERE category='Appearance';
SELECT @pos:=orderby FROM cw_config WHERE `name`='images_dimensions' AND config_category_id=@cidApp;
UPDATE cw_config SET orderby=orderby+300 WHERE orderby>=@pos AND config_category_id=@cidApp;
UPDATE cw_config SET config_category_id=@cidApp, orderby=orderby+200 WHERE  config_category_id=@cidtags;
INSERT INTO `cw_config` (`name`, `comment`, `value`, `config_category_id`, `orderby`, `type`, `defvalue`, `variants`) VALUES
('tags', 'Tags', '', @cidApp, 200, 'separator', '', '');
DELETE FROM cw_config_categories WHERE config_category_id=@cidtags;

-- Delete Warranties functionality
DROP TABLE cw_warranties;
DROP TABLE cw_warranties_lng;
delete from cw_menu where target='warranties';
ALTER TABLE cw_products DROP warranty_id;
delete from cw_languages WHERE `name` IN ('lbl_warranties','lbl_warranty','txt_warranty_desciption',
'lng.txt_warranty_desciption','lbl_warranty_by_manufacturer','lbl_product_covered_by_warranty','lbl_items_covered_by_warranty');

-- Delete Watermarks settings
SELECT @cidW:=config_category_id FROM cw_config_categories WHERE category='Watermarks';
DELETE FROM cw_config WHERE config_category_id=@cidW;
DELETE FROM cw_config_categories WHERE config_category_id=@cidW;
DELETE FROM cw_webmaster_images WHERE id=30;

-- optimization settings
delete from cw_config where `name`='online_support_skype';
SELECT @cidG:=config_category_id FROM cw_config_categories WHERE category='General';
SELECT @pos:=orderby FROM cw_config WHERE `name`='sep40' AND config_category_id=@cidG;
REPLACE INTO `cw_config_categories` (`config_category_id` ,`category` ,`is_local`) VALUES (NULL , 'performance', '0');
SET @config_category_id = LAST_INSERT_ID();
UPDATE cw_config SET config_category_id=@config_category_id WHERE orderby>=@pos AND config_category_id=@cidG;
REPLACE INTO cw_languages SET code='EN', `name`='option_title_performance', `value`='Performance Options', topic='Options';

-- advanced_search -> search. Move two params to Search config category
SELECT @cidAS:=config_category_id FROM cw_config_categories WHERE category='advanced_search';
UPDATE cw_config_categories SET category='search' WHERE config_category_id=@cidAS;
UPDATE cw_languages SET `name`='option_title_search', value='Search Options' WHERE `name`='option_title_advanced_search';
UPDATE cw_config SET config_category_id=@cidAS, orderby=100+orderby WHERE `name` IN ('allow_search_by_words','single_search_res');
INSERT INTO `cw_config` (`name`, `comment`, `value`, `config_category_id`, `orderby`, `type`, `defvalue`, `variants`) VALUES
('search_search_sep', 'Product search', '', @cidAS, 150, 'separator', '', '');
INSERT INTO `cw_config` (`name`, `comment`, `value`, `config_category_id`, `orderby`, `type`, `defvalue`, `variants`) VALUES
('adv_search_sep', 'Advanced Product Search', '', @cidAS, 1000, 'separator', '', '');

-- Merge SEO config category into General
SELECT @cidSEO:=config_category_id FROM cw_config_categories WHERE category='SEO';
SELECT @cidG:=config_category_id FROM cw_config_categories WHERE category='General';
UPDATE cw_config SET config_category_id=@cidG, orderby=orderby+700 WHERE config_category_id=@cidSEO;
INSERT INTO `cw_config` (`name`, `comment`, `value`, `config_category_id`, `orderby`, `type`, `defvalue`, `variants`) VALUES
('sep_SEO', 'SEO', '', @cidG, 700, 'separator', '', '');
DELETE FROM cw_config_categories WHERE config_category_id=@cidSEO;
delete from cw_languages WHERE `name` IN ('option_title_SEO');
DELETE FROM cw_config WHERE `name`='page_title_limit';

update cw_languages SET value='Company Information' WHERE `name`='option_title_Company';

-- Collect options into new "Product" config category
REPLACE INTO `cw_config_categories` (`config_category_id` ,`category` ,`is_local`) VALUES (NULL , 'product', '0');
SET @cid = LAST_INSERT_ID();
REPLACE INTO cw_languages SET code='EN', `name`='option_title_product', `value`='Product Options', topic='Options';

INSERT INTO `cw_config` (`name`, `comment`, `value`, `config_category_id`, `orderby`, `type`, `defvalue`, `variants`) VALUES
('new_product_sep', 'New Product', '', @cid, 100, 'separator', '', '');
INSERT INTO `cw_config` (`name`, `comment`, `value`, `config_category_id`, `orderby`, `type`, `defvalue`, `variants`) VALUES
('price_product_sep', 'Price', '', @cid, 1000, 'separator', '', '');
INSERT INTO `cw_config` (`name`, `comment`, `value`, `config_category_id`, `orderby`, `type`, `defvalue`, `variants`) VALUES
('filter_product_sep', 'Product Filter', '', @cid, 2000, 'separator', '', '');
INSERT INTO `cw_config` (`name`, `comment`, `value`, `config_category_id`, `orderby`, `type`, `defvalue`, `variants`) VALUES
('recommended_product_sep', 'Recommended Products', '', @cid, 3000, 'separator', '', '');

SELECT @cid2:=config_category_id FROM cw_config_categories WHERE category='product_settings';
UPDATE cw_config SET config_category_id=@cid, orderby=orderby+100 WHERE config_category_id=@cid2;
DELETE FROM cw_config_categories WHERE config_category_id=@cid2;

SELECT @cid2:=config_category_id FROM cw_config_categories WHERE category='price';
UPDATE cw_config SET config_category_id=@cid, orderby=orderby+1000 WHERE config_category_id=@cid2;
DELETE FROM cw_config_categories WHERE config_category_id=@cid2;

SELECT @cid2:=config_category_id FROM cw_config_categories WHERE category='product_filter';
update cw_config set name=concat('pf_',name) where config_category_id=@cid2;
UPDATE cw_config SET config_category_id=@cid, orderby=orderby+2000 WHERE config_category_id=@cid2;
DELETE FROM cw_config_categories WHERE config_category_id=@cid2;

SELECT @cid2:=config_category_id FROM cw_config_categories WHERE category='recommended_products';
UPDATE cw_config SET config_category_id=@cid, orderby=orderby+3000 WHERE config_category_id=@cid2;
DELETE FROM cw_config_categories WHERE config_category_id=@cid2;

UPDATE cw_config SET config_category_id=@cid, orderby=110 WHERE `name`='product_descr_is_required';

-- docs -> orders
SELECT @ciddocs:=config_category_id FROM cw_config_categories WHERE category='docs';
UPDATE cw_config_categories SET category='order' WHERE config_category_id=@ciddocs;
delete from cw_languages WHERE `name` IN ('option_title_docs');
REPLACE INTO cw_languages SET code='EN', `name`='option_title_order', `value`='Order Options', topic='Options';

-- move local config to pages
update cw_config_categories SET is_local=1 WHERE category IN ('Taxes','category_settings','Logging','Shipping','special_sections','order','product');

-- Remove stop list from menu
select @mid:=menu_id, @pid:=parent_menu_id FROM cw_menu where title='lbl_stop_list';
delete from cw_menu where parent_menu_id=@pid;
