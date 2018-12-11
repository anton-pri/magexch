REPLACE INTO `cw_languages` (`code`, `name`, `value`, `topic`) VALUES ('EN', 'addon_name_magazineexchange', 'Custom magazineexchange', 'Addons');

REPLACE INTO `cw_addons` (`addon`, `descr`, `active`, `status`, `parent`,`version`,`orderby`) VALUES ('custom_magazineexchange', 'magazineexchange', 1, 1, '', '0.1',0);

REPLACE INTO cw_languages SET code='EN', name='addon_descr_custom_magazineexchange', value='Custom modifications for magazineexchange site', topic='Addons';
REPLACE INTO cw_languages SET code='EN', name='addon_name_custom_magazineexchange', value='Custom magazineexchange', topic='Addons';
REPLACE INTO cw_languages SET code='EN', name='option_title_custom_magazineexchange', value='Custom magazineexchange options', topic='Options';

-- configuration options
REPLACE INTO `cw_config_categories` (`config_category_id` ,`category` ,`is_local`) VALUES (NULL , 'custom_magazineexchange', '0');
SET @config_category_id = LAST_INSERT_ID();
REPLACE INTO cw_config SET name='magexch_default_root_category', comment='Category id for home page category menu', value='282', config_category_id=@config_category_id, orderby='100', type='numeric', defvalue='282', variants='';


insert into cw_attributes (name, type, field, is_required, active, orderby, addon, item_type, protection) values ('Category Type', 'selectbox', 'magexch_category_type', 1, 1, 1, 'custom_magazineexchange', 'C', 15);
SET @attribute_id = LAST_INSERT_ID();
insert into cw_attributes_default (value, attribute_id, is_default ,active) values ('Default', @attribute_id, 1, 1);
insert into cw_attributes_default (value, attribute_id, is_default ,active) values ('Catalog', @attribute_id, 0, 1);
insert into cw_attributes_default (value, attribute_id, is_default ,active) values ('Section', @attribute_id, 0, 1);
insert into cw_attributes_default (value, attribute_id, is_default ,active) values ('SubSection', @attribute_id, 0, 1);
insert into cw_attributes_default (value, attribute_id, is_default ,active) values ('Magazine', @attribute_id, 0, 1);
insert into cw_attributes_default (value, attribute_id, is_default ,active) values ('Year', @attribute_id, 0, 1);


insert into cw_attributes (name, type, field, is_required, active, orderby, addon, item_type, protection) values ('Main Image', 'text', 'magexch_category_main_image', 0, 1, 1, 'custom_magazineexchange', 'C', 15);
insert into cw_attributes (name, type, field, is_required, active, orderby, addon, item_type, protection) values ('Pop-up category image URL (110x60)', 'text', 'magexch_popup_category_image', 0, 1, 2, 'custom_magazineexchange', 'C', 15);
insert into cw_attributes (name, type, field, is_required, active, orderby, addon, item_type, protection) values ('Avatar image URL', 'text', 'magexch_category_avatar_image', 0, 1, 3, 'custom_magazineexchange', 'C', 15);
insert into cw_attributes (name, type, field, is_required, active, orderby, addon, item_type, protection) values ('Use parent avatar image', 'yes_no', 'magexch_category_use_parent_avatar', 0, 1, 4, 'custom_magazineexchange', 'C', 15);
insert into cw_attributes (name, type, field, is_required, active, orderby, addon, item_type, protection) values ('Roll-over image', 'text', 'magexch_category_rollover_image', 0, 1, 5, 'custom_magazineexchange', 'C', 15);

insert into cw_attributes (name, type, field, is_required, active, orderby, addon, item_type, protection) values ('Section Image', 'text', 'magexch_category_section_image', 0, 1, 5, 'custom_magazineexchange', 'C', 15);

