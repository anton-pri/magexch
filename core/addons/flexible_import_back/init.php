<?php
$tables['flexible_import_profiles'] = 'cw_flexible_import_profiles';
$tables['flexible_import_files']    = 'cw_flexible_import_files';

$tables['flexible_import_loaded_files_hash'] = 'cw_flexible_import_loaded_files_hash';

$tables['datahub_log'] = 'cw_datahub_log';
//$tables['datahub_import_buffer'] = 'antonp_saratoga_hub.`cw_import_feed`';

$tables['datahub_import_buffer'] = 'cw_datahub_import_buffer';
$tables['datahub_import_buffer_images'] = 'cw_datahub_import_buffer_images';
/*
create view cw_datahub_import_buffer as select cif.*, '' as `Match Items` from antonp_saratoga_hub.cw_import_feed cif
-------------
create table cw_datahub_import_buffer like antonp_saratoga_hub.cw_import_feed;
insert into cw_datahub_import_buffer select * from antonp_saratoga_hub.cw_import_feed;
alter table cw_datahub_import_buffer  add column `Match Items` varchar(255) not null default '';
*/

$tables['datahub_main_data'] = 'cw_datahub_main_data';
/*
create view cw_datahub_main_data as select i.* from antonp_saratoga_hub.item i
----------------------------------------------------------------
create table cw_datahub_main_data like antonp_saratoga_hub.item;
insert into cw_datahub_main_data select * from antonp_saratoga_hub.item;
alter table cw_datahub_main_data drop column extendedimage;

alter table cw_datahub_main_data change column `sub-appellation` `sub_appellation` varchar(50);
alter table cw_datahub_main_data change column `RP Rating` `RP_Rating` varchar(50);
alter table cw_datahub_main_data change column `RP Review` `RP_Review` text;
alter table cw_datahub_main_data change column `WS Rating` `WS_Rating` varchar(50);
alter table cw_datahub_main_data change column `WS Review` `WS_Review` text;
alter table cw_datahub_main_data change column `WE Rating` `WE_Rating` varchar(50);
alter table cw_datahub_main_data change column `WE Review` `WE_Review` text;
alter table cw_datahub_main_data change column `DC Rating` `DC_Rating` varchar(50);
alter table cw_datahub_main_data change column `DC Review` `DC_Review` text;
alter table cw_datahub_main_data change column `ST Rating` `ST_Rating` varchar(50);
alter table cw_datahub_main_data change column `ST Review` `ST_Review` text;
alter table cw_datahub_main_data change column `W&S Rating` `W_S_Rating` varchar(50);
alter table cw_datahub_main_data change column `W&S Review` `W_S_Review` text;
alter table cw_datahub_main_data change column `BTI Rating` `BTI_Rating` varchar(50);
alter table cw_datahub_main_data change column `BTI Review` `BTI_Review` text;
alter table cw_datahub_main_data change column `Winery Rating` `Winery_Rating` varchar(50);
alter table cw_datahub_main_data change column `Winery Review` `Winery_Review` text;


alter table cw_datahub_main_data change column RP_Rating RP_Rating int(11) not null default 0;
alter table cw_datahub_main_data change column WS_Rating WS_Rating int(11) not null default 0;
alter table cw_datahub_main_data change column WE_Rating WE_Rating int(11) not null default 0;
alter table cw_datahub_main_data change column DC_Rating DC_Rating int(11) not null default 0;
alter table cw_datahub_main_data change column ST_Rating ST_Rating int(11) not null default 0;
alter table cw_datahub_main_data change column W_S_Rating W_S_Rating int(11) not null default 0;
alter table cw_datahub_main_data change column BTI_Rating BTI_Rating int(11) not null default 0;
alter table cw_datahub_main_data change column Winery_Rating Winery_Rating int(11) not null default 0;

*/

$tables['datahub_buffer_match_config'] = 'cw_datahub_buffer_match_config';
/*
create table cw_datahub_buffer_match_config (`mfield` varchar(127) not null default '', `bfield` varchar(127) not null default '', `custom_sql` text not null default '', `update_cond` char(1) not null default 'A', PRIMARY KEY (mfield));
*/

$tables['datahub_main_data_images'] = 'cw_datahub_main_data_images';
/*
create table cw_datahub_main_data_images (id int(11) not null default 0, filename varchar(255) not null default '', filesize int(11) not null default 0, web_path varchar(255) not null default '', system_path varchar(255) not null default '', PRIMARY KEY (id));
insert into cw_datahub_main_data_images (id, filename, web_path, system_path) select ID, cimageurl, cimageurl, cimageurl from cw_datahub_main_data where cimageurl != '';

alter table cw_datahub_main_data change column cimageurl cimageurl int(11) not null default 0;
update cw_datahub_main_data set cimageurl=ID;
alter table cw_datahub_main_data  add index dmd_cimageurl (`cimageurl`);

alter table cw_datahub_main_data_images add column item_id int(11) not null default 0;
update cw_datahub_main_data_images set item_id=id;
alter table cw_datahub_main_data_images drop column id;
alter table cw_datahub_main_data_images add column id int(11) not null auto_increment;
alter table cw_datahub_main_data_images add index dmdi_item_id (item_id);
update cw_datahub_main_data dmd, cw_datahub_main_data_images dmdi set dmd.cimageurl = dmdi.id where dmd.ID=dmdi.item_id;

alter table cw_datahub_main_data add column price decimal(19,2) not null default 0;
alter table cw_datahub_main_data add column cost decimal(19,2) not null default 0;
alter table cw_datahub_main_data add column stock int(11) not null default 0;
update cw_datahub_main_data dmd, antonp_saratoga_hub.item_price ip set dmd.price=ip.price, dmd.cost=ip.cost, dmd.stock=ip.stock where dmd.ID=ip.item_id and ip.store_id = 1;

*/
$tables['datahub_words_weight'] = 'cw_datahub_words_weight';
//create table cw_datahub_words_weight (word varchar(255) not null default '', weight decimal(19,2) not null default 0, PRIMARY KEY (word))


