truncate table cw_datahub_pricing_aipo;
truncate table cw_datahub_pricing_IPO_avg_price;
truncate table cw_datahub_price_settings;
insert into cw_datahub_pricing_aipo select * from saratoga_live_hub.pricing_aipo;
insert into cw_datahub_pricing_IPO_avg_price select * from saratoga_live_hub.pricing_IPO_avg_price;
insert into cw_datahub_price_settings select * from saratoga_live_hub.hub_price_settings;

truncate table cw_datahub_splitcase_charges;
truncate table cw_datahub_BevAccessFeeds;
truncate table cw_datahub_beva_UP_VPR;
truncate table cw_datahub_beva_UP_prod;
truncate table cw_datahub_beva_company_supplierid_map;
truncate table cw_datahub_beva_reg_text;
truncate table cw_datahub_beva_typetbl;
truncate table cw_datahub_beva_up_prod_xrefs;
insert into cw_datahub_splitcase_charges select * from saratoga_live_hub.splitcase_charges;
insert into cw_datahub_BevAccessFeeds select * from saratoga_live_hub.BevAccessFeeds;
insert into cw_datahub_beva_UP_VPR select * from saratoga_live_hub.UP_VPR;
insert into cw_datahub_beva_UP_prod select * from saratoga_live_hub.UP_prod;
insert into cw_datahub_beva_company_supplierid_map select * from saratoga_live_hub.BevA_company_supplierID_map;
insert into cw_datahub_beva_reg_text select * from saratoga_live_hub.BevA_Reg_text;
insert into cw_datahub_beva_typetbl select * from saratoga_live_hub.BevA_Typetbl;
insert into cw_datahub_beva_up_prod_xrefs select * from saratoga_live_hub.up_prod_xrefs;

truncate table cw_datahub_main_data;
alter table cw_datahub_main_data add column cimageurl_temp varchar(50) not null default 0 after cimageurl;

INSERT INTO cw_datahub_main_data (ID, dup_catid,Producer,name,Vintage,Size,cimageurl_temp,TareWeight,LongDesc,Region,drysweet,country,keywords,varietal,Appellation,sub_appellation,RP_Rating,RP_Review,WS_Rating,WS_Review,WE_Rating,WE_Review,DC_Rating,DC_Review,ST_Rating,ST_Review,W_S_Rating,W_S_Review,BTI_Rating,BTI_Review,Winery_Rating,Winery_Review,bot_per_case,initial_xref, catalog_id)
SELECT ID, dup_catid,Producer,name,Vintage,Size,cimageurl,TareWeight,LongDesc,Region,drysweet,country,keywords,varietal,Appellation,`sub-appellation`,`RP Rating`,`RP Review`,`WS Rating`,`WS Review`,`WE Rating`,`WE Review`,`DC Rating`,`DC Review`,`ST Rating`,`ST Review`,`W&S Rating`,`W&S Review`,`BTI Rating`,`BTI Review`,`Winery Rating`,`Winery Review`,bot_per_case,initial_xref, ID from saratoga_live_hub.item;

truncate table cw_datahub_main_data_images;
insert into cw_datahub_main_data_images (item_id, filename, web_path, system_path) select ID, cimageurl_temp, cimageurl_temp, cimageurl_temp from cw_datahub_main_data where cimageurl_temp != '' and cimageurl_temp not like '%no_image%';

update cw_datahub_main_data d, cw_datahub_main_data_images i set d.cimageurl=i.id where d.ID=i.item_id;

alter table cw_datahub_main_data drop column cimageurl_temp;

update cw_datahub_main_data d, saratoga_live_hub.item_xref i, saratoga_live_hub.xfer_products_SWE xps set d.initial_xref=i.xref, d.price=xps.cprice, d.twelve_bot_price=xps.ctwelvebottleprice, d.cost=i.cost_per_bottle, d.cost_per_case=i.cost_per_case, d.stock=i.qty_avail, d.split_case_charge=i.split_case_charge, d.supplier_id=i.supplier_id, d.min_price=i.min_price, d.bot_per_case=i.bot_per_case, d.avail_code=xps.avail_code, d.store_sku=xps.sku where d.catalog_id = xps.catalogid and xps.ccode=i.xref and i.item_id=d.catalog_id and i.store_id=1;


delete cw_datahub_import_buffer_blacklist.* from cw_datahub_import_buffer_blacklist inner join saratoga_live_hub.block_xref_from_compare bc on bc.xref=cw_datahub_import_buffer_blacklist.item_xref;
replace into cw_datahub_import_buffer_blacklist (Source, item_xref) select feed, xref from saratoga_live_hub.block_xref_from_compare;

drop table if exists block_xref_from_compare;
create table block_xref_from_compare like saratoga_live_hub.block_xref_from_compare;
insert into block_xref_from_compare select * from saratoga_live_hub.block_xref_from_compare;

truncate table cw_datahub_pos;
insert into cw_datahub_pos select * from saratoga_live_hub.pos;

drop table if exists item_xref;
create table item_xref like saratoga_live_hub.item_xref;
insert into item_xref select * from saratoga_live_hub.item_xref;

drop table if exists item_last;
drop table if exists item;
create table item like saratoga_live_hub.item;
insert into item select * from saratoga_live_hub.item;
alter table item add column dhmd_id int(11) not null default 0;
update item set dhmd_id=ID;

drop table if exists item_store2;
create table item_store2 like saratoga_live_hub.item_store2;
insert into item_store2 select * from saratoga_live_hub.item_store2;

drop table if exists pos;
create table pos like saratoga_live_hub.pos;
insert into pos select * from saratoga_live_hub.pos;

drop table if exists Compare_last;
drop table if exists feeds_item_compare;
create table feeds_item_compare like saratoga_live_hub.feeds_item_compare;
alter table feeds_item_compare add column dhmd_id int(11) not null default 0;

drop table if exists SWE_store_feed;
create table SWE_store_feed like saratoga_live_hub.SWE_store_feed;
insert into SWE_store_feed select * from saratoga_live_hub.SWE_store_feed;

drop table if exists xfer_products_SWE_snapshot;
create table xfer_products_SWE_snapshot like saratoga_live_hub.xfer_products_SWE_snapshot;
insert into xfer_products_SWE_snapshot select * from saratoga_live_hub.xfer_products_SWE_snapshot;
truncate table xfer_products_SWE;

create table if not exists item_price like saratoga_live_hub.item_price;
create table if not exists item_price_twelve_bottle like  saratoga_live_hub.item_price_twelve_bottle;
