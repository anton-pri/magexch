alter table cw_order_statuses add column inventory_decreasing int(1) not null default 0;
update cw_order_statuses set inventory_decreasing=1 where code in ('C', 'Q', 'P');