$tables['datahub_match_search_cache'] = 'cw_datahub_match_search_cache';
//create table cw_datahub_match_search_cache (ID int(11) not null default 0, words text not null default '', words_count int(11) not null default 0, PRIMARY KEY (ID));

$tables['datahub_match_links'] = 'cw_datahub_match_links';
// create table cw_datahub_match_links (catalog_id int(11) not null default 0, item_xref varchar(50) not null default '', primary key (catalog_id, item_xref));

$tables['datahub_buffer_merge_config'] = 'cw_datahub_buffer_merge_config';

$tables['datahub_price_settings'] = 'cw_datahub_price_settings';

$tables['datahub_pos'] = 'cw_datahub_pos';
$tables['datahub_pos_update_config'] = 'cw_datahub_pos_update_config';

$tables['datahub_beva_UP_VPR'] = 'cw_datahub_beva_UP_VPR';
$tables['datahub_beva_UP_prod'] = 'cw_datahub_beva_UP_prod';
$tables['datahub_beva_up_prod_xrefs'] = 'cw_datahub_beva_up_prod_xrefs';

$tables['datahub_beva_company_supplierid_map'] = 'cw_datahub_beva_company_supplierid_map';
$tables['datahub_beva_reg_text'] = 'cw_datahub_beva_reg_text';
$tables['datahub_beva_typetbl'] = 'cw_datahub_beva_typetbl';                
$tables['datahub_BevAccessFeeds'] = 'cw_datahub_BevAccessFeeds';

$tables['datahub_splitcase_charges'] = 'cw_datahub_splitcase_charges';

$tables['datahub_rnt_hidden_item'] = 'cw_datahub_rnt_hidden_item';
$tables['datahub_item_store2'] = 'item_store2';

$tables['datahub_competitor_prices'] = 'cw_datahub_competitor_prices';

$var_dirs['flex_import_test'] = $app_dir.'/files/flex_import_test';
$var_dirs['flexible_import'] = $app_dir.'/files/flexible_import';

define ("fi_files_path", "./files/flexible_import/");
define ("fi_hub_db", "devsarat_cw");

$cw_allowed_tunnels[] = 'cw_flexible_import_add_prefix';

global $csvxc_field_types;

$csvxc_field_types = array(
    'PRICE' => "decimal(12,2) NOT NULL DEFAULT '0.00'",
    'PRICE_MODIFIER' => "decimal(12,2) NOT NULL DEFAULT '0.00'",
    'DESCR' => "text NOT NULL DEFAULT ''",
    'FULLDESCR' => "text NOT NULL DEFAULT ''",
    'PRODUCTID' => "int(11) NOT NULL DEFAULT '0'",
    'PRODUCTID_TO' => "int(11) NOT NULL DEFAULT '0'",
    'OPTIONID' => "int(11) NOT NULL DEFAULT '0'",
    'CLASSID' => "int(11) NOT NULL DEFAULT '0'",
    'ADD_DATE' => "int(11) NOT NULL DEFAULT '0'",
    'WEIGHT' => "decimal(12,2) NOT NULL DEFAULT '0.00'",
    'LIST_PRICE' => "decimal(12,2) NOT NULL DEFAULT '0.00'",
    'AVAIL' => "int(11) NOT NULL DEFAULT '0'",
    'MIN_AMOUNT' => "int(11) NOT NULL DEFAULT '0'",
    'LOW_AVAIL_LIMIT' => "int(11) NOT NULL DEFAULT '0'",
    'default' => "varchar(255) NOT NULL DEFAULT ''"
);

global $tmp_load_tables;

