alter table cw_products modify column productcode varchar(64) not null default '';
alter table cw_product_variants modify column productcode varchar(64) not null default '';
alter table cw_products modify column eancode varchar(64) not null default '';
alter table cw_product_variants modify column eancode varchar(64) not null default '';
alter table cw_product_variants modify column mpn varchar(64) not null default '';
