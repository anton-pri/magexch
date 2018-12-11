REPLACE INTO `cw_languages` (`code`, `name`, `value`, `tooltip`, `topic`) VALUES
('EN', 'lbl_cs_update_content_section', 'Update Content Section', '', 'Labels');

REPLACE INTO `cw_languages` (`code`, `name`, `value`, `tooltip`, `topic`) VALUES
('EN', 'lbl_cs_type', 'Type', '', 'Labels');

REPLACE INTO `cw_languages` (`code`, `name`, `value`, `tooltip`, `topic`) VALUES
('EN', 'lbl_cs_type_embedded_html', 'Embedded HTML', '', 'Labels');
REPLACE INTO `cw_languages` (`code`, `name`, `value`, `tooltip`, `topic`) VALUES
('EN', 'lbl_cs_type_embedded_image', 'Embedded Image', '', 'Labels');
REPLACE INTO `cw_languages` (`code`, `name`, `value`, `tooltip`, `topic`) VALUES
('EN', 'lbl_cs_type_separate_static_page', 'Separate Static Page', '', 'Labels');
REPLACE INTO `cw_languages` (`code`, `name`, `value`, `tooltip`, `topic`) VALUES
('EN', 'lbl_cs_type_static_content_popup', 'Static Content Popup', '', 'Labels');
REPLACE INTO `cw_languages` (`code`, `name`, `value`, `tooltip`, `topic`) VALUES
('EN', 'lbl_cs_service_code', 'Service Code', '', 'Labels');
REPLACE INTO `cw_languages` (`code`, `name`, `value`, `tooltip`, `topic`) VALUES
('EN', 'lbl_cs_title', 'Title', '', 'Labels');
REPLACE INTO `cw_languages` (`code`, `name`, `value`, `tooltip`, `topic`) VALUES
('EN', 'lbl_cs_alt_text', 'Alt text', '', 'Labels');
REPLACE INTO `cw_languages` (`code`, `name`, `value`, `tooltip`, `topic`) VALUES
('EN', 'lbl_cs_open_link_in', 'Open Link In', '', 'Labels');
REPLACE INTO `cw_languages` (`code`, `name`, `value`, `tooltip`, `topic`) VALUES
('EN', 'lbl_cs_target_same_window', 'Same Window', '', 'Labels');
REPLACE INTO `cw_languages` (`code`, `name`, `value`, `tooltip`, `topic`) VALUES
('EN', 'lbl_cs_target_new_window', 'New Window', '', 'Labels');
REPLACE INTO `cw_languages` (`code`, `name`, `value`, `tooltip`, `topic`) VALUES
('EN', 'lbl_cs_url', 'URL', '', 'Labels');
REPLACE INTO `cw_languages` (`code`, `name`, `value`, `tooltip`, `topic`) VALUES
('EN', 'lbl_cs_layout_of_multiple_sections', 'Layout of Multiple Sections', '','Labels');
REPLACE INTO `cw_languages` (`code`, `name`, `value`, `tooltip`, `topic`) VALUES
('EN', 'lbl_cs_layout_comment', 'The layout setting of this content section will affect all sections with the same service code ({{service_code}}).', '', 'Labels');
REPLACE INTO `cw_languages` (`code`, `name`, `value`, `tooltip`, `topic`) VALUES
('EN', 'lbl_cs_restrict_to_categories', 'Restrict to Categories', '', 'Labels');
REPLACE INTO `cw_languages` (`code`, `name`, `value`, `tooltip`, `topic`) VALUES
('EN', 'lbl_cs_restrict_to_products', 'Restrict to Products', '', 'Labels');
REPLACE INTO `cw_languages` (`code`, `name`, `value`, `tooltip`, `topic`) VALUES
('EN', 'lbl_cs_restrict_to_manufacturers', 'Restrict to Manufacturers', '', 'Labels');

REPLACE INTO `cw_languages` (`code`, `name`, `value`, `tooltip`, `topic`) VALUES
('EN', 'lbl_cs_save_content_section', 'Save Content Section', '', 'Labels');

REPLACE INTO `cw_languages` (`code`, `name`, `value`, `tooltip`, `topic`) VALUES
('EN', 'lbl_cs_add_content_section', 'Add Content Section', '', 'Labels');

REPLACE INTO `cw_languages` (`code`, `name`, `value`, `tooltip`, `topic`) VALUES
('EN', 'lbl_cs_parse_smarty_tags', 'Parse Smarty Tags', '', 'Labels');

REPLACE INTO `cw_languages` (`code`, `name`, `value`, `tooltip`, `topic`) VALUES
('EN', 'lbl_cs_content_sections', 'Content Sections', '', 'Labels');

update cw_navigation_menu set title='lbl_cs_content_sections' where title='lbl_banners';