$tmp_load_tables = array(
    'CATEGORIES' => array(
        'CATEGORYID' => array('type'=>'int', 'key'=>true),
        'CATEGORY' => array('type'=>'text', 'key'=>true),
        'DESCR' => array('type'=>'longtext'),
        'META_DESCR' => array('type'=>'longtext'),
        'AVAIL' => array('type'=>'bool'),
        'ORDERBY' => array('type'=>'int'),
        'META_KEYWORDS' => array('type'=>'text'),
//      'VIEWS_STATS'
//      'PRODUCT_COUNT'
//      'MEMBERSHIPID'
//      'MEMBERSHIP'
        'ICON' => array('type'=>'text', 'file_path'=>true), 
        'dynamic_field_set' => array('query'=>"select field from $tables[attributes] where item_type='C' and addon in ('', 'custom_magazineexchange')") 
    ),
    'PRODUCTS' => array(
        'PRODUCTID' => array('type' => 'int', 'key' => true), 
        'PRODUCTCODE' => array('type' => 'text', 'key' => true),
        'PRODUCT' => array('type' => 'text', 'key' => true),
        'WEIGHT' => array('type' => 'text'),
        'LIST_PRICE' => array('type' => 'text'),
        'DESCR' => array('type' => 'longtext'),
        'FULLDESCR' => array('type' => 'longtext'),
        'KEYWORDS' => array('type' => 'text'),  
        'AVAIL' => array('type' => 'text'),
        'RATING' => array('type' => 'text'),
        'FORSALE' => array('type' => 'text'),
        'SHIPPING_FREIGHT' => array('type' =>'text'),
        'FREE_SHIPPING' => array('type' => 'text'),
        'DISCOUNT_AVAIL' => array('type' => 'text'),
        'MIN_AMOUNT' => array('type' => 'text'),
        'DIM_X' => array('type' => 'text'),
        'DIM_Y' => array('type' => 'text'),
        'DIM_Z' => array('type' => 'text'),
        'LOW_AVAIL_LIMIT' => array('type' => 'text'),
        'FREE_TAX' => array('type' => 'text'),
        'CATEGORYID' => array('type' => 'int', 'grouped_key' => array('CATEGORY')),
        'CATEGORY' => array('type' => 'text', 'grouped_key' => array('CATEGORYID')),
        'MEMBERSHIP' => array('type' => 'text', 'grouped_key' => array('MEMBERSHIPID')),
        'PRICE' => array('type' => 'text'),
        'THUMBNAIL' => array('type' =>'text'),
        'IMAGE' => array('type' => 'text'),
        'TAXES' => array('type' => 'text'),
        'ADD_DATE' => array('type' => 'text'),
//      'VIEWS_STATS'
//      'SALES_STATS'
//      'DEL_STATS'
        'MANUFACTURERID' => array('type' => 'int', 'grouped_key' => array('MANUFACTURER')),
        'MANUFACTURER' => array('type' => 'text', 'grouped_key' => array('MANUFACTURERID')),
        'MEMBERSHIPID' => array('type' => 'int', 'grouped_key' => array('MEMBERSHIP')),  
        'SUPPLIERID' => array('type' => 'text'),  
        'COST' => array('type' => 'text'),
        'dynamic_field_set' => array('query'=>"select field from $tables[attributes] where item_type='P' and addon in ('', 'custom_magazineexchange')") 
    ),
    'PRODUCTS_EXTRA_FIELD_VALUES' => array(
        'PRODUCTID' => array('type' => 'int', 'key' => true),
        'PRODUCTCODE' => array('type' => 'text', 'key' => true),
        'PRODUCT' => array('type' => 'text', 'key' => true),
        'dynamic_field_set' => array('query'=>"select field from $tables[attributes] where item_type='P' and addon in ('', 'custom_saratogawine_magazines','custom_saratogawine_backorder','clean_urls')")
    ),
/*
    'vias_feed' => array(
        'dynamic_field_set' => array('query'=>"desc ". fi_hub_db . ".`vias_feed`") 
    ),
*/
);

if (APP_AREA == 'customer') {
    cw_include('addons/flexible_import/include/func.flexible_import.php');
    cw_include('addons/flexible_import/include/func.import.csvxcart.php');
    cw_include('addons/flexible_import/include/func.datahub.php');
    cw_set_controller(APP_AREA.'/datahub_load_feeds.php', 'addons/flexible_import/datahub/load_feeds.php', EVENT_REPLACE);
}