insert into cw_attributes (name, type, field, is_required, active, orderby, addon, item_type, protection) values ('Tab title 2', 'text', 'magexch_category_tab_title_2', 0, 1, 6, 'custom_magazineexchange', 'C', 15);
insert into cw_attributes (name, type, field, is_required, active, orderby, addon, item_type, protection) values ('Tab content 2', 'text', 'magexch_category_tab_content_2', 0, 1, 7, 'custom_magazineexchange', 'C', 15);
insert into cw_attributes (name, type, field, is_required, active, orderby, addon, item_type, protection) values ('Tab title 3', 'text', 'magexch_category_tab_title_3', 0, 1, 8, 'custom_magazineexchange', 'C', 15);
insert into cw_attributes (name, type, field, is_required, active, orderby, addon, item_type, protection) values ('Tab content 3', 'text', 'magexch_category_tab_content_3', 0, 1, 9, 'custom_magazineexchange', 'C', 15);
insert into cw_attributes (name, type, field, is_required, active, orderby, addon, item_type, protection) values ('Tab title 4', 'text', 'magexch_category_tab_title_4', 0, 1, 10, 'custom_magazineexchange', 'C', 15);
insert into cw_attributes (name, type, field, is_required, active, orderby, addon, item_type, protection) values ('Tab content 4', 'text', 'magexch_category_tab_content_4', 0, 1, 11, 'custom_magazineexchange', 'C', 15);

insert into cw_attributes (name, type, field, is_required, active, orderby, addon, item_type, protection) values ('X-Cart site page #', 'text', 'magexch_xc_pageid', 0, 1, 1, 'custom_magazineexchange', 'AB', 15);
insert into cw_attributes (name, type, field, is_required, active, orderby, addon, item_type, protection) values ('X-Cart site page Help menu', 'text', 'magexch_xc_show_in_menu', 0, 1, 2, 'custom_magazineexchange', 'AB', 15);
insert into cw_attributes (name, type, field, is_required, active, orderby, addon, item_type, protection) values ('X-Cart site page Background Image URL', 'text', 'magexch_xc_background_url', 0, 1, 3, 'custom_magazineexchange', 'AB', 15);
insert into cw_attributes (name, type, field, is_required, active, orderby, addon, item_type, protection) values ('X-Cart site page Page Template', 'text', 'magexch_xc_page_template', 0, 1, 4, 'custom_magazineexchange', 'AB', 15);
insert into cw_attributes (name, type, field, is_required, active, orderby, addon, item_type, protection) values ('Custom Page Template Name', 'text', 'magexch_custom_page_template_name', 0, 1, 5, 'custom_magazineexchange', 'AB', 15);

insert into cw_attributes (name, type, field, is_required, active, orderby, addon, item_type, protection) values ('Short Product Name', 'text', 'magexch_product_short_product', 0, 1, 0, 'custom_magazineexchange', 'P', 15);
insert into cw_attributes (name, type, field, is_required, active, orderby, addon, item_type, protection) values ('Avatar image URL', 'text', 'magexch_product_avatar_image', 0, 1, 1, 'custom_magazineexchange', 'P', 15);
insert into cw_attributes (name, type, field, is_required, active, orderby, addon, item_type, protection) values ('Use parent avatar image', 'yes_no', 'magexch_product_use_parent_avatar', 0, 1, 2, 'custom_magazineexchange', 'P', 15);
insert into cw_attributes (name, type, field, is_required, active, orderby, addon, item_type, protection) values ('Roll-over image', 'text', 'magexch_product_rollover_image', 0, 1, 3, 'custom_magazineexchange', 'P', 15);
insert into cw_attributes (name, type, field, is_required, active, orderby, addon, item_type, protection) values ('Item link on external website', 'text', 'magexch_product_external_url', 0, 1, 4, 'custom_magazineexchange', 'P', 15);
insert into cw_attributes (name, type, field, is_required, active, orderby, addon, item_type, protection) values ('Number of Pages', 'text', 'magexch_product_NUMBER_PAGES', 0, 1, 5, 'custom_magazineexchange', 'P', 15);
insert into cw_attributes (name, type, field, is_required, active, orderby, addon, item_type, protection) values ('Ebay Title', 'text', 'magexch_product_EBAY_TITLE', 0, 1, 6, 'custom_magazineexchange', 'P', 15);
insert into cw_attributes (name, type, field, is_required, active, orderby, addon, item_type, protection) values ('Adverts Extra Link (ex-keywords)', 'text', 'magexch_product_keywords_extra_link', 0, 1, 7, 'custom_magazineexchange', 'P', 15);