REPLACE INTO `cw_languages` (`code`, `name`, `value`, `tooltip`, `topic`) VALUES
('EN', 'lbl_cs_add_new_content_section', 'Add New Content Section', '', 'Labels');

REPLACE INTO `cw_languages` (`code`, `name`, `value`, `tooltip`, `topic`) VALUES
('EN', 'lbl_cs_content_sections_search', 'Search', '', 'Labels');

REPLACE INTO `cw_languages` (`code`, `name`, `value`, `tooltip`, `topic`) VALUES
('EN', 'lbl_cs_contentsection_id', 'contentsection_id', '', 'Labels');

REPLACE INTO `cw_languages` (`code`, `name`, `value`, `tooltip`, `topic`) VALUES
('EN', 'lbl_cs_viewed', 'Viewed', '', 'Labels');

REPLACE INTO `cw_languages` (`code`, `name`, `value`, `tooltip`, `topic`) VALUES
('EN', 'lbl_cs_start_date', 'Start date', '', 'Labels');

REPLACE INTO `cw_languages` (`code`, `name`, `value`, `tooltip`, `topic`) VALUES
('EN', 'lbl_cs_end_date', 'End date', '', 'Labels');

update cw_navigation_tabs set title='lbl_cs_content_sections_search' where title='lbl_ab_banners_search';
update cw_navigation_tabs set title='lbl_cs_add_new_content_section' where title='lbl_ab_add_new_banner';
update cw_navigation_tabs set title='lbl_cs_update_content_section' where title='lbl_ab_update_banner';
update cw_navigation_tabs set link=replace(link, 'banner_id', 'contentsection_id') where link like '%banner_id%';
update cw_breadcrumbs set title='lbl_cs_update_content_section' where title='lbl_ab_update_banner';
update cw_breadcrumbs set title='lbl_cs_add_new_content_section' where title='lbl_ab_add_new_banner';
update cw_breadcrumbs set title='lbl_cs_content_sections' where title='lbl_ab_adb';
update cw_breadcrumbs set link=replace(link, 'banner_id', 'contentsection_id') where link like '%banner%';
update cw_navigation_sections set title='lbl_cs_content_sections' where title='lbl_ab_adb';
update cw_navigation_tabs set title='lbl_cs_content_sections' where title='lbl_ab_adb';
update cw_navigation_menu set link='index.php?target=adb&mode=list&cs_type=staticpage' where title='lbl_static_pages';
update cw_navigation_targets set params=replace(params, 'banner_id', 'contentsection_id') where params like '%banner_id%';
update cw_navigation_targets set visible=replace(visible, 'banner_id', 'contentsection_id') where visible like '%banner_id%';
update cw_languages set name='txt_delete_selected_contentsections_warning', value='Are you sure you want to delete the selected content sections?' where name='txt_delete_selected_banners_warning';
update cw_languages set name='txt_ab_there_are_no_contentsections_found', value='There are no content sections' where name='txt_ab_there_are_no_banners_found';
update cw_languages set name='msg_ab_err_servicecode_is_empty', value='The service code is not specified.' where name='msg_ab_err_bannercode_is_empty';
update cw_languages set name='msg_ab_err_wrong_servicecode_format', value='Wrong service code format. The alphanumeric characters and underscores can be used only. For example, my_section_12.' where name='msg_ab_err_wrong_bannercode_format';
update cw_languages set name='msg_ab_warn_empty_contentsection_name', value='The content section is not specified. We recommend to set a name of the content section.' where name='msg_ab_warn_empty_banner_name';
update cw_languages set name='msg_ab_warn_empty_contentsection_url', value='The content section URL is not specified. Are you sure to leave URL empty?' where name='msg_ab_warn_empty_banner_url';
update cw_languages set name='msg_ab_warn_empty_contentsection_alt_text', value='The content section alt text is not specified.' where name='msg_ab_warn_empty_banner_alt_text';

update cw_adb set banner_id = banner_id + 100;
update cw_adb_alt_languages set banner_id = banner_id + 100;
update cw_adb_categories set banner_id = banner_id + 100;
update cw_adb_clean_urls set banner_id = banner_id + 100;
update cw_adb_manufacturers set banner_id = banner_id + 100;
update cw_adb_products set banner_id = banner_id + 100;
update cw_adb_user_counters set banner_id = banner_id + 100;
update cw_adb_images set id = id + 100;
update cw_attributes_values set item_id = item_id+100 where item_type='AB';


replace into cw_adb (banner_id, type, name, content, orderby, active) select page_id, 'staticpage', title, content, orderby, active from cw_pages;

update cw_attributes_values set item_type='AB' where item_type='S';

update cw_attributes_values set attribute_id=(select attribute_id from cw_attributes where addon='multi_domains' and item_type='AB' limit 1) where attribute_id=(select attribute_id from cw_attributes where addon='multi_domains' and item_type='S' limit 1);