if (APP_AREA == 'admin'){

    cw_include('addons/flexible_import/include/func.flexible_import.php');
    cw_include('addons/flexible_import/include/func.import.csvxcart.php');
    cw_include('addons/flexible_import/include/csv_def_arrays.php');

    cw_set_controller('admin/import.php', 'addons/flexible_import/admin/flexible_import.php', EVENT_POST);
    cw_set_controller('admin/import.php', 'addons/flexible_import/admin/flexible_import_profile.php', EVENT_POST);

    cw_addons_set_template(
        array('replace', 'admin/import_export/flexible_import.tpl', 'addons/flexible_import/flexible_import.tpl'),
        array('replace', 'admin/import_export/flexible_import_profile.tpl', 'addons/flexible_import/add_modify_import_profile.tpl'),
        array('replace', 'admin/main/flexible_import_preview.tpl', 'addons/flexible_import/flexible_import_preview.tpl')
    );

    cw_addons_add_js('addons/flexible_import/flexible_import.js');
    cw_addons_add_css('addons/flexible_import/flexible_import.css');

    cw_set_hook('cw_error_check', 'cw_flexible_import_validate_import_file', EVENT_POST);

    cw_addons_set_controllers(
        array('replace', 'admin/recurring_import.php', 'addons/flexible_import/admin/recurring_import.php'),
        array('replace', 'admin/flexible_import_preview.php', 'addons/flexible_import/admin/flexible_import_preview.php')
    );
//--------data hub----------------------
    global $event_type_names;
    $event_type_names = array('E'=>'Error', 'I'=>'Event', 'W'=>'Warning');

    global $dh_buffer_table_fields;
    $dh_buffer_table_fields = array(
        'item_xref' => array('read_only'=>true),
        'Vintage' => array('variants_sel' => true, 'quick_preview' => true),
        'Source' => array('no_edit' => true),
        'wholesaler' => array('excl_list' => true,'no_edit' => true),
        'Wine' => array('edit_type'=>'mediumtext', 'quick_preview' => true),
        'Producer' => array('edit_type'=>'text', 'variants_sel' => true, 'quick_preview' => true),
        'Name' => array('edit_type'=>'mediumtext'),
        'size' => array('variants_sel' => true, 'quick_preview' => true),
        'ITEMID' => array('no_edit' => true),
        'country' => array('variants_sel' => true, 'quick_preview' => true),
        'Region' => array('variants_sel' => true, 'quick_preview' => true),
        'varietal' => array('variants_sel' => true, 'quick_preview' => true),
        'Appellation' => array('variants_sel' => true, 'quick_preview' => true),
        'sub-appellation' => array('variants_sel' => true, 'quick_preview' => true),
        'Parker_rating' => array('excl_list' => true, 'edit_type'=>'numeric'),
        'Parker_review' => array('excl_list' => true, 'edit_type'=>'largetext'),
        'Spectator_rating' => array('excl_list' => true, 'edit_type'=>'numeric'),
        'Spectator_review' => array('excl_list' => true, 'edit_type'=>'largetext'),
        'Tanzer_rating' => array('excl_list' => true, 'edit_type'=>'numeric'),
        'Tanzer_review' => array('excl_list' => true, 'edit_type'=>'largetext'),
        'W&S_rating' => array('excl_list' => true, 'edit_type'=>'numeric'),
        'W&S_review' => array('excl_list' => true, 'edit_type'=>'largetext'),
        'Description' => array('excl_list' => true),
        'store_id' => array('excl_list' => true,'no_edit' => true),
        'qty_in_stock' => array('excl_list' => true, 'edit_type'=>'numeric'),
        'supplier_id' => array('excl_list' => 0,'no_edit' => true),
        'feed_short_name' => array('title'=>'Feed Name', 'no_edit' => true),
        'item_xref_qty_avail' => array('title'=>'Qty avail', 'edit_type'=>'numeric'),
        'item_xref_min_price' => array('title'=>'Min Price', 'edit_type'=>'numeric'),
        'item_xref_bot_per_case' => array('title'=>'Btls/case', 'edit_type'=>'numeric'),
        'item_xref_cost_per_case' => array('title'=>'Cost/case', 'edit_type'=>'numeric'),
        'item_xref_cost_per_bottle' => array('title'=>'Cost', 'edit_type'=>'numeric'),
        'split_case_charge' => array('title'=>'Split case charge', 'excl_list' => 0, 'edit_type'=>'numeric'),
        'Match Items' => array('no_edit' => true, 'no_text_search'=>true)
    );

/*
    global $main_table_display_fields;
    $main_table_display_fields = array('ID', 'name', 'Producer', 'Vintage', 'LongDesc', 'size', 'Region', 'Appellation'); 

    global $main_table_enabled_edit_fields;
    $main_table_enabled_edit_fields = array('ID', 'dup_catid', 'name', 'Producer', 'Vintage', 'LongDesc', 'varietal', 'size', 'Region', 'Country', 'Appellation'); 
*/

    global $dh_main_table_fields;
    $dh_main_table_fields = array(
        'ID' => array('main_display' => 0, 'buffer_match_preview'=>0),
        'catalog_id' => array('main_display' => 1, 'buffer_match_preview'=>1), 
        'dup_catid' => array('disabled' => false, 'title'=>'Duplicate ID'),
        'name' => array('title' => 'Wine Name', 'main_display' => true, 'buffer_match_preview'=>true),
        'Producer' => array('main_display' => true, 'buffer_match_preview'=>true),
        'Vintage' => array('main_display' => true, 'buffer_match_preview'=>true),
        'Size' => array('main_display' => true, 'buffer_match_preview'=>true),
        'country' => array('buffer_match_preview'=>true),
        'cimageurl' => array('title' => 'Image', 'type' => 'upload', 'main_display' => true, 'is_image'=>true),
        'TareWeight'  => array('disabled' => true),
        'Region' => array('main_display' => true, 'buffer_match_preview'=>true),
        'varietal' => array('main_display' => true),
        'Appellation' => array('main_display' => true, 'buffer_match_preview'=>true),
        'sub_appellation' => array('title' => 'Sub-appellation', 'buffer_match_preview'=>true),
        'LongDesc' => array('type' => 'textarea', 'title' => 'Description', 'main_display' => 0, "item_select_popup_searchable_off"=>1),
        'RP_Review' => array('type' => 'textarea', 'title' => 'Robert Parker Review'),
        'RP_Rating' => array('title' => 'Robert Parker Rating', 'type' => 'select', 'is_rating'=>true),
        'WS_Review' => array('type' => 'textarea', 'title' => 'Wine Spectator Review'),
        'WS_Rating' => array('title' => 'Wine Spectator Rating', 'type' => 'select', 'is_rating'=>true),
        'WE_Review' => array('type' => 'textarea', 'title' => 'Wine Enthusiast Review'), 
        'WE_Rating' => array('title' => 'Wine Enthusiast Rating', 'type' => 'select', 'is_rating'=>true),
        'DC_Review' => array('type' => 'textarea', 'title' => 'Decanter Review'),
        'DC_Rating' => array('title' => 'Decanter Rating', 'type' => 'select', 'is_rating'=>true),
        'ST_Review' => array('type' => 'textarea', 'title' => 'Stephen Tanzer Review'),
        'ST_Rating' => array('title' => 'Stephen Tanzer Rating', 'type' => 'select', 'is_rating'=>true),
        'W_S_Review' => array('type' => 'textarea', 'title' => 'Wine & Spirits Review'),
        'W_S_Rating' => array('title' => 'Wine & Spirits Rating', 'type' => 'select', 'is_rating'=>true),
        'BTI_Review' => array('type' => 'textarea', 'title' => 'Beverage Tasting Institute Review'),
        'BTI_Rating' => array('title' => 'Beverage Tasting Institute Rating', 'type' => 'select', 'is_rating'=>true),
        'Winery_Review' => array('type' => 'textarea', 'title' => 'Winery Review'),
        'Winery_Rating' => array('title' => 'Winery Rating', 'type' => 'select', 'is_rating'=>true),
        'CG_Review' => array('type' => 'textarea', 'title' => 'Connoisseurs Guide Review'),
        'CG_Rating' => array('title' => 'Connoisseurs Guide Rating', 'type' => 'select', 'is_rating'=>true),
        'JH_Review' => array('type' => 'textarea', 'title' => 'James Halliday Review'),
        'JH_Rating' => array('title' => 'James Halliday Rating', 'type' => 'select', 'is_rating'=>true),
        'MJ_Review' => array('type' => 'textarea', 'title' => 'Michael Jackson Review'), 
        'MJ_Rating' => array('title' => 'Michael Jackson Rating', 'type' => 'select', 'is_rating'=>true),
        'TWN_Review' => array('type' => 'textarea', 'title' => 'The Wine News Review'),
        'TWN_Rating' => array('title' => 'The Wine News Rating', 'type' => 'select', 'is_rating'=>true),
        'bot_per_case' => array('title' => 'Bottles per case'),
        'initial_xref' => array('disabled' => 0, 'main_display' => true),
        'price' => array('main_display' => true, 'buffer_match_preview' => true),
        'cost' => array('main_display' => true, 'buffer_match_preview' => true),  
        'stock' => array('main_display' => true),
        'store_sku' => array('title'=>'Store SKU'),
        'minimumquantity' => array('title'=>'Min Order Qty'),
        'meta_description' => array('title'=>'Meta Description', 'type'=>'textarea')
    );
/*
| twelve_bot_price        | decimal(19,2) | NO   |     | 0.00    |                |
| cost_per_case           | decimal(19,2) | NO   |     | 0.00    |                |
| store_stock             | int(11)       | NO   |     | 0       |                |
| split_case_charge       | decimal(19,4) | NO   |     | 0.0000  |                |
| manual_price            | decimal(19,2) | NO   |     | 0.00    |                |
| twelve_bot_manual_price | decimal(19,2) | NO   |     | 0.00    |                |
| supplier_id             | int(11)       | NO   |     | 0       |                |
| min_price               | decimal(19,2) | NO   |     | 0.00    |                |
| weight                  | decimal(19,2) | NO   |     | 0.00    |                |
| avail_code              | int(11)       | NO   |     | 1       |                |
| hide      
*/

    global $price_settings_fields;

    $price_settings_fields = array(
        'store_id' => array('comment'=>'', 'title'=>'', 'hide'=>1),
        'min_qty_avail_code_2' => array('comment'=>'', 'title'=>'', 'hide'=>1), 
        'oversize_surcharge' => array('comment'=>'', 'title'=>'', 'hide'=>1),
        'SWE_min_qty_under_cost_threshold' => array('comment'=>'', 'title'=>'', 'hide'=>0),
        'SWE_cost_threshold' => array('comment'=>'', 'title'=>'', 'hide'=>0),
        'SWE_min_order_profit' => array('comment'=>'', 'title'=>'', 'hide'=>0),
        'SWE_min_order_profit_instock' => array('comment'=>'', 'title'=>'', 'hide'=>0),
        'competitor_price_threshold' => array('comment'=>'', 'title'=>'', 'hide'=>1),
        'SWE_min_markup' => array('comment'=>'', 'title'=>'', 'hide'=>0),
        'SWE_max_markup' => array('comment'=>'', 'title'=>'', 'hide'=>0),
        'order_days' => array('comment'=>'', 'title'=>'', 'hide'=>0),
        'twelve_bottle_discount' => array('comment'=>'', 'title'=>'', 'hide'=>1),
        'in_stock_sale' => array('comment'=>'', 'title'=>'', 'hide'=>1),
        'use_12bot_price_with_in_stock_sale' => array('comment'=>'', 'title'=>'', 'hide'=>1)
    );   

    if ($target == 'datahub_raw_import') {
        cw_addons_add_js('addons/flexible_import/datahub/javascript/jquery.fixheadertable.js');
        cw_addons_add_css('addons/flexible_import/datahub/css/base.css');
    }

    if ($target == 'datahub_configuration') {
        cw_addons_add_css('addons/flexible_import/datahub/css/base.css');
    }

    if ($target == 'datahub_buffer_match' || $target == 'datahub_item_select_popup') { 
        cw_addons_add_js('addons/flexible_import/datahub/javascript/jquery.dataTables.min.js');
        cw_addons_add_css('addons/flexible_import/datahub/css/jquery.dataTables.min.css');
        cw_addons_add_css('addons/flexible_import/datahub/css/alt.datatables.css');
        cw_addons_add_css('addons/flexible_import/datahub/css/base.css');
    }

    if ($target == 'datahub_buffer_match_edit') {
        cw_addons_add_css('addons/flexible_import/datahub/css/alt.datatables.css');
        cw_addons_add_css('addons/flexible_import/datahub/css/base.css');
    }

    if ($target == 'datahub_main_edit') {
        cw_addons_add_js('addons/flexible_import/datahub/javascript/jquery.dataTables.v2.min.js');
        cw_addons_add_js('addons/flexible_import/datahub/javascript/dataTables.buttons.min.js'); 
        cw_addons_add_js('addons/flexible_import/datahub/javascript/dataTables.select.min.js');
        cw_addons_add_js('addons/flexible_import/datahub/javascript/dataTables.editor.min.js');

        cw_addons_add_css('addons/flexible_import/datahub/css/alt.datatables.css');
        cw_addons_add_css('addons/flexible_import/datahub/css/jquery.dataTables.min.css');
        cw_addons_add_css('addons/flexible_import/datahub/css/buttons.dataTables.min.css');
        cw_addons_add_css('addons/flexible_import/datahub/css/select.dataTables.min.css');
        cw_addons_add_css('addons/flexible_import/datahub/css/editor.dataTables.min.css');
        cw_addons_add_css('addons/flexible_import/datahub/css/base.css');
    }

    cw_include('addons/flexible_import/include/func.datahub.php');

    cw_addons_set_controllers(
        array('replace', 'admin/datahub_raw_import.php', 'addons/flexible_import/datahub/raw_import.php'),
        array('replace', 'admin/datahub_buffer_match.php', 'addons/flexible_import/datahub/buffer_match.php'),
        array('replace', 'admin/datahub_buffer_match_data.php', 'addons/flexible_import/datahub/buffer_match_data.php'),
        array('replace', 'admin/datahub_buffer_match_edit.php', 'addons/flexible_import/datahub/buffer_match_edit.php'),
        array('replace', 'admin/dh_buffer_item_edit.php', 'addons/flexible_import/datahub/buffer_item_edit.php'),
        array('replace', 'admin/dh_buffer_item_edit_merge_src.php', 'addons/flexible_import/datahub/buffer_item_edit_merge_src.php'),
        array('replace', 'admin/dh_save_match.php', 'addons/flexible_import/datahub/save_match_item.php'),
        array('replace', 'admin/datahub_configuration.php', 'addons/flexible_import/datahub/configuration.php'),
        array('replace', 'admin/datahub_main_edit.php', 'addons/flexible_import/datahub/main_edit.php'),
        array('replace', 'admin/dh_main_edit.php', 'addons/flexible_import/datahub/main_edit_ajax.php'),
        array('replace', 'admin/datahub_gen_matches.php', 'addons/flexible_import/datahub/generate_matches.php'),
        array('replace', 'admin/datahub_gen_matches_bm25.php', 'addons/flexible_import/datahub/generate_matches_bm25.php'),
        array('replace', 'admin/datahub_item_select_popup.php', 'addons/flexible_import/datahub/item_select_popup.php'),
        array('replace', 'admin/datahub_item_select_popup_data.php', 'addons/flexible_import/datahub/item_select_popup_data.php'),
        array('replace', 'admin/dh_set_column_visibility.php', 'addons/flexible_import/datahub/set_column_visibility.php'),
        array('replace', 'admin/datahub_check_transfer_live.php', 'addons/flexible_import/datahub/check_transfer_live.php'),
        array('replace', 'admin/datahub_transfer_live.php', 'addons/flexible_import/datahub/transfer_live.php'),
        array('replace', 'admin/datahub_reset_transfer_live.php', 'addons/flexible_import/datahub/reset_transfer_live.php'),
        array('replace', 'admin/datahub_transfer_after_import.php', 'addons/flexible_import/datahub/transfer_after_import.php'),
        array('replace', 'admin/datahub_update_prices_monthly.php', 'addons/flexible_import/datahub/update_prices_monthly.php'),
        array('replace', 'admin/datahub_update_prices.php', 'addons/flexible_import/datahub/update_prices.php'),
        array('replace', 'admin/datahub_pos_update.php', 'addons/flexible_import/datahub/pos_update.php'),
        array('replace', 'admin/datahub_beva_monthly.php', 'addons/flexible_import/datahub/beva_monthly.php'),
        array('replace', 'admin/datahub_beva_daily.php', 'addons/flexible_import/datahub/beva_daily.php'),
        array('replace', 'admin/datahub_rnt.php', 'addons/flexible_import/datahub/rnt.php'),
        array('replace', 'admin/datahub_rnt_tag_print.php', 'addons/flexible_import/datahub/rnt_tag_print.php'),
        array('replace', 'admin/datahub_tools.php', 'addons/flexible_import/datahub/tools.php'),
        array('replace', 'admin/datahub_main_snapshot.php', 'addons/flexible_import/datahub/main_snapshot.php'), 
        array('replace', 'admin/datahub_clean_unused_images.php', 'addons/flexible_import/datahub/clean_unused_images.php'),

        array('replace', 'admin/datahub_SWE_store_import_and_update.php', 'addons/flexible_import/datahub/SWE_store_import_and_update.php'),
        array('replace', 'admin/datahub_calc_output.php', 'addons/flexible_import/datahub/calc_output.php'),
        array('replace', 'admin/datahub_run_cw_update.php', 'addons/flexible_import/datahub/run_cw_update.php'),
        array('replace', 'admin/datahub_step_pos_update.php', 'addons/flexible_import/datahub/step_pos_update.php'),
        array('replace', 'admin/datahub_prepare_hubv1_tables.php', 'addons/flexible_import/datahub/prepare_hubv1_tables.php')

    );

    cw_addons_set_template(
        array('replace', 'admin/main/datahub_raw_import.tpl', 'addons/flexible_import/datahub/raw_import.tpl'),
        array('replace', 'admin/main/datahub_buffer_match.tpl', 'addons/flexible_import/datahub/buffer_match.tpl'),
        array('replace', 'admin/main/datahub_buffer_match_edit.tpl', 'addons/flexible_import/datahub/buffer_match_edit.tpl'),
        array('replace', 'admin/main/datahub_configuration.tpl', 'addons/flexible_import/datahub/configuration.tpl'),
        array('replace', 'admin/main/datahub_main_edit.tpl', 'addons/flexible_import/datahub/main_edit.tpl'),
        array('replace', 'admin/main/datahub_item_select_popup.tpl', 'addons/flexible_import/datahub/item_select_popup.tpl'),
        array('replace', 'admin/main/datahub_rnt.tpl', 'addons/flexible_import/datahub/rnt.tpl'),
        array('replace', 'admin/main/datahub_tools.tpl', 'addons/flexible_import/datahub/tools.tpl') 
    );

    global $dh_product_to_hub;

    $dh_product_to_hub = array(
        1 => array('dh_field'=>'Producer', 'cw_field'=>'value', 'cw_table'=>'attributes_values', 'cw_extra_cond'=>"attribute_id=(SELECT attribute_id FROM cw_attributes WHERE field='manufacturer_id' and item_type='P')", 'cw_id_field'=>'item_id'),

        2 => array('dh_field'=>'name', 'cw_field'=>'product', 'cw_table'=>'products', 'cw_id_field'=>'product_id'),
        3 => array('dh_field'=>'name', 'cw_field'=>'product', 'cw_table'=>'products_lng', 'cw_id_field'=>'product_id'),

        4 => array('dh_field'=>'Vintage', 'cw_field'=>'value', 'cw_table'=>'attributes_values', 'cw_extra_cond'=>"attribute_id=(SELECT attribute_id FROM cw_attributes WHERE field='vintage' and item_type='P')", 'cw_id_field'=>'item_id'),
        5 => array('dh_field'=>'Size', 'cw_field'=>'value', 'cw_table'=>'attributes_values', 'cw_extra_cond'=>"attribute_id=(SELECT attribute_id FROM cw_attributes WHERE field='size' and item_type='P')", 'cw_id_field'=>'item_id'),
        6 => array('dh_field'=>'cimageurl', 'cw_field'=>'md5', 'cw_table'=>'products_images_thumb', 'cw_id_field'=>'id'),
        7 => array('dh_field'=>'cimageurl', 'cw_field'=>'md5', 'cw_table'=>'products_images_det', 'cw_id_field'=>'id'),

        8 => array('dh_field'=>'LongDesc','cw_field'=>'descr', 'cw_table'=>'products', 'cw_extra_cond'=>'', 'cw_id_field'=>'product_id'),
        9 => array('dh_field'=>'LongDesc','cw_field'=>'fulldescr', 'cw_table'=>'products', 'cw_extra_cond'=>'', 'cw_id_field'=>'product_id'),
        10 => array('dh_field'=>'LongDesc','cw_field'=>'descr', 'cw_table'=>'products_lng', 'cw_extra_cond'=>'', 'cw_id_field'=>'product_id'),
        11 => array('dh_field'=>'LongDesc','cw_field'=>'fulldescr', 'cw_table'=>'products_lng', 'cw_extra_cond'=>'', 'cw_id_field'=>'product_id'),

        12 => array('dh_field'=>'Region', 'cw_field'=>'value', 'cw_table'=>'attributes_values', 'cw_extra_cond'=>"attribute_id=(SELECT attribute_id FROM cw_attributes WHERE field='region' and item_type='P')", 'cw_id_field'=>'item_id'),

        13 => array('dh_field'=>'country', 'cw_field'=>'value', 'cw_table'=>'attributes_values', 'cw_extra_cond'=>"attribute_id=(SELECT attribute_id FROM cw_attributes WHERE field='country' and item_type='P')", 'cw_id_field'=>'item_id'),

        14 => array('dh_field'=>'keywords', 'cw_field'=>'value', 'cw_table'=>'attributes_values', 'cw_extra_cond'=>"attribute_id=(SELECT attribute_id FROM cw_attributes WHERE field='meta_keywords' and item_type='P')", 'cw_id_field'=>'item_id'),

        15 => array('dh_field'=>'varietal', 'cw_field'=>'value', 'cw_table'=>'attributes_values', 'cw_extra_cond'=>"attribute_id=(SELECT attribute_id FROM cw_attributes WHERE field='varietal' and item_type='P')", 'cw_id_field'=>'item_id'),

        16 => array('dh_field'=>'Appellation', 'cw_field'=>'value', 'cw_table'=>'attributes_values', 'cw_extra_cond'=>"attribute_id=(SELECT attribute_id FROM cw_attributes WHERE field='appellation' and item_type='P')", 'cw_id_field'=>'item_id'),

        17 => array('dh_field'=>'sub_appellation', 'cw_field'=>'value', 'cw_table'=>'attributes_values', 'cw_extra_cond'=>"attribute_id=(SELECT attribute_id FROM cw_attributes WHERE field='subappellation' and item_type='P')", 'cw_id_field'=>'item_id'),

        18 => array('dh_field'=>'RP_Rating', 'cw_field'=>'value', 'cw_table'=>'attributes_values', 'cw_extra_cond'=>"attribute_id=(SELECT attribute_id FROM cw_attributes WHERE field='magazine_rating_6' and item_type='P')", 'cw_id_field'=>'item_id'),

        19 => array('dh_field'=>'RP_Review', 'cw_field'=>'value', 'cw_table'=>'attributes_values', 'cw_extra_cond'=>"attribute_id=(SELECT attribute_id FROM cw_attributes WHERE field='magazine_review_6' and item_type='P')", 'cw_id_field'=>'item_id'),

        20 => array('dh_field'=>'WS_Rating', 'cw_field'=>'value', 'cw_table'=>'attributes_values', 'cw_extra_cond'=>"attribute_id=(SELECT attribute_id FROM cw_attributes WHERE field='magazine_rating_11' and item_type='P')", 'cw_id_field'=>'item_id'),

        21 => array('dh_field'=>'WS_Review', 'cw_field'=>'value', 'cw_table'=>'attributes_values', 'cw_extra_cond'=>"attribute_id=(SELECT attribute_id FROM cw_attributes WHERE field='magazine_review_11' and item_type='P')", 'cw_id_field'=>'item_id'),

        22 => array('dh_field'=>'WE_Rating', 'cw_field'=>'value', 'cw_table'=>'attributes_values', 'cw_extra_cond'=>"attribute_id=(SELECT attribute_id FROM cw_attributes WHERE field='magazine_rating_10' and item_type='P')", 'cw_id_field'=>'item_id'),

        23 => array('dh_field'=>'WE_Review', 'cw_field'=>'value', 'cw_table'=>'attributes_values', 'cw_extra_cond'=>"attribute_id=(SELECT attribute_id FROM cw_attributes WHERE field='magazine_review_10' and item_type='P')", 'cw_id_field'=>'item_id'),

        24 => array('dh_field'=>'DC_Rating', 'cw_field'=>'value', 'cw_table'=>'attributes_values', 'cw_extra_cond'=>"attribute_id=(SELECT attribute_id FROM cw_attributes WHERE field='magazine_rating_3' and item_type='P')", 'cw_id_field'=>'item_id'),

        25 => array('dh_field'=>'DC_Review', 'cw_field'=>'value', 'cw_table'=>'attributes_values', 'cw_extra_cond'=>"attribute_id=(SELECT attribute_id FROM cw_attributes WHERE field='magazine_review_3' and item_type='P')", 'cw_id_field'=>'item_id'),

        26 => array('dh_field'=>'ST_Rating', 'cw_field'=>'value', 'cw_table'=>'attributes_values', 'cw_extra_cond'=>"attribute_id=(SELECT attribute_id FROM cw_attributes WHERE field='magazine_rating_7' and item_type='P')", 'cw_id_field'=>'item_id'),

        27 => array('dh_field'=>'ST_Review', 'cw_field'=>'value', 'cw_table'=>'attributes_values', 'cw_extra_cond'=>"attribute_id=(SELECT attribute_id FROM cw_attributes WHERE field='magazine_review_7' and item_type='P')", 'cw_id_field'=>'item_id'),

        28 => array('dh_field'=>'W_S_Rating', 'cw_field'=>'value', 'cw_table'=>'attributes_values', 'cw_extra_cond'=>"attribute_id=(SELECT attribute_id FROM cw_attributes WHERE field='magazine_rating_9' and item_type='P')", 'cw_id_field'=>'item_id'),

        29 => array('dh_field'=>'W_S_Review', 'cw_field'=>'value', 'cw_table'=>'attributes_values', 'cw_extra_cond'=>"attribute_id=(SELECT attribute_id FROM cw_attributes WHERE field='magazine_review_9' and item_type='P')", 'cw_id_field'=>'item_id'),

        30 => array('dh_field'=>'BTI_Rating', 'cw_field'=>'value', 'cw_table'=>'attributes_values', 'cw_extra_cond'=>"attribute_id=(SELECT attribute_id FROM cw_attributes WHERE field='magazine_rating_1' and item_type='P')", 'cw_id_field'=>'item_id'),

        31 => array('dh_field'=>'BTI_Review', 'cw_field'=>'value', 'cw_table'=>'attributes_values', 'cw_extra_cond'=>"attribute_id=(SELECT attribute_id FROM cw_attributes WHERE field='magazine_review_1' and item_type='P')", 'cw_id_field'=>'item_id'),

        32 => array('dh_field'=>'CG_Rating', 'cw_field'=>'value', 'cw_table'=>'attributes_values', 'cw_extra_cond'=>"attribute_id=(SELECT attribute_id FROM cw_attributes WHERE field='magazine_rating_2' and item_type='P')", 'cw_id_field'=>'item_id'),

        33 => array('dh_field'=>'CG_Review', 'cw_field'=>'value', 'cw_table'=>'attributes_values', 'cw_extra_cond'=>"attribute_id=(SELECT attribute_id FROM cw_attributes WHERE field='magazine_review_2' and item_type='P')", 'cw_id_field'=>'item_id'),

        34 => array('dh_field'=>'JH_Rating', 'cw_field'=>'value', 'cw_table'=>'attributes_values', 'cw_extra_cond'=>"attribute_id=(SELECT attribute_id FROM cw_attributes WHERE field='magazine_rating_4' and item_type='P')", 'cw_id_field'=>'item_id'),

        35 => array('dh_field'=>'JH_Review', 'cw_field'=>'value', 'cw_table'=>'attributes_values', 'cw_extra_cond'=>"attribute_id=(SELECT attribute_id FROM cw_attributes WHERE field='magazine_review_4' and item_type='P')", 'cw_id_field'=>'item_id'),

        36 => array('dh_field'=>'MJ_Rating', 'cw_field'=>'value', 'cw_table'=>'attributes_values', 'cw_extra_cond'=>"attribute_id=(SELECT attribute_id FROM cw_attributes WHERE field='magazine_rating_5' and item_type='P')", 'cw_id_field'=>'item_id'),

        37 => array('dh_field'=>'MJ_Review', 'cw_field'=>'value', 'cw_table'=>'attributes_values', 'cw_extra_cond'=>"attribute_id=(SELECT attribute_id FROM cw_attributes WHERE field='magazine_review_5' and item_type='P')", 'cw_id_field'=>'item_id'),

        38 => array('dh_field'=>'TWN_Rating', 'cw_field'=>'value', 'cw_table'=>'attributes_values', 'cw_extra_cond'=>"attribute_id=(SELECT attribute_id FROM cw_attributes WHERE field='magazine_rating_8' and item_type='P')", 'cw_id_field'=>'item_id'),

        39 => array('dh_field'=>'TWN_Review', 'cw_field'=>'value', 'cw_table'=>'attributes_values', 'cw_extra_cond'=>"attribute_id=(SELECT attribute_id FROM cw_attributes WHERE field='magazine_review_8' and item_type='P')", 'cw_id_field'=>'item_id')

    ); 

    cw_addons_set_controllers(
        array('pre', 'include/products/modify.php', 'addons/flexible_import/datahub/pre_product_modify.php')
    );
    cw_event_listen('on_product_modify_end', 'cw_datahub_on_product_modify_end');
 
}

//cw_event_listen('on_cron_regular', 'cw_flexible_import_recurring_imports');