update cw_attributes set item_type='AB' where item_type='S' and addon!='multi_domains';

alter table cw_sections_pos add column section_template varchar(255) not null default '';
update cw_sections_pos set section_template='customer/menu/categories.tpl' where section='categories';
update cw_sections_pos set section_template='customer/menu/special_product_links.tpl' where section='special_product_links';
update cw_sections_pos set section_template='addons/manufacturers/menu_manufacturers.tpl' where section='manufacturers';
update cw_sections_pos set section_template='customer/menu/recent_categories.tpl' where section='recent_categories';
update cw_sections_pos set section_template='addons/survey/all_menu_surveys.tpl' where section='menu_survey';
update cw_sections_pos set section_template='customer/menu/accessories.tpl' where section='accessories';
update cw_sections_pos set section_template='customer/menu/cart.tpl' where section='your_cart';
update cw_sections_pos set section_template='elements/authentication.tpl' where section='authentication';
update cw_sections_pos set section_template='customer/menu/account.tpl' where section='my_account';
update cw_sections_pos set section_template='addons/bestsellers/menu_bestsellers.tpl' where section='top_sellers';
update cw_sections_pos set section_template='customer/menu/arrivals.tpl' where section='new_arrivals';
update cw_sections_pos set section_template='customer/product-filter/menu-view/section_template.tpl' where section='product_filter';
update cw_sections_pos set section_template='addons/news/menu/news.tpl' where section='news';
update cw_sections_pos set section_template='customer/menu/resource.tpl' where section='resources';
update cw_sections_pos set section_template='customer/menu/online_support.tpl' where section='online_support';
update cw_sections_pos set section_template='customer/menu/warehouse.tpl' where section='dealers_and_distributers';



alter table cw_adb change column banner_id contentsection_id int(11) not null auto_increment;
alter table cw_adb change column banner_code service_code varchar(64) not null default '';

alter table cw_adb_alt_languages change column banner_id contentsection_id int(11) not null default 0;
alter table cw_adb_categories change column banner_id contentsection_id int(11) not null default 0;
alter table cw_adb_manufacturers change column banner_id contentsection_id int(11) not null default 0;
alter table cw_adb_products change column banner_id contentsection_id int(11) not null default 0;
alter table cw_adb_user_counters change column banner_id contentsection_id int(11) not null default 0;
alter table cw_adb_clean_urls change column banner_id contentsection_id int(11) not null default 0;

update cw_addons set addon='cms', descr='Content Management Sections. Allows to manage banners, static pages or static popup content in frontend' where addon='adb';


CREATE TABLE IF NOT EXISTS `cw_cms_attributes` (
  `contentsection_id` int(11) NOT NULL DEFAULT '0',
  `attribute_id` int(11) NOT NULL DEFAULT '0',
  `operation` varchar(10) NOT NULL DEFAULT 'eq',
  `value_id` int(11) NOT NULL DEFAULT '0',
  `value` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`contentsection_id`,`attribute_id`,`operation`,`value_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;


rename table cw_adb to cw_cms;
rename table cw_adb_alt_languages to cw_cms_alt_languages;
rename table cw_adb_categories to cw_cms_categories;
rename table cw_adb_images to cw_cms_images;
rename table cw_adb_manufacturers to cw_cms_manufacturers;
rename table cw_adb_products to cw_cms_products;
rename table cw_adb_user_counters to cw_cms_user_counters;
rename table cw_adb_clean_urls to cw_cms_clean_urls;

update cw_available_images set name = 'cms_images' where name='adb_images';
update cw_breadcrumbs set link=replace(link, 'adb', 'cms') where link like '%adb%';
update cw_cms_images set image_path=replace(image_path, 'adb', 'cms') where image_path like '%adb%';
update cw_languages set name='lbl_cs_cms', value='Content Sections' where name='lbl_ab_adb';
update cw_languages set name='addon_descr_cms', value='Allows to display static content in customer area' where name='addon_descr_adb';
update cw_languages set name='addon_name_cms', value='Content Menegment Sections' where name='addon_name_adb';
update cw_languages set name='lbl_cms_search', value='Content Sections Search' where name='lbl_adb_search';
update cw_navigation_menu set link=replace(link,'adb','cms') where link like '%adb%';
update cw_navigation_menu set addon='cms' where link like '%target=cms&%';
update cw_navigation_sections set link=replace(link, 'adb', 'cms') where link like '%adb%';
update cw_navigation_sections set addon='cms' where link like '%target=cms&%';
update cw_navigation_sections set addon='cms' where link like '%target=cms%';
update cw_navigation_targets set target='cms' where target='adb';
update cw_navigation_tabs set link=replace(link, 'target=adb', 'target=cms') where link like '%adb%';
update cw_navigation_sections set skins_subdir='cms' where skins_subdir='adb';
